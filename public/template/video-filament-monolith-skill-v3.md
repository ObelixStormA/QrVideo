# Video Platform — Laravel Monolit + Filament CRUD Skill

> **Yondashuv:** Monolit — API yo'q, `routes/web.php` + Blade template.
> Faqat **3 model**: `Category`, `Tag`, `Video` + `video_tag` pivot.
>
> **UI → Backend mapping:**
> | HTML elementi | Laravel |
> |---|---|
> | Sidebar "Tarixiy shaxslar" | `Category::active()` |
> | Sidebar "Teglar" | `Tag::active()` |
> | Chips (yuqori filter) | `?category=slug` query param |
> | Video grid | `Video::published()` |
> | Qidiruv | `?search=...` query param |

---

## 🗄️ MIGRATSIYALAR

```bash
php artisan make:migration create_categories_table
php artisan make:migration create_tags_table
php artisan make:migration create_videos_table
php artisan make:migration create_video_tag_table
```

### `create_categories_table`

```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name', 150);
    $table->string('slug', 150)->unique();
    $table->text('description')->nullable();
    $table->string('image', 500)->nullable();      // sidebar avatar
    $table->string('icon', 100)->nullable()->default('fas fa-user');
    $table->string('color', 7)->nullable()->default('#8B5CF6');
    $table->unsignedSmallInteger('sort_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### `create_tags_table`

```php
Schema::create('tags', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('slug', 100)->unique();
    $table->string('color', 7)->nullable()->default('#10B981');
    $table->unsignedSmallInteger('sort_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### `create_videos_table`

```php
Schema::create('videos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
    $table->string('title', 500);
    $table->string('slug', 500)->unique();
    $table->text('description')->nullable();
    $table->string('thumbnail', 500)->nullable();
    $table->string('video_url', 500)->nullable();
    $table->unsignedInteger('duration_seconds')->default(0);
    $table->unsignedBigInteger('views_count')->default(0);
    $table->string('author_name', 255)->nullable();
    $table->string('author_avatar', 500)->nullable();
    $table->boolean('is_live')->default(false);
    $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
});
```

### `create_video_tag_table`

```php
Schema::create('video_tag', function (Blueprint $table) {
    $table->foreignId('video_id')->constrained()->cascadeOnDelete();
    $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
    $table->primary(['video_id', 'tag_id']);
});
```

---

## 🏛️ MODELLAR

### `Category`

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'image',
        'icon', 'color', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn ($m) => $m->slug ??= Str::slug($m->name));
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }
}
```

### `Tag`

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = ['name', 'slug', 'color', 'sort_order', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn ($m) => $m->slug ??= Str::slug($m->name));
    }

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }
}
```

### `Video`

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Video extends Model
{
    protected $fillable = [
        'category_id', 'title', 'slug', 'description',
        'thumbnail', 'video_url', 'duration_seconds',
        'views_count', 'author_name', 'author_avatar',
        'is_live', 'status', 'published_at',
    ];

    protected $casts = [
        'is_live'      => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn ($m) => $m->slug ??= Str::slug($m->title));
    }

    // ── Scopes ───────────────────────────────────────────────
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published')
                 ->whereNotNull('published_at')
                 ->where('published_at', '<=', now());
    }

    // ── Relationships ────────────────────────────────────────
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    // ── Accessors ────────────────────────────────────────────

    // 4532 → "1:15:32"
    public function getDurationFormattedAttribute(): string
    {
        if ($this->is_live) return 'JONLI';
        $s = $this->duration_seconds;
        $h = intdiv($s, 3600);
        $m = intdiv($s % 3600, 60);
        $sec = $s % 60;
        return $h > 0
            ? sprintf('%d:%02d:%02d', $h, $m, $sec)
            : sprintf('%d:%02d', $m, $sec);
    }

    // 1250000 → "1.2M"
    public function getViewsFormattedAttribute(): string
    {
        $v = $this->views_count;
        if ($v >= 1_000_000) return round($v / 1_000_000, 1) . 'M';
        if ($v >= 1_000)     return round($v / 1_000, 1) . 'K';
        return (string) $v;
    }

    // thumbnail yo'li → to'liq URL
    public function getThumbnailUrlAttribute(): string
    {
        return $this->thumbnail
            ? asset('storage/' . $this->thumbnail)
            : "https://picsum.photos/seed/{$this->id}/480/270";
    }

    // author avatar URL
    public function getAuthorAvatarUrlAttribute(): string
    {
        return $this->author_avatar
            ? asset('storage/' . $this->author_avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->author_name ?? 'U') . '&size=80';
    }
}
```

---

## 🌐 ROUTE — `routes/web.php`

```php
<?php
use App\Http\Controllers\VideoController;

