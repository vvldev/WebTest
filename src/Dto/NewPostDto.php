<?php

declare(strict_types=1);

namespace App\Dto;

use DateTimeImmutable;

final class NewPostDto
{
    /**
     * @param list<int> $categoryIds
     */
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $content,
        public readonly ?string $imagePath,
        public readonly int $views,
        public readonly DateTimeImmutable $publishedAt,
        public readonly array $categoryIds,
    ) {
    }
}
