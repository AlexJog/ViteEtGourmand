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

$stmt = $pdo->query("SELECT m.*, r.libelle AS regime_nom, t.libelle AS theme_nom
                     FROM menu m
                     LEFT JOIN regime r ON m.regime_id = r.regime_id
                     LEFT JOIN theme t ON m.theme_id = t.theme_id
                     ORDER BY m.menu_id DESC");
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    'menus'    => $menus,
    'messages' => $messages
]);
?>