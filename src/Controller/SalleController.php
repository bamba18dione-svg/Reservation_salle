<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerSalleDTOBuilder;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;
use App\Validation\SalleValidator;
use App\Support\Flash;
use App\View\ViewRenderer;

final class SalleController
{
    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly SalleValidator $validator,
        private readonly ViewRenderer $views,
    ) {
    }

    public function index(): string
    {
        return $this->views->render('salle/index', ['salles' => $this->salles->all()]);
    }

    public function show(int $id): string
    {
        $salle = $this->salles->findById($id);

        return $salle === null
            ? $this->notFound()
            : $this->views->render('salle/show', ['salle' => $salle]);
    }

    public function create(): string
    {
        if ($redirect = $this->requireAuth()) {
            return $redirect;
        }

        return $this->views->render('salle/form', ['salle' => null, 'errors' => [], 'old' => []]);
    }

    public function store(array $input): string
    {
        if ($redirect = $this->requireAuth()) {
            return $redirect;
        }

        $input['active'] = (bool) ($input['active'] ?? false);
        if (isset($input['capacite'])) {
            $input['capacite'] = (int) $input['capacite'];
        }

        $result = $this->validator->validate($input);

        if (!$result->isValid()) {
            return $this->views->render('salle/form', ['salle' => null, 'errors' => $result->errors(), 'old' => $input]);
        }

        $data = $result->acceptedData();
        $dto = (new CreerSalleDTOBuilder())
            ->nom($data['nom'])
            ->batiment($data['batiment'])
            ->capacite($data['capacite'])
            ->type($data['type'])
            ->active($data['active'])
            ->build();

        $this->salles->save(new Salle((array) $dto));
        (new Flash())->success('Salle créée avec succès.');

        return $this->redirect('/salles');
    }

    public function edit(int $id): string
    {
        $salle = $this->salles->findById($id);

        return $salle === null
            ? $this->notFound()
            : $this->views->render('salle/form', ['salle' => $salle, 'errors' => [], 'old' => []]);
    }

    public function update(int $id, array $input): string
    {
        $salle = $this->salles->findById($id);

        if ($salle === null) {
            return $this->notFound();
        }

        $input['active'] = (bool) ($input['active'] ?? false);
        if (isset($input['capacite'])) {
            $input['capacite'] = (int) $input['capacite'];
        }

        $result = $this->validator->validate($input);

        if (!$result->isValid()) {
            return $this->views->render('salle/form', ['salle' => $salle, 'errors' => $result->errors(), 'old' => $input]);
        }

        $salle->fill($result->acceptedData());
        $this->salles->save($salle);
        (new Flash())->success('Salle mise à jour avec succès.');

        return $this->redirect('/salles/' . $id);
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