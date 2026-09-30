<?php

declare(strict_types=1);

namespace App\View;

interface TemplateRendererInterface
{
    /**
     * @param array<string, mixed> $params
     */
    public function render(string $template, array $params = []): string;
}
