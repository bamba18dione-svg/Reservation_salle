<?php

declare(strict_types=1);

namespace App;

use App\Http\Router;
use App\Support\Flash;
use Illuminate\Database\Capsule\Manager as Capsule;
use Throwable;

final class Application
{
    public function __construct(
        private readonly Router $router,
        private readonly Capsule $database,
    )
    {
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $input = $method === 'POST' ? $_POST : [];

        if ($method === 'GET' && isset($_GET['salle']) && ctype_digit((string) $_GET['salle'])) {
            $input['salle'] = (int) $_GET['salle'];
        }

        echo $this->router->dispatch($method, $uri, $input);
    }

    /**
     * Gestion propre des exceptions non interceptées :
     * message générique à l'utilisateur, détail en log.
     */
    public function runSafe(): void
    {
        try {
            $this->run();
        } catch (Throwable $exception) {
            error_log(sprintf(
                '[reservation-app] %s: %s in %s:%d',
                $exception::class,
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
            ));

            http_response_code(500);
            (new Flash())->error(
                'Une erreur interne est survenue. Veuillez réessayer ou contacter l\'administrateur.'
            );

            try {
                echo (new \App\View\ViewRenderer(dirname(__DIR__) . '/templates'))->render('error/500');
            } catch (Throwable) {
                echo 'Une erreur interne est survenue.';
            }
        }
    }
}