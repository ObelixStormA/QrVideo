<?php

declare(strict_types=1);

namespace App\Actions\Videos;

use App\DataTransferObjects\QrCodeFile;
use App\Models\Video;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class GenerateArInvitationPdfAction
{
    public function __construct(private readonly GenerateArQrCodeAction $generateArQrCodeAction) {}

    public function execute(Video $video): QrCodeFile
    {
        if (blank($video->marker_image_path)) {
            throw new RuntimeException('Video uchun marker rasm yuklanmagan.');
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($video->marker_image_path)) {
            throw new RuntimeException('Marker rasm faylini topib bo\'lmadi.');
        }

        $imageJpeg = $this->flattenToJpeg($disk->get($video->marker_image_path));
        $qrFile = $this->generateArQrCodeAction->execute($video, 'png');

        $pdf = Pdf::loadView('pdf.ar-invitation', [
            'title' => $video->title,
            'imageSrc' => 'data:image/jpeg;base64,'.base64_encode($imageJpeg),
            'qrSrc' => 'data:image/png;base64,'.base64_encode($qrFile->content),
        ])->setPaper('a5', 'portrait');

        return new QrCodeFile(
            content: $pdf->output(),
            mimeType: 'application/pdf',
            filename: Str::slug($video->title).'-ar-taklifnoma.pdf',
        );
    }

    private function flattenToJpeg(string $imageContent): string
    {
        $source = imagecreatefromstring($imageContent);
        $width = imagesx($source);
        $height = imagesy($source);

        $flattened = imagecreatetruecolor($width, $height);
        imagefill($flattened, 0, 0, imagecolorallocate($flattened, 255, 255, 255));
        imagecopy($flattened, $source, 0, 0, 0, 0, $width, $height);
        imagedestroy($source);

        ob_start();
        imagejpeg($flattened, quality: 90);
        $jpeg = ob_get_clean();

        imagedestroy($flattened);

        return $jpeg;
    }
}
