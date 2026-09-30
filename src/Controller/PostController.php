<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Repository\PostRepositoryInterface;
use App\Service\PostService;
use App\View\TemplateRendererInterface;

final class PostController
{
    public function __construct(
        private readonly PostService $postService,
        private readonly PostRepositoryInterface $postRepository,
        private readonly TemplateRendererInterface $renderer,
        private readonly int $similarPostsLimit,
    ) {
    }

    public function show(Request $request, int $id): Response
    {
        return new Response($this->renderer->render('pages/post.tpl', [
            'post' => $this->postService->viewPost($id),
            'similarPosts' => $this->postRepository->findSimilar($id, $this->similarPostsLimit),
        ]));
    }
}
