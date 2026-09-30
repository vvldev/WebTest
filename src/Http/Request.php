<?php

declare(strict_types=1);

namespace App\Http;

final class Request
{
    /**
     * @param array<string, mixed> $query
     */
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query,
    ) {
    }

    public static function fromGlobals(): self
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        return new self(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            is_string($path) ? $path : '/',
            $_GET,
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function queryString(string $name, string $default = ''): string
    {
        $value = $this->query[$name] ?? null;

        return is_string($value) ? $value : $default;
    }

    public function queryInt(string $name, int $default = 0): int
    {
        $value = filter_var($this->query[$name] ?? null, FILTER_VALIDATE_INT);

        return is_int($value) ? $value : $default;
    }
}
