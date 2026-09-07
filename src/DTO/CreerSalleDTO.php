<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class CreerSalleDTO
{
    public function __construct(
        public string $nom,
        public string $batiment,
        public int $capacite,
        public string $type,
        public bool $active,
    ) {
    }
}