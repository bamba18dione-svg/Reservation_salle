<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerReservationDTO;
use App\Exception\ReservationInvalideException;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use DateTimeImmutable;

final class CreerReservationService
{
    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly ReservationRepositoryInterface $reservations,
    ) {
    }

    public function execute(CreerReservationDTO $dto): Reservation
    {
        $salle = $this->salles->findById($dto->salleId);

        if ($salle === null || !$salle->active) {
            throw new SalleIndisponibleException('La salle demandee est inexistante ou inactive.');
        }

        if ($dto->dateDebut >= $dto->dateFin) {
            throw new ReservationInvalideException('La date de debut doit preceder la date de fin.');
        }

        $duree = $dto->dateFin->getTimestamp() - $dto->dateDebut->getTimestamp();

        if ($duree > 4 * 60 * 60) {
            throw new ReservationInvalideException('Une reservation ne peut pas depasser quatre heures.');
        }

        if ($dto->dateDebut <= new DateTimeImmutable()) {
            throw new ReservationInvalideException('La reservation doit commencer dans le futur.');
        }

        if ($this->reservations->findConflict($dto->salleId, $dto->dateDebut, $dto->dateFin) !== null) {
            throw new SalleIndisponibleException('La salle est indisponible pendant cette periode.');
        }

        return $this->reservations->save(new Reservation([
            'salle_id' => $dto->salleId,
            'responsable' => $dto->responsable,
            'email' => $dto->email,
            'motif' => $dto->motif,
            'date_debut' => $dto->dateDebut,
            'date_fin' => $dto->dateFin,
            'statut' => 'confirmee',
        ]));
    }
}