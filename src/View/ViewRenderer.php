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

        if (!is_file($path)) {
            throw new RuntimeException("Vue introuvable : {$template}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $path;

        return (string) ob_get_clean();
    }
}