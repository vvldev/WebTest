<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\CategoryDto;
use App\Dto\PostDto;
use App\Dto\PostPreviewDto;
use DateTimeImmutable;

/**
 * Turns database rows into DTOs; shared by repositories so the mapping lives in one place.
 */
final class RowMapper
{
    /**
     * @param array<string, mixed> $row
     * @param string $prefix column alias prefix, e.g. "category_" for category_id, category_name
     */
    public function toCategory(array $row, string $prefix = ''): CategoryDto
    {
        return new CategoryDto(
            (int) $row[$prefix . 'id'],
            (string) $row[$prefix . 'name'],
            (string) $row[$prefix . 'description'],
        );
    }

    /**
     * @param array<string, mixed> $row
     */
    public function toPostPreview(array $row): PostPreviewDto
    {
        return new PostPreviewDto(
            (int) $row['id'],
            (string) $row['title'],
            (string) $row['description'],
            $row['image'] === null ? null : (string) $row['image'],
            (int) $row['views'],
            new DateTimeImmutable((string) $row['published_at']),
        );
    }

    /**
     * @param array<string, mixed> $row
     * @param list<CategoryDto> $categories
     */
    public function toPost(array $row, array $categories): PostDto
    {
        return new PostDto(
            (int) $row['id'],
            (string) $row['title'],
            (string) $row['description'],
            $this->splitParagraphs((string) $row['content']),
            $row['image'] === null ? null : (string) $row['image'],
            (int) $row['views'],
            new DateTimeImmutable((string) $row['published_at']),
            $categories,
        );
    }

    /**
     * Paragraphs are separated by an empty line (possibly with spaces).
     *
     * @return list<string>
     */
    private function splitParagraphs(string $content): array
    {
        $paragraphs = preg_split('/\R\s*\R/u', trim($content), -1, PREG_SPLIT_NO_EMPTY);

        return $paragraphs === false ? [] : $paragraphs;
    }
}
