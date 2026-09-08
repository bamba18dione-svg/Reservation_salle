<h1><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?></h1>
<p>Batiment : <?= htmlspecialchars($salle->batiment, ENT_QUOTES, 'UTF-8') ?></p>
<p>Capacite : <?= (int) $salle->capacite ?> places</p>
<p>Type : <?= htmlspecialchars($salle->type, ENT_QUOTES, 'UTF-8') ?></p>
<p>Statut : <?= $salle->active ? 'Active' : 'Inactive' ?></p>
<p><a href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a></p>