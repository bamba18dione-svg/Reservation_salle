<?php

declare(strict_types=1);

namespace App\Auth;

use App\Model\User;

final class AuthService
{
    private const SESSION_USER_KEY = 'auth_user';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Tente une connexion. Retourne l'utilisateur si le couple
     * email/mot de passe est correct, sinon null.
     */
    public function attempt(string $email, string $password): ?User
    {
        $user = User::where('email', mb_strtolower(trim($email)))->first();

        if ($user === null || !password_verify($password, $user->password_hash)) {
            return null;
        }

        // Regeneration d'ID de session : protection contre la fixation de session.
        session_regenerate_id(true);
        $_SESSION[self::SESSION_USER_KEY] = [
            'id' => $user->id,
            'nom' => $user->nom,
            'email' => $user->email,
        ];

        return $user;
    }

    /**
     * Utilisateur connecte, ou null si visiteur anonyme.
     *
     * @return array{id: int, nom: string, email: string}|null
     */
    public function user(): ?array
    {
        return $_SESSION[self::SESSION_USER_KEY] ?? null;
    }

    public function check(): bool
    {
        return isset($_SESSION[self::SESSION_USER_KEY]);
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_USER_KEY]);
        session_regenerate_id(true);
    }
}
