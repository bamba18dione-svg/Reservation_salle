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

if (!$schema->hasTable('salles')) {
    $schema->create('salles', static function (Blueprint $table): void {
        $table->id();
        $table->string('nom', 100);
        $table->string('batiment', 100);
        $table->unsignedInteger('capacite');
        $table->string('type', 30);
        $table->boolean('active')->default(true);
        $table->timestamps();
    });
}

if (!$schema->hasTable('reservations')) {
    $schema->create('reservations', static function (Blueprint $table): void {
        $table->id();
        $table->foreignId('salle_id')->constrained('salles')->restrictOnDelete();
        $table->string('responsable', 120);
        $table->string('email', 255);
        $table->string('motif', 255);
        $table->dateTime('date_debut');
        $table->dateTime('date_fin');
        $table->string('statut', 20)->default('confirmee');
        $table->timestamps();
    });
}