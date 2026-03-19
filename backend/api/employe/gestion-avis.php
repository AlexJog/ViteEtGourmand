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

$filtre_statut = isset($_GET['statut']) ? $_GET['statut'] : 'tous';

// Récupérer les avis
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

// Stats par statut
$stats = [];
$stmt_stats = $pdo->query("SELECT statut, COUNT(*) as nb FROM avis GROUP BY statut");
while ($row = $stmt_stats->fetch()) {
    $stats[$row['statut']] = $row['nb'];
}

// Messages de session
$messages = [];
if (isset($_SESSION['succes_employe'])) {
    $messages['succes'] = $_SESSION['succes_employe'];
    unset($_SESSION['succes_employe']);
}
if (isset($_SESSION['error_employe'])) {
    $messages['erreur'] = $_SESSION['error_employe'];
    unset($_SESSION['error_employe']);
}

echo json_encode([
    'avis'          => $avis_list,
    'stats'         => $stats,
    'filtre_statut' => $filtre_statut,
    'messages'      => $messages
]);
?>