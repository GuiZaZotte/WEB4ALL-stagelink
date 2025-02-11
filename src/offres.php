<?php
$pageTitle = "Offres - StageLink";
require_once 'config.php';
include('header.php');

// Récupérer les likes de l'utilisateur connecté
$likes = [];
if (isset($_SESSION['email'])) {
    // Récupérer d'abord l'ID de l'utilisateur
    $stmt = $conn->prepare("SELECT id FROM Utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user) {
        // Récupérer les likes de l'utilisateur
        $stmt = $conn->prepare("SELECT offre_id FROM WishList WHERE utilisateur_id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $likes[] = (int)$row['offre_id'];
        }
    }
}
?>

<head>
    <link rel="stylesheet" href="style/offres.css" />
</head>

<div class="easter-egg">
    <img src="img/favicon.svg" alt="" class="logo" />
    <img src="img/Frame 3 (1).svg" alt="" class="logo-text" />
</div>

<div class="offre">
    <h2>Offres de stage</h2>

    <?php
    // Récupérer les offres avec leurs entreprises
    $sql = "SELECT o.id, o.titre, o.description, o.base_remuneration, o.date_debut, o.date_fin, e.nom AS entreprise_nom 
            FROM Offres o
            JOIN Entreprises e ON o.entreprise_id = e.id
            ORDER BY o.date_debut DESC";
    $offres = $conn->query($sql);

    while ($row = $offres->fetch_assoc()): 
        $isLiked = in_array((int)$row['id'], $likes);
    ?>
    <div class="container">
        <div class="offre-title">
            <div class="like-container">
                <h2><?= htmlspecialchars($row['titre']) ?> H/F</h2>
                <?php if (isset($_SESSION['email'])) { ?>
                <button class="like-button <?= $isLiked ? 'liked' : '' ?>" data-offre-id="<?= $row['id'] ?>">
                    <svg class="like-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="#000000" width="40" height="40">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                </button>
                <?php } ?>
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
            $result_comp = $stmt_comp->get_result();
        ?>
        <h3>Compétences requises :</h3>
        <div class="competences">
            <?php while ($comp = $result_comp->fetch_assoc()): ?>
            <p class="balise2"><?= htmlspecialchars($comp['nom']) ?></p>
            <?php endwhile; ?>
        </div>
        <div class="postuler">
            <p class="date">
                Du <?= date('d/m/Y', strtotime($row['date_debut'])) ?>
                au <?= $row['date_fin'] ? date('d/m/Y', strtotime($row['date_fin'])) : 'Non spécifié' ?>
            </p>
            <div class="button">Voir l'offre</div>
        </div>
    </div>
    <?php endwhile; ?>
</div>


<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".like-button").forEach(button => {
        button.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();

            const offreId = this.dataset.offreId;
            const isLiked = !this.classList.contains("liked");
            const thisButton = this;

            fetch("like.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: JSON.stringify({
                        offre_id: offreId,
                        liked: isLiked
                    }),
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Mettre à jour tous les boutons en fonction des likes reçus
                        document.querySelectorAll(".like-button").forEach(btn => {
                            const btnOffreId = parseInt(btn.dataset.offreId);
                            if (data.likes.includes(btnOffreId)) {
                                btn.classList.add("liked");
                            } else {
                                btn.classList.remove("liked");
                            }
                        });
                    } else {
                        throw new Error(data.message || 'Erreur inconnue');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    thisButton.classList.remove("liked");
                });
        });
    });
});
</script>

<?php include("footer.php"); ?>