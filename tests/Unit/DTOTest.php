<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\CreerReservationDTOBuilder;
use App\DTO\CreerSalleDTOBuilder;
use DateTimeImmutable;
use LogicException;
use PHPUnit\Framework\TestCase;

final class DTOTest extends TestCase
{
    public function testSalleBuilderBuildsTypedDTO(): void
    {
        $dto = (new CreerSalleDTOBuilder())
            ->nom('Salle B12')
            ->batiment('Bâtiment B')
            ->capacite(40)
            ->type('cours')
            ->active(true)
            ->build();

        self::assertSame('Salle B12', $dto->nom);
        self::assertSame(40, $dto->capacite);
        self::assertTrue($dto->active);
    }

    public function testReservationBuilderConvertsDatesToImmutableObjects(): void
    {
        $dto = (new CreerReservationDTOBuilder())
            ->salleId(1)
            ->responsable('Awa Ndiaye')
            ->email('awa@example.com')
            ->motif('Cours de PHP')
            ->dateDebut('2026-09-08 10:00:00')
            ->dateFin(new DateTimeImmutable('2026-09-08 12:00:00'))
            ->build();

        self::assertSame(1, $dto->salleId);
        self::assertInstanceOf(DateTimeImmutable::class, $dto->dateDebut);
        self::assertInstanceOf(DateTimeImmutable::class, $dto->dateFin);
    }

    public function testBuilderRejectsIncompleteDTO(): void
    {
        $this->expectException(LogicException::class);

        (new CreerSalleDTOBuilder())->nom('Salle B12')->build();
    }
}