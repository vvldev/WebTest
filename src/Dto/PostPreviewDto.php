<?php

declare(strict_types=1);

namespace App\Dto;

use DateTimeImmutable;

final class PostPreviewDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $description,
        public readonly ?string $imagePath,
        public readonly int $views,
        public readonly DateTimeImmutable $publishedAt,
    ) {
    }
}
