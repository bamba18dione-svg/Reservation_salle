<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use App\Model\Salle;
use App\Model\Reservation;
use App\Repository\EloquentSalleRepository;
use App\Repository\EloquentReservationRepository;
use App\Service\AnnulerReservationService;

class EloquentIntegrationTest extends TestCase
{
    private $salleRepo;
    private $reservationRepo;

    protected function setUp(): void
    {
        // Configuration de SQLite en mémoire pour l'intégration
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        // Migration des tables en mémoire
        Capsule::schema()->create('salles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nom');
            $table->integer('capacite');
            $table->string('type');
            $table->string('responsable');
            $table->boolean('est_active')->default(true);
            $table->timestamps();
        });

        Capsule::schema()->create('reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('salle_id');
            $table->string('client_email');
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->string('statut')->default('confirmee');
            $table->timestamps();
        });

        $this->salleRepo = new EloquentSalleRepository(new Salle());
        $this->reservationRepo = new EloquentReservationRepository(new Reservation());
    }

    public function testCreationSalleAvecEloquent(): void
    {
        $salle = $this->salleRepo->save([
            'nom' => 'Salle Conférence',
            'capacite' => 50,
            'type' => 'conference',
            'responsable' => 'Alice',
            'est_active' => true,
        ]);

        $this->assertNotNull($salle->id);
        $this->assertEquals('Salle Conférence', $salle->nom);
    }

    public function testRelationSalleReservations(): void
    {
        $salle = Salle::create([
            'nom' => 'Salle TP',
            'capacite' => 15,
            'type' => 'tp',
            'responsable' => 'Bob',
            'est_active' => true,
        ]);

        $reservation = Reservation::create([
            'salle_id' => $salle->id,
            'client_email' => 'user@test.com',
            'date_debut' => '2026-11-01 10:00:00',
            'date_fin' => '2026-11-01 12:00:00',
            'statut' => 'confirmee',
        ]);

        $this->assertCount(1, $salle->reservations);
        $this->assertEquals($salle->id, $reservation->salle->id);
    }

    public function testRechercheDeChevauchement(): void
    {
        $salle = Salle::create([
            'nom' => 'Salle Réunion',
            'capacite' => 10,
            'type' => 'reunion',
            'responsable' => 'Charlie',
            'est_active' => true,
        ]);

        Reservation::create([
            'salle_id' => $salle->id,
            'client_email' => 'user1@test.com',
            'date_debut' => '2026-11-01 10:00:00',
            'date_fin' => '2026-11-01 12:00:00',
            'statut' => 'confirmee',
        ]);

        // Chevauchement partiel (11:00 à 13:00)
        $hasOverlap = $this->reservationRepo->hasOverlapping(
            $salle->id,
            '2026-11-01 11:00:00',
            '2026-11-01 13:00:00'
        );

        $this->assertTrue($hasOverlap);
    }

    public function testAnnulationDUneReservation(): void
    {
        $salle = Salle::create([
            'nom' => 'Salle Labo',
            'capacite' => 5,
            'type' => 'labo',
            'responsable' => 'David',
            'est_active' => true,
        ]);

        $reservation = Reservation::create([
            'salle_id' => $salle->id,
            'client_email' => 'user2@test.com',
            'date_debut' => '2026-11-01 14:00:00',
            'date_fin' => '2026-11-01 16:00:00',
            'statut' => 'confirmee',
        ]);

        $annulerService = new AnnulerReservationService($this->reservationRepo);
        $annulerService->execute($reservation->id);

        $reservationMiseAJour = $this->reservationRepo->find($reservation->id);
        $this->assertEquals('annulee', $reservationMiseAJour->statut);
    }
}