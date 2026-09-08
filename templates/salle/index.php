<h1>Les salles</h1>
<p><a href="/salles/create">Ajouter une salle</a></p>
<ul>
<?php foreach ($salles as $salle): ?>
    <li><a href="/salles/<?= (int) $salle->id ?>"><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?></a> - <?= (int) $salle->capacite ?> places</li>
<?php endforeach; ?>
</ul>