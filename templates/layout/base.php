<?php $pageTitle = $pageTitle ?? 'Gestion des reservations'; ?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header><a href="/">Reservations universitaires</a></header>
<main><?= $content ?? '' ?></main>
</body>
</html>