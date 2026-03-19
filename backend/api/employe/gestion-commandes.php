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

// Récupérer les commandes
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

// Compter les commandes par statut
$stats = [];
$stmt_stats = $pdo->query("SELECT statut, COUNT(*) as nb FROM commande GROUP BY statut");
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
    'commandes'     => $commandes,
    'stats'         => $stats,
    'filtre_statut' => $filtre_statut,
    'messages'      => $messages
]);
?>