<?php

declare(strict_types=1);

namespace App;

use App\Http\Router;
use Illuminate\Database\Capsule\Manager as Capsule;

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

        echo $this->router->dispatch($method, $uri, $input);
    }
}