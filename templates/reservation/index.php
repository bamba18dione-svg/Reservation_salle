<h1>Les reservations</h1>
<p><a href="/reservations/create">Creer une reservation</a></p>
<ul>
<?php foreach ($reservations as $reservation): ?>
    <li><a href="/reservations/<?= (int) $reservation->id ?>"><?= htmlspecialchars($reservation->responsable, ENT_QUOTES, 'UTF-8') ?></a> - <?= htmlspecialchars($reservation->date_debut->format('Y-m-d H:i'), ENT_QUOTES, 'UTF-8') ?></li>
<?php endforeach; ?>
</ul>