<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Dotenv\Dotenv;

//  Chargement des variables d'environnement (.env)
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

//  Vérification des variables d'environnement requises
$dotenv->required(['DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_DRIVER']);

$capsule = new Capsule();

try {
    $capsule->addConnection([
        'driver'    => $_ENV['DB_DRIVER'] ?? 'mysql',
        'host'      => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port'      => $_ENV['DB_PORT'] ?? '3306',
        'database'  => $_ENV['DB_DATABASE'] ?? 'reservation_salles',
        'username'  => $_ENV['DB_USERNAME'] ?? 'root',
        'password'  => $_ENV['DB_PASSWORD'] ?? '',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
    ]);

    // Rendre l'instance de Capsule disponible globalement via des méthodes statiques
    $capsule->setAsGlobal();

    $capsule->bootEloquent();

} catch (\Throwable $e) {
    die("Erreur critique de connexion à la base de données : " . $e->getMessage());
}

return $capsule;