<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Pekerjaan', 'slug' => 'pekerjaan', 'color' => '#3B82F6', 'icon' => 'briefcase'],
            ['name' => 'Pribadi', 'slug' => 'pribadi', 'color' => '#10B981', 'icon' => 'user'],
            ['name' => 'Belajar', 'slug' => 'belajar', 'color' => '#8B5CF6', 'icon' => 'book'],
            ['name' => 'Kesehatan', 'slug' => 'kesehatan', 'color' => '#EF4444', 'icon' => 'heart'],
            ['name' => 'Keuangan', 'slug' => 'keuangan', 'color' => '#F59E0B', 'icon' => 'wallet'],
            ['name' => 'Belanja', 'slug' => 'belanja', 'color' => '#EC4899', 'icon' => 'shopping-cart'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
