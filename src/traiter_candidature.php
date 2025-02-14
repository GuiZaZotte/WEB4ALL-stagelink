<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    header('Location: connexion.php');
    exit;
}

// Récupérer l'ID de l'utilisateur
$stmt = $conn->prepare("SELECT id FROM Utilisateurs WHERE email = ?");
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION['error'] = "Utilisateur non trouvé.";
    header('Location: offres.php');
    exit;
}

$utilisateur_id = $user['id'];

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: offres.php');
    exit;
}

// Récupérer l'ID de l'offre et vérifier qu'il est valide
$offre_id = isset($_POST['offre_id']) ? (int)$_POST['offre_id'] : 0;
if ($offre_id <= 0) {
    $_SESSION['error'] = "ID de l'offre invalide.";
    header('Location: offres.php');
    exit;
}

// Vérifier que l'offre existe
$stmt = $conn->prepare("SELECT id FROM Offres WHERE id = ?");
$stmt->bind_param("i", $offre_id);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
    $_SESSION['error'] = "Cette offre n'existe pas.";
    header('Location: offres.php');
    exit;
}

// Vérifier si l'utilisateur a déjà postulé à cette offre
$stmt = $conn->prepare("SELECT id FROM Candidatures WHERE offre_id = ? AND utilisateur_id = ?");
$stmt->bind_param("ii", $offre_id, $utilisateur_id);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    $_SESSION['error'] = "Vous avez déjà postulé à cette offre.";
    header("Location: offre_details.php?id=" . $offre_id);
    exit;
}

// Vérifier le fichier CV
if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = "Erreur lors du téléchargement du CV.";
    header("Location: offre_details.php?id=" . $offre_id);
    exit;
}

// Vérifier le type de fichier
$allowed_types = ['application/pdf'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $_FILES['cv']['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_types)) {
    $_SESSION['error'] = "Le CV doit être au format PDF.";
    header("Location: offre_details.php?id=" . $offre_id);
    exit;
}

// Créer le dossier de stockage des CV s'il n'existe pas
$upload_dir = __DIR__ . '/uploads/cv';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Générer un nom de fichier unique
$extension = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
$filename = uniqid('cv_') . '.' . $extension;
$filepath = $upload_dir . '/' . $filename;

// Déplacer le fichier
if (!move_uploaded_file($_FILES['cv']['tmp_name'], $filepath)) {
    $_SESSION['error'] = "Erreur lors de l'enregistrement du CV.";
    header("Location: offre_details.php?id=" . $offre_id);
    exit;
}

// Récupérer la lettre de motivation
$lettre_motivation = trim($_POST['lettre_motivation']);
if (empty($lettre_motivation)) {
    unlink($filepath); // Supprimer le CV si la lettre est vide
    $_SESSION['error'] = "La lettre de motivation est requise.";
    header("Location: offre_details.php?id=" . $offre_id);
    exit;
}

// Insérer la candidature dans la base de données
$stmt = $conn->prepare("INSERT INTO Candidatures (offre_id, utilisateur_id, cv, lettre_motivation, date_candidature) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("iiss", $offre_id, $utilisateur_id, $filename, $lettre_motivation);

if ($stmt->execute()) {
    $_SESSION['success'] = "Votre candidature a été envoyée avec succès !";
} else {
    unlink($filepath); // Supprimer le CV en cas d'erreur
    $_SESSION['error'] = "Une erreur est survenue lors de l'envoi de votre candidature.";
}

header("Location: offre_details.php?id=" . $offre_id);
exit;
