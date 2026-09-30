<?php

declare(strict_types=1);

namespace App\Http;

use App\Exception\NotFoundException;

final class Router
{
    /**
     * @param list<array{0: string, 1: string, 2: array{0: class-string, 1: string}}> $routes
     * @param array<class-string, object> $controllers
     */
    public function __construct(
        private readonly array $routes,
        private readonly array $controllers,
    ) {
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as [$method, $pattern, [$controllerClass, $action]]) {
            $params = $this->match($pattern, $request->path());
            if ($method !== $request->method() || $params === null) {
                continue;
            }

            return $this->controllers[$controllerClass]->$action($request, ...$params);
        }

        throw new NotFoundException('Page not found: ' . $request->path());
    }

    /**
     * @return list<int>|null
     */
    private function match(string $pattern, string $path): ?array
    {
        $regex = '#^' . str_replace('\{id\}', '(\d+)', preg_quote($pattern, '#')) . '$#';
        if (preg_match($regex, $path, $matches) !== 1) {
            return null;
        }

        return array_map('intval', array_slice($matches, 1));
    }
}
