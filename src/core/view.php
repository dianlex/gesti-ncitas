<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    public static function render(string $template, array $viewData = []): void
    {
        $root = dirname(__DIR__, 2) . '/views';
        $file = $root . '/' . $template . '.php';

        if (!is_file($file)) {
            throw new RuntimeException("La vista {$template} no existe.");
        }

        extract($viewData, EXTR_SKIP);
        ob_start();
        require $file;
        $content = (string) ob_get_clean();

        require $root . '/layout.php';
    }
}