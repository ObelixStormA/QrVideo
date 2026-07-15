<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

readonly class QrCodeFile
{
    public function __construct(
        public string $content,
        public string $mimeType,
        public string $filename,
    ) {}
}
