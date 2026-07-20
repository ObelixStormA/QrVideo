<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Videos\ResolveArMarkerRatioAction;
use App\Models\Video;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ArViewController extends Controller
{
    public function __invoke(string $uuid, ResolveArMarkerRatioAction $resolveRatio): View|Response
    {
        $video = Video::where('ar_uuid', $uuid)->first();

        if (! $video) {
            return response()->view('ar.error', [
                'message' => 'Bunday AR video topilmadi. QR kodni qayta tekshiring.',
            ], 404);
        }

        if (! $video->isArReady()) {
            return response()->view('ar.error', [
                'message' => match ($video->mind_compile_status) {
                    'processing', 'pending' => 'AR target hali tayyorlanmoqda. Birozdan so\'ng qayta urinib ko\'ring.',
                    'failed' => 'AR target tayyorlashda xatolik yuz berdi. Administratorga murojaat qiling.',
                    default => 'Bu video uchun AR funksiyasi hali yoqilmagan.',
                },
            ], 404);
        }

        return response()->view('ar.show', [
            'video' => $video,
            'mindUrl' => $video->mind_file_url,
            'videoUrl' => $video->video_url_resolved,
            'ratio' => $resolveRatio->execute($video),
        ]);
    }
}
