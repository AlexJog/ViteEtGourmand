<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/json-config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erreur' => 'non_connecte']);
    exit;
}

if ($_SESSION['user_role'] !== 'admin') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

$resultat = synchroniserStatsJSON($pdo);

if ($resultat !== false) {
    $data         = lireStatsJSON();
    $nb_commandes = count($data);
    echo json_encode([
        'succes' => "✅ Synchronisation réussie : $nb_commandes commandes synchronisées dans la base JSON (NoSQL)."
    ]);
} else {
    echo json_encode([
        'erreur' => "❌ Erreur lors de la synchronisation."
    ]);
}
?>