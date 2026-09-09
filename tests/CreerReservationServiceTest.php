<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Service\CreerReservationService;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Validation\ReservationValidator;
use App\Model\Salle;
use App\Model\Reservation;
use DateTimeImmutable;
use InvalidArgumentException;

class CreerReservationServiceTest extends TestCase
{
    private $reservationRepo;
    private $salleRepo;
    private $validator;
    private $service;

    protected function setUp(): void
    {
        $this->reservationRepo = $this->createMock(ReservationRepositoryInterface::class);
        $this->salleRepo = $this->createMock(SalleRepositoryInterface::class);
        $this->validator = new ReservationValidator();

        $this->service = new CreerReservationService(
            $this->reservationRepo,
            $this->salleRepo,
            $this->validator
        );
    }

    private function createValidSalle(int $id = 1, bool $estActive = true): Salle
    {
        $salle = new Salle();
        $salle->id = $id;
        $salle->nom = "Salle A";
        $salle->est_active = $estActive;
        return $salle;
    }

    public function testReservationValide(): void
    {
        $salle = $this->createValidSalle();
        $maintenant = new DateTimeImmutable('+1 day');
        $debut = $maintenant->format('Y-m-d H:00:00');
        $fin = $maintenant->modify('+2 hours')->format('Y-m-d H:00:00');

        $this->salleRepo->method('find')->with(1)->willReturn($salle);
        $this->reservationRepo->method('hasOverlapping')->willReturn(false);
        $this->reservationRepo->method('save')->willReturnCallback(function ($res) {
            $res->id = 10;
            return $res;
        });

        $resultat = $this->service->execute([
            'salle_id' => 1,
            'client_email' => 'client@example.com',
            'date_debut' => $debut,
            'date_fin' => $fin,
        ]);

        $this->assertNotNull($resultat);
        $this->assertEquals(10, $resultat->id);
    }

    public function testSalleInexistante(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->salleRepo->method('find')->with(999)->willReturn(null);

        $this->service->execute([
            'salle_id' => 999,
            'client_email' => 'client@example.com',
            'date_debut' => '2026-10-10 10:00:00',
            'date_fin' => '2026-10-10 12:00:00',
        ]);
    }

    public function testSalleInactive(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $salleInactive = $this->createValidSalle(1, false);
        $this->salleRepo->method('find')->with(1)->willReturn($salleInactive);

        $this->service->execute([
            'salle_id' => 1,
            'client_email' => 'client@example.com',
            'date_debut' => '2026-10-10 10:00:00',
            'date_fin' => '2026-10-10 12:00:00',
        ]);
    }

    public function testDateFinAnterieureAuDebut(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $salle = $this->createValidSalle();
        $this->salleRepo->method('find')->willReturn($salle);

        $this->service->execute([
            'salle_id' => 1,
            'client_email' => 'client@example.com',
            'date_debut' => '2026-10-10 14:00:00',
            'date_fin' => '2026-10-10 10:00:00',
        ]);
    }

    public function testDureeSuperieureAQuatreHeures(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $salle = $this->createValidSalle();
        $this->salleRepo->method('find')->willReturn($salle);

        $this->service->execute([
            'salle_id' => 1,
            'client_email' => 'client@example.com',
            'date_debut' => '2026-10-10 10:00:00',
            'date_fin' => '2026-10-10 15:00:00', // 5 heures
        ]);
    }

    public function testDatePassee(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $salle = $this->createValidSalle();
        $this->salleRepo->method('find')->willReturn($salle);

        $this->service->execute([
            'salle_id' => 1,
            'client_email' => 'client@example.com',
            'date_debut' => '2020-01-01 10:00:00',
            'date_fin' => '2020-01-01 12:00:00',
        ]);
    }

    public function testConflitAvecUneReservation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $salle = $this->createValidSalle();
        $this->salleRepo->method('find')->willReturn($salle);
        $this->reservationRepo->method('hasOverlapping')->willReturn(true);

        $this->service->execute([
            'salle_id' => 1,
            'client_email' => 'client@example.com',
            'date_debut' => '2026-10-10 10:00:00',
            'date_fin' => '2026-10-10 12:00:00',
        ]);
    }

    public function testReservationVoisineSansChevauchement(): void
    {
        $salle = $this->createValidSalle();
        $maintenant = new DateTimeImmutable('+1 day');
        $debut = $maintenant->format('Y-m-d 12:00:00');
        $fin = $maintenant->format('Y-m-d 14:00:00');

        $this->salleRepo->method('find')->willReturn($salle);
        // Pas de chevauchement si la précédente finit exactement à 12:00
        $this->reservationRepo->method('hasOverlapping')->willReturn(false);
        $this->reservationRepo->method('save')->willReturnCallback(fn($r) => $r);

        $resultat = $this->service->execute([
            'salle_id' => 1,
            'client_email' => 'client@example.com',
            'date_debut' => $debut,
            'date_fin' => $fin,
        ]);

        $this->assertNotNull($resultat);
    }
}