<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

// Récupérer le token depuis le header
$headers = getallheaders();
$token   = $headers['X-AUTH-TOKEN'] ?? null;

// Effacer le token en BDD
if ($token) {
    $stmt = $pdo->prepare("UPDATE utilisateur SET api_token = NULL WHERE api_token = :token");
    $stmt->execute(['token' => $token]);
}

echo json_encode(['succes' => true]);
?>