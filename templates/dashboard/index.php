<?php
$pageTitle = 'Tableau de bord';
$activeNav = 'dashboard';
$statusLabels = [
    'confirmee' => 'Confirmée',
    'annulee' => 'Annulée',
];
?>
<section class="page-heading">
    <div class="page-heading__content">
        <p class="page-heading__eyebrow">Vue d'ensemble</p>
        <h1>Tableau de bord</h1>
        <p class="page-heading__intro">L'état des salles et des réservations en un coup d'œil.</p>
        <div class="register-rule" aria-hidden="true"></div>
    </div>
    <div class="page-heading__actions">
        <a class="button button--primary" href="/reservations/create">Créer une réservation</a>
    </div>
</section>

<section class="stat-grid" aria-label="Indicateurs clés">
    <article class="stat-card">
        <p class="stat-card__value"><?= (int) $totalSalles ?></p>
        <p class="stat-card__label">Salles enregistrées</p>
    </article>
    <article class="stat-card stat-card--success">
        <p class="stat-card__value"><?= (int) $sallesActives ?></p>
        <p class="stat-card__label">Salles actives</p>
    </article>
    <article class="stat-card stat-card--success">
        <p class="stat-card__value"><?= (int) $reservationsConfirmees ?></p>
        <p class="stat-card__label">Réservations confirmées</p>
    </article>
    <article class="stat-card stat-card--danger">
        <p class="stat-card__value"><?= (int) $reservationsAnnulees ?></p>
        <p class="stat-card__label">Réservations annulées</p>
    </article>
</section>

<section class="dashboard-panel" aria-labelledby="next-reservations-title">
    <h2 id="next-reservations-title">Prochaines réservations confirmées</h2>
    <?php if ($prochainesReservations === []): ?>
        <p class="dashboard-panel__empty">Aucune réservation à venir pour le moment.</p>
        <a class="button button--secondary" href="/reservations/create">Créer une réservation</a>
    <?php else: ?>
        <div class="ledger-table-wrap">
            <table class="ledger-table">
                <caption class="sr-only">Prochaines réservations confirmées</caption>
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
                <?php foreach ($prochainesReservations as $reservation): ?>
                    <tr>
                        <td class="time-cell" data-label="Créneau">
                            <time datetime="<?= htmlspecialchars($reservation->date_debut->format(\DateTimeInterface::ATOM), ENT_QUOTES, 'UTF-8') ?>">
                                <span class="time-cell__range"><?= $reservation->date_debut->format('H:i') ?> – <?= $reservation->date_fin->format('H:i') ?></span>
                                <span class="time-cell__day"><?= $reservation->date_debut->format('d/m/Y') ?></span>
                            </time>
                        </td>
                        <td data-label="Salle"><?= htmlspecialchars($reservation->salle?->nom ?? 'Salle indisponible', ENT_QUOTES, 'UTF-8') ?></td>
                        <td data-label="Responsable"><?= htmlspecialchars($reservation->responsable, ENT_QUOTES, 'UTF-8') ?></td>
                        <td data-label="Motif"><?= htmlspecialchars($reservation->motif, ENT_QUOTES, 'UTF-8') ?></td>
                        <td data-label="Statut">
                            <span class="status-mark status-mark--success">
                                <span class="status-mark__dot" aria-hidden="true"></span>
                                <?= htmlspecialchars($statusLabels[(string) $reservation->statut] ?? (string) $reservation->statut, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="dashboard-panel__footer">
            <a class="text-link" href="/reservations">Voir toutes les réservations →</a>
        </div>
    <?php endif; ?>
</section>
