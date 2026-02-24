<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Category;

class AiParseService
{
    protected string $apiKey;
    protected string $model;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('gemini.api_key');
        $this->model = config('gemini.model');
        $this->endpoint = config('gemini.endpoint');
    }

    /**
     * Parse natural language input into structured task data.
     *
     * @param string $input User's natural language input
     * @return array Parsed task data (title, description, category_id, priority, deadline)
     */
    public function parseTaskInput(string $input): array
    {
        $categories = Category::pluck('name', 'id')->toArray();
        $categoryList = implode(', ', $categories);
        $today = now()->format('Y-m-d H:i:s');
        $dayOfWeek = now()->format('l');

        $prompt = <<<PROMPT
Kamu adalah asisten AI yang membantu mengubah input bahasa manusia menjadi data tugas yang terstruktur.

Informasi waktu saat ini:
- Tanggal & Waktu: {$today}
- Hari: {$dayOfWeek}

Kategori yang tersedia (gunakan ID-nya):
{$this->formatCategories($categories)}

Aturan parsing:
1. **title**: Buat judul tugas yang singkat dan jelas dari input user (3-8 kata).
2. **description**: Buat deskripsi lebih detail jika diperlukan, atau null jika input sudah cukup jelas.
3. **category_id**: Pilih kategori yang paling sesuai dari daftar di atas (gunakan ID angka). Jika tidak cocok, gunakan null.
4. **priority**: Tentukan prioritas berdasarkan urgensi dalam kalimat:
   - "urgent"/"darurat"/"segera"/"ASAP" → "urgent"
   - "penting"/"harus" → "high"
   - default → "medium"
   - "santai"/"kapan-kapan"/"nanti" → "low"
5. **deadline**: Parse tanggal/waktu dari input. Contoh:
   - "besok" → tanggal besok jam 23:59
   - "lusa" → tanggal lusa jam 23:59
   - "minggu depan" → senin depan jam 23:59
   - "tanggal 25" → tanggal 25 bulan ini/depan jam 23:59
   - "3 hari lagi" → 3 hari dari sekarang jam 23:59
   - Jika tidak ada info waktu → null
   Format deadline: "YYYY-MM-DD HH:mm:ss" atau null

PENTING: Response HARUS dalam format JSON valid, tanpa markdown code block, tanpa penjelasan tambahan. Hanya JSON murni.

Format response:
{"title": "string", "description": "string atau null", "category_id": number atau null, "priority": "low|medium|high|urgent", "deadline": "YYYY-MM-DD HH:mm:ss atau null"}

Input user: "{$input}"
PROMPT;

        try {
            $url = $this->endpoint . $this->model . ':generateContent';

            Log::info('AI Parse: Sending request', ['url' => $url, 'model' => $this->model, 'input' => $input]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'x-goog-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'topP' => 0.8,
                        'maxOutputTokens' => 1024,
                        'responseMimeType' => 'application/json',
                    ],
                ]);

            Log::info('AI Parse: Response status', ['status' => $response->status()]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text', '');

                Log::info('AI Parse: Raw AI text', ['text' => substr($text, 0, 500)]);

                // Clean up response
                $text = preg_replace('/```json\s*/', '', $text);
                $text = preg_replace('/```\s*/', '', $text);
                // Remove control characters that break json_decode
                $text = preg_replace('/[\x00-\x1F\x7F]/', ' ', $text);
                $text = trim($text);

                Log::info('AI Parse: Cleaned text', ['text' => $text]);

                $parsed = json_decode($text, true);

                if (json_last_error() === JSON_ERROR_NONE && $parsed) {
                    $result = $this->validateParsedData($parsed);
                    Log::info('AI Parse: Final result', $result);
                    return $result;
                }

                Log::warning('AI Parse: Invalid JSON response', ['text' => $text, 'json_error' => json_last_error_msg()]);
            } else {
                Log::error('AI Parse: API error', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('AI Parse: Exception', ['message' => $e->getMessage()]);
        }

        // Fallback: return basic structure with the input as title
        Log::warning('AI Parse: Using fallback');
        return $this->fallbackParse($input);
    }

    /**
     * Format categories for the AI prompt.
     */
    protected function formatCategories(array $categories): string
    {
        $lines = [];
        foreach ($categories as $id => $name) {
            $lines[] = "- ID {$id}: {$name}";
        }
        return implode("\n", $lines);
    }

    /**
     * Validate and sanitize parsed data from AI.
     */
    protected function validateParsedData(array $data): array
    {
        $validPriorities = ['low', 'medium', 'high', 'urgent'];

        return [
            'title' => substr($data['title'] ?? 'Untitled Task', 0, 255),
            'description' => $data['description'] ?? null,
            'category_id' => $this->validateCategoryId($data['category_id'] ?? null),
            'priority' => in_array($data['priority'] ?? '', $validPriorities) ? $data['priority'] : 'medium',
            'deadline' => $this->validateDeadline($data['deadline'] ?? null),
        ];
    }

    /**
     * Validate category ID exists in database.
     */
    protected function validateCategoryId($categoryId): ?int
    {
        if ($categoryId === null) {
            return null;
        }

        return Category::where('id', $categoryId)->exists() ? (int) $categoryId : null;
    }

    /**
     * Validate and parse deadline string.
     */
    protected function validateDeadline($deadline): ?string
    {
        if ($deadline === null || $deadline === 'null') {
            return null;
        }

        try {
            $date = \Carbon\Carbon::parse($deadline);
            // Don't accept dates in the past
            if ($date->isPast()) {
                return null;
            }
            return $date->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Fallback parsing when AI fails.
     */
    protected function fallbackParse(string $input): array
    {
        return [
            'title' => substr($input, 0, 255),
            'description' => null,
            'category_id' => null,
            'priority' => 'medium',
            'deadline' => null,
        ];
    }
}
