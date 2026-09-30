<?php

declare(strict_types=1);

namespace App\Repository;

use App\Enum\PostSort;
use PDO;

final class PdoPostRepository implements PostRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly RowMapper $rowMapper,
    ) {
    }

    public function countByCategory(int $categoryId): int
    {
        $statement = $this->pdo->prepare('SELECT COUNT(*) FROM category_post WHERE category_id = :categoryId');
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->execute();

        return (int) $statement->fetchColumn();
    }

    public function findByCategory(int $categoryId, PostSort $sort, int $limit, int $offset): array
    {
        $statement = $this->pdo->prepare(
            'SELECT p.id, p.title, p.description, p.image, p.views, p.published_at
            FROM posts p
            JOIN category_post cp ON cp.post_id = p.id
            WHERE cp.category_id = :categoryId
            ORDER BY ' . $sort->orderByClause() . '
            LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        $posts = [];
        foreach ($statement->fetchAll() as $row) {
            $posts[] = $this->rowMapper->toPostPreview($row);
        }

        return $posts;
    }
}
