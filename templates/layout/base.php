<?php
$pageTitle = $pageTitle ?? 'Réservations universitaires';
$activeNav = $activeNav ?? '';
$navigation = [
    'dashboard' => ['label' => 'Tableau de bord', 'href' => '/'],
    'reservations' => ['label' => 'Réservations', 'href' => '/reservations'],
    'salles' => ['label' => 'Salles', 'href' => '/salles'],
];
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Gestion des salles et réservations universitaires">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> · Réservations universitaires</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body class="app-shell">
<header class="site-header">
    <div class="site-header__inner page-container">
        <a class="brand" href="/" aria-label="Réservations universitaires — accueil">
            <span class="brand__mark" aria-hidden="true">RU</span>
            <span class="brand__name">Réservations<br>universitaires</span>
        </a>
        <nav class="site-nav" aria-label="Navigation principale">
            <?php foreach ($navigation as $key => $item): ?>
                <a class="site-nav__link <?= $activeNav === $key ? 'is-active' : '' ?>" href="<?= $item['href'] ?>" <?= $activeNav === $key ? 'aria-current="page"' : '' ?>><?= $item['label'] ?></a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>

<main id="main-content" class="page-main page-container">
    <?= $content ?? '' ?>
</main>
</body>
</html>
