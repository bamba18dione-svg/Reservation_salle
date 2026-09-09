<?php
$isEdit = $salle !== null;
$pageTitle = $isEdit ? 'Modifier une salle' : 'Ajouter une salle';
$activeNav = 'salles';
$old = $old ?? [];
$errors = $errors ?? [];
$value = static fn (string $field, mixed $default = ''): mixed => $old[$field] ?? ($salle?->{$field} ?? $default);
$hasError = static fn (string $field): bool => !empty($errors[$field]);
$fieldLabels = [
    'nom' => 'Nom',
    'batiment' => 'Bâtiment',
    'capacite' => 'Capacité',
    'type' => 'Type',
    'active' => 'Active',
];
$roomTypeLabels = [
    'cours' => 'Salle de cours',
    'informatique' => 'Salle informatique',
    'laboratoire' => 'Laboratoire',
    'amphitheatre' => 'Amphithéâtre',
    'reunion' => 'Salle de réunion',
];
?>
<a class="back-link" href="/salles">Retour aux salles</a>

<section class="page-heading">
    <div class="page-heading__content">
        <p class="page-heading__eyebrow">Catalogue institutionnel</p>
        <h1><?= $isEdit ? 'Modifier une salle' : 'Ajouter une salle' ?></h1>
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

<form class="record-sheet form-layout" method="post" action="<?= $isEdit ? '/salles/' . (int) $salle->id . '/edit' : '/salles' ?>" novalidate>
    <section class="form-section" aria-labelledby="room-identity-title">
        <div class="form-section__heading">
            <h2 id="room-identity-title">Identification</h2>
            <p>Les informations qui permettent de retrouver la salle.</p>
        </div>
        <div class="field-grid">
            <div class="form-field form-field--full">
                <label for="nom">Nom <span class="required" aria-hidden="true">*</span></label>
                <input class="form-control" id="nom" name="nom" type="text" value="<?= htmlspecialchars((string) $value('nom'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" aria-invalid="<?= $hasError('nom') ? 'true' : 'false' ?>" aria-describedby="nom-help<?= $hasError('nom') ? ' nom-error' : '' ?>" required>
                <p class="form-help" id="nom-help">Exemple : Salle B12.</p>
                <?php foreach ($errors['nom'] ?? [] as $error): ?><p class="form-error" id="nom-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>
            </div>
            <div class="form-field form-field--full">
                <label for="batiment">Bâtiment <span class="required" aria-hidden="true">*</span></label>
                <input class="form-control" id="batiment" name="batiment" type="text" value="<?= htmlspecialchars((string) $value('batiment'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="organization" aria-invalid="<?= $hasError('batiment') ? 'true' : 'false' ?>" aria-describedby="batiment-help<?= $hasError('batiment') ? ' batiment-error' : '' ?>" required>
                <p class="form-help" id="batiment-help">Indiquez le bâtiment ou le site universitaire.</p>
                <?php foreach ($errors['batiment'] ?? [] as $error): ?><p class="form-error" id="batiment-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="form-section" aria-labelledby="room-characteristics-title">
        <div class="form-section__heading">
            <h2 id="room-characteristics-title">Caractéristiques</h2>
            <p>Les informations utiles au choix d’une salle.</p>
        </div>
        <div class="field-grid">
            <div class="form-field">
                <label for="type">Type <span class="required" aria-hidden="true">*</span></label>
                <select class="form-control" id="type" name="type" aria-invalid="<?= $hasError('type') ? 'true' : 'false' ?>" aria-describedby="type-help<?= $hasError('type') ? ' type-error' : '' ?>" required>
                    <?php foreach ($roomTypeLabels as $type => $label): ?>
                        <option value="<?= $type ?>" <?= (string) $value('type') === $type ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="form-help" id="type-help">Choisissez le type d’espace correspondant.</p>
                <?php foreach ($errors['type'] ?? [] as $error): ?><p class="form-error" id="type-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>
            </div>
            <div class="form-field">
                <label for="capacite">Capacité <span class="required" aria-hidden="true">*</span></label>
                <input class="form-control" id="capacite" name="capacite" type="number" min="1" max="1000" inputmode="numeric" value="<?= htmlspecialchars((string) $value('capacite'), ENT_QUOTES, 'UTF-8') ?>" aria-invalid="<?= $hasError('capacite') ? 'true' : 'false' ?>" aria-describedby="capacite-help<?= $hasError('capacite') ? ' capacite-error' : '' ?>" required>
                <p class="form-help" id="capacite-help">Entre 1 et 1 000 places.</p>
                <?php foreach ($errors['capacite'] ?? [] as $error): ?><p class="form-error" id="capacite-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="form-section" aria-labelledby="room-status-title">
        <div class="form-section__heading">
            <h2 id="room-status-title">Statut</h2>
            <p>Une salle inactive ne peut pas recevoir de nouvelle réservation.</p>
        </div>
        <div class="checkbox-field">
            <input id="active" name="active" type="checkbox" value="1" <?= $value('active', true) ? 'checked' : '' ?> aria-invalid="<?= $hasError('active') ? 'true' : 'false' ?>">
            <label for="active">Active</label>
        </div>
        <?php foreach ($errors['active'] ?? [] as $error): ?><p class="form-error" id="active-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>
    </section>

    <div class="form-actions">
        <button class="button button--primary" type="submit"><?= $isEdit ? 'Enregistrer les modifications' : 'Ajouter la salle' ?></button>
        <a class="text-link" href="/salles">Annuler</a>
    </div>
</form>