// Asosiy sahifa
Route::get('/', [VideoController::class, 'index'])->name('home');

// Ko'rishlar sanash (form POST, redirect yo'q)
Route::post('/videos/{video}/view', [VideoController::class, 'recordView'])
     ->name('videos.view');
```

---

## 🎮 CONTROLLER — `app/Http/Controllers/VideoController.php`

```php
<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        // ── Sidebar ma'lumotlari ─────────────────────────────
        $categories = Category::active()->withCount('videos')->get();
        $tags       = Tag::active()->withCount('videos')->get();

        // ── Video query ──────────────────────────────────────
        $query = Video::published()
            ->with(['category:id,name,slug,color', 'tags:id,name,slug,color']);

        // Category filtri (?category=amir-temur)
        $activeCategory = $request->query('category');
        if ($activeCategory && $activeCategory !== 'all') {
            $query->whereHas('category', fn ($q) =>
                $q->where('slug', $activeCategory)
            );
        }

        // Tag filtri (?tag=tarix)
        $activeTag = $request->query('tag');
        if ($activeTag) {
            $query->whereHas('tags', fn ($q) =>
                $q->where('slug', $activeTag)
            );
        }

        // Qidiruv (?search=ibn)
        $search = $request->query('search');
        if ($search) {
            $query->where(fn ($q) =>
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%")
            );
        }

        $videos = $query->orderByDesc('published_at')->paginate(12)->withQueryString();

        return view('videos.index', compact(
            'videos', 'categories', 'tags',
            'activeCategory', 'activeTag', 'search'
        ));
    }

    public function recordView(Video $video)
    {
        $video->increment('views_count');
        return back();
    }
}
```

---

## 🖼️ BLADE TEMPLATE — `resources/views/videos/index.blade.php`

```blade
<!DOCTYPE html>
<html lang="uz" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Video — UZ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
{{-- CSS — asl HTML fayldagi barcha :root va komponent stillari shu yerda --}}
<style>
/* ... asl youtube-platform-template.html dagi <style> bloklari ... */
</style>
</head>
<body>

{{-- ══ NAVBAR ══════════════════════════════════════════════ --}}
<nav class="yt-navbar" id="navbar">
  <div class="navbar-left">
    <button class="hamburger-btn" id="hamburgerBtn"><i class="fas fa-bars"></i></button>
    <a class="navbar-brand" href="{{ route('home') }}">
      <img src="{{ asset('images/logo.png') }}" alt="Logo">
      <span>Video</span>
      <sup class="country-badge">UZ</sup>
    </a>
  </div>

  {{-- Qidiruv — GET form, sahifa reload bilan ─────────── --}}
  <div class="navbar-center">
    <form method="GET" action="{{ route('home') }}" class="search-container" style="display:flex">
      @if($activeCategory)
        <input type="hidden" name="category" value="{{ $activeCategory }}">
      @endif
      @if($activeTag)
        <input type="hidden" name="tag" value="{{ $activeTag }}">
      @endif
      <input type="search" name="search" placeholder="Qidirish"
             class="search-input" value="{{ $search ?? '' }}">
      <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
    </form>
  </div>

  <div class="navbar-right">
    <button class="icon-btn" id="themeToggle"><i class="fas fa-circle-half-stroke"></i></button>
  </div>
</nav>

