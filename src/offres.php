<?php
$pageTitle = "Offres - StageLink";
include('header.php');
?>

<head>
    <link rel="stylesheet" href="style/offres.css" />
</head>
<div class="easter-egg">
    <img src="img/favicon.svg" alt="" class="logo" /><img src="img/Frame 3 (1).svg" alt="" class="logo-text" />
</div>

<?php
require_once 'config.php';

// Récupérer les offres avec leurs entreprises
$sql = "SELECT o.id, o.titre, o.description, o.base_remuneration, o.date_debut, o.date_fin, e.nom AS entreprise_nom 
        FROM Offres o
        JOIN Entreprises e ON o.entreprise_id = e.id
        ORDER BY o.date_debut DESC";
$offres = $conn->query($sql); ?>

<div class="offre">
    <h2>Offres de stage</h2>

    <?php while ($row = $offres->fetch_assoc()): ?>
    <div class="container">
        <div class="offre-title">
            <div class="like-container">
                <h2><?= htmlspecialchars($row['titre']) ?></h2>
                <img class="like-svg" src="img/offres/like.svg" alt="" />
            </div>
            <p class="entreprise"><?= htmlspecialchars($row['entreprise_nom']) ?></p>
        </div>
        <div class="balise-container">
            <p class="balise">Stage</p>

            <p class="balise">
                <?= htmlspecialchars($row['base_remuneration']) ?>
                € / mois
            </p>
        </div>
        <?php
            $sql_comp = "SELECT c.nom FROM Competences c
               JOIN Offres_Competences oc ON c.id = oc.competence_id
            WHERE oc.offre_id = ?";
            $stmt_comp = $conn->prepare($sql_comp);
            $stmt_comp->bind_param("i", $row['id']);
            $stmt_comp->execute();
            $result_comp = $stmt_comp->get_result(); ?>

        <h3>Compétences requises :</h3>
        <div class="competences">
            <?php while ($comp = $result_comp->fetch_assoc()): ?>

            <p class="balise2"><?= htmlspecialchars($comp['nom']) ?></p>
            <?php endwhile; ?>
        </div>
        <div class="postuler">
            <p class="date">
                Du
                <?= date('d/m/Y', strtotime($row['date_debut'])) ?>
                au
                <?= $row['date_fin'] ? date('d/m/Y', strtotime($row['date_fin'])) : 'Non spécifié' ?>
            </p>
            <div class="button">Voir l'offre</div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php include("footer.php"); ?>