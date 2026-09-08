<?php

declare(strict_types=1);

namespace App\Http;

use Psr\Container\ContainerInterface;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

final class Router
{
    private Dispatcher $dispatcher;

    public function __construct(
        private readonly ContainerInterface $container,
        string $routesFile,
    ) {
        $this->dispatcher = simpleDispatcher(static function (RouteCollector $routes) use ($routesFile): void {
            $register = require $routesFile;
            $register($routes);
        });
    }

    public function dispatch(string $method, string $uri, array $input = []): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $route = $this->dispatcher->dispatch($method, $path);

        return match ($route[0]) {
            Dispatcher::NOT_FOUND => $this->respond('404 - Page introuvable', 404),
            Dispatcher::METHOD_NOT_ALLOWED => $this->methodNotAllowed($route[1]),
            Dispatcher::FOUND => $this->invoke($route[1], $route[2], $input, $method),
            default => $this->respond('500 - Erreur de routage', 500),
        };
    }

    private function invoke(array $handler, array $parameters, array $input, string $method): string
    {
        [$class, $action] = $handler;
        $controller = $this->container->get($class);
        $arguments = array_map(static fn (string $value): int|string => ctype_digit($value) ? (int) $value : $value, $parameters);

        if ($method === 'POST') {
            $arguments[] = $input;
        }

        return (string) $controller->{$action}(...$arguments);
    }

    private function methodNotAllowed(array $allowedMethods): string
    {
        header('Allow: ' . implode(', ', $allowedMethods));

        return $this->respond('405 - Methode non autorisee', 405);
    }

    private function respond(string $body, int $status): string
    {
        http_response_code($status);

        return $body;
    }
}