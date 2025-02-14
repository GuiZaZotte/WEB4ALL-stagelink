<?php
session_start();
require_once 'config.php';

include('header.php');

// Vérifier si l'utilisateur est connecté et est un administrateur ou un pilote
if (!isset($_SESSION['email']) || !in_array($_SESSION['role'], ['Administrateur', 'Pilote'])) {
    header('Location: index.php');
    exit;
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

// Récupérer et nettoyer les données du formulaire
$nom = trim($_POST['nom']);
$description = trim($_POST['description']);
$email = trim($_POST['email']);
$telephone = trim($_POST['telephone']);

// Validation des données
$errors = [];

if (empty($nom)) {
    $errors[] = "Le nom de l'entreprise est requis.";
}

if (empty($description)) {
    $errors[] = "La description de l'entreprise est requise.";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "L'email est invalide.";
}

if (empty($telephone) || !preg_match("/^[0-9]{10}$/", $telephone)) {
    $errors[] = "Le numéro de téléphone doit contenir 10 chiffres.";
}

// Vérifier si l'email est déjà utilisé
$stmt = $conn->prepare("SELECT id FROM Entreprises WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    $errors[] = "Cette adresse email est déjà utilisée par une autre entreprise.";
}

// S'il y a des erreurs, rediriger vers le dashboard avec les messages d'erreur
if (!empty($errors)) {
    $_SESSION['error'] = implode("<br>", $errors);
    header('Location: dashboard.php');
    exit;
}

// Insérer l'entreprise dans la base de données
$stmt = $conn->prepare("INSERT INTO Entreprises (nom, description, email, telephone) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nom, $description, $email, $telephone);

if ($stmt->execute()) {
    $_SESSION['success'] = "L'entreprise a été créée avec succès !";
} else {
    $_SESSION['error'] = "Une erreur est survenue lors de la création de l'entreprise.";
}

header('Location: dashboard.php');
exit;
