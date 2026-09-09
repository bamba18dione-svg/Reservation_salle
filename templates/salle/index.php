<?php
$pageTitle = 'Les salles';
$activeNav = 'salles';
$roomTypeLabels = [
    'cours' => 'Salle de cours',
    'informatique' => 'Salle informatique',
    'laboratoire' => 'Laboratoire',
    'amphitheatre' => 'Amphithéâtre',
    'reunion' => 'Salle de réunion',
];
?>
<section class="page-heading">
    <div class="page-heading__content">
        <p class="page-heading__eyebrow">Catalogue institutionnel</p>
        <h1>Les salles</h1>
        <p class="page-heading__intro">Consultez les espaces disponibles et leurs caractéristiques.</p>
        <div class="register-rule" aria-hidden="true"></div>
    </div>
    <div class="page-heading__actions">
        <a class="button button--primary" href="/salles/create">Ajouter une salle</a>
    </div>
</section>

<?php if ($salles === []): ?>
    <section class="empty-state" aria-labelledby="empty-rooms-title">
        <h2 id="empty-rooms-title">Aucune salle n’est enregistrée.</h2>
        <p>Ajoutez une salle pour commencer à organiser les espaces universitaires.</p>
        <a class="button button--primary" href="/salles/create">Ajouter une salle</a>
    </section>
<?php else: ?>
    <div class="ledger-table-wrap">
        <table class="ledger-table">
            <caption class="sr-only">Inventaire des salles universitaires</caption>
            <thead>
            <tr>
                <th scope="col">Salle</th>
                <th scope="col">Bâtiment</th>
                <th scope="col">Type</th>
                <th scope="col">Capacité</th>
                <th scope="col">Statut</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($salles as $salle): ?>
                <tr>
                    <td data-label="Salle">
                        <a class="record-link" href="/salles/<?= (int) $salle->id ?>"><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?></a>
                    </td>
                    <td data-label="Bâtiment"><?= htmlspecialchars($salle->batiment, ENT_QUOTES, 'UTF-8') ?></td>
                    <td data-label="Type" class="muted"><?= htmlspecialchars($roomTypeLabels[$salle->type] ?? (string) $salle->type, ENT_QUOTES, 'UTF-8') ?></td>
                    <td data-label="Capacité" class="numeric"><?= (int) $salle->capacite ?> places</td>
                    <td data-label="Statut">
                        <span class="status-mark <?= $salle->active ? 'status-mark--success' : 'status-mark--neutral' ?>">
                            <span class="status-mark__dot" aria-hidden="true"></span>
                            <?= $salle->active ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
