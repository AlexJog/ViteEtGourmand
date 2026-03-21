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

$filtre_statut = isset($_GET['statut']) ? $_GET['statut'] : 'tous';

$sql = "SELECT c.*, m.nom AS menu_nom, u.prenom, u.nom AS user_nom, u.email, u.telephone
        FROM commande c
        INNER JOIN menu m ON c.menu_id = m.menu_id
        INNER JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id";

if ($filtre_statut !== 'tous') {
    $sql .= " WHERE c.statut = :statut";
}

$sql .= " ORDER BY c.date_commande DESC";

$stmt = $pdo->prepare($sql);

if ($filtre_statut !== 'tous') {
    $stmt->execute(['statut' => $filtre_statut]);
} else {
    $stmt->execute();
}

$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stats = [];
$stmt_stats = $pdo->query("SELECT statut, COUNT(*) as nb FROM commande GROUP BY statut");
while ($row = $stmt_stats->fetch()) {
    $stats[$row['statut']] = $row['nb'];
}

echo json_encode([
    'commandes'     => $commandes,
    'stats'         => $stats,
    'filtre_statut' => $filtre_statut,
    'messages'      => [] // Plus de sessions
]);
?>