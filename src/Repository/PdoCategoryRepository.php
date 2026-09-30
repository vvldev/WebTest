<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\CategoryDto;
use App\Dto\CategoryWithPostsDto;
use App\Dto\NewCategoryDto;
use PDO;

final class PdoCategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly RowMapper $rowMapper,
    ) {
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

    public function findById(int $id): ?CategoryDto
    {
        $statement = $this->pdo->prepare('SELECT id, name, description FROM categories WHERE id = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();

        return $row === false ? null : $this->rowMapper->toCategory($row);
    }

    public function add(NewCategoryDto $category): int
    {
        $statement = $this->pdo->prepare('INSERT INTO categories (name, description) VALUES (:name, :description)');
        $statement->bindValue(':name', $category->name);
        $statement->bindValue(':description', $category->description);
        $statement->execute();

        return (int) $this->pdo->lastInsertId();
    }

    public function deleteAll(): void
    {
        // Links in category_post go away via ON DELETE CASCADE.
        $this->pdo->exec('DELETE FROM categories');
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
            $categories[$categoryId] ??= $this->rowMapper->toCategory($row, 'category_');
            $postsByCategory[$categoryId][] = $this->rowMapper->toPostPreview($row);
        }

        $result = [];
        foreach ($categories as $categoryId => $category) {
            $result[] = new CategoryWithPostsDto($category, $postsByCategory[$categoryId]);
        }

        return $result;
    }
}
