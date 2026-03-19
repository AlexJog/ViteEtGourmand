<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erreur' => 'non_connecte']);
    exit;
}

if ($_SESSION['user_role'] !== 'utilisateur') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

// Récupérer les commandes
$stmt = $pdo->prepare("SELECT c.*, m.nom AS menu_nom, m.prix_par_personne
                        FROM commande c
                        INNER JOIN menu m ON c.menu_id = m.menu_id
                        WHERE c.utilisateur_id = :utilisateur_id
                        ORDER BY c.date_commande DESC");
$stmt->execute(['utilisateur_id' => $_SESSION['user_id']]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pour chaque commande terminée, vérifier si un avis existe
foreach ($commandes as &$commande) {
    if ($commande['statut'] === 'terminée') {
        $stmt_avis = $pdo->prepare("SELECT avis_id FROM avis WHERE commande_id = :commande_id AND utilisateur_id = :utilisateur_id");
        $stmt_avis->execute([
            'commande_id'    => $commande['commande_id'],
            'utilisateur_id' => $_SESSION['user_id']
        ]);
        $commande['avis_existe'] = $stmt_avis->fetch() ? true : false;
    }
}

// Messages de session
$messages = [];
if (isset($_SESSION['succes_user'])) {
    $messages['succes'] = $_SESSION['succes_user'];
    unset($_SESSION['succes_user']);
}
if (isset($_SESSION['error_user'])) {
    $messages['erreur'] = $_SESSION['error_user'];
    unset($_SESSION['error_user']);
}

echo json_encode([
    'commandes' => $commandes,
    'messages'  => $messages
]);
?>