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

    public function parseTaskInput(string $input): array
    {
        $categories = Category::pluck('name', 'id')->toArray();
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
1. **title**: Judul tugas singkat dan jelas (3-8 kata).
2. **description**: Deskripsi detail jika perlu, atau null.
3. **category_id**: ID kategori yang sesuai, atau null.
4. **priority**: "urgent"/"darurat"/"segera" → "urgent", "penting"/"harus" → "high", default → "medium", "santai"/"nanti" → "low"
5. **deadline**: Parse waktu dari input (format: "YYYY-MM-DD HH:mm:ss"), atau null jika tidak ada.

Response HARUS JSON valid tanpa markdown code block.

Format: {"title": "string", "description": "string|null", "category_id": number|null, "priority": "low|medium|high|urgent", "deadline": "YYYY-MM-DD HH:mm:ss|null"}

Input user: "{$input}"
PROMPT;

        try {
            $url = $this->endpoint . $this->model . ':generateContent';

            $response = Http::timeout(30)
                ->withHeaders([
                    'x-goog-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'topP' => 0.8,
                        'maxOutputTokens' => 1024,
                        'responseMimeType' => 'application/json',
                    ],
                ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text', '');

                // Sanitize response
                $text = preg_replace('/```json\s*/', '', $text);
                $text = preg_replace('/```\s*/', '', $text);
                $text = preg_replace('/[\x00-\x1F\x7F]/', ' ', $text);
                $text = trim($text);

                $parsed = json_decode($text, true);

                if (json_last_error() === JSON_ERROR_NONE && $parsed) {
                    return $this->validateParsedData($parsed);
                }

                Log::warning('AI Parse: Invalid JSON', ['text' => $text]);
            } else {
                Log::error('AI Parse: API error', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('AI Parse: Exception', ['message' => $e->getMessage()]);
        }

        return $this->fallbackParse($input);
    }

    protected function formatCategories(array $categories): string
    {
        $lines = [];
        foreach ($categories as $id => $name) {
            $lines[] = "- ID {$id}: {$name}";
        }
        return implode("\n", $lines);
    }

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

    protected function validateCategoryId($categoryId): ?int
    {
        if ($categoryId === null)
            return null;

        return Category::where('id', $categoryId)->exists() ? (int) $categoryId : null;
    }

    protected function validateDeadline($deadline): ?string
    {
        if ($deadline === null || $deadline === 'null')
            return null;

        try {
            $date = \Carbon\Carbon::parse($deadline);
            return $date->isPast() ? null : $date->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

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
