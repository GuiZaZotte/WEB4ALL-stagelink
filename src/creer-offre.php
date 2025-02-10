<?php
require_once 'config.php'; // Connexion à la base de données

$message = "";

// Récupérer la liste des entreprises
$entreprises = $conn->query("SELECT id, nom FROM Entreprises");

// Récupérer la liste des compétences
$competences = $conn->query("SELECT id, nom FROM Competences");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $remuneration = trim($_POST['remuneration']);
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    // Vérification si 'entreprise_id' existe dans le tableau $_POST
    $entreprise_id = isset($_POST['entreprise_id']) ? $_POST['entreprise_id'] : null;
    $competences_selectionnees = $_POST['competences'] ?? [];

    if (!empty($titre) && !empty($description) && !empty($remuneration) && !empty($entreprise_id)) {
        // Insérer l'offre dans la table Offres
        $sql = "INSERT INTO Offres (entreprise_id, titre, description, base_remuneration, date_debut, date_fin) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issdss", $entreprise_id, $titre, $description, $remuneration, $date_debut, $date_fin);

        if ($stmt->execute()) {
            $offre_id = $stmt->insert_id;

            // Insérer les compétences liées dans Offres_Competences
            if (!empty($competences_selectionnees)) {
                foreach ($competences_selectionnees as $competence_id) {
                    $sql_comp = "INSERT INTO Offres_Competences (offre_id, competence_id) VALUES (?, ?)";
                    $stmt_comp = $conn->prepare($sql_comp);
                    $stmt_comp->bind_param("ii", $offre_id, $competence_id);
                    $stmt_comp->execute();
                }
            }

            $message = "<p class='success'>L'offre a été créée avec succès !</p>";
            header("Location: offres.php"); // Redirection vers la page des offres
            exit();
        } else {
            $message = "<p class='error'>Erreur lors de la création de l'offre.</p>";
        }
        $stmt->close();
    } else {
        $message = "<p class='error'>Veuillez remplir tous les champs.</p>";
    }
}
?>

<head>
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/variable.css">

</head>

<div class="container">
    <h2>Créer une nouvelle offre</h2>
    <?= $message ?>
    <form action="dashboard.php" method="post">
        <label for="titre">Titre de l'offre :</label>
        <input type="text" id="titre" name="titre" required>

        <label for="entreprise_id">Entreprise :</label>
        <select id="entreprise_id" name="entreprise_id" required>
            <option value="">Sélectionnez une entreprise</option>
            <?php while ($row = $entreprises->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nom']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="description">Description :</label>
        <textarea id="description" name="description" required></textarea>

        <label for="remuneration">Base de rémunération (€) :</label>
        <input type="number" id="remuneration" name="remuneration" step="0.01" required>

        <label for="date_debut">Date de début :</label>
        <input type="date" id="date_debut" name="date_debut" required>

        <label for="date_fin">Date de fin :</label>
        <input type="date" id="date_fin" name="date_fin">

        <div class="competences-container">
            <label for="competences">Compétences requises :</label>
            <select id="competences" name="competences[]" multiple required>
                <?php while ($row = $competences->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nom']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit">Créer</button>
    </form>
</div>