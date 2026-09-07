<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeImmutable;

interface ReservationRepositoryInterface
{
    /** @return list<Reservation> */
    public function all(?int $salleId = null): array;

    public function findById(int $id): ?Reservation;

    public function findConflict(
        int $salleId,
        DateTimeImmutable $dateDebut,
        DateTimeImmutable $dateFin,
    ): ?Reservation;

    public function save(Reservation $reservation): Reservation;

    public function cancel(Reservation $reservation): Reservation;
}