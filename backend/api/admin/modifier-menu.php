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

$menu_id = (int)($_GET['id'] ?? 0);

if (empty($menu_id)) {
    echo json_encode(['erreur' => 'redirect']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM menu WHERE menu_id = :menu_id");
$stmt->execute(['menu_id' => $menu_id]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
    echo json_encode(['erreur' => 'redirect']);
    exit;
}

$regimes = $pdo->query("SELECT * FROM regime ORDER BY libelle")->fetchAll(PDO::FETCH_ASSOC);
$themes  = $pdo->query("SELECT * FROM theme ORDER BY libelle")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'menu'    => $menu,
    'regimes' => $regimes,
    'themes'  => $themes
]);
?>