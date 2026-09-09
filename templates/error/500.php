<?php $pageTitle = 'Erreur interne'; ?>
<?php $activeNav = ''; ob_start(); ?>
<section class="error-page">
    <h1>500 — Erreur interne</h1>
    <p>Une erreur inattendue est survenue. Veuillez réessayer plus tard.</p>
    <p><a href="/">Retour au tableau de bord</a></p>
</section>
<?php $content = ob_get_clean(); require dirname(__DIR__) . '/layout/base.php'; ?>
