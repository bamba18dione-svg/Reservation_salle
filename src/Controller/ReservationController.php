<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerReservationDTOBuilder;
use App\Exception\SalleIndisponibleException;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Validation\ReservationValidator;
use App\View\ViewRenderer;

final class ReservationController
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservations,
        private readonly SalleRepositoryInterface $salles,
        private readonly ReservationValidator $validator,
        private readonly CreerReservationService $creator,
        private readonly AnnulerReservationService $canceller,
        private readonly ViewRenderer $views,
    ) {
    }

    public function index(?int $salleId = null): string
    {
        return $this->views->render('reservation/index', ['reservations' => $this->reservations->all($salleId), 'salles' => $this->salles->all(), 'salleId' => $salleId]);
    }

    public function show(int $id): string
    {
        $reservation = $this->reservations->findById($id);

        return $reservation === null
            ? $this->views->render('error/404')
            : $this->views->render('reservation/show', ['reservation' => $reservation]);
    }

    public function create(): string
    {
        return $this->views->render('reservation/form', ['salles' => $this->salles->all(), 'errors' => [], 'old' => []]);
    }

    public function store(array $input): string
    {
        if (isset($input['salle_id'])) {
            $input['salle_id'] = (int) $input['salle_id'];
        }

        $result = $this->validator->validate($input);

        if (!$result->isValid()) {
            return $this->views->render('reservation/form', ['salles' => $this->salles->all(), 'errors' => $result->errors(), 'old' => $input]);
        }

        $data = $result->acceptedData();
        $dto = (new CreerReservationDTOBuilder())
            ->salleId($data['salle_id'])
            ->responsable($data['responsable'])
            ->email($data['email'])
            ->motif($data['motif'])
            ->dateDebut($data['date_debut'])
            ->dateFin($data['date_fin'])
            ->build();

        try {
            $reservation = $this->creator->execute($dto);
        } catch (SalleIndisponibleException $exception) {
            return $this->views->render('reservation/form', ['salles' => $this->salles->all(), 'errors' => ['salle_id' => [$exception->getMessage()]], 'old' => $input]);
        }

        return $this->redirect('/reservations/' . $reservation->id);
    }

    public function cancel(int $id): string
    {
        try {
            $this->canceller->execute($id);
        } catch (\DomainException) {
            return $this->views->render('error/404');
        }

        return $this->redirect('/reservations');
    }

    private function redirect(string $location): string
    {
        header('Location: ' . $location, true, 303);
        return '';
    }
}