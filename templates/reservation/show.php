<h1>Reservation de <?= htmlspecialchars($reservation->responsable, ENT_QUOTES, 'UTF-8') ?></h1>
<p>Email : <?= htmlspecialchars($reservation->email, ENT_QUOTES, 'UTF-8') ?></p>
<p>Motif : <?= htmlspecialchars($reservation->motif, ENT_QUOTES, 'UTF-8') ?></p>
<p>Statut : <?= htmlspecialchars($reservation->statut, ENT_QUOTES, 'UTF-8') ?></p>
<form method="post" action="/reservations/<?= (int) $reservation->id ?>/cancel"><button type="submit">Annuler</button></form>