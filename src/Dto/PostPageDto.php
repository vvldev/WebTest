<?php

declare(strict_types=1);

namespace App\Dto;

final class PostPageDto
{
    /**
     * @param list<PostPreviewDto> $posts
     */
    public function __construct(
        public readonly array $posts,
        public readonly PaginationDto $pagination,
    ) {
    }
}
