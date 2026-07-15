<?php

declare(strict_types=1);

namespace App\Models;

use App\Jobs\CompileMindTarget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'video_url',
        'duration_seconds',
        'views_count',
        'author_name',
        'author_avatar',
        'is_live',
        'status',
        'published_at',
        'marker_image_path',
        'mind_file_path',
        'ar_enabled',
        'mind_compile_status',
        'mind_compile_error',
    ];

    protected function casts(): array
    {
        return [
            'is_live' => 'boolean',
            'published_at' => 'datetime',
            'ar_enabled' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $video): void {
            $video->slug ??= Str::slug($video->title);
            $video->ar_uuid ??= (string) Str::uuid();
        });

        static::saved(function (self $video): void {
            if ($video->wasChanged('marker_image_path') && blank($video->mind_file_path)) {
                CompileMindTarget::dispatch($video->id);
            }

            if ($video->wasChanged('mind_file_path') && filled($video->mind_file_path) && $video->mind_compile_status !== 'ready') {
                static::whereKey($video->id)->update([
                    'mind_compile_status' => 'ready',
                    'mind_compile_error' => null,
                ]);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'video_tag');
    }

    public function getDurationFormattedAttribute(): string
    {
        if ($this->is_live) {
            return 'JONLI';
        }

        $seconds = (int) $this->duration_seconds;
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;

        return $hours > 0
            ? sprintf('%d:%02d:%02d', $hours, $minutes, $secs)
            : sprintf('%d:%02d', $minutes, $secs);
    }

    public function getViewsFormattedAttribute(): string
    {
        $views = $this->views_count;

        if ($views >= 1_000_000) {
            return round($views / 1_000_000, 1).'M';
        }

        if ($views >= 1_000) {
            return round($views / 1_000, 1).'K';
        }

        return (string) $views;
    }

    public function getThumbnailUrlAttribute(): string
    {
        if (! $this->thumbnail) {
            return "https://picsum.photos/seed/video-{$this->id}/480/270";
        }

        return $this->resolveMediaUrl($this->thumbnail);
    }

    public function getVideoUrlResolvedAttribute(): ?string
    {
        return $this->video_url ? $this->resolveMediaUrl($this->video_url) : null;
    }

    public function getMarkerImageUrlAttribute(): ?string
    {
        return $this->marker_image_path ? $this->resolveMediaUrl($this->marker_image_path) : null;
    }

    public function getMindFileUrlAttribute(): ?string
    {
        return $this->mind_file_path ? $this->resolveMediaUrl($this->mind_file_path) : null;
    }

    public function isArReady(): bool
    {
        return $this->ar_enabled
            && $this->mind_compile_status === 'ready'
            && filled($this->mind_file_path);
    }

    public function getAuthorAvatarUrlAttribute(): string
    {
        if (! $this->author_avatar) {
            return 'https://ui-avatars.com/api/?name='.urlencode($this->author_name ?? 'U').'&size=80';
        }

        return $this->resolveMediaUrl($this->author_avatar);
    }

    private function resolveMediaUrl(string $path): string
    {
        return str_starts_with($path, 'http') ? $path : Storage::url($path);
    }
}
