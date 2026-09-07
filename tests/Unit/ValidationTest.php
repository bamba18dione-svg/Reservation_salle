<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use PHPUnit\Framework\TestCase;

final class ValidationTest extends TestCase
{
    public function testSalleValidatorRejectsInvalidCapacityAndType(): void
    {
        $result = (new SalleValidator())->validate([
            'nom' => 'Salle A',
            'batiment' => 'Bloc A',
            'capacite' => -1,
            'type' => 'inconnu',
            'active' => true,
        ]);

        self::assertFalse($result->isValid());
        self::assertArrayHasKey('capacite', $result->errors());
        self::assertArrayHasKey('type', $result->errors());
    }

    public function testReservationValidatorRejectsInvalidEmailAndDate(): void
    {
        $result = (new ReservationValidator())->validate([
            'salle_id' => 1,
            'responsable' => 'Awa Ndiaye',
            'email' => 'adresse-invalide',
            'motif' => 'Cours valide',
            'date_debut' => 'date incorrecte',
            'date_fin' => '2026-09-08 12:00:00',
        ]);

        self::assertFalse($result->isValid());
        self::assertArrayHasKey('email', $result->errors());
        self::assertArrayHasKey('date_debut', $result->errors());
    }

    public function testReservationValidatorRejectsEmptyResponsible(): void
    {
        $result = (new ReservationValidator())->validate([
            'salle_id' => 1,
            'responsable' => '',
            'email' => 'awa@example.com',
            'motif' => 'Cours valide',
            'date_debut' => '2026-09-08 10:00:00',
            'date_fin' => '2026-09-08 12:00:00',
        ]);

        self::assertFalse($result->isValid());
        self::assertArrayHasKey('responsable', $result->errors());
    }

    public function testValidatorKeepsOnlyAcceptedFields(): void
    {
        $result = (new SalleValidator())->validate([
            'nom' => 'Salle A',
            'batiment' => 'Bloc A',
            'capacite' => 40,
            'type' => 'cours',
            'active' => true,
            'champ_inattendu' => 'ignore',
        ]);

        self::assertTrue($result->isValid());
        self::assertArrayNotHasKey('champ_inattendu', $result->acceptedData());
        self::assertSame(40, $result->acceptedData()['capacite']);
    }
}