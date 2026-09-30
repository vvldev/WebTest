<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\CategoryDto;
use App\Dto\CategoryWithPostsDto;

interface CategoryRepositoryInterface
{
    /**
     * Categories that have at least one post, each with its latest posts.
     *
     * @return list<CategoryWithPostsDto>
     */
    public function findAllWithLatestPosts(int $postsPerCategory): array;

    public function findById(int $id): ?CategoryDto;
}
