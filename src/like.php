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
    die(json_encode(['success' => false, 'message' => 'Session expirée']));
}

// Récupérer et valider les données
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE || !isset($data['offre_id']) || !isset($data['liked'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Données invalides']));
}

try {
    // Récupérer l'ID de l'utilisateur
    $stmt = $conn->prepare("SELECT id FROM Utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        throw new Exception('Utilisateur non trouvé');
    }

    $utilisateur_id = (int)$user['id'];
    $offre_id = (int)$data['offre_id'];
    $liked = (bool)$data['liked'];

    // Commencer une transaction
    $conn->begin_transaction();

    try {
        if ($liked) {
            // Vérifier si le like existe déjà
            $stmt = $conn->prepare("SELECT 1 FROM WishList WHERE utilisateur_id = ? AND offre_id = ?");
            $stmt->bind_param("ii", $utilisateur_id, $offre_id);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows === 0) {
                // Ajouter le like s'il n'existe pas
                $stmt = $conn->prepare("INSERT INTO WishList (utilisateur_id, offre_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $utilisateur_id, $offre_id);
                $stmt->execute();
            }
        } else {
            // Supprimer le like
            $stmt = $conn->prepare("DELETE FROM WishList WHERE utilisateur_id = ? AND offre_id = ?");
            $stmt->bind_param("ii", $utilisateur_id, $offre_id);
            $stmt->execute();
        }

        // Valider la transaction
        $conn->commit();

        // Récupérer tous les likes de l'utilisateur après la modification
        $stmt = $conn->prepare("SELECT offre_id FROM WishList WHERE utilisateur_id = ?");
        $stmt->bind_param("i", $utilisateur_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $likes = [];
        while ($row = $result->fetch_assoc()) {
            $likes[] = (int)$row['offre_id'];
        }

        echo json_encode([
            'success' => true,
            'likes' => $likes,
            'action' => $liked ? 'added' : 'removed'
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