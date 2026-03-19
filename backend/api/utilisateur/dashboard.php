<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erreur' => 'non_connecte']);
    exit;
}

// Vérifier le rôle
if ($_SESSION['user_role'] !== 'utilisateur') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

// Compter les commandes
$stmt = $pdo->prepare("SELECT COUNT(*) as nb_commandes FROM commande WHERE utilisateur_id = :utilisateur_id");
$stmt->execute(['utilisateur_id' => $_SESSION['user_id']]);
$nb_commandes = $stmt->fetch()['nb_commandes'];

echo json_encode([
    'prenom'       => $_SESSION['user_prenom'],
    'nb_commandes' => $nb_commandes
]);
?>