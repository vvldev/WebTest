<?php

declare(strict_types=1);

namespace App\Dto;

final class CategoryWithPostsDto
{
    /**
     * @param list<PostPreviewDto> $posts
     */
    public function __construct(
        public readonly CategoryDto $category,
        public readonly array $posts,
    ) {
    }
}
