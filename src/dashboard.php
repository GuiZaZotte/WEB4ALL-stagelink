<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} else {
    // Session is already started
}
require_once 'config.php';

$pageTitle = "Tableau de bord - StageLink";
include('header.php');

// Vérifier si l'utilisateur est connecté et est un administrateur ou un pilote
if (!isset($_SESSION['email']) || !in_array($_SESSION['role'], ['Administrateur', 'Pilote'])) {
    header('Location: index.php');
    exit;
}
?>


<div class="dashboard">
    <div class="dashboard-header">
        <h1>Tableau de bord</h1>
    </div>

    <div class="dashboard-content">
        <div class="card">
            <h2>Créer une entreprise</h2>
            <form action="traiter_entreprise.php" method="POST" class="form-entreprise">
                <div class="form-group">
                    <label for="nom">Nom de l'entreprise</label>
                    <input type="text" id="nom" name="nom" required>
                </div>

                <div class="form-group">
                    <label for="description">Description de l'entreprise</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="email">Email de contact</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone de contact</label>
                    <input type="tel" id="telephone" name="telephone" pattern="[0-9]{10}" required>
                    <small>Format : 0123456789</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Créer l'entreprise</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("creer-offre.php"); ?>
<?php include('footer.php'); ?>