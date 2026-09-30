<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

final class PdoFactory
{
    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $database,
        private readonly string $user,
        private readonly string $password,
    ) {
    }

    public function create(): PDO
    {
        return new PDO($this->buildDsn(), $this->user, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Real prepared statements: values never get interpolated into SQL.
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    private function buildDsn(): string
    {
        return sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $this->host,
            $this->port,
            $this->database,
        );
    }
}
