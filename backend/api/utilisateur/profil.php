<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

// Vérifier le token
$user_connecte = verifierToken($pdo);

if ($user_connecte['role_nom'] !== 'utilisateur') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE utilisateur_id = :utilisateur_id");
$stmt->execute(['utilisateur_id' => $user_connecte['utilisateur_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['erreur' => 'non_trouve']);
    exit;
}

// Ne jamais retourner le mot de passe
unset($user['password']);
unset($user['reset_token']);
unset($user['reset_token_expire']);
unset($user['api_token']);

echo json_encode(['user' => $user]);
?>