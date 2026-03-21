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

// Récupérer les commandes
$stmt = $pdo->prepare("SELECT c.*, m.nom AS menu_nom, m.prix_par_personne
                        FROM commande c
                        INNER JOIN menu m ON c.menu_id = m.menu_id
                        WHERE c.utilisateur_id = :utilisateur_id
                        ORDER BY c.date_commande DESC");
$stmt->execute(['utilisateur_id' => $utilisateur_id]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pour chaque commande terminée, vérifier si un avis existe
foreach ($commandes as &$commande) {
    if ($commande['statut'] === 'terminée') {
        $stmt_avis = $pdo->prepare("SELECT avis_id FROM avis WHERE commande_id = :commande_id AND utilisateur_id = :utilisateur_id");
        $stmt_avis->execute([
            'commande_id'    => $commande['commande_id'],
            'utilisateur_id' => $utilisateur_id
        ]);
        $commande['avis_existe'] = $stmt_avis->fetch() ? true : false;
    }
}

echo json_encode([
    'commandes' => $commandes,
    'messages'  => [] // Plus de sessions, les messages passent par l'URL
]);
?>