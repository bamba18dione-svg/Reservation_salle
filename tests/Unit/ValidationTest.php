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
            'client_email' => 'email-invalide-sans-at',
            'date_debut' => '2026-10-10 10:00:00',
            'date_fin' => '2026-10-10 12:00:00',
        ];

        $erreurs = $this->reservationValidator->validate($donnees);
        $this->assertArrayHasKey('client_email', $erreurs);
    }

    public function testResponsableVide(): void
    {
        $donnees = [
            'nom' => 'Salle B',
            'capacite' => 10,
            'type' => 'reunion',
            'responsable' => '', // Vide
        ];

        $erreurs = $this->salleValidator->validate($donnees);
        $this->assertArrayHasKey('responsable', $erreurs);
    }

    public function testCapaciteNegative(): void
    {
        $donnees = [
            'nom' => 'Salle C',
            'capacite' => -5,
            'type' => 'reunion',
            'responsable' => 'Jean Dupont',
        ];

        $erreurs = $this->salleValidator->validate($donnees);
        $this->assertArrayHasKey('capacite', $erreurs);
    }

    public function testTypeDeSalleInconnu(): void
    {
        $donnees = [
            'nom' => 'Salle D',
            'capacite' => 20,
            'type' => 'type_inexistant',
            'responsable' => 'Jean Dupont',
        ];

        $erreurs = $this->salleValidator->validate($donnees);
        $this->assertArrayHasKey('type', $erreurs);
    }

    public function testDateIncorrecte(): void
    {
        $donnees = [
            'client_email' => 'test@example.com',
            'date_debut' => '2026-13-45 25:99:00', // Format / Valeur invalide
            'date_fin' => '2026-10-10 12:00:00',
        ];

        $erreurs = $this->reservationValidator->validate($donnees);
        $this->assertArrayHasKey('date_debut', $erreurs);
    }
}