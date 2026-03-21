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

$sql = "SELECT a.*, u.prenom, u.nom, c.commande_id, m.nom AS menu_nom
        FROM avis a
        INNER JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
        INNER JOIN commande c ON a.commande_id = c.commande_id
        INNER JOIN menu m ON c.menu_id = m.menu_id";

if ($filtre_statut !== 'tous') {
    $sql .= " WHERE a.statut = :statut";
}

$sql .= " ORDER BY a.date_avis DESC";

$stmt = $pdo->prepare($sql);

if ($filtre_statut !== 'tous') {
    $stmt->execute(['statut' => $filtre_statut]);
} else {
    $stmt->execute();
}

$avis_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stats = [];
$stmt_stats = $pdo->query("SELECT statut, COUNT(*) as nb FROM avis GROUP BY statut");
while ($row = $stmt_stats->fetch()) {
    $stats[$row['statut']] = $row['nb'];
}

echo json_encode([
    'avis'          => $avis_list,
    'stats'         => $stats,
    'filtre_statut' => $filtre_statut,
    'messages'      => [] // Plus de sessions
]);
?>