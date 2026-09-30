<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\CategoryDto;
use App\Dto\CategoryWithPostsDto;
use App\Dto\PostPreviewDto;
use DateTimeImmutable;
use PDO;

final class PdoCategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findAllWithLatestPosts(int $postsPerCategory): array
    {
        // Inner JOINs drop categories without posts; ROW_NUMBER keeps the latest N per category in one query.
        $statement = $this->pdo->prepare(
            'SELECT ranked.* FROM (
                SELECT c.id AS category_id, c.name AS category_name, c.description AS category_description,
                       p.id, p.title, p.description, p.image, p.views, p.published_at,
                       ROW_NUMBER() OVER (PARTITION BY c.id ORDER BY p.published_at DESC, p.id DESC) AS position
                FROM categories c
                JOIN category_post cp ON cp.category_id = c.id
                JOIN posts p ON p.id = cp.post_id
            ) AS ranked
            WHERE ranked.position <= :limit
            ORDER BY ranked.category_name, ranked.position'
        );
        $statement->bindValue(':limit', $postsPerCategory, PDO::PARAM_INT);
        $statement->execute();

        return $this->groupByCategory($statement->fetchAll());
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<CategoryWithPostsDto>
     */
    private function groupByCategory(array $rows): array
    {
        $categories = [];
        $postsByCategory = [];
        foreach ($rows as $row) {
            $categoryId = (int) $row['category_id'];
            $categories[$categoryId] ??= $this->toCategory($row);
            $postsByCategory[$categoryId][] = $this->toPostPreview($row);
        }

        $result = [];
        foreach ($categories as $categoryId => $category) {
            $result[] = new CategoryWithPostsDto($category, $postsByCategory[$categoryId]);
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function toCategory(array $row): CategoryDto
    {
        return new CategoryDto(
            (int) $row['category_id'],
            (string) $row['category_name'],
            (string) $row['category_description'],
        );
    }

    /**
     * @param array<string, mixed> $row
     */
    private function toPostPreview(array $row): PostPreviewDto
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
}
