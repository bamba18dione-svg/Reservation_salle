<?php
$pageTitle = 'Connexion';
$activeNav = '';
$old = $old ?? [];
$errors = $errors ?? [];
$hasError = static fn (string $field): bool => !empty($errors[$field]);

// Identifiants de démonstration pour le coach / tests
$demoEmail = 'admin@example.com';
$demoPassword = 'password123';
$valueEmail = $old['email'] ?? $demoEmail;
$valuePassword = $old['password'] ?? $demoPassword;
?>
<section class="page-heading">
    <div class="page-heading__content">
        <p class="page-heading__eyebrow">Espace responsable</p>
        <h1>Connexion</h1>
        <p class="page-heading__intro">Connectez-vous pour creer et gerer les reservations.</p>
        <div class="register-rule" aria-hidden="true"></div>
    </div>
</section>

<?php if ($errors !== []): ?>
    <div class="error-summary" role="alert">
        <h2>Veuillez corriger les champs indiques.</h2>
        <ul>
            <?php foreach ($errors as $messages): ?>
                <?php foreach ($messages as $message): ?>
                    <li><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="demo-credentials" role="note">
    <h2>Identifiants de démonstration</h2>
    <p>Ces identifiants sont pré-remplis pour les tests.</p>
    <ul>
        <li><strong>E-mail</strong> <?= htmlspecialchars($demoEmail, ENT_QUOTES, 'UTF-8') ?></li>
        <li><strong>Mot de passe</strong> <?= htmlspecialchars($demoPassword, ENT_QUOTES, 'UTF-8') ?></li>
    </ul>
</div>

<form class="record-sheet form-layout" method="post" action="/login" novalidate>
    <div class="form-field">
        <label for="email">Adresse e-mail</label>
        <input
            type="email"
            id="email"
            name="email"
            value="<?= htmlspecialchars($valueEmail, ENT_QUOTES, 'UTF-8') ?>"
            required
            autofocus>
    </div>

    <div class="form-field">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" value="<?= htmlspecialchars($valuePassword, ENT_QUOTES, 'UTF-8') ?>" required>
    </div>

    <button type="submit" class="btn btn--primary">Se connecter</button>
</form>

<p class="auth-hint">Connectez-vous avec le compte fourni par l'administrateur.</p>
