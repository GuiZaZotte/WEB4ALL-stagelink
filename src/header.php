<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
} else {
    // Session is already started
}

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
            <a href="offres.php" class="pages <?= (in_array($page, ['offres.php', 'offre_details.php'])) ? 'activer' : '' ?>
            ">Offres</a> <a href="entreprises.php" class="pages <?= (in_array($page, ['entreprises.php', 'entreprise_details.php'])) ? 'activer' : '' ?>
">Entreprises</a>

            <?php if ($role == "Administrateur" || $role == "Pilote"): ?>
                <a href="dashboard.php" class="pages <?= ($page == 'dashboard.php') ? 'activer' : '' ?>">Dashboard</a>
            <?php endif; ?>
        </nav>
    </header>