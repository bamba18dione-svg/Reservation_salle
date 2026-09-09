<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Validation\SalleValidator;
use App\Validation\ReservationValidator;

class ValidationTest extends TestCase
{
    private $salleValidator;
    private $reservationValidator;

    protected function setUp(): void
    {
        $this->salleValidator = new SalleValidator();
        $this->reservationValidator = new ReservationValidator();
    }

    public function testAdresseElectroniqueInvalide(): void
    {
        $donnees = [
            'email' => 'email-invalide-sans-at',
            'date_debut' => '2026-10-10 10:00:00',
            'date_fin' => '2026-10-10 12:00:00',
        ];

        $erreurs = $this->reservationValidator->validate($donnees)->errors();
        $this->assertArrayHasKey('email', $erreurs);
    }

    public function testNomVide(): void
    {
        $donnees = [
            'nom' => '', // Vide
            'batiment' => 'Bloc A',
            'capacite' => 10,
            'type' => 'reunion',
            'active' => true,
        ];

        $erreurs = $this->salleValidator->validate($donnees)->errors();
        $this->assertArrayHasKey('nom', $erreurs);
    }

    public function testCapaciteNegative(): void
    {
        $donnees = [
            'nom' => 'Salle C',
            'batiment' => 'Bloc A',
            'capacite' => -5,
            'type' => 'reunion',
            'active' => true,
        ];

        $erreurs = $this->salleValidator->validate($donnees)->errors();
        $this->assertArrayHasKey('capacite', $erreurs);
    }

    public function testTypeDeSalleInconnu(): void
    {
        $donnees = [
            'nom' => 'Salle D',
            'batiment' => 'Bloc A',
            'capacite' => 20,
            'type' => 'type_inexistant',
            'active' => true,
        ];

        $erreurs = $this->salleValidator->validate($donnees)->errors();
        $this->assertArrayHasKey('type', $erreurs);
    }

    public function testDateIncorrecte(): void
    {
        $donnees = [
            'email' => 'test@example.com',
            'date_debut' => '2026-13-45 25:99:00', // Format / Valeur invalide
            'date_fin' => '2026-10-10 12:00:00',
        ];

        $erreurs = $this->reservationValidator->validate($donnees)->errors();
        $this->assertArrayHasKey('date_debut', $erreurs);
    }
}