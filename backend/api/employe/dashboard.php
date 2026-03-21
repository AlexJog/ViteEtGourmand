<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

// Vérifier le token
$user_connecte = verifierToken($pdo);

if ($user_connecte['role_nom'] !== 'employe' && $user_connecte['role_nom'] !== 'admin') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

$nb_attente = $pdo->query("SELECT COUNT(*) as nb FROM commande WHERE statut = 'en attente'")->fetch()['nb'];

echo json_encode([
    'prenom'     => $user_connecte['prenom'],
    'nb_attente' => $nb_attente
]);
?>