<?php

declare(strict_types=1);

namespace App\Actions\Videos;

use App\Models\Video;

class RecordVideoViewAction
{
    public function execute(Video $video): void
    {
        $video->increment('views_count');
    }
}
