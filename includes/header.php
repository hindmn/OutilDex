<!DOCTYPE html>
<html lang="fr-CA">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title><?= affichage($titrePage ?? 'Accueil') ?> | Dex</title>
</head>

<body>
    <header>
        <a href="index.php">Dex</a>

        <?php // ======== BONUS ======== ?>
        <?php if (!empty($_SESSION['nom'])): ?>
            <p>Salut, <?= affichage($_SESSION['nom']) ?> !</p>
        <?php endif; ?>
    </header>
    <main>