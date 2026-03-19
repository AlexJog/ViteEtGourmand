<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erreur' => 'non_connecte']);
    exit;
}

if ($_SESSION['user_role'] !== 'admin') {
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