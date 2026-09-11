<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/database.php';

use App\Model\Salle;

// Liste des salles initiales exigées par le cahier des charges
$sallesInitiales = [
    [
        'nom' => 'Amphithéâtre A',
        'batiment' => 'Bâtiment Principal',
        'capacite' => 250,
        'type' => 'amphitheatre',
        'active' => true,
    ],
    [
        'nom' => 'Salle B12',
        'batiment' => 'Bâtiment B',
        'capacite' => 40,
        'type' => 'cours',
        'active' => true,
    ],
    [
        'nom' => 'Laboratoire Chimie',
        'batiment' => 'Bloc Scientifique',
        'capacite' => 24,
        'type' => 'laboratoire',
        'active' => true,
    ],
    [
        'nom' => 'Salle Informatique 1',
        'batiment' => 'Bloc Informatique',
        'capacite' => 30,
        'type' => 'informatique',
        'active' => true,
    ],
    [
        'nom' => 'Salle de réunion',
        'batiment' => 'Administration',
        'capacite' => 12,
        'type' => 'reunion',
        'active' => true,
    ],
];

echo "Début de l'insertion des données initiales...\n";

$ajoutees = 0;
$existantes = 0;

foreach ($sallesInitiales as $data) {
    
    $salle = Salle::firstOrCreate(
        [
            'nom' => $data['nom'],
            'batiment' => $data['batiment'],
        ],
        $data
    );

    if ($salle->wasRecentlyCreated) {
        echo "  [+] Salle créée : {$salle->nom} ({$salle->batiment})\n";
        $ajoutees++;
    } else {
        echo "  [-] Salle déjà existante : {$salle->nom} ({$salle->batiment})\n";
        $existantes++;
    }
}

echo "Seeding terminé : {$ajoutees} créée(s), {$existantes} déjà présente(s).\n";