<?php
session_start();
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Vérifier si c'est une requête AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Accès non autorisé']));
}

require_once 'config.php';

// Vérifier la session
if (!isset($_SESSION['email'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Vous devez être connecté pour évaluer une entreprise']));
}

// Récupérer et valider les données
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE || !isset($data['entreprise_id']) || !isset($data['note'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Données invalides']));
}

$note = (int)$data['note'];
if ($note < 1 || $note > 5) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'La note doit être comprise entre 1 et 5']));
}

try {
    // Récupérer l'ID de l'utilisateur
    $stmt = $conn->prepare("SELECT id FROM Utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        throw new Exception('Utilisateur non trouvé');
    }

    $utilisateur_id = (int)$user['id'];
    $entreprise_id = (int)$data['entreprise_id'];

    // Commencer une transaction
    $conn->begin_transaction();

    try {
        // Vérifier si l'utilisateur a déjà évalué cette entreprise
        $stmt = $conn->prepare("SELECT id FROM Evaluations WHERE utilisateur_id = ? AND entreprise_id = ?");
        $stmt->bind_param("ii", $utilisateur_id, $entreprise_id);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        if ($existing) {
            // Mettre à jour l'évaluation existante
            $stmt = $conn->prepare("UPDATE Evaluations SET note = ? WHERE utilisateur_id = ? AND entreprise_id = ?");
            $stmt->bind_param("iii", $note, $utilisateur_id, $entreprise_id);
        } else {
            // Créer une nouvelle évaluation
            $stmt = $conn->prepare("INSERT INTO Evaluations (utilisateur_id, entreprise_id, note) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $utilisateur_id, $entreprise_id, $note);
        }
        
        $stmt->execute();

        // Récupérer la nouvelle moyenne et le nombre d'avis
        $stmt = $conn->prepare("SELECT AVG(note) as moyenne, COUNT(*) as total FROM Evaluations WHERE entreprise_id = ?");
        $stmt->bind_param("i", $entreprise_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        $conn->commit();

        echo json_encode([
            'success' => true,
            'moyenne' => round($result['moyenne'], 1),
            'nombre_avis' => $result['total']
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
