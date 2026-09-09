<?php
$pageTitle = 'Méthode non autorisée';
$activeNav = '';
?>
<section class="empty-state" aria-labelledby="method-not-allowed-title">
    <p class="page-heading__eyebrow">Erreur 405</p>
    <h1 id="method-not-allowed-title">Méthode non autorisée</h1>
    <p>La méthode HTTP utilisée n’est pas permise pour cette page.</p>
    <div class="page-heading__actions" style="justify-content: center;">
        <a class="button button--primary" href="/reservations">Voir les réservations</a>
        <a class="button button--secondary" href="/salles">Voir les salles</a>
    </div>
</section>
