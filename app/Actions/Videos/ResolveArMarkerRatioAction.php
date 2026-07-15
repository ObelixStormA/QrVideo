<?php

declare(strict_types=1);

namespace App\Actions\Videos;

use App\Models\Video;
use Illuminate\Support\Facades\Storage;

class ResolveArMarkerRatioAction
{
    private const float DEFAULT_RATIO = 0.5625;

    public function execute(Video $video): float
    {
        if (blank($video->marker_image_path)) {
            return self::DEFAULT_RATIO;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($video->marker_image_path)) {
            return self::DEFAULT_RATIO;
        }

        $size = @getimagesize($disk->path($video->marker_image_path));

        if (! $size || $size[0] <= 0) {
            return self::DEFAULT_RATIO;
        }

        [$width, $height] = $size;

        return round($height / $width, 4);
    }
}
