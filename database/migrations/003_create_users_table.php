<?php

declare(strict_types=1);


use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

$projectRoot = dirname(__DIR__, 2);
require $projectRoot . '/vendor/autoload.php';

$capsule = require $projectRoot . '/config/database.php';

if (!$capsule instanceof Capsule) {
    throw new RuntimeException('La configuration de la base doit retourner Capsule\\Manager.');
}

$schema = $capsule->schema();

if (!$schema->hasTable('users')) {
    $schema->create('users', static function (Blueprint $table): void {
        $table->id();
        $table->string('nom', 100);
        $table->string('email', 255)->unique();
        $table->string('password_hash', 255);
        $table->timestamps();
    });

   
    if (Capsule::table('users')->count() === 0) {
        Capsule::table('users')->insert([
            'nom' => 'Administrateur',
            'email' => 'admin@example.com',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
