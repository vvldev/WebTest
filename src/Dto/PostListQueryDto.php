<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enum\PostSort;

final class PostListQueryDto
{
    public function __construct(
        public readonly int $categoryId,
        public readonly PostSort $sort,
        public readonly int $page,
    ) {
    }
}
