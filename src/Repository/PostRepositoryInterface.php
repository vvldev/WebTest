<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\PostDto;
use App\Dto\PostPreviewDto;
use App\Enum\PostSort;

interface PostRepositoryInterface
{
    public function countByCategory(int $categoryId): int;

    /**
     * @return list<PostPreviewDto>
     */
    public function findByCategory(int $categoryId, PostSort $sort, int $limit, int $offset): array;

    /**
     * @return bool false when the post does not exist
     */
    public function incrementViews(int $id): bool;

    public function findById(int $id): ?PostDto;

    /**
     * Posts sharing categories with the given one: more shared categories first, then newer.
     *
     * @return list<PostPreviewDto>
     */
    public function findSimilar(int $postId, int $limit): array;
}
