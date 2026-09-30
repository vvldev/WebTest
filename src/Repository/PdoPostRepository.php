<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\CategoryDto;
use App\Dto\NewPostDto;
use App\Dto\PostDto;
use App\Dto\PostPreviewDto;
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

        return $this->toPostPreviews($statement->fetchAll());
    }

    public function incrementViews(int $id): bool
    {
        // Atomic in MySQL: concurrent views never overwrite each other.
        $statement = $this->pdo->prepare('UPDATE posts SET views = views + 1 WHERE id = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->rowCount() > 0;
    }

    public function findById(int $id): ?PostDto
    {
        $statement = $this->pdo->prepare(
            'SELECT id, title, description, content, image, views, published_at FROM posts WHERE id = :id'
        );
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();
        if ($row === false) {
            return null;
        }

        return $this->rowMapper->toPost($row, $this->findCategoriesByPostId($id));
    }

    public function findSimilar(int $postId, int $limit): array
    {
        $statement = $this->pdo->prepare(
            'SELECT p.id, p.title, p.description, p.image, p.views, p.published_at, COUNT(*) AS common_categories
            FROM category_post cp
            JOIN posts p ON p.id = cp.post_id
            WHERE cp.category_id IN (SELECT category_id FROM category_post WHERE post_id = :postId)
              AND cp.post_id <> :excludedPostId
            GROUP BY p.id
            ORDER BY common_categories DESC, p.published_at DESC, p.id DESC
            LIMIT :limit'
        );
        // Native prepares forbid reusing one placeholder, hence two names for the same id.
        $statement->bindValue(':postId', $postId, PDO::PARAM_INT);
        $statement->bindValue(':excludedPostId', $postId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $this->toPostPreviews($statement->fetchAll());
    }

    public function add(NewPostDto $post): int
    {
        // A post without its category links must never be visible, so both go in one transaction.
        $this->pdo->beginTransaction();

        $statement = $this->pdo->prepare(
            'INSERT INTO posts (title, description, content, image, views, published_at)
            VALUES (:title, :description, :content, :image, :views, :publishedAt)'
        );
        $statement->bindValue(':title', $post->title);
        $statement->bindValue(':description', $post->description);
        $statement->bindValue(':content', $post->content);
        $statement->bindValue(':image', $post->imagePath, $post->imagePath === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $statement->bindValue(':views', $post->views, PDO::PARAM_INT);
        $statement->bindValue(':publishedAt', $post->publishedAt->format('Y-m-d H:i:s'));
        $statement->execute();
        $postId = (int) $this->pdo->lastInsertId();

        // One prepared statement, executed once per category.
        $linkStatement = $this->pdo->prepare(
            'INSERT INTO category_post (category_id, post_id) VALUES (:categoryId, :postId)'
        );
        $linkStatement->bindValue(':postId', $postId, PDO::PARAM_INT);
        foreach ($post->categoryIds as $categoryId) {
            $linkStatement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
            $linkStatement->execute();
        }

        $this->pdo->commit();

        return $postId;
    }

    public function deleteAll(): void
    {
        // Links in category_post go away via ON DELETE CASCADE.
        $this->pdo->exec('DELETE FROM posts');
    }

    /**
     * @return list<CategoryDto>
     */
    private function findCategoriesByPostId(int $postId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT c.id, c.name, c.description
            FROM categories c
            JOIN category_post cp ON cp.category_id = c.id
            WHERE cp.post_id = :postId
            ORDER BY c.name'
        );
        $statement->bindValue(':postId', $postId, PDO::PARAM_INT);
        $statement->execute();

        $categories = [];
        foreach ($statement->fetchAll() as $row) {
            $categories[] = $this->rowMapper->toCategory($row);
        }

        return $categories;
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<PostPreviewDto>
     */
    private function toPostPreviews(array $rows): array
    {
        $posts = [];
        foreach ($rows as $row) {
            $posts[] = $this->rowMapper->toPostPreview($row);
        }

        return $posts;
    }
}
