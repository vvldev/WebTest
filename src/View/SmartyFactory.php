<?php

declare(strict_types=1);

namespace App\View;

use Smarty\Smarty;

final class SmartyFactory
{
    public function __construct(
        private readonly string $templateDir,
        private readonly string $compileDir,
        private readonly string $cacheDir,
    ) {
    }

    public function create(): Smarty
    {
        $smarty = new Smarty();
        $smarty->setTemplateDir($this->templateDir);
        $smarty->setCompileDir($this->compileDir);
        $smarty->setCacheDir($this->cacheDir);
        $smarty->setEscapeHtml(true);

        return $smarty;
    }
}
