<?php
// Paramètres de connexion à la base de données
$servername = "localhost";
$username = "root"; // Utilisateur par défaut pour MySQL dans WAMP
$password = ""; // Mot de passe par défaut pour MySQL dans WAMP
$dbname = "StageManagement"; // Nom de la base de données

// Variable pour le message
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Créer une connexion
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        $message = "La connexion a échoué: " . $conn->connect_error;
    } else {
        // Requête pour vérifier l'utilisateur dans la base de données
        $sql = "SELECT * FROM Utilisateurs WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Si l'utilisateur existe, vérifier le mot de passe
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($mot_de_passe == $row['mot_de_passe']) { // Comparaison des mots de passe (en clair)
                session_start();
                $_SESSION['prenom'] = $row['prenom'];
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $row['role'];
                header("Location: accueil.php"); // Redirection vers une page sécurisée après connexion
                exit();
            } else {
                $message = "Mot de passe incorrect.";
            }
        } else {
            $message = "Utilisateur non trouvé.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style/variable.css" />
    <link rel="stylesheet" href="style/login.css" />
    <link rel="icon" href="img/favicon.svg" type="image/svg" />
    <title>Page de login</title>
</head>

<body>
    <div class="container">
        <div class="stagelink">StageLink</div>
        <div class="form-container">
            <div class="texte-intro">
                Ravi de vous retrouver sur StageLink !<br />
                Retrouvez toutes vos offres et candidatures en vous connectant :
            </div>

            <!-- Bloc de message pour afficher les erreurs -->
            <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="bloc-input">
                    <label for="">Adresse mail <input type="email" name="email" required /></label>
                    <label for="">Mot de passe <input type="password" name="mot_de_passe" required /></label>
                </div>
                <button type="submit">Je me connecte</button>
            </form>
        </div>
    </div>
</body>

</html>