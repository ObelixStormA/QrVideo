<?php

declare(strict_types=1);

namespace App\Actions\Videos;

use App\Models\Video;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListPublishedVideosAction
{
    public function execute(?string $category, ?string $tag, ?string $search): LengthAwarePaginator
    {
        $query = Video::published()
            ->with(['category:id,name,slug,color', 'tags:id,name,slug,color']);

        if ($category && $category !== 'all') {
            $query->whereHas('category', fn ($q) => $q->where('slug', $category));
        }

        if ($tag) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $tag));
        }

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('published_at')->paginate(12)->withQueryString();
    }
}
