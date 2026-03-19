<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$token = trim($_GET['token'] ?? '');

if (empty($token)) {
    echo json_encode(['erreur' => 'Lien invalide.']);
    exit;
}

$stmt = $pdo->prepare("SELECT utilisateur_id, prenom, reset_token_expire 
                        FROM utilisateur 
                        WHERE reset_token = :token");
$stmt->execute(['token' => $token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['erreur' => 'Ce lien de réinitialisation n\'est pas valide.']);
    exit;
}

if (strtotime($user['reset_token_expire']) < time()) {
    echo json_encode(['erreur' => 'expired']);
    exit;
}

echo json_encode(['prenom' => $user['prenom']]);
?>