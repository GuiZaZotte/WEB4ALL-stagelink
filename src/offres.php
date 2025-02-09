<?php
$pageTitle = "Offres - StageLink"; 
include('header.php'); 
?>
<head>
    <link rel="stylesheet" href="style/offres.css" />
</head>
    <div class="easter-egg">
      <img src="img/favicon.svg" alt="" class="logo" /><img
        src="img/Frame 3 (1).svg"
        alt=""
        class="logo-text"
      />
    </div>

    <?php
require_once 'config.php';

// Récupérer les offres avec leurs entreprises
$sql = "SELECT o.id, o.titre, o.description, o.base_remuneration, o.date_debut, o.date_fin, e.nom AS entreprise_nom 
        FROM Offres o
        JOIN Entreprises e ON o.entreprise_id = e.id
        ORDER BY o.date_debut DESC";
$offres = $conn->query($sql);
?>

<div class="offre">
    <h2>Offres de stage</h2>
    <?php while ($row = $offres->fetch_assoc()): ?>
        <div class="container">
            <h3><?= htmlspecialchars($row['titre']) ?></h3>
            <p><strong>Entreprise :</strong> <?= htmlspecialchars($row['entreprise_nom']) ?></p>
            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
            <p><strong>Rémunération :</strong> <?= htmlspecialchars($row['base_remuneration']) ?> €</p>
            <p><strong>Durée :</strong> du <?= $row['date_debut'] ?> au <?= $row['date_fin'] ?: 'Non spécifié' ?></p>
            <p><strong>Compétences requises :</strong> 
                <?php
                $sql_comp = "SELECT c.nom FROM Competences c
                            JOIN Offres_Competences oc ON c.id = oc.competence_id
                            WHERE oc.offre_id = ?";
                $stmt_comp = $conn->prepare($sql_comp);
                $stmt_comp->bind_param("i", $row['id']);
                $stmt_comp->execute();
                $result_comp = $stmt_comp->get_result();
                $competences = [];
                while ($comp = $result_comp->fetch_assoc()) {
                    $competences[] = htmlspecialchars($comp['nom']);
                }
                echo implode(', ', $competences);
                ?>
            </p>
        </div>
    <?php endwhile; ?>
</div>




<?php include("footer.php"); ?>

