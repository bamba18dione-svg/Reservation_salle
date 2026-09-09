<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\View\ViewRenderer;
use DateTimeImmutable;

final class DashboardController
{
    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly ReservationRepositoryInterface $reservations,
        private readonly ViewRenderer $views,
    ) {
    }

    public function index(): string
    {
        $salles = $this->salles->all();
        $reservations = $this->reservations->all();

        $now = new DateTimeImmutable();

        $sallesActives = 0;
        foreach ($salles as $salle) {
            if ($salle->active) {
                $sallesActives++;
            }
        }

        $confirmees = 0;
        $annulees = 0;
        $aVenir = [];
        foreach ($reservations as $reservation) {
            $statut = (string) $reservation->statut;
            if ($statut === 'confirmee') {
                $confirmees++;
            } elseif ($statut === 'annulee') {
                $annulees++;
            }

            if ($statut === 'confirmee' && $reservation->date_fin > $now) {
                $aVenir[] = $reservation;
            }
        }

        usort($aVenir, static fn ($a, $b): int => $a->date_debut <=> $b->date_debut);
        $prochaines = array_slice($aVenir, 0, 5);

        return $this->views->render('dashboard/index', [
            'totalSalles' => count($salles),
            'sallesActives' => $sallesActives,
            'totalReservations' => count($reservations),
            'reservationsConfirmees' => $confirmees,
            'reservationsAnnulees' => $annulees,
            'prochainesReservations' => $prochaines,
        ]);
    }
}
