<h1>Creer une reservation</h1>
<form method="post">
    <label>Salle<select name="salle_id"><?php foreach ($salles as $salle): ?><option value="<?= (int) $salle->id ?>" <?= (string) (($old['salle_id'] ?? '') === (string) $salle->id) ? 'selected' : '' ?>><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></label>
    <?php foreach (['responsable' => 'Responsable', 'email' => 'Email', 'motif' => 'Motif', 'date_debut' => 'Debut', 'date_fin' => 'Fin'] as $field => $label): ?>
    <label><?= $label ?><input name="<?= $field ?>" value="<?= htmlspecialchars((string) ($old[$field] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
    <?php foreach ($errors[$field] ?? [] as $error): ?><p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>
    <?php endforeach; ?>
    <button type="submit">Reserver</button>
</form>