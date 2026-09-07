<?php

declare(strict_types=1);

namespace App\DTO;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

final class CreerReservationDTOBuilder
{
    private ?int $salleId = null;
    private ?string $responsable = null;
    private ?string $email = null;
    private ?string $motif = null;
    private ?DateTimeImmutable $dateDebut = null;
    private ?DateTimeImmutable $dateFin = null;

    public function salleId(int $salleId): self
    {
        $this->salleId = $salleId;
        return $this;
    }

    public function responsable(string $responsable): self
    {
        $this->responsable = $responsable;
        return $this;
    }

    public function email(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function motif(string $motif): self
    {
        $this->motif = $motif;
        return $this;
    }

    public function dateDebut(string|DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $this->toImmutable($dateDebut);
        return $this;
    }

    public function dateFin(string|DateTimeInterface $dateFin): self
    {
        $this->dateFin = $this->toImmutable($dateFin);
        return $this;
    }

    public function build(): CreerReservationDTO
    {
        if ($this->salleId === null || $this->responsable === null || $this->email === null || $this->motif === null || $this->dateDebut === null || $this->dateFin === null) {
            throw new \LogicException('Tous les champs de la reservation sont obligatoires.');
        }

        return new CreerReservationDTO(
            $this->salleId,
            $this->responsable,
            $this->email,
            $this->motif,
            $this->dateDebut,
            $this->dateFin,
        );
    }

    private function toImmutable(string|DateTimeInterface $date): DateTimeImmutable
    {
        if ($date instanceof DateTimeImmutable) {
            return $date;
        }

        if ($date instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($date);
        }

        try {
            return new DateTimeImmutable($date);
        } catch (\Exception $exception) {
            throw new InvalidArgumentException('Date invalide pour le DTO de reservation.', 0, $exception);
        }
    }
}