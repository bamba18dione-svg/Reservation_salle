<?php

declare(strict_types=1);

/**
 * Exécuteur de migrations autonomes (sans framework).
 * Charge chaque migration depuis database/migrations/ et appelle up() ou down().
 *
 * Utilisation :
 *   php database/migrate.php            -> applique toutes les migrations
 *   php database/migrate.php refresh    -> down() puis up() de chaque migration
 *   php database/migrate.php rollback   -> down() de la dernière migration
 */

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/database.php';

use App\Migration\MigrationInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

if (!($capsule instanceof Capsule)) {
    throw new RuntimeException('La configuration de la base doit retourner Capsule\\Manager.');
}

$schema = $capsule->schema();

$migrationDir = dirname(__DIR__) . '/database/migrations';
$files = glob($migrationDir . '/*_*.php');
sort($files);

$migrations = [];
foreach ($files as $file) {
    $return = require $file;
    if ($return instanceof MigrationInterface) {
        $migrations[] = $return;
    }
}

if (count($migrations) === 0) {
    echo "Aucune migration trouvée.\n";
    exit(0);
}

$mode = $argv[1] ?? 'up';

if ($mode === 'up') {
    foreach ($migrations as $migration) {
        $migration->up($schema);
        echo "Migration appliquée.\n";
    }
} elseif ($mode === 'refresh') {
    foreach ($migrations as $migration) {
        $migration->down($schema);
        $migration->up($schema);
        echo "Migration rafraîchie.\n";
    }
} elseif ($mode === 'rollback') {
    $last = end($migrations);
    $last->down($schema);
    echo "Dernière migration défaite.\n";
} else {
    echo "Mode inconnu : {$mode}\n";
    echo "Utilisation : php database/migrate.php [up|refresh|rollback]\n";
    exit(1);
}