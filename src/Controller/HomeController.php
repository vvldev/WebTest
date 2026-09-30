<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Repository\CategoryRepositoryInterface;
use App\View\TemplateRendererInterface;

final class HomeController
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly TemplateRendererInterface $renderer,
        private readonly int $postsPerCategory,
    ) {
    }

    public function index(): Response
    {
        return new Response($this->renderer->render('pages/home.tpl', [
            'categories' => $this->categoryRepository->findAllWithLatestPosts($this->postsPerCategory),
        ]));
    }
}
