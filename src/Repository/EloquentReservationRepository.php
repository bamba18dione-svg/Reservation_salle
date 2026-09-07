<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeImmutable;

final class EloquentReservationRepository implements ReservationRepositoryInterface
{
    public function all(?int $salleId = null): array
    {
        $query = Reservation::query()->with('salle')->orderBy('date_debut');

        if ($salleId !== null) {
            $query->where('salle_id', $salleId);
        }

        return $query->get()->all();
    }

    public function findById(int $id): ?Reservation
    {
        return Reservation::query()->with('salle')->find($id);
    }

    public function findConflict(
        int $salleId,
        DateTimeImmutable $dateDebut,
        DateTimeImmutable $dateFin,
    ): ?Reservation {
        return Reservation::query()
            ->where('salle_id', $salleId)
            ->where('statut', 'confirmee')
            ->where('date_debut', '<', $dateFin)
            ->where('date_fin', '>', $dateDebut)
            ->first();
    }

    public function save(Reservation $reservation): Reservation
    {
        $reservation->save();

        return $reservation;
    }

    public function cancel(Reservation $reservation): Reservation
    {
        $reservation->statut = 'annulee';
        $reservation->save();

        return $reservation;
    }
}