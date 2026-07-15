<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Videos\ListPublishedVideosAction;
use App\Actions\Videos\RecordVideoViewAction;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(Request $request, ListPublishedVideosAction $action): View
    {
        $activeCategory = $request->query('category');
        $activeTag = $request->query('tag');
        $search = $request->query('search');

        $videos = $action->execute($activeCategory, $activeTag, $search);

        return view('videos.index', compact('videos', 'activeCategory', 'activeTag', 'search'));
    }

    public function show(Video $video, RecordVideoViewAction $action): View
    {
        abort_unless($video->status === 'published', 404);

        $action->execute($video);

        return view('videos.show', compact('video'));
    }
}
