<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erreur' => 'non_connecte']);
    exit;
}

if ($_SESSION['user_role'] !== 'admin') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

$stmt = $pdo->query("SELECT u.*, r.libelle AS role_nom
                     FROM utilisateur u
                     INNER JOIN role r ON u.role_id = r.role_id
                     WHERE r.libelle = 'employe'
                     ORDER BY u.nom ASC");
$employes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$messages = [];
if (isset($_SESSION['succes_admin'])) {
    $messages['succes'] = $_SESSION['succes_admin'];
    unset($_SESSION['succes_admin']);
}
if (isset($_SESSION['error_admin'])) {
    $messages['erreur'] = $_SESSION['error_admin'];
    unset($_SESSION['error_admin']);
}

echo json_encode([
    'employes' => $employes,
    'messages' => $messages
]);
?>