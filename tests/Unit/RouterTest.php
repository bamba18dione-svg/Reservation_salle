<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Router;
use FastRoute\RouteCollector;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class RouterTest extends TestCase
{
    public function testUnknownPathReturns404(): void
    {
        $router = new Router(new FakeContainer(), dirname(__DIR__, 2) . '/routes/web.php');

        self::assertSame('404 - Page introuvable', $router->dispatch('GET', '/inconnue'));
        self::assertSame(404, http_response_code());
    }

    public function testUnsupportedMethodReturns405(): void
    {
        $router = new Router(new FakeContainer(), dirname(__DIR__, 2) . '/routes/web.php');

        self::assertSame('405 - Methode non autorisee', $router->dispatch('DELETE', '/salles'));
        self::assertSame(405, http_response_code());
    }
}

final class FakeContainer implements ContainerInterface
{
    public function get(string $id): mixed
    {
        throw new \LogicException('Le contrôleur ne doit pas être résolu pour ce test.');
    }

    public function has(string $id): bool
    {
        return false;
    }
}