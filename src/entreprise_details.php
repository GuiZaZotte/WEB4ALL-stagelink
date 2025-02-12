<?php
$pageTitle = "Détails de l'entreprise - StageLink";
require_once 'config.php';
include('header.php');

// Vérifier si l'ID est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: entreprises.php');
    exit;
}

$id = (int)$_GET['id'];

// Récupérer les détails de l'entreprise
$sql = "SELECT e.*, 
        COALESCE(AVG(ev.note), 0) as moyenne_evaluations,
        COUNT(DISTINCT ev.id) as nombre_avis,
        COUNT(DISTINCT c.id) as nombre_candidatures
        FROM Entreprises e
        LEFT JOIN Evaluations ev ON e.id = ev.entreprise_id
        LEFT JOIN Offres o ON e.id = o.entreprise_id
        LEFT JOIN Candidatures c ON o.id = c.offre_id
        WHERE e.id = ?
        GROUP BY e.id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$entreprise = $stmt->get_result()->fetch_assoc();

if (!$entreprise) {
    header('Location: entreprises.php');
    exit;
}

// Récupérer les offres de l'entreprise
$sql = "SELECT o.*, 
        COUNT(c.id) as nombre_candidatures 
        FROM Offres o 
        LEFT JOIN Candidatures c ON o.id = c.offre_id
        WHERE o.entreprise_id = ?
        GROUP BY o.id
        ORDER BY o.date_debut DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$offres = $stmt->get_result();
?>

<head>
    <link rel="stylesheet" href="style/entreprise-detail.css">
</head>
<div class="entreprise-details">
    <h1><?= htmlspecialchars($entreprise['nom']) ?></h1>

    <div class="entreprise-info-details">
        <h2>Informations générales</h2>
        <p><strong>Description :</strong><br><?= nl2br(htmlspecialchars($entreprise['description'])) ?></p>

        <p><strong>Contact :</strong><br>
            Email : <?= htmlspecialchars($entreprise['email']) ?><br>
            Téléphone : <?= htmlspecialchars($entreprise['telephone']) ?></p>

        <div class="statistiques">
            <p><strong>Nombre de candidatures reçues :</strong> <?= $entreprise['nombre_candidatures'] ?></p>
            <p><strong>Moyenne des évaluations :</strong> <?= number_format($entreprise['moyenne_evaluations'], 1) ?>/5
                (<?= $entreprise['nombre_avis'] ?> avis)</p>
        </div>
    </div>

    <div class="offres-entreprise">
        <h2>Offres de stage</h2>
        <?php if ($offres->num_rows > 0): ?>
        <?php while ($offre = $offres->fetch_assoc()): ?>
        <div class="offre">
            <h3><?= htmlspecialchars($offre['titre']) ?></h3>
            <p class="description"><?= nl2br(htmlspecialchars($offre['description'])) ?></p>
            <div class="offre-details">
                <p><strong>Période :</strong> Du <?= date('d/m/Y', strtotime($offre['date_debut'])) ?>
                    au <?= date('d/m/Y', strtotime($offre['date_fin'])) ?></p>
                <?php if($offre['base_remuneration']): ?>
                <p><strong>Base de rémunération :</strong> <?= number_format($offre['base_remuneration'], 2) ?> €</p>
                <?php endif; ?>
                <p><strong>Nombre de candidatures :</strong> <?= $offre['nombre_candidatures'] ?></p>
            </div>
            <a href="offre_details.php?id=<?= $offre['id'] ?>" class="voir-plus">Voir plus</a>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p>Aucune offre de stage disponible pour le moment.</p>
        <?php endif; ?>
    </div>
</div>