<?php

declare(strict_types=1);

namespace App\View;

use RuntimeException;

final class ViewRenderer
{
    public function __construct(private readonly string $templatesPath)
    {
    }

    public function render(string $template, array $data = []): string
    {
        $path = $this->templatesPath . '/' . $template . '.php';
        $layoutPath = $this->templatesPath . '/layout/base.php';

        if (!is_file($path)) {
            throw new RuntimeException("Vue introuvable : {$template}");
        }

        if (!is_file($layoutPath)) {
            throw new RuntimeException('Layout introuvable : layout/base');
        }

        ob_start();

        try {
            extract($data, EXTR_SKIP);
            require $path;
            $content = (string) ob_get_clean();
        } catch (\Throwable $exception) {
            ob_end_clean();
            throw $exception;
        }

        ob_start();

        try {
            require $layoutPath;

            return (string) ob_get_clean();
        } catch (\Throwable $exception) {
            ob_end_clean();
            throw $exception;
        }
    }
}
