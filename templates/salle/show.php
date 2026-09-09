<?php
$pageTitle = (string) $salle->nom;
$activeNav = 'salles';
$roomTypeLabels = [
    'cours' => 'Salle de cours',
    'informatique' => 'Salle informatique',
    'laboratoire' => 'Laboratoire',
    'amphitheatre' => 'Amphithéâtre',
    'reunion' => 'Salle de réunion',
];
?>
<a class="back-link" href="/salles">Retour aux salles</a>

<section class="page-heading detail-heading">
    <div class="page-heading__content">
        <p class="page-heading__eyebrow">Fiche de salle</p>
        <h1><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="register-rule" aria-hidden="true"></div>
    </div>
    <div class="page-heading__actions">
        <a class="button button--secondary" href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a>
    </div>
</section>

<section class="record-sheet" aria-labelledby="room-information-title">
    <h2 id="room-information-title" class="sr-only">Informations de la salle</h2>
    <dl class="definition-grid">
        <div>
            <dt>Bâtiment</dt>
            <dd><?= htmlspecialchars($salle->batiment, ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt>Capacité</dt>
            <dd class="numeric"><?= (int) $salle->capacite ?> places</dd>
        </div>
        <div>
            <dt>Type</dt>
            <dd><?= htmlspecialchars($roomTypeLabels[$salle->type] ?? (string) $salle->type, ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt>Statut</dt>
            <dd>
                <span class="status-mark <?= $salle->active ? 'status-mark--success' : 'status-mark--neutral' ?>">
                    <span class="status-mark__dot" aria-hidden="true"></span>
                    <?= $salle->active ? 'Active' : 'Inactive' ?>
                </span>
            </dd>
        </div>
    </dl>
</section>
