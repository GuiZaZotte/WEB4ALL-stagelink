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
            <form onsubmit="return false;" <?php if ($page === 'accueil.php' || $page === 'dashboard.php') echo 'style="display: none;"'; ?>>
                <input type="text" id="searchInput" placeholder="<?php
                    switch ($page) {
                        case 'offres.php':
                            echo 'Rechercher une offre...';
                            break;
                        case 'entreprises.php':
                            echo 'Rechercher une entreprise...';
                            break;
                        default:
                            echo 'Rechercher...';
                    }
                ?>" />
                <button type="button"><img src="img/search.svg" alt="Rechercher" /></button>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        if (!searchInput) return;

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            // Sur la page des offres
            if (window.location.pathname.includes('offres.php')) {
                const offres = document.querySelectorAll('.offre-link');
                offres.forEach(offre => {
                    const titre = offre.querySelector('h2').textContent.toLowerCase();
                    
                    if (titre.includes(searchTerm)) {
                        offre.style.display = '';
                    } else {
                        offre.style.display = 'none';
                    }
                });
            }
            
            // Sur la page des entreprises
            if (window.location.pathname.includes('entreprises.php')) {
                const entreprises = document.querySelectorAll('.entreprise-link');
                entreprises.forEach(entreprise => {
                    const nom = entreprise.querySelector('h2').textContent.toLowerCase();
                    const description = entreprise.querySelector('.description') ? 
                        entreprise.querySelector('.description').textContent.toLowerCase() : '';
                    
                    if (nom.includes(searchTerm) || description.includes(searchTerm)) {
                        entreprise.style.display = '';
                    } else {
                        entreprise.style.display = 'none';
                    }
                });
            }
        });
    });
    </script>