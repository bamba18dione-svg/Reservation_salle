<?php

declare(strict_types=1);

use App\Migration\MigrationInterface;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Capsule\Manager as Capsule;

return new class implements MigrationInterface {
    public function up(Builder $schema): void
    {
        if ($schema->hasTable('users')) {
            return;
        }

        $schema->create('users', function (Blueprint $table) {
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

    public function down(Builder $schema): void
    {
        $schema->dropIfExists('users');
    }
};