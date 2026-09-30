<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\PostPreviewDto;
use App\Enum\PostSort;

interface PostRepositoryInterface
{
    public function countByCategory(int $categoryId): int;

    /**
     * @return list<PostPreviewDto>
     */
    public function findByCategory(int $categoryId, PostSort $sort, int $limit, int $offset): array;
}
