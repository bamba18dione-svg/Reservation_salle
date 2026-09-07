<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Model\Reservation;
use App\Model\Salle;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class RepositoryTest extends TestCase
{
    public function testReservationRepositoryBuildsConflictQueryContract(): void
    {
        self::assertInstanceOf(EloquentReservationRepository::class, new EloquentReservationRepository());
        self::assertTrue(method_exists(EloquentReservationRepository::class, 'findConflict'));
    }

    public function testRepositoriesImplementTheirContracts(): void
    {
        self::assertInstanceOf(\App\Repository\SalleRepositoryInterface::class, new EloquentSalleRepository());
        self::assertInstanceOf(\App\Repository\ReservationRepositoryInterface::class, new EloquentReservationRepository());
        self::assertTrue((new Salle())->getTable() === 'salles');
        self::assertTrue((new Reservation())->getTable() === 'reservations');
        self::assertInstanceOf(DateTimeImmutable::class, new DateTimeImmutable());
    }
}