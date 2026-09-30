<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\PostListQueryDto;
use App\Enum\PostSort;
use App\Exception\NotFoundException;
use App\Http\Request;
use App\Http\Response;
use App\Repository\CategoryRepositoryInterface;
use App\Service\PostService;
use App\View\TemplateRendererInterface;

final class CategoryController
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly PostService $postService,
        private readonly TemplateRendererInterface $renderer,
    ) {
    }

    public function show(Request $request, int $id): Response
    {
        $category = $this->categoryRepository->findById($id);
        if ($category === null) {
            throw new NotFoundException('Category not found: ' . $id);
        }

        $sort = PostSort::fromQuery($request->queryString('sort'));
        $query = new PostListQueryDto($id, $sort, max(1, $request->queryInt('page', 1)));

        return new Response($this->renderer->render('pages/category.tpl', [
            'category' => $category,
            'postPage' => $this->postService->getCategoryPage($query),
            'sort' => $sort,
            'sortOptions' => PostSort::cases(),
        ]));
    }
}
