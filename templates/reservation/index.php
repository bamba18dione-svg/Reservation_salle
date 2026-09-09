<?php
$pageTitle = 'Les réservations';
$activeNav = 'reservations';
$statusLabels = [
    'confirmee' => 'Confirmée',
    'annulee' => 'Annulée',
];
$groups = [];
$days = [
    1 => 'Lundi',
    2 => 'Mardi',
    3 => 'Mercredi',
    4 => 'Jeudi',
    5 => 'Vendredi',
    6 => 'Samedi',
    7 => 'Dimanche',
];
$months = [
    1 => 'janvier',
    2 => 'février',
    3 => 'mars',
    4 => 'avril',
    5 => 'mai',
    6 => 'juin',
    7 => 'juillet',
    8 => 'août',
    9 => 'septembre',
    10 => 'octobre',
    11 => 'novembre',
    12 => 'décembre',
];
$formatFrenchDate = static function ($date) use ($days, $months): string {
    return sprintf('%s %d %s', $days[(int) $date->format('N')], (int) $date->format('j'), $months[(int) $date->format('n')]);
};
foreach ($reservations as $reservation) {
    $groups[$reservation->date_debut->format('Y-m-d')][] = $reservation;
}
?>
<section class="page-heading">
    <div class="page-heading__content">
        <p class="page-heading__eyebrow">Registre chronologique</p>
        <h1>Les réservations</h1>
        <p class="page-heading__intro">Consultez les créneaux réservés et les personnes responsables.</p>
        <div class="register-rule" aria-hidden="true"></div>
    </div>
    <div class="page-heading__actions">
        <a class="button button--primary" href="/reservations/create">Créer une réservation</a>
    </div>
</section>

<section class="filter-bar" aria-label="Filtrer par salle">
    <form method="get" action="/reservations" class="filter-form">
        <label for="filter-salle">Filtrer par salle</label>
        <select class="form-control" id="filter-salle" name="salle" onchange="this.form.submit()">
            <option value="">Toutes les salles</option>
            <?php foreach ($salles as $salle): ?>
                <option value="<?= (int) $salle->id ?>" <?= $salleId !== null && (int) $salleId === (int) $salle->id ? 'selected' : '' ?>><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
        <noscript><button class="button button--secondary" type="submit">Filtrer</button></noscript>
    </form>
</section>

<?php if ($reservations === []): ?>
    <section class="empty-state" aria-labelledby="empty-reservations-title">
        <h2 id="empty-reservations-title">Aucune réservation n’est enregistrée.</h2>
        <p>Créez un premier créneau pour organiser l’utilisation des salles.</p>
        <a class="button button--primary" href="/reservations/create">Créer une réservation</a>
    </section>
<?php else: ?>
    <?php foreach ($groups as $dateKey => $dateReservations): ?>
        <?php $groupDate = $dateReservations[0]->date_debut; ?>
        <section class="date-group" aria-labelledby="date-<?= htmlspecialchars($dateKey, ENT_QUOTES, 'UTF-8') ?>">
            <h2 class="date-group__heading" id="date-<?= htmlspecialchars($dateKey, ENT_QUOTES, 'UTF-8') ?>">
                <span><?= htmlspecialchars($formatFrenchDate($groupDate), ENT_QUOTES, 'UTF-8') ?></span>
                <span class="date-group__count"><?= count($dateReservations) ?> réservation<?= count($dateReservations) > 1 ? 's' : '' ?></span>
            </h2>
            <div class="ledger-table-wrap">
                <table class="ledger-table">
                    <caption class="sr-only">Réservations du <?= htmlspecialchars($formatFrenchDate($groupDate), ENT_QUOTES, 'UTF-8') ?></caption>
                    <thead>
                    <tr>
                        <th scope="col">Créneau</th>
                        <th scope="col">Salle</th>
                        <th scope="col">Responsable</th>
                        <th scope="col">Motif</th>
                        <th scope="col">Statut</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dateReservations as $reservation): ?>
                        <?php
                        $status = (string) $reservation->statut;
                        $statusClass = $status === 'confirmee' ? 'status-mark--success' : ($status === 'annulee' ? 'status-mark--danger' : 'status-mark--neutral');
                        $statusLabel = $statusLabels[$status] ?? $status;
                        $roomName = $reservation->salle?->nom ?? 'Salle indisponible';
                        ?>
                        <tr>
                            <td class="time-cell" data-label="Créneau">
                                <time datetime="<?= htmlspecialchars($reservation->date_debut->format(\DateTimeInterface::ATOM), ENT_QUOTES, 'UTF-8') ?>">
                                    <span class="time-cell__range"><?= $reservation->date_debut->format('H:i') ?> – <?= $reservation->date_fin->format('H:i') ?></span>
                                    <span class="time-cell__day"><?= htmlspecialchars($formatFrenchDate($reservation->date_debut), ENT_QUOTES, 'UTF-8') ?></span>
                                </time>
                            </td>
                            <td data-label="Salle"><a class="record-link" href="/reservations/<?= (int) $reservation->id ?>"><?= htmlspecialchars($roomName, ENT_QUOTES, 'UTF-8') ?></a></td>
                            <td data-label="Responsable"><?= htmlspecialchars($reservation->responsable, ENT_QUOTES, 'UTF-8') ?></td>
                            <td data-label="Motif"><span class="reservation-motive" title="<?= htmlspecialchars($reservation->motif, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($reservation->motif, ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td data-label="Statut">
                                <span class="status-mark <?= $statusClass ?>">
                                    <span class="status-mark__dot" aria-hidden="true"></span>
                                    <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endforeach; ?>
<?php endif; ?>
