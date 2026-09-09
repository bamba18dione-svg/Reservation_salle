<?php
$pageTitle = 'Réservation de ' . (string) $reservation->responsable;
$activeNav = 'reservations';
$status = (string) $reservation->statut;
$statusLabels = [
    'confirmee' => 'Confirmée',
    'annulee' => 'Annulée',
];
$statusClass = $status === 'confirmee' ? 'status-mark--success' : ($status === 'annulee' ? 'status-mark--danger' : 'status-mark--neutral');
$statusLabel = $statusLabels[$status] ?? $status;
$roomName = $reservation->salle?->nom ?? 'Salle indisponible';
$roomId = $reservation->salle?->id;
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
    return sprintf('%s %d %s %d', $days[(int) $date->format('N')], (int) $date->format('j'), $months[(int) $date->format('n')], (int) $date->format('Y'));
};
?>
<a class="back-link" href="/reservations">Retour aux réservations</a>

<section class="page-heading detail-heading">
    <div class="page-heading__content">
        <p class="page-heading__eyebrow">Fiche de réservation</p>
        <h1>Réservation de <?= htmlspecialchars($reservation->responsable, ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="register-rule" aria-hidden="true"></div>
    </div>
    <div class="page-heading__actions">
        <span class="status-mark <?= $statusClass ?>">
            <span class="status-mark__dot" aria-hidden="true"></span>
            <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
        </span>
    </div>
</section>

<section class="schedule-band" aria-labelledby="schedule-title">
    <div>
        <span class="schedule-band__label" id="schedule-title">Créneau</span>
        <time datetime="<?= htmlspecialchars($reservation->date_debut->format(\DateTimeInterface::ATOM), ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($formatFrenchDate($reservation->date_debut), ENT_QUOTES, 'UTF-8') ?><br>
            <?= $reservation->date_debut->format('H:i') ?> – <?= $reservation->date_fin->format('H:i') ?>
        </time>
    </div>
    <div>
        <span class="schedule-band__label">Salle</span>
        <?php if ($roomId !== null): ?>
            <a class="schedule-band__room" href="/salles/<?= (int) $roomId ?>"><?= htmlspecialchars($roomName, ENT_QUOTES, 'UTF-8') ?></a>
        <?php else: ?>
            <span class="schedule-band__room"><?= htmlspecialchars($roomName, ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif; ?>
    </div>
</section>

<div class="detail-grid detail-section">
    <section class="detail-grid__section" aria-labelledby="contact-title">
        <h2 id="contact-title">Contact</h2>
        <dl class="definition-grid definition-grid--two">
            <div>
                <dt>Responsable</dt>
                <dd><?= htmlspecialchars($reservation->responsable, ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt>Adresse e-mail</dt>
                <dd><a class="text-link" href="mailto:<?= htmlspecialchars($reservation->email, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($reservation->email, ENT_QUOTES, 'UTF-8') ?></a></dd>
            </div>
        </dl>
    </section>
    <section class="detail-grid__section" aria-labelledby="motive-title">
        <h2 id="motive-title">Motif</h2>
        <p><?= nl2br(htmlspecialchars($reservation->motif, ENT_QUOTES, 'UTF-8')) ?></p>
    </section>
</div>

<section class="destructive-zone" aria-labelledby="cancel-title">
    <div>
        <p class="eyebrow">Action sensible</p>
        <h2 id="cancel-title">Annuler cette réservation</h2>
        <p>Cette action modifiera le statut de la réservation.</p>
    </div>
    <form id="cancel-form" method="post" action="/reservations/<?= (int) $reservation->id ?>/cancel" onsubmit="return window.confirm('Annuler cette réservation ?');">
        <button class="button button--danger" type="submit">Annuler la réservation</button>
    </form>
</section>

<dialog class="cancel-dialog" id="cancel-dialog" aria-labelledby="cancel-dialog-title">
    <h2 id="cancel-dialog-title">Annuler cette réservation&nbsp;?</h2>
    <p>Cette action modifiera le statut de la réservation.</p>
    <div class="cancel-dialog__actions">
        <button type="button" data-dialog-close>Conserver la réservation</button>
        <form method="post" action="/reservations/<?= (int) $reservation->id ?>/cancel">
            <button class="button button--danger" type="submit">Annuler la réservation</button>
        </form>
    </div>
</dialog>

<script>
(() => {
    const form = document.querySelector('#cancel-form');
    const dialog = document.querySelector('#cancel-dialog');
    const closeButton = dialog?.querySelector('[data-dialog-close]');

    if (!form || !dialog || typeof dialog.showModal !== 'function') {
        return;
    }

    form.addEventListener('submit', (event) => {
        if (event.defaultPrevented) {
            return;
        }

        event.preventDefault();
        dialog.showModal();
        closeButton?.focus();
    });

    closeButton?.addEventListener('click', () => dialog.close());
})();
</script>
