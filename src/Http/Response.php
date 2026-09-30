<?php

declare(strict_types=1);

namespace App\Http;

final class Response
{
    public function __construct(
        private readonly string $body,
        private readonly int $statusCode = 200,
    ) {
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: text/html; charset=utf-8');
        echo $this->body;
    }
}
