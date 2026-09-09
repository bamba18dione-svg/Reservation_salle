<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Messages "flash" stockés en session et affichés une seule fois
 * lors de la requête suivante (après une redirection).
 */
final class Flash
{
    private const KEY = '_flash_messages';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function add(string $type, string $message): void
    {
        $_SESSION[self::KEY][$type][] = $message;
    }

    public function success(string $message): void
    {
        $this->add('success', $message);
    }

    public function error(string $message): void
    {
        $this->add('error', $message);
    }

    /**
     * Retourne les messages accumulés puis les vide (affichage unique).
     *
     * @return array{success: string[], error: string[]}
     */
    public function pull(): array
    {
        $messages = $_SESSION[self::KEY] ?? ['success' => [], 'error' => []];
        unset($_SESSION[self::KEY]);

        return [
            'success' => $messages['success'] ?? [],
            'error' => $messages['error'] ?? [],
        ];
    }
}
