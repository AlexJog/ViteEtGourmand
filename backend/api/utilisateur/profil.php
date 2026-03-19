<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erreur' => 'non_connecte']);
    exit;
}

if ($_SESSION['user_role'] !== 'utilisateur') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE utilisateur_id = :utilisateur_id");
$stmt->execute(['utilisateur_id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['erreur' => 'non_trouve']);
    exit;
}

// Ne jamais retourner le mot de passe
unset($user['password']);
unset($user['reset_token']);
unset($user['reset_token_expire']);

echo json_encode(['user' => $user]);
?>