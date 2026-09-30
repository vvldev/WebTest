<?php

declare(strict_types=1);

namespace App\Http;

use App\Exception\NotFoundException;
use App\View\TemplateRendererInterface;
use ErrorException;
use Throwable;

final class ExceptionHandler
{
    public function __construct(
        private readonly TemplateRendererInterface $renderer,
        private readonly bool $debug,
    ) {
    }

    public function handleException(Throwable $exception): void
    {
        $statusCode = $this->statusCodeFor($exception);
        if ($statusCode === 500) {
            error_log((string) $exception);
        }

        $this->renderErrorPage($exception, $statusCode)->send();
    }

    public function handleError(int $severity, string $message, string $file, int $line): bool
    {
        // Respect the current error_reporting level (e.g. errors silenced by a library).
        if ((error_reporting() & $severity) === 0) {
            return false;
        }

        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    private function statusCodeFor(Throwable $exception): int
    {
        return $exception instanceof NotFoundException ? 404 : 500;
    }

    private function renderErrorPage(Throwable $exception, int $statusCode): Response
    {
        $body = $this->renderer->render('pages/error.tpl', [
            'statusCode' => $statusCode,
            'message' => $statusCode === 404 ? 'Страница не найдена' : 'Внутренняя ошибка сервера',
            // A stack trace only helps with real failures, not with a missing page.
            'details' => $this->debug && $statusCode === 500 ? (string) $exception : null,
        ]);

        return new Response($body, $statusCode);
    }
}
