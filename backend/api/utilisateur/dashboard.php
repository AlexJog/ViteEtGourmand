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

$stmt = $pdo->prepare("SELECT COUNT(*) as nb_commandes FROM commande WHERE utilisateur_id = :utilisateur_id");
$stmt->execute(['utilisateur_id' => $user_connecte['utilisateur_id']]);
$nb_commandes = $stmt->fetch()['nb_commandes'];

echo json_encode([
    'prenom'       => $user_connecte['prenom'],
    'nb_commandes' => $nb_commandes
]);
?>