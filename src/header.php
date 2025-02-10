<?php
session_start(); // Démarrer la session

// Récupérer les informations de l'utilisateur
$prenom = isset($_SESSION['prenom']) ? $_SESSION['prenom'] : 'Mon Compte';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Etudiant'; // Par défaut "Etudiant"

// Détecter la page actuelle
$page = basename($_SERVER['PHP_SELF']);
?>



<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo isset($pageTitle) ? $pageTitle : 'StageLink'; ?></title>
    <link rel="stylesheet" href="style/variable.css" />
    <link rel="icon" href="img/favicon.svg" type="image/svg" />
</head>

<body>
    <header>
        <div class="first-bar">
            <div class="stagelink">StageLink</div>
            <form action="recherche.php" method="get">
                <input type="text" name="q" placeholder="Rechercher..." />
                <button type="submit"><img src="img/search.svg" alt="Rechercher" /></button>
            </form>
            <div class="compte">
                <img src="img/compte.svg" alt="Mon compte" />
                <div class="nom-compte">
                    <?= isset($_SESSION['prenom']) ? htmlspecialchars($_SESSION['prenom']) : "Mon Compte"; ?>
                </div>
            </div>
        </div>
        <nav>
            <a href="accueil.php" class="pages <?= ($page == 'accueil.php') ? 'activer' : '' ?>">Accueil</a>
            <a href="offres.php" class="pages <?= ($page == 'offres.php') ? 'activer' : '' ?>">Offres</a>
            <a href="entreprises.php" class="pages <?= ($page == 'entreprises.php') ? 'activer' : '' ?>">Entreprises</a>

            <?php if ($role == "Administrateur" || $role == "Pilote"): ?>
            <a href="dashboard.php" class="pages <?= ($page == 'dashboard.php') ? 'activer' : '' ?>">Dashboard</a>
            <?php endif; ?>
        </nav>
    </header>