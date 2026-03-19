<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erreur' => 'non_connecte']);
    exit;
}

if ($_SESSION['user_role'] !== 'employe' && $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

$nb_attente = $pdo->query("SELECT COUNT(*) as nb FROM commande WHERE statut = 'en attente'")->fetch()['nb'];

echo json_encode([
    'prenom'     => $_SESSION['user_prenom'],
    'nb_attente' => $nb_attente
]);
?>