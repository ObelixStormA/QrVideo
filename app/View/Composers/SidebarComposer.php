<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\Category;
use App\Models\Video;
use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'categories' => Category::active()->withCount('videos')->get(),
            'channels' => Video::published()
                ->whereNotNull('author_name')
                ->select('author_name', 'author_avatar')
                ->groupBy('author_name', 'author_avatar')
                ->limit(6)
                ->get(),
        ]);
    }
}
