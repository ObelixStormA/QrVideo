<?php

declare(strict_types=1);

namespace App\Actions\Videos;

use App\DataTransferObjects\QrCodeFile;
use App\Models\Video;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Str;

class GenerateArQrCodeAction
{
    private const int SIZE = 600;

    public function execute(Video $video, string $format): QrCodeFile
    {
        $url = route('ar.show', $video->ar_uuid);
        $filename = Str::slug($video->title).'-qr';

        return match ($format) {
            'png' => new QrCodeFile(
                content: $this->renderPng($url),
                mimeType: 'image/png',
                filename: "{$filename}.png",
            ),
            'svg' => new QrCodeFile(
                content: $this->renderSvg($url),
                mimeType: 'image/svg+xml',
                filename: "{$filename}.svg",
            ),
            default => throw new \InvalidArgumentException("Noma'lum QR format: {$format}"),
        };
    }

    private function renderPng(string $url): string
    {
        $options = new QROptions([
            'outputType' => QROutputInterface::GDIMAGE_PNG,
            'outputBase64' => false,
            'scale' => 20,
            'quietzoneSize' => 2,
        ]);

        $raw = (new QRCode($options))->render($url);

        $source = imagecreatefromstring($raw);
        $resized = imagecreatetruecolor(self::SIZE, self::SIZE);
        imagefill($resized, 0, 0, imagecolorallocate($resized, 255, 255, 255));
        imagecopyresampled(
            $resized, $source,
            0, 0, 0, 0,
            self::SIZE, self::SIZE,
            imagesx($source), imagesy($source),
        );

        ob_start();
        imagepng($resized);
        $png = ob_get_clean();

        imagedestroy($source);
        imagedestroy($resized);

        return $png;
    }

    private function renderSvg(string $url): string
    {
        $options = new QROptions([
            'outputType' => QROutputInterface::MARKUP_SVG,
            'outputBase64' => false,
            'scale' => 20,
            'quietzoneSize' => 2,
        ]);

        $svg = (new QRCode($options))->render($url);

        return preg_replace(
            '/<svg /',
            '<svg width="'.self::SIZE.'" height="'.self::SIZE.'" ',
            $svg,
            limit: 1,
        );
    }
}
