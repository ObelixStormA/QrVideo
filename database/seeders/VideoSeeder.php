<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->videos() as $data) {
            $tags = $data['tags'];
            unset($data['tags']);

            $data['category_id'] = Category::where('slug', $data['category'])->first()?->id;
            unset($data['category']);

            $video = Video::firstOrCreate(['slug' => $data['slug']], $data);
            $video->tags()->sync($tags);
        }
    }

    private function videos(): array
    {
        $tagIds = Tag::pluck('id', 'slug');

        return [
            [
                'slug' => 'javascript-tolik-kurs-yangi-boshlovchilar-uchun-2026',
                'category' => 'education',
                'title' => "JavaScript — To'liq Kurs Yangi Boshlovchilar Uchun 2026",
                'description' => "Ushbu darsda JavaScript dasturlash tilining asoslaridan boshlab, zamonaviy imkoniyatlarigacha bo'lgan barcha mavzular ko'rib chiqiladi.",
                'thumbnail' => 'https://picsum.photos/seed/v1/480/270',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                'duration_seconds' => 16335,
                'views_count' => 1250000,
                'author_name' => 'Dasturlash Akademiyasi',
                'author_avatar' => 'https://picsum.photos/seed/ch1/80/80',
                'is_live' => false,
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'tags' => [$tagIds['tasdiqlangan']],
            ],
            [
                'slug' => 'yozgi-hit-qoshiqlar-toplami-2026',
                'category' => 'music',
                'title' => "Yozgi Hit Qo'shiqlar To'plami 2026 — Eng Yaxshi Treklar",
                'description' => "2026-yilning eng mashhur yozgi qo'shiqlaridan iborat maxsus to'plam.",
                'thumbnail' => 'https://picsum.photos/seed/v2/480/270',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
                'duration_seconds' => 3767,
                'views_count' => 3400000,
                'author_name' => 'Music Uzbekistan',
                'author_avatar' => 'https://picsum.photos/seed/ch2/80/80',
                'is_live' => false,
                'status' => 'published',
                'published_at' => now()->subHours(8),
                'tags' => [$tagIds['tasdiqlangan'], $tagIds['yangi']],
            ],
            [
                'slug' => 'gta-6-birinchi-gameplay-sharhi-va-tahlili',
                'category' => 'gaming',
                'title' => 'GTA 6 — Birinchi Gameplay Sharhi va Tahlili',
                'description' => "GTA 6 o'yinining birinchi gameplay videosi bo'yicha to'liq tahlil va fikrlar.",
                'thumbnail' => 'https://picsum.photos/seed/v3/480/270',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
                'duration_seconds' => 1104,
                'views_count' => 892000,
                'author_name' => 'GameZone UZ',
                'author_avatar' => 'https://picsum.photos/seed/ch3/80/80',
                'is_live' => false,
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'tags' => [$tagIds['trend']],
            ],
            [
                'slug' => 'uy-sharoitida-osh-tayyorlash-oson-retsept',
                'category' => 'food',
                'title' => 'Uy Sharoitida Osh Tayyorlash — Oson Retsept',
                'description' => 'Milliy taomimiz osh tayyorlashning eng oson va mazali retsepti.',
                'thumbnail' => 'https://picsum.photos/seed/v4/480/270',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
                'duration_seconds' => 730,
                'views_count' => 456000,
                'author_name' => 'Oshxona Sirlari',
                'author_avatar' => 'https://picsum.photos/seed/ch4/80/80',
                'is_live' => false,
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'tags' => [$tagIds['tasdiqlangan']],
            ],
            [
                'slug' => 'iphone-17-pro-tolik-sharh-kamera-testi-va-narxi',
                'category' => 'tech',
                'title' => 'iPhone 17 Pro — To\'liq Sharh, Kamera Testi va Narxi',
                'description' => "iPhone 17 Pro haqida to'liq sharh: dizayn, unumdorlik, kamera testlari va narxi.",
                'thumbnail' => 'https://picsum.photos/seed/v5/480/270',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4',
                'duration_seconds' => 1325,
                'views_count' => 2100000,
                'author_name' => 'Tech Review UZ',
                'author_avatar' => 'https://picsum.photos/seed/ch5/80/80',
                'is_live' => false,
                'status' => 'published',
                'published_at' => now()->subHours(30),
                'tags' => [$tagIds['tasdiqlangan'], $tagIds['populyar']],
            ],
            [
                'slug' => 'ozbekiston-milliy-terma-jamoasi-eng-yaxshi-gollari',
                'category' => 'sport',
                'title' => "O'zbekiston Milliy Terma Jamoasi — Eng Yaxshi Gollari",
                'description' => "O'zbekiston milliy terma jamoasining so'nggi mavsumdagi eng yaxshi gollari to'plami.",
                'thumbnail' => 'https://picsum.photos/seed/v6/480/270',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4',
                'duration_seconds' => 587,
                'views_count' => 678000,
                'author_name' => 'Sport Olami',
                'author_avatar' => 'https://picsum.photos/seed/ch6/80/80',
                'is_live' => false,
                'status' => 'published',
                'published_at' => now()->subDays(6),
                'tags' => [],
            ],
            [
                'slug' => 'bugungi-asosiy-yangiliklar-qisqacha-sharh',
                'category' => 'news',
                'title' => 'Bugungi Asosiy Yangiliklar — Qisqacha Sharh',
                'description' => 'Bugungi kunning eng muhim yangiliklari qisqacha sharh shaklida.',
                'thumbnail' => 'https://picsum.photos/seed/v7/480/270',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerMeltdowns.mp4',
                'duration_seconds' => 932,
                'views_count' => 145000,
                'author_name' => 'Yangiliklar Kanali',
                'author_avatar' => 'https://picsum.photos/seed/ch7/80/80',
                'is_live' => false,
                'status' => 'published',
                'published_at' => now()->subHours(4),
                'tags' => [$tagIds['tasdiqlangan'], $tagIds['yangi']],
            ],
            [
                'slug' => 'kulgili-videolar-toplami-kulmasdan-iloji-yoq',
                'category' => 'comedy',
                'title' => "Kulgili Videolar To'plami — Kulmasdan Iloji Yo'q!",
                'description' => "Eng kulgili lahzalardan iborat maxsus to'plam — kulmasdan iloji yo'q!",
                'thumbnail' => 'https://picsum.photos/seed/v8/480/270',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4',
                'duration_seconds' => 658,
                'views_count' => 5600000,
                'author_name' => 'Hazil Studio',
                'author_avatar' => 'https://picsum.photos/seed/ch8/80/80',
                'is_live' => false,
                'status' => 'published',
                'published_at' => now()->subDays(12),
                'tags' => [$tagIds['populyar'], $tagIds['trend']],
            ],
            [
                'slug' => 'jonli-efir-dasturlash-boyicha-savol-javob',
                'category' => 'live',
                'title' => "Jonli Efir: Dasturlash Bo'yicha Savol-Javob",
                'description' => "Dasturlash bo'yicha jonli efirda savol-javob sessiyasi.",
                'thumbnail' => 'https://picsum.photos/seed/v9/480/270',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/SubaruOutbackOnStreetAndDirt.mp4',
                'duration_seconds' => 0,
                'views_count' => 23000,
                'author_name' => 'Dasturlash Akademiyasi',
                'author_avatar' => 'https://picsum.photos/seed/ch1/80/80',
                'is_live' => true,
                'status' => 'published',
                'published_at' => now()->subMinutes(45),
                'tags' => [$tagIds['tasdiqlangan'], $tagIds['jonli-efir'], $tagIds['yangi']],
            ],
            [
                'slug' => 'python-bilan-sunniy-intellekt-amaliy-loyiha',
                'category' => 'education',
                'title' => "Python bilan Sun'iy Intellekt — Amaliy Loyiha",
                'description' => "Python tilida sun'iy intellekt asosida amaliy loyiha yaratish darsi.",
                'thumbnail' => 'https://picsum.photos/seed/v10/480/270',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4',
                'duration_seconds' => 2112,
                'views_count' => 987000,
                'author_name' => 'Dasturlash Akademiyasi',
                'author_avatar' => 'https://picsum.photos/seed/ch1/80/80',
                'is_live' => false,
                'status' => 'published',
                'published_at' => now()->subDays(20),
                'tags' => [$tagIds['tasdiqlangan']],
            ],
            [
                'slug' => 'eng-yaxshi-noutbuklar-2026-xarid-qilishdan-oldin-koring',
                'category' => 'tech',
                'title' => "Eng Yaxshi Noutbuklar 2026 — Xarid Qilishdan Oldin Ko'ring",
                'description' => '2026-yilning eng yaxshi noutbuklari sharhi va xarid qilishdan oldingi tavsiyalar.',
                'thumbnail' => 'https://picsum.photos/seed/v11/480/270',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/VolkswagenGTIReview.mp4',
                'duration_seconds' => 880,
                'views_count' => 334000,
                'author_name' => 'Tech Review UZ',
                'author_avatar' => 'https://picsum.photos/seed/ch5/80/80',
                'is_live' => false,
                'status' => 'published',
                'published_at' => now()->subDays(9),
                'tags' => [$tagIds['tasdiqlangan'], $tagIds['tavsiya']],
            ],
            [
                'slug' => 'akustik-kontsert-tolik-ijro',
                'category' => 'music',
                'title' => "Akustik Kontsert — To'liq Ijro",
                'description' => "Akustik kontsertning to'liq ijrosi — jonli tovush bilan.",
                'thumbnail' => 'https://picsum.photos/seed/v12/480/270',
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WeAreGoingOnBullrun.mp4',
                'duration_seconds' => 2913,
                'views_count' => 156000,
                'author_name' => 'Music Uzbekistan',
                'author_avatar' => 'https://picsum.photos/seed/ch2/80/80',
                'is_live' => false,
                'status' => 'published',
                'published_at' => now()->subHour(),
                'tags' => [$tagIds['tasdiqlangan'], $tagIds['yangi']],
            ],
        ];
    }
}
