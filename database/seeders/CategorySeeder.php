<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['slug' => 'music', 'name' => 'Musiqa', 'icon' => 'fas fa-music', 'color' => '#10B981', 'sort_order' => 1],
            ['slug' => 'gaming', 'name' => 'Gaming', 'icon' => 'fas fa-gamepad', 'color' => '#8B5CF6', 'sort_order' => 2],
            ['slug' => 'news', 'name' => 'Yangiliklar', 'icon' => 'fas fa-newspaper', 'color' => '#3B82F6', 'sort_order' => 3],
            ['slug' => 'live', 'name' => 'Jonli efir', 'icon' => 'fas fa-tower-broadcast', 'color' => '#EF4444', 'sort_order' => 4],
            ['slug' => 'sport', 'name' => 'Sport', 'icon' => 'fas fa-trophy', 'color' => '#F59E0B', 'sort_order' => 5],
            ['slug' => 'education', 'name' => "Ta'lim", 'icon' => 'fas fa-graduation-cap', 'color' => '#06B6D4', 'sort_order' => 6],
            ['slug' => 'tech', 'name' => 'Texnologiya', 'icon' => 'fas fa-microchip', 'color' => '#6366F1', 'sort_order' => 7],
            ['slug' => 'comedy', 'name' => 'Komediya', 'icon' => 'fas fa-face-laugh', 'color' => '#EC4899', 'sort_order' => 8],
            ['slug' => 'food', 'name' => 'Oziq-ovqat', 'icon' => 'fas fa-utensils', 'color' => '#F97316', 'sort_order' => 9],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                    'color' => $category['color'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
