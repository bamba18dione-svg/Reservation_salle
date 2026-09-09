<?php
$pageTitle = 'Page introuvable';
$activeNav = '';
?>
<section class="empty-state" aria-labelledby="not-found-title">
    <p class="page-heading__eyebrow">Erreur 404</p>
    <h1 id="not-found-title">Page introuvable</h1>
    <p>La salle ou la réservation demandée n’existe pas.</p>
    <div class="page-heading__actions" style="justify-content: center;">
        <a class="button button--primary" href="/reservations">Voir les réservations</a>
        <a class="button button--secondary" href="/salles">Voir les salles</a>
    </div>
</section>
