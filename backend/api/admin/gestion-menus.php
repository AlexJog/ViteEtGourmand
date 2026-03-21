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

$stmt = $pdo->query("SELECT m.*, r.libelle AS regime_nom, t.libelle AS theme_nom
                     FROM menu m
                     LEFT JOIN regime r ON m.regime_id = r.regime_id
                     LEFT JOIN theme t ON m.theme_id = t.theme_id
                     ORDER BY m.menu_id DESC");
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'menus'    => $menus,
    'messages' => [] // Plus de sessions
]);
?>