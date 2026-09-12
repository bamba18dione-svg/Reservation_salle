<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/database.php';

use App\Model\Reservation;
use App\Model\Salle;

// 4 reservations de démonstration, sans chevauchement, dans le futur.
$reservations = [
    [
        'salle' => 'Amphithéâtre A',
        'responsable' => 'Dr. Diop',
        'email' => 'diop@univ.example',
        'motif' => 'Conférence annuelle',
        'date_debut' => '2026-09-15 09:00:00',
        'date_fin' => '2026-09-15 12:00:00',
    ],
    [
        'salle' => 'Salle B12',
        'responsable' => 'M. Ndiaye',
        'email' => 'ndiaye@univ.example',
        'motif' => 'Cours de base de données',
        'date_debut' => '2026-09-16 14:00:00',
        'date_fin' => '2026-09-16 16:00:00',
    ],
    [
        'salle' => 'Laboratoire Chimie',
        'responsable' => 'Mme. Sow',
        'email' => 'sow@univ.example',
        'motif' => 'Travaux pratiques',
        'date_debut' => '2026-09-17 10:00:00',
        'date_fin' => '2026-09-17 12:30:00',
    ],
    [
        'salle' => 'Salle Informatique 1',
        'responsable' => 'M. Ba',
        'email' => 'ba@univ.example',
        'motif' => 'Projet informatique',
        'date_debut' => '2026-09-18 08:30:00',
        'date_fin' => '2026-09-18 11:30:00',
    ],
];

echo "Début de l'insertion des réservations de démonstration...\n";

$ajoutees = 0;
$existantes = 0;

foreach ($reservations as $data) {
    $salle = Salle::where('nom', $data['salle'])->first();

    if ($salle === null) {
        echo "  [!] Salle introuvable : {$data['salle']}\n";
        continue;
    }

    $existing = Reservation::where('salle_id', $salle->id)
        ->where('responsable', $data['responsable'])
        ->where('date_debut', $data['date_debut'])
        ->first();

    if ($existing !== null) {
        echo "  [-] Réservation déjà existante : {$data['motif']} ({$data['salle']})\n";
        $existantes++;
        continue;
    }

    Reservation::create([
        'salle_id' => $salle->id,
        'responsable' => $data['responsable'],
        'email' => $data['email'],
        'motif' => $data['motif'],
        'date_debut' => $data['date_debut'],
        'date_fin' => $data['date_fin'],
        'statut' => 'confirmee',
    ]);

    echo "  [+] Réservation créée : {$data['motif']} ({$data['salle']})\n";
    $ajoutees++;
}

echo "Seeding terminé : {$ajoutees} créée(s), {$existantes} déjà présente(s).\n";