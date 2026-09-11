<?php

declare(strict_types=1);

namespace App\Controller;

use App\Auth\AuthService;
use App\Support\Flash;
use App\View\ViewRenderer;


final class AuthController
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly ViewRenderer $views,
    ) {
    }

    public function showLogin(): string
    {
       
        if ($this->auth->check()) {
            return $this->redirect('/salles');
        }

        return $this->views->render('auth/login', ['errors' => [], 'old' => []]);
    }

    public function login(array $input): string
    {
        $email = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $errors = [];

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = ['Veuillez saisir une adresse e-mail valide.'];
        }
        if ($password === '') {
            $errors['password'] = ['Le mot de passe est obligatoire.'];
        }

        if ($errors !== []) {
            return $this->views->render('auth/login', ['errors' => $errors, 'old' => ['email' => $email]]);
        }

        $user = $this->auth->attempt($email, $password);

        if ($user === null) {
           
            return $this->views->render('auth/login', [
                'errors' => ['email' => ['Identifiants incorrects.']],
                'old' => ['email' => $email],
            ]);
        }

        (new Flash())->success('Bienvenue, ' . $user->nom . ' ! Vous etes connecte.');

        return $this->redirect('/salles');
    }

    public function logout(): string
    {
        $this->auth->logout();
        (new Flash())->success('Vous etes deconnecte.');

        return $this->redirect('/login');
    }

    private function redirect(string $location): string
    {
        header('Location: ' . $location, true, 303);

        return '';
    }
}
