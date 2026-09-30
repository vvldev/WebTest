<?php

declare(strict_types=1);

namespace App\View;

use Smarty\Smarty;

final class SmartyTemplateRenderer implements TemplateRendererInterface
{
    public function __construct(private readonly Smarty $smarty)
    {
    }

    public function render(string $template, array $params = []): string
    {
        // A separate template object keeps params from leaking into the next render.
        $smartyTemplate = $this->smarty->createTemplate($template);
        $smartyTemplate->assign($params);

        return $smartyTemplate->fetch();
    }
}
