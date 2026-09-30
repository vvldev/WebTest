<?php

declare(strict_types=1);

namespace App\Dto;

use DateTimeImmutable;

final class PostDto
{
    /**
     * @param list<string> $paragraphs
     * @param list<CategoryDto> $categories
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $description,
        public readonly array $paragraphs,
        public readonly ?string $imagePath,
        public readonly int $views,
        public readonly DateTimeImmutable $publishedAt,
        public readonly array $categories,
    ) {
    }
}
