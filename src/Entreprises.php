<?php
$pageTitle = "Entreprises - StageLink"; 
require_once 'config.php';
include('header.php'); 

// Récupérer toutes les entreprises avec leur note moyenne et la note de l'utilisateur actuel
$sql = "SELECT e.*, 
        COALESCE(AVG(ev.note), 0) as note_moyenne,
        COUNT(ev.id) as nombre_avis,
        user_eval.note as user_note
        FROM Entreprises e
        LEFT JOIN Evaluations ev ON e.id = ev.entreprise_id
        LEFT JOIN (
            SELECT entreprise_id, note 
            FROM Evaluations 
            WHERE utilisateur_id = (
                SELECT id FROM Utilisateurs WHERE email = ?
            )
        ) user_eval ON e.id = user_eval.entreprise_id
        GROUP BY e.id
        ORDER BY e.nom";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();
?>

<head>
    <link rel="stylesheet" href="style/entreprises.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<div class="easter-egg">
    <img src="img/favicon.svg" alt="" class="logo" />
    <img src="img/Frame 3 (1).svg" alt="" class="logo-text" />
</div>

<div class="entreprises">
    <h2>Les entreprises partenaires</h2>

    <?php while ($row = $result->fetch_assoc()): 
        $note_moyenne = round($row['note_moyenne'], 1);
        $nombre_avis = $row['nombre_avis'];
    ?>
    <a href="entreprise_details.php?id=<?= $row['id'] ?>" class="entreprise-link">
        <div class="container">
            <div class="entreprise-header">
                <h2><?= htmlspecialchars($row['nom']) ?></h2>

            </div>

            <div class="entreprise-info">
                <?php if(isset($_SESSION['email'])): ?>
                <div class="rating">
                    <div class="stars" data-entreprise-id="<?= $row['id'] ?>"
                        <?= $row['user_note'] ? 'data-rating="'.$row['user_note'].'"' : '' ?>>
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <i class="star fa-star <?= $i <= ($row['user_note'] ?: $note_moyenne) ? 'fas' : 'far' ?>"
                            data-value="<?= $i ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="avis-count"><?= $nombre_avis ?> avis</span>
                </div>
                <?php endif; ?>
                <p><?= nl2br(htmlspecialchars($row['description'] ?? '')) ?></p>
                <?php if(!empty($row['site_web'])): ?>
                <p><strong>Site web :</strong>
                    <a href="<?= htmlspecialchars($row['site_web']) ?>" target="_blank">
                        <?= htmlspecialchars($row['site_web']) ?>
                    </a>
                </p>
                <?php endif; ?>
                <?php if(!empty($row['adresse'])): ?>
                <p><strong>Adresse :</strong> <?= htmlspecialchars($row['adresse']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </a>
    <?php endwhile; ?>
</div>



<script src="script/notation.js"></script>

<?php include("footer.php"); ?>