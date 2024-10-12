<?php

namespace App\Services;

class VideoService
{
    private const SECTION_VIDEOS = [
        'LandScaping' => 'landscaping.mp4',
        'Cladding' => 'cladding.mp4',
    ];

    public function getVideoPath(string $type, string $basePath): ?string
    {
        return self::SECTION_VIDEOS[$type] ?? null ? "{$basePath}/" . self::SECTION_VIDEOS[$type] : null;
    }
}