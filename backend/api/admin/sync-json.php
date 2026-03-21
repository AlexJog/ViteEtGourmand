<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/json-config.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

// Vérifier le token
$user_connecte = verifierToken($pdo);

if ($user_connecte['role_nom'] !== 'admin') {
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