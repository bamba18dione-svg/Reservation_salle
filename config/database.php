<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

function loadEnvironment(string $projectRoot): void
{
	Dotenv::createImmutable($projectRoot)->safeLoad();
}

function databaseConfiguration(): array
{
	return [
		'driver' => $_ENV['DB_DRIVER'] ?? 'mysql',
		'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
		'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
		'database' => $_ENV['DB_DATABASE'] ?? 'reservation_salles',
		'username' => $_ENV['DB_USERNAME'] ?? 'root',
		'password' => $_ENV['DB_PASSWORD'] ?? '',
		'charset' => 'utf8mb4',
		'collation' => 'utf8mb4_unicode_ci',
		'prefix' => '',
	];
}

function createDatabaseManager(array $configuration): Capsule
{
	$capsule = new Capsule();
	$capsule->addConnection($configuration);
	$capsule->setAsGlobal();
	$capsule->bootEloquent();

	return $capsule;
}
