<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

// Vérifier le token
$user_connecte = verifierToken($pdo);

if ($user_connecte['role_nom'] !== 'admin') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

$regimes = $pdo->query("SELECT * FROM regime ORDER BY libelle")->fetchAll(PDO::FETCH_ASSOC);
$themes  = $pdo->query("SELECT * FROM theme ORDER BY libelle")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'regimes' => $regimes,
    'themes'  => $themes
]);
?>