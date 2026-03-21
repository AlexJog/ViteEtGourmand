<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

// Vérifier le token
$user_connecte  = verifierToken($pdo);
$utilisateur_id = $user_connecte['utilisateur_id'];

if ($user_connecte['role_nom'] !== 'utilisateur') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

$commande_id = (int)($_GET['commande_id'] ?? 0);

if (empty($commande_id)) {
    echo json_encode(['erreur' => 'redirect_commandes']);
    exit;
}

$stmt = $pdo->prepare("SELECT c.*, m.nom AS menu_nom 
                        FROM commande c
                        INNER JOIN menu m ON c.menu_id = m.menu_id
                        WHERE c.commande_id = :commande_id 
                        AND c.utilisateur_id = :utilisateur_id 
                        AND c.statut = 'terminée'");
$stmt->execute([
    'commande_id'    => $commande_id,
    'utilisateur_id' => $utilisateur_id
]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    echo json_encode(['erreur' => 'redirect_commandes']);
    exit;
}

$stmt_check = $pdo->prepare("SELECT avis_id FROM avis WHERE commande_id = :commande_id AND utilisateur_id = :utilisateur_id");
$stmt_check->execute([
    'commande_id'    => $commande_id,
    'utilisateur_id' => $utilisateur_id
]);

if ($stmt_check->fetch()) {
    echo json_encode(['erreur' => 'avis_existe']);
    exit;
}

echo json_encode(['menu_nom' => $commande['menu_nom']]);
?>