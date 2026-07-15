<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Models\Video;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompileMindTarget implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 1;

    public int $timeout = 320;

    public function __construct(public readonly int $videoId) {}

    public function handle(): void
    {
        $video = Video::find($this->videoId);

        if (! $video) {
            return;
        }

        if (blank($video->marker_image_path)) {
            Log::warning('MindAR compile skipped: no marker image', ['video_id' => $video->id]);

            return;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($video->marker_image_path)) {
            $this->markFailed($video, 'Marker rasm faylni topib bo\'lmadi.');

            return;
        }

        $video->update([
            'mind_compile_status' => 'processing',
            'mind_compile_error' => null,
        ]);

        $relativeOutput = "mind-targets/{$video->id}.mind";
        $disk->makeDirectory('mind-targets');

        $result = Process::timeout((int) config('services.mind_ar.timeout'))->run([
            config('services.mind_ar.node_binary'),
            config('services.mind_ar.compile_script'),
            $disk->path($video->marker_image_path),
            $disk->path($relativeOutput),
        ]);

        if ($result->successful() && $disk->exists($relativeOutput)) {
            $video->update([
                'mind_file_path' => $relativeOutput,
                'mind_compile_status' => 'ready',
                'mind_compile_error' => null,
            ]);

            return;
        }

        $error = trim($result->errorOutput() ?: $result->output()) ?: 'Node kompilyatsiya muvaffaqiyatsiz tugadi.';

        $this->markFailed($video, $error);
    }

    private function markFailed(Video $video, string $error): void
    {
        Log::error('MindAR target compile failed', [
            'video_id' => $video->id,
            'error' => $error,
        ]);

        $video->update([
            'mind_compile_status' => 'failed',
            'mind_compile_error' => Str::limit($error, 2000),
            'ar_enabled' => false,
        ]);

        User::permission('update_video')->get()->each(
            fn (User $user) => Notification::make()
                ->danger()
                ->title('.mind fayl kompilyatsiyasi muvaffaqiyatsiz')
                ->body("«{$video->title}» videosi uchun marker rasmni kompilyatsiya qilib bo'lmadi.")
                ->sendToDatabase($user)
        );
    }
}
