<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['slug' => 'tasdiqlangan', 'name' => 'Tasdiqlangan', 'color' => '#10B981', 'sort_order' => 1],
            ['slug' => 'jonli-efir', 'name' => 'JonliEfir', 'color' => '#EF4444', 'sort_order' => 2],
            ['slug' => 'yangi', 'name' => 'Yangi', 'color' => '#3B82F6', 'sort_order' => 3],
            ['slug' => 'trend', 'name' => 'Trend', 'color' => '#F59E0B', 'sort_order' => 4],
            ['slug' => 'tavsiya', 'name' => 'Tavsiya', 'color' => '#8B5CF6', 'sort_order' => 5],
            ['slug' => 'populyar', 'name' => 'Populyar', 'color' => '#EC4899', 'sort_order' => 6],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['slug' => $tag['slug']],
                [
                    'name' => $tag['name'],
                    'color' => $tag['color'],
                    'sort_order' => $tag['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
