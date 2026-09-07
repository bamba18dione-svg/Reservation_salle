<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;

final class EloquentSalleRepository implements SalleRepositoryInterface
{
    public function all(): array
    {
        return Salle::query()->orderBy('nom')->get()->all();
    }

    public function findById(int $id): ?Salle
    {
        return Salle::query()->find($id);
    }

    public function save(Salle $salle): Salle
    {
        $salle->save();

        return $salle;
    }
}