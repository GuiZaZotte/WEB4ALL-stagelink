<?php
$pageTitle = "Entreprises - StageLink"; 
require_once 'config.php';
include('header.php'); 

// Récupérer toutes les entreprises avec leur note moyenne
$sql = "SELECT e.*, 
        COALESCE(AVG(ev.note), 0) as note_moyenne,
        COUNT(ev.id) as nombre_avis
        FROM Entreprises e
        LEFT JOIN Evaluations ev ON e.id = ev.entreprise_id
        GROUP BY e.id
        ORDER BY e.nom";

$result = $conn->query($sql);
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
    <div class="container">
        <div class="entreprise-header">
            <h2><?= htmlspecialchars($row['nom']) ?></h2>

        </div>

        <div class="entreprise-info">
            <?php if(isset($_SESSION['email'])): ?>
            <div class="rating">
                <div class="stars" data-entreprise-id="<?= $row['id'] ?>">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                    <i class="star fa-star <?= $i <= $note_moyenne ? 'fas' : 'far' ?>" data-value="<?= $i ?>"></i>
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
    <?php endwhile; ?>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.stars').forEach(starsContainer => {
        const stars = starsContainer.querySelectorAll('.star');
        const entrepriseId = starsContainer.dataset.entrepriseId;

        stars.forEach(star => {
            // Gestion du survol
            star.addEventListener('mouseover', function() {
                const rating = this.dataset.value;
                stars.forEach(s => {
                    if (s.dataset.value <= rating) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                    }
                });
            });

            // Rétablir l'affichage initial quand la souris quitte la zone
            starsContainer.addEventListener('mouseleave', function() {
                const currentRating = this.getAttribute('data-rating') || 0;
                stars.forEach(s => {
                    if (s.dataset.value <= currentRating) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                    }
                });
            });

            // Gestion du clic
            star.addEventListener('click', function() {
                if (!confirm('Voulez-vous vraiment noter cette entreprise ?')) return;

                const rating = this.dataset.value;
                fetch('rate_entreprise.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            entreprise_id: entrepriseId,
                            note: rating
                        }),
                        credentials: 'same-origin'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            starsContainer.setAttribute('data-rating', rating);
                            const avisCount = starsContainer.parentElement
                                .querySelector('.avis-count');
                            avisCount.textContent = data.nombre_avis + ' avis';

                            // Mettre à jour les étoiles
                            stars.forEach(s => {
                                if (s.dataset.value <= data.moyenne) {
                                    s.classList.remove('far');
                                    s.classList.add('fas');
                                } else {
                                    s.classList.remove('fas');
                                    s.classList.add('far');
                                }
                            });

                            alert('Merci pour votre évaluation !');
                        } else {
                            throw new Error(data.message ||
                                'Erreur lors de l\'évaluation');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue lors de l\'évaluation');
                    });
            });
        });
    });
});
</script>

<?php include("footer.php"); ?>