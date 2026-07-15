# Project Instructions

Always read and follow all rules from:

- .claude/knowledge/architecture-rules.md
- .claude/knowledge/coding-standards.md
- .claude/knowledge/filament4-guidelines.md
- .claude/knowledge/laravel-best-practices.md
- .claude/knowledge/spatie-guidelines.md
- .claude/knowledge/rbac-guidelines.md
- .claude/knowledge/api-guidelines.md
- .claude/knowledge/menu-builder-guidelines.md
- .claude/knowledge/ui-design-system.md
- .claude/knowledge/testing-rules.md
- .claude/knowledge/security-rules.md
- .claude/knowledge/deployment-guidelines.md

Requirements:

- Laravel 12
- PHP 8.4+
- Filament 4
- MySQL
- Tailwind CSS
- Alpine.js

Architecture:

- SOLID
- PSR-12
- Action Pattern
- DTO Pattern
- Service Layer
- Policies
- Events

Rules:

- Thin Controllers
- Business Logic in Actions/Services
- Form Requests for validation
- Pest for testing
- Spatie Permission for RBAC
- Spatie Activity Log
- Spatie Media Library

Generate production-ready code only.

---

# Template → Dynamic Website Task

Bu bo'lim faqat `public/template/` papkasi mavjud bo'lganda ishga tushadi.

## ⚠️ MAVJUD KOD HIMOYASI — HAR DOIM BIRINCHI BAJARILADIGAN

Har bir qadamdan OLDIN mavjud kodni tekshir:

```bash
ls database/migrations/
ls app/Models/
ls app/Http/Controllers/
ls app/Filament/Resources/
cat routes/web.php
```

Qoidalar:
- Fayl MAVJUD → skip, menga ayt, davom et
- Jadval mavjud → `add_column` migration yoz, qayta migration YARATMA
- `migrate:fresh` / `migrate:reset` → **HECH QACHON**
- Faqat `php artisan migrate`
- Seeder → `firstOrCreate()` / `updateOrCreate()`, hech qachon `create()`
- Mavjud Spatie Permission, ActivityLog, Media Library → O'ZGARTIRMA

## Bajarilish tartibi

### 0. Schema o'qish
`public/template/database_schema.md` faylini o'qi.
Topilmasa → to'xtab xabar ber.
O'qib bo'lgach ko'rsat: jadvallar ro'yxati, relation lar, mavjud migration lar bilan conflict bormi.

### 1. Template tahlili
`public/template/` dagi barcha HTML fayllarni o'qi:
- Sahifalar ro'yxati
- Hardcoded ma'lumotlar (matn, rasm, jadvallar)
- Qayta ishlatiladigan komponentlar (header, footer, nav)
- Asset yo'llari (css/, js/, images/)

### 2. Migration
`database_schema.md` asosida faqat YO'Q jadvallar uchun migration yarat.

Standart ustunlar:
- Har bir jadval: `id`, `timestamps()`
- Kontent: `is_published` (boolean, default true), `order` (integer, default 0)
- Rasm: `image` (string, nullable)
- Ko'p til: `title_uz`, `title_ru`, `title_en`
- FK: `constrained()->onDelete('cascade')`
- Slug: `string`, `unique`
- SEO: `meta_title`, `meta_description` (nullable)

Mavjud jadvalga ustun qo'shish:
```php
Schema::table('news', function (Blueprint $table) {
    if (!Schema::hasColumn('news', 'slug')) {
        $table->string('slug')->unique()->after('title_uz');
    }
});
```

```bash
php artisan migrate
```

### 3. Model
Faqat YO'Q modellar yaratilsin. Har birida:
- `$fillable`, `$casts`
- `scopePublished()` — `is_published = true` + `orderBy('order')`
- Relation lar (`hasMany`, `belongsTo`, `belongsToMany`)
- Slug auto-generate (`boot()` ichida `Str::slug($model->title_uz)`)
- Spatie Media Library kerak bo'lsa `HasMedia`, `InteractsWithMedia` trait

