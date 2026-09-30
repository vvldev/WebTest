<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\PaginationDto;
use App\Dto\PostListQueryDto;
use App\Dto\PostPageDto;
use App\Exception\NotFoundException;
use App\Repository\PostRepositoryInterface;

final class PostService
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository,
        private readonly int $postsPerPage,
    ) {
    }

    public function getCategoryPage(PostListQueryDto $query): PostPageDto
    {
        $totalItems = $this->postRepository->countByCategory($query->categoryId);
        $totalPages = $this->countPages($totalItems);
        if ($query->page > $totalPages) {
            throw new NotFoundException('Page out of range: ' . $query->page);
        }

        $posts = $this->postRepository->findByCategory(
            $query->categoryId,
            $query->sort,
            $this->postsPerPage,
            $this->offsetFor($query->page),
        );

        return new PostPageDto($posts, new PaginationDto(
            $query->page,
            $totalPages,
            $totalItems,
            $this->postsPerPage,
            $query->page > 1,
            $query->page < $totalPages,
        ));
    }

    /**
     * An empty category still has one (empty) page.
     */
    private function countPages(int $totalItems): int
    {
        return max(1, (int) ceil($totalItems / $this->postsPerPage));
    }

    private function offsetFor(int $page): int
    {
        return ($page - 1) * $this->postsPerPage;
    }
}
