<?php
$pageTitle = 'Créer une réservation';
$activeNav = 'reservations';
$old = $old ?? [];
$errors = $errors ?? [];
$hasError = static fn (string $field): bool => !empty($errors[$field]);
$fieldLabels = [
    'salle_id' => 'Salle',
    'responsable' => 'Responsable',
    'email' => 'Adresse e-mail',
    'motif' => 'Motif',
    'date_debut' => 'Début',
    'date_fin' => 'Fin',
];
?>
<a class="back-link" href="/reservations">Retour aux réservations</a>

<section class="page-heading">
    <div class="page-heading__content">
        <p class="page-heading__eyebrow">Registre chronologique</p>
        <h1>Créer une réservation</h1>
        <p class="page-heading__intro">Indiquez la salle, le créneau et la personne responsable.</p>
        <div class="register-rule" aria-hidden="true"></div>
    </div>
</section>

<?php if ($errors !== []): ?>
    <div class="error-summary" id="error-summary" role="alert" tabindex="-1">
        <h2>Veuillez corriger les champs indiqués.</h2>
        <ul>
            <?php foreach ($errors as $field => $messages): ?>
                <?php if (!empty($messages)): ?>
                    <li><a href="#<?= htmlspecialchars((string) $field, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($fieldLabels[$field] ?? (string) $field, ENT_QUOTES, 'UTF-8') ?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form class="record-sheet form-layout" method="post" action="/reservations" novalidate>
    <?php if ($salles === []): ?>
        <section class="empty-state" aria-labelledby="no-room-title">
            <h2 id="no-room-title">Aucune salle n’est enregistrée.</h2>
            <p>Ajoutez une salle avant de créer une réservation.</p>
            <a class="button button--secondary" href="/salles/create">Ajouter une salle</a>
        </section>
    <?php else: ?>
        <section class="form-section" aria-labelledby="booking-place-title">
            <div class="form-section__heading">
                <h2 id="booking-place-title">Salle et créneau</h2>
                <p>Choisissez un espace actif et le format attendu pour les dates.</p>
            </div>
            <div class="field-grid">
                <div class="form-field form-field--full">
                    <label for="salle_id">Salle <span class="required" aria-hidden="true">*</span></label>
                    <select class="form-control" id="salle_id" name="salle_id" aria-invalid="<?= $hasError('salle_id') ? 'true' : 'false' ?>" aria-describedby="salle_id-help<?= $hasError('salle_id') ? ' salle_id-error' : '' ?>" required>
                        <option value="">Sélectionnez une salle</option>
                        <?php foreach ($salles as $salle): ?>
                            <option value="<?= (int) $salle->id ?>" <?= (string) ($old['salle_id'] ?? '') === (string) $salle->id ? 'selected' : '' ?>><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($salle->batiment, ENT_QUOTES, 'UTF-8') ?> · <?= (int) $salle->capacite ?> places</option>
                        <?php endforeach; ?>
                    </select>
                    <p class="form-help" id="salle_id-help">Les salles inactives ne peuvent pas recevoir de nouvelle réservation.</p>
                    <?php foreach ($errors['salle_id'] ?? [] as $error): ?><p class="form-error" id="salle_id-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>
                </div>
                <div class="form-field">
                    <label for="date_debut">Début <span class="required" aria-hidden="true">*</span></label>
                    <input class="form-control" id="date_debut" name="date_debut" type="text" value="<?= htmlspecialchars((string) ($old['date_debut'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="AAAA-MM-JJ HH:MM:SS" autocomplete="off" aria-invalid="<?= $hasError('date_debut') ? 'true' : 'false' ?>" aria-describedby="date_debut-help<?= $hasError('date_debut') ? ' date_debut-error' : '' ?>" required>
                    <p class="form-help" id="date_debut-help">Format : AAAA-MM-JJ HH:MM:SS.</p>
                    <?php foreach ($errors['date_debut'] ?? [] as $error): ?><p class="form-error" id="date_debut-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>
                </div>
                <div class="form-field">
                    <label for="date_fin">Fin <span class="required" aria-hidden="true">*</span></label>
                    <input class="form-control" id="date_fin" name="date_fin" type="text" value="<?= htmlspecialchars((string) ($old['date_fin'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="AAAA-MM-JJ HH:MM:SS" autocomplete="off" aria-invalid="<?= $hasError('date_fin') ? 'true' : 'false' ?>" aria-describedby="date_fin-help<?= $hasError('date_fin') ? ' date_fin-error' : '' ?>" required>
                    <p class="form-help" id="date_fin-help">Format : AAAA-MM-JJ HH:MM:SS.</p>
                    <?php foreach ($errors['date_fin'] ?? [] as $error): ?><p class="form-error" id="date_fin-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="form-section" aria-labelledby="booking-contact-title">
            <div class="form-section__heading">
                <h2 id="booking-contact-title">Responsable</h2>
                <p>La personne à contacter pour ce créneau.</p>
            </div>
            <div class="field-grid">
                <div class="form-field">
                    <label for="responsable">Responsable <span class="required" aria-hidden="true">*</span></label>
                    <input class="form-control" id="responsable" name="responsable" type="text" value="<?= htmlspecialchars((string) ($old['responsable'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="name" aria-invalid="<?= $hasError('responsable') ? 'true' : 'false' ?>" aria-describedby="responsable-help<?= $hasError('responsable') ? ' responsable-error' : '' ?>" required>
                    <p class="form-help" id="responsable-help">Nom et prénom de la personne responsable.</p>
                    <?php foreach ($errors['responsable'] ?? [] as $error): ?><p class="form-error" id="responsable-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>
                </div>
                <div class="form-field">
                    <label for="email">Adresse e-mail <span class="required" aria-hidden="true">*</span></label>
                    <input class="form-control" id="email" name="email" type="email" value="<?= htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="email" aria-invalid="<?= $hasError('email') ? 'true' : 'false' ?>" aria-describedby="email-help<?= $hasError('email') ? ' email-error' : '' ?>" required>
                    <p class="form-help" id="email-help">Utilisée pour le suivi de la réservation.</p>
                    <?php foreach ($errors['email'] ?? [] as $error): ?><p class="form-error" id="email-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="form-section" aria-labelledby="booking-reason-title">
            <div class="form-section__heading">
                <h2 id="booking-reason-title">Motif</h2>
                <p>Décrivez brièvement l’utilisation prévue de la salle.</p>
            </div>
            <div class="field-grid field-grid--single">
                <div class="form-field form-field--full">
                    <label for="motif">Motif <span class="required" aria-hidden="true">*</span></label>
                    <textarea class="form-control" id="motif" name="motif" rows="4" aria-invalid="<?= $hasError('motif') ? 'true' : 'false' ?>" aria-describedby="motif-help<?= $hasError('motif') ? ' motif-error' : '' ?>" required><?= htmlspecialchars((string) ($old['motif'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                    <p class="form-help" id="motif-help">Exemple : cours de PHP pour le groupe L3.</p>
                    <?php foreach ($errors['motif'] ?? [] as $error): ?><p class="form-error" id="motif-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>
                </div>
            </div>
        </section>

        <div class="form-actions">
            <button class="button button--primary" type="submit">Réserver</button>
            <a class="text-link" href="/reservations">Annuler</a>
        </div>
    <?php endif; ?>
</form>