{{-- ══ WRAPPER ══════════════════════════════════════════════ --}}
<div class="yt-wrapper">

  {{-- ── SIDEBAR ─────────────────────────────────────────── --}}
  <aside class="yt-sidebar" id="sidebar">

    {{-- Bosh sahifa --}}
    <div class="sidebar-section">
      <a href="{{ route('home') }}"
         class="nav-item {{ !$activeCategory && !$activeTag && !$search ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <span class="nav-label">Bosh sahifa</span>
      </a>
    </div>

    {{-- Tarixiy shaxslar = Categories --}}
    <div class="sidebar-section">
      <div class="section-title">Tarixiy shaxslar</div>

      @foreach($categories as $cat)
        <a href="{{ route('home', ['category' => $cat->slug]) }}"
           class="nav-item {{ $activeCategory === $cat->slug ? 'active' : '' }}">
          @if($cat->image)
            <img src="{{ asset('storage/' . $cat->image) }}"
                 style="width:24px;height:24px;border-radius:50%;object-fit:cover" alt="">
          @else
            <i class="{{ $cat->icon }}" style="color:{{ $cat->color }}"></i>
          @endif
          <span class="nav-label">{{ $cat->name }}</span>
          <span class="ms-auto" style="font-size:11px;color:var(--text-secondary)">
            {{ $cat->videos_count }}
          </span>
        </a>
      @endforeach

      @if($categories->isEmpty())
        <p class="nav-label" style="padding:8px 12px;color:var(--text-secondary);font-size:13px">
          Kategoriya yo'q
        </p>
      @endif
    </div>

    {{-- Teglar = Tags --}}
    <div class="sidebar-section">
      <div class="section-title">Teglar</div>

      @foreach($tags as $tag)
        <a href="{{ route('home', ['tag' => $tag->slug]) }}"
           class="nav-item {{ $activeTag === $tag->slug ? 'active' : '' }}">
          <i class="fas fa-hashtag" style="color:{{ $tag->color }}"></i>
          <span class="nav-label">#{{ $tag->name }}</span>
          <span class="ms-auto" style="font-size:11px;color:var(--text-secondary)">
            {{ $tag->videos_count }}
          </span>
        </a>
      @endforeach

      @if($tags->isEmpty())
        <p class="nav-label" style="padding:8px 12px;color:var(--text-secondary);font-size:13px">
          Teglar yo'q
        </p>
      @endif
    </div>

  </aside>

  <div class="sidebar-overlay" id="overlay"></div>

  {{-- ── MAIN CONTENT ─────────────────────────────────────── --}}
  <main class="yt-main" id="mainContent">

    {{-- Chips — Category filtri --}}
    <div class="chips-container">
      <div class="chips-scroll" id="chipsScroll">
        <a href="{{ route('home', array_filter(['search' => $search, 'tag' => $activeTag])) }}"
           class="chip {{ !$activeCategory ? 'active' : '' }}">
          Hammasi
        </a>

        @foreach($categories as $cat)
          <a href="{{ route('home', array_filter(['category' => $cat->slug, 'search' => $search, 'tag' => $activeTag])) }}"
             class="chip {{ $activeCategory === $cat->slug ? 'active' : '' }}"
             style="{{ $activeCategory === $cat->slug ? 'border-left:3px solid '.$cat->color : '' }}">
            {{ $cat->name }}
          </a>
        @endforeach
      </div>
    </div>

    {{-- Faol filter ko'rsatkich --}}
    @if($activeCategory || $activeTag || $search)
      <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
        @if($activeCategory)
          @php $cat = $categories->firstWhere('slug', $activeCategory) @endphp
          <span class="badge" style="background:{{ $cat?->color ?? '#666' }};font-size:13px;padding:6px 10px">
            <i class="{{ $cat?->icon ?? 'fas fa-user' }}"></i>
            {{ $cat?->name ?? $activeCategory }}
            <a href="{{ route('home', array_filter(['tag' => $activeTag, 'search' => $search])) }}"
               style="color:inherit;margin-left:6px">✕</a>
          </span>
        @endif
        @if($activeTag)
          @php $tag = $tags->firstWhere('slug', $activeTag) @endphp
          <span class="badge" style="background:{{ $tag?->color ?? '#10B981' }};font-size:13px;padding:6px 10px">
            #{{ $tag?->name ?? $activeTag }}
            <a href="{{ route('home', array_filter(['category' => $activeCategory, 'search' => $search])) }}"
               style="color:inherit;margin-left:6px">✕</a>
          </span>
        @endif
        @if($search)
          <span class="badge bg-secondary" style="font-size:13px;padding:6px 10px">
            🔍 "{{ $search }}"
            <a href="{{ route('home', array_filter(['category' => $activeCategory, 'tag' => $activeTag])) }}"
               style="color:inherit;margin-left:6px">✕</a>
          </span>
        @endif
        <small class="text-secondary">{{ $videos->total() }} ta natija</small>
      </div>
    @endif

    {{-- ── Video Grid ──────────────────────────────────────── --}}
    @if($videos->isEmpty())
      <div style="text-align:center;padding:60px 0;color:var(--text-secondary)">
        <i class="fas fa-film" style="font-size:48px;margin-bottom:16px;display:block"></i>
        <p>Videolar topilmadi.</p>
        <a href="{{ route('home') }}" class="chip" style="display:inline-block;margin-top:8px">
          Hammasini ko'rish
        </a>
      </div>
    @else
      <div class="videos-grid">
        @foreach($videos as $video)
          <div class="video-card">

            {{-- Thumbnail --}}
            <div class="thumbnail-wrapper">
              <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" loading="lazy">
              <span class="duration-badge {{ $video->is_live ? 'live-badge' : '' }}">
                {{ $video->duration_formatted }}
              </span>
              <div class="play-overlay"><i class="fas fa-play"></i></div>
            </div>

            {{-- Card info --}}
            <div class="card-info d-flex gap-2 mt-2">
              <img src="{{ $video->author_avatar_url }}"
                   class="channel-avatar rounded-circle"
                   width="36" height="36"
                   alt="{{ $video->author_name }}" loading="lazy">

              <div class="video-meta">
                <h6 class="video-title">{{ $video->title }}</h6>

                <p class="channel-name">
                  {{ $video->author_name }}
                  @if($video->category)
                    <span style="color:{{ $video->category->color }};margin-left:4px">
                      • {{ $video->category->name }}
                    </span>
                  @endif
                </p>

                <p class="video-stats">
                  {{ $video->views_formatted }} ko'rishlar
                  • {{ $video->published_at?->diffForHumans() }}
                </p>

                {{-- Teglar --}}
                @if($video->tags->isNotEmpty())
                  <div class="mt-1">
                    @foreach($video->tags as $tag)
                      <a href="{{ route('home', ['tag' => $tag->slug]) }}"
                         style="font-size:11px;color:{{ $tag->color }};margin-right:4px">
                        #{{ $tag->name }}
                      </a>
                    @endforeach
                  </div>
                @endif
              </div>

              {{-- Ko'rishlar sanash --}}
              <form method="POST" action="{{ route('videos.view', $video) }}"
                    class="ms-auto" style="display:inline">
                @csrf
                <button type="submit" class="more-btn" title="Ko'rish">
                  <i class="fas fa-play-circle"></i>
                </button>
              </form>
            </div>

          </div>
        @endforeach
      </div>

      {{-- Pagination --}}
      <div class="mt-4 d-flex justify-content-center">
        {{ $videos->links() }}
      </div>
    @endif

  </main>
</div>

{{-- Mobile bottom nav --}}
<nav class="mobile-bottom-nav">
  <a href="{{ route('home') }}"
     class="mobile-nav-item {{ !$activeCategory && !$activeTag ? 'active' : '' }}">
    <i class="fas fa-home"></i><span>Bosh sahifa</span>
  </a>
  <a href="{{ route('home', ['category' => $categories->first()?->slug]) }}"
     class="mobile-nav-item {{ $activeCategory ? 'active' : '' }}">
    <i class="fas fa-user-group"></i><span>Shaxslar</span>
  </a>
  <a href="{{ route('home', ['tag' => $tags->first()?->slug]) }}"
     class="mobile-nav-item {{ $activeTag ? 'active' : '' }}">
    <i class="fas fa-hashtag"></i><span>Teglar</span>
  </a>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle (asl HTML fayldagi JS bilan bir xil)
const sidebar     = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const overlay     = document.getElementById('overlay');

document.getElementById('hamburgerBtn').addEventListener('click', () => {
  if (window.innerWidth < 992) {
    const open = sidebar.classList.toggle('mobile-open');
    overlay.classList.toggle('active', open);
    document.body.style.overflow = open ? 'hidden' : '';
  } else {
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
  }
});

overlay.addEventListener('click', () => {
  overlay.classList.remove('active');
  sidebar.classList.remove('mobile-open');
  document.body.style.overflow = '';
});

// Dark/Light mode
const htmlEl = document.documentElement;
document.getElementById('themeToggle').addEventListener('click', () => {
  const next = htmlEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
  htmlEl.setAttribute('data-theme', next);
  localStorage.setItem('yt-theme', next);
});
htmlEl.setAttribute('data-theme', localStorage.getItem('yt-theme') || 'dark');

window.addEventListener('resize', () => {
  if (window.innerWidth >= 992) {
    overlay.classList.remove('active');
    sidebar.classList.remove('mobile-open');
    document.body.style.overflow = '';
  }
});
</script>

</body>
</html>
```

---

## 🎛️ FILAMENT RESOURCES

```bash
# O'rnatish
composer require filament/filament:"^3.0" -W
php artisan filament:install --panels
php artisan make:filament-user

# Resources
php artisan make:filament-resource Category --generate
php artisan make:filament-resource Tag      --generate
php artisan make:filament-resource Video    --generate
```

### `CategoryResource`

```php
<?php
namespace App\Filament\Resources;

use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model            = Category::class;
    protected static ?string $navigationIcon   = 'heroicon-o-user-group';
    protected static ?string $navigationGroup  = 'Kontent';
    protected static ?string $modelLabel       = 'Tarixiy shaxs';
    protected static ?string $pluralModelLabel = 'Tarixiy shaxslar';
    protected static ?int    $navigationSort   = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Asosiy')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Ismi')->required()->maxLength(150)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($s, Forms\Set $set) => $set('slug', Str::slug($s))),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')->required()->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('description')
                    ->label('Tavsif')->rows(3)->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Ko\'rinish')->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Rasm')->image()->imageEditor()
                    ->imageCropAspectRatio('1:1')
                    ->directory('categories')->maxSize(2048),

                Forms\Components\TextInput::make('icon')
                    ->label('Font Awesome icon')
                    ->default('fas fa-user')
                    ->placeholder('fas fa-user'),

                Forms\Components\ColorPicker::make('color')
                    ->label('Rang')->default('#8B5CF6'),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Tartib')->numeric()->default(0),

                Forms\Components\Toggle::make('is_active')
                    ->label('Faol')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('')->circular()->size(40),
                Tables\Columns\TextColumn::make('name')->label('Ismi')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug')->badge()->color('gray'),
                Tables\Columns\ColorColumn::make('color')->label('Rang'),
                Tables\Columns\TextColumn::make('videos_count')->label('Video')
                    ->counts('videos')->badge()->color('info'),
                Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Faol')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Holati'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
```

---

### `TagResource`

```php
<?php
namespace App\Filament\Resources;

use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TagResource extends Resource
{
    protected static ?string $model            = Tag::class;
    protected static ?string $navigationIcon   = 'heroicon-o-hashtag';
    protected static ?string $navigationGroup  = 'Kontent';
    protected static ?string $modelLabel       = 'Teg';
    protected static ?string $pluralModelLabel = 'Teglar';
    protected static ?int    $navigationSort   = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nomi')->required()->maxLength(100)->prefix('#')
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($s, Forms\Set $set) => $set('slug', Str::slug($s))),

            Forms\Components\TextInput::make('slug')
                ->label('Slug')->required()->unique(ignoreRecord: true),

            Forms\Components\ColorPicker::make('color')
                ->label('Rang')->default('#10B981'),

            Forms\Components\TextInput::make('sort_order')
                ->label('Tartib')->numeric()->default(0),

            Forms\Components\Toggle::make('is_active')
                ->label('Faol')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Teg')
                    ->formatStateUsing(fn ($s) => '#' . $s)->searchable()->sortable(),
                Tables\Columns\ColorColumn::make('color')->label('Rang'),
                Tables\Columns\TextColumn::make('videos_count')->label('Video')
                    ->counts('videos')->badge()->color('success'),
                Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Faol')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Holati'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit'   => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
```

---

### `VideoResource`

```php
<?php
namespace App\Filament\Resources;

use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VideoResource extends Resource
{
    protected static ?string $model            = Video::class;
    protected static ?string $navigationIcon   = 'heroicon-o-play-circle';
    protected static ?string $navigationGroup  = 'Kontent';
    protected static ?string $modelLabel       = 'Video';
    protected static ?string $pluralModelLabel = 'Videolar';
    protected static ?int    $navigationSort   = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([

            // ─── Chap: asosiy (2/3 kenglik) ────────────────
            Forms\Components\Group::make()->schema([

                Forms\Components\Section::make('Video ma\'lumotlari')->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Sarlavha')->required()->maxLength(500)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($s, Forms\Set $set) => $set('slug', Str::slug($s)))
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')->required()->unique(ignoreRecord: true)->columnSpanFull(),

                    Forms\Components\Select::make('category_id')
                        ->label('Tarixiy shaxs')
                        ->relationship('category', 'name')
                        ->searchable()->preload()->nullable(),

                    Forms\Components\Select::make('tags')
                        ->label('Teglar')
                        ->relationship('tags', 'name')
                        ->multiple()->searchable()->preload(),

                    Forms\Components\Textarea::make('description')
                        ->label('Tavsif')->rows(4)->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('Media')->schema([
                    Forms\Components\FileUpload::make('thumbnail')
                        ->label('Thumbnail')->image()->imageEditor()
                        ->imageCropAspectRatio('16:9')
                        ->directory('videos/thumbnails')->maxSize(5120)
                        ->helperText('Tavsiya: 1280×720 px'),

                    Forms\Components\TextInput::make('video_url')
                        ->label('Video URL')->columnSpanFull(),

                    Forms\Components\TextInput::make('duration_seconds')
                        ->label('Davomiyligi (soniya)')->numeric()->default(0)
                        ->suffix('soniya')->helperText('272 → "4:32"'),
                ])->columns(2),

                Forms\Components\Section::make('Muallif')->schema([
                    Forms\Components\TextInput::make('author_name')
                        ->label('Muallif nomi'),

                    Forms\Components\FileUpload::make('author_avatar')
                        ->label('Muallif avatari')->image()->imageEditor()
                        ->imageCropAspectRatio('1:1')->directory('authors'),
                ])->columns(2),

            ])->columnSpan(['lg' => 2]),

            // ─── O'ng: meta (1/3 kenglik) ───────────────────
            Forms\Components\Group::make()->schema([

                Forms\Components\Section::make('Nashr holati')->schema([
                    Forms\Components\Select::make('status')
                        ->label('Holat')
                        ->options([
                            'draft'     => '📝 Qoralama',
                            'published' => '✅ Nashr etilgan',
                            'archived'  => '📦 Arxiv',
                        ])
                        ->default('draft')->required()->native(false),

                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Nashr vaqti')->helperText('Bo\'sh = hozir'),
                ]),

                Forms\Components\Section::make('Sozlamalar')->schema([
                    Forms\Components\Toggle::make('is_live')
                        ->label('JONLI efir'),
                ]),

                Forms\Components\Section::make('Statistika')->schema([
                    Forms\Components\TextInput::make('views_count')
                        ->label('Ko\'rishlar')->numeric()->default(0),
                ]),

            ])->columnSpan(['lg' => 1]),

        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('')->width(120)->height(67)
                    ->extraImgAttributes(['style' => 'border-radius:8px;object-fit:cover']),

                Tables\Columns\TextColumn::make('title')
                    ->label('Video')->searchable()->sortable()->limit(45)
                    ->description(fn ($r) => ($r->author_name ?? '—') . ' • ' . $r->views_formatted . " ko'rishlar"),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Shaxs')->badge()->color('purple'),

                Tables\Columns\TextColumn::make('tags.name')
                    ->label('Teglar')->badge()->color('success')->separator(','),

                Tables\Columns\TextColumn::make('duration_seconds')
                    ->label('Vaqt')
                    ->formatStateUsing(fn ($s, $r) => $r->duration_formatted),

                Tables\Columns\BadgeColumn::make('status')->label('Holat')
                    ->colors(['gray' => 'draft', 'success' => 'published', 'warning' => 'archived'])
                    ->formatStateUsing(fn ($s) => match ($s) {
                        'draft'     => 'Qoralama',
                        'published' => 'Nashr etilgan',
                        'archived'  => 'Arxiv',
                    }),

                Tables\Columns\IconColumn::make('is_live')->label('Jonli')->boolean(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Sana')->dateTime('d.m.Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')->label('Tarixiy shaxs'),
                Tables\Filters\SelectFilter::make('status')->label('Holat')
                    ->options(['draft' => 'Qoralama', 'published' => 'Nashr etilgan', 'archived' => 'Arxiv']),
                Tables\Filters\TernaryFilter::make('is_live')->label('Jonli efir'),
            ])
            ->actions([
                // Bir tugma bilan nashr
                Tables\Actions\Action::make('publish')
                    ->label('Nashr et')->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn ($r) => $r->status !== 'published')
                    ->action(fn ($r) => $r->update(['status' => 'published', 'published_at' => now()])),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish_all')
                        ->label('Tanlanganlari nashr et')
                        ->icon('heroicon-o-check-circle')->color('success')
                        ->action(fn ($records) => $records->each->update([
                            'status' => 'published', 'published_at' => now(),
                        ])),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit'   => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
```

---

## ⚙️ Qo'shimcha sozlamalar

### Pagination stilini Bootstrap bilan moslashtirish

`app/Providers/AppServiceProvider.php`:

```php
use Illuminate\Pagination\Paginator;

public function boot(): void
{
    Paginator::useBootstrap();
}
```

### Storage link va `.env`

```bash
php artisan storage:link
```

```env
APP_URL=http://localhost
FILESYSTEM_DISK=public
```

---

## ✅ Tekshiruv ro'yxati

### DB va Modellar
- [ ] 4 migratsiya bajarilgan (`php artisan migrate`)
- [ ] `storage:link` bajarilgan
- [ ] `Category`, `Tag`, `Video` modellari yaratilgan

### Web routes
- [ ] `GET /` — bosh sahifa ishlaydi
- [ ] `GET /?category=amir-temur` — kategoriya filtri ishlaydi
- [ ] `GET /?tag=tarix` — teg filtri ishlaydi
- [ ] `GET /?search=ibn` — qidiruv ishlaydi
- [ ] `POST /videos/{id}/view` — ko'rishlar sanaydi

### Blade
- [ ] Sidebar "Tarixiy shaxslar" → DB dan kategoriyalar
- [ ] Sidebar "Teglar" → DB dan teglar
- [ ] Chips → DB dan kategoriyalar
- [ ] Video grid → DB dan videolar (paginate 12)
- [ ] Faol filter badge va "✕" tugmasi ishlaydi
- [ ] Bo'sh holat (0 video) ko'rsatilmoqda
- [ ] Pagination ko'rsatilmoqda

### Filament Admin
- [ ] `GET /admin` — login ishlaydi
- [ ] `CategoryResource` — CRUD, rasm yuklash, drag-drop sort
- [ ] `TagResource` — CRUD, rang, drag-drop sort
- [ ] `VideoResource` — thumbnail, category, tags, publish action

---

## 📁 Fayl tuzilmasi

```
app/
├── Http/Controllers/
│   └── VideoController.php
├── Models/
│   ├── Category.php
│   ├── Tag.php
│   └── Video.php
├── Filament/Resources/
│   ├── CategoryResource/
│   │   └── Pages/{List,Create,Edit}Categories.php
│   ├── TagResource/
│   │   └── Pages/{List,Create,Edit}Tags.php
│   └── VideoResource/
│       └── Pages/{List,Create,Edit}Videos.php

database/migrations/
├── ..._create_categories_table.php
├── ..._create_tags_table.php
├── ..._create_videos_table.php
└── ..._create_video_tag_table.php

resources/views/
└── videos/
    └── index.blade.php

routes/
└── web.php
```

---

*Skill versiyasi: 3.0.0 — Monolit (Blade, API yo'q) | Sana: 2026*