### 4. Seeder
HTML fayllardan real ma'lumotlar bilan:
```php
News::firstOrCreate(
    ['slug' => 'birinchi-yangilik'],
    [
        'title_uz'     => 'HTML dan o\'qilgan sarlavha',
        'image'        => 'template/assets/images/news1.jpg',
        'is_published' => true,
        'order'        => 1,
    ]
);
```

Seed qilish — har birini alohida:
```bash
php artisan db:seed --class=SettingSeeder
php artisan db:seed --class=NewsSeeder
```

### 5. Blade View
```
resources/views/
├── layouts/app.blade.php
├── components/
│   ├── header.blade.php
│   ├── footer.blade.php
│   └── nav.blade.php
├── pages/
│   ├── home.blade.php
│   ├── about.blade.php
│   └── contact.blade.php
└── [template sahifalari asosida]
```

Konvertatsiya:
```blade
{{-- Asset yo'llari --}}
{{ asset('template/css/style.css') }}
{{ asset('template/js/main.js') }}

{{-- DB rasm --}}
{{ Storage::url($item->image) }}

{{-- Global settings (AppServiceProvider da View::share) --}}
{{ $settings->site_name }}

{{-- Ro'yxat --}}
@foreach($items as $item) ... @endforeach

{{-- Til --}}
{{ app()->getLocale() === 'uz' ? $item->title_uz : ($item->title_ru ?? $item->title_en) }}
```

`layouts/app.blade.php` da:
```blade
<title>@yield('title', $settings->site_name ?? 'Sayt')</title>
@stack('meta')
@stack('styles')
...
@stack('scripts')
```

### 6. Controller va Route
Thin Controller — biznes logika Service/Action da:
```php
class NewsController extends Controller
{
    public function index()
    {
        $news = News::published()->with('category')->paginate(12);
        return view('news.index', compact('news'));
    }

    public function show(string $slug)
    {
        $item = News::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();
        return view('news.show', compact('item'));
    }
}
```

`AppServiceProvider::boot()`:
```php
View::share('settings', \App\Models\Setting::first());
```

Mavjud route larga qo'shib yoz, o'CHIRMA:
```php
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/yangiliklar', [NewsController::class, 'index'])->name('news.index');
Route::get('/yangiliklar/{slug}', [NewsController::class, 'show'])->name('news.show');
```

### 7. Filament 4 Resource
Faqat YO'Q Resource lar. Mavjud RBAC / ActivityLog / Permission resource larni O'ZGARTIRMA.

```bash
php artisan make:filament-resource News --generate
```

Har bir Resource:
```php
protected static ?string $navigationGroup = 'Kontent';
// Grouplar: 'Kontent', 'Tuzilma', 'Sozlamalar', 'Media'
```

Standart form fields:
```php
Forms\Components\Tabs::make()->tabs([
    Tab::make('O\'zbek')->schema([
        TextInput::make('title_uz')->required(),
        RichEditor::make('body_uz'),
    ]),
    Tab::make('Русский')->schema([
        TextInput::make('title_ru'),
        RichEditor::make('body_ru'),
    ]),
    Tab::make('English')->schema([
        TextInput::make('title_en'),
        RichEditor::make('body_en'),
    ]),
])->columnSpanFull(),

FileUpload::make('image')
    ->image()
    ->directory('uploads/news')
    ->maxSize(10240)
    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),

TextInput::make('slug')->unique(ignoreRecord: true),
TextInput::make('order')->numeric()->default(0),
Toggle::make('is_published')->default(true),
DateTimePicker::make('published_at'),

Section::make('SEO')->schema([
    TextInput::make('meta_title'),
    Textarea::make('meta_description'),
])->collapsed(),
```

RelationManager (hasMany bo'lsa):
```bash
php artisan make:filament-relation-manager AlbumResource images title_uz
```

## Har qadam bajarilgach

Qisqacha nima qilganingni ayt va **keyingisiga o'tish uchun mening tasdiqimni kut**.

## Xato bo'lsa
```bash
php artisan migrate:status    # conflict tekshirish
php artisan optimize:clear    # cache tozalash
php artisan filament:upgrade  # filament xatosi
php artisan storage:link      # storage link
```