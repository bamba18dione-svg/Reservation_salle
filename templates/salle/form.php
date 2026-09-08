<?php $isEdit = $salle !== null; $value = static fn (string $field, mixed $default = ''): mixed => $old[$field] ?? ($salle?->{$field} ?? $default); ?>
<h1><?= $isEdit ? 'Modifier une salle' : 'Ajouter une salle' ?></h1>
<form method="post">
<?php foreach (['nom' => 'Nom', 'batiment' => 'Batiment', 'capacite' => 'Capacite'] as $field => $label): ?>
    <label><?= $label ?><input name="<?= $field ?>" value="<?= htmlspecialchars((string) $value($field), ENT_QUOTES, 'UTF-8') ?>"></label>
    <?php foreach ($errors[$field] ?? [] as $error): ?><p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>
<?php endforeach; ?>
    <label>Type<select name="type"><?php foreach (['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion'] as $type): ?><option value="<?= $type ?>" <?= $value('type') === $type ? 'selected' : '' ?>><?= $type ?></option><?php endforeach; ?></select></label>
    <label><input type="checkbox" name="active" value="1" <?= $value('active', true) ? 'checked' : '' ?>> Active</label>
    <button type="submit">Enregistrer</button>
</form>