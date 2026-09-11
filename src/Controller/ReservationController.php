<?php

declare(strict_types=1);

namespace App\Controller;

use App\Auth\AuthService;
use App\DTO\CreerReservationDTOBuilder;
use App\Exception\ReservationInvalideException;
use App\Exception\SalleIndisponibleException;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Support\Flash;
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
        return $this->views->render('reservation/index', [
            'reservations' => $this->reservations->all($salleId),
            'salles' => $this->salles->all(),
            'salleId' => $salleId,
        ]);
    }

    public function show(int $id): string
    {
        $reservation = $this->reservations->findById($id);

        return $reservation === null
            ? $this->notFound()
            : $this->views->render('reservation/show', ['reservation' => $reservation]);
    }

    public function create(): string
    {
        if ($redirect = $this->requireAuth()) {
            return $redirect;
        }

        return $this->views->render('reservation/form', ['salles' => $this->salles->all(), 'errors' => [], 'old' => []]);
    }

    public function store(array $input): string
    {
        if ($redirect = $this->requireAuth()) {
            return $redirect;
        }

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
        } catch (SalleIndisponibleException|ReservationInvalideException $exception) {
            $field = $exception instanceof ReservationInvalideException ? 'date_debut' : 'salle_id';

            return $this->views->render('reservation/form', ['salles' => $this->salles->all(), 'errors' => [$field => [$exception->getMessage()]], 'old' => $input]);
        }

        (new Flash())->success('Réservation créée avec succès.');

        return $this->redirect('/reservations/' . $reservation->id);
    }

    public function cancel(int $id): string
    {
        if ($redirect = $this->requireAuth()) {
            return $redirect;
        }

        try {
            $this->canceller->execute($id);
        } catch (\DomainException) {
            (new Flash())->error('Impossible d\'annuler cette réservation : elle est introuvable ou déjà annulée.');

            return $this->redirect('/reservations');
        }

        (new Flash())->success('Réservation annulée avec succès.');

        return $this->redirect('/reservations');
    }

    /**
     * Gardien d'authentification : un visiteur anonyme est redirige vers /login.
     */
    private function requireAuth(): ?string
    {
        if ((new AuthService())->check()) {
            return null;
        }

        (new Flash())->error('Veuillez vous connecter pour effectuer cette action.');
        header('Location: /login', true, 303);

        return '';
    }

    private function notFound(): string
    {
        http_response_code(404);

        return $this->views->render('error/404');
    }

    private function redirect(string $location): string
    {
        header('Location: ' . $location, true, 303);
        return '';
    }
}