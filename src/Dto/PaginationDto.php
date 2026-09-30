<?php

declare(strict_types=1);

namespace App\Dto;

final class PaginationDto
{
    public function __construct(
        public readonly int $currentPage,
        public readonly int $totalPages,
        public readonly int $totalItems,
        public readonly int $perPage,
        public readonly bool $hasPrevious,
        public readonly bool $hasNext,
    ) {
    }
}
