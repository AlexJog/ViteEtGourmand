<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erreur' => 'non_connecte']);
    exit;
}

if ($_SESSION['user_role'] !== 'admin') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erreur' => 'Méthode non autorisée.']);
    exit;
}

$menu_id = (int)($_POST['menu_id'] ?? 0);

if (empty($menu_id)) {
    echo json_encode(['erreur' => 'ID manquant.']);
    exit;
}

// Récupérer les infos du menu
$stmt = $pdo->prepare("SELECT * FROM menu WHERE menu_id = :menu_id");
$stmt->execute(['menu_id' => $menu_id]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
    echo json_encode(['erreur' => "Ce menu n'existe pas."]);
    exit;
}

try {
    // Supprimer l'image si elle existe
    if (!empty($menu['image_url']) && file_exists('../../' . $menu['image_url'])) {
        unlink('../../' . $menu['image_url']);
    }

    // Supprimer le menu de la BDD
    $stmt_delete = $pdo->prepare("DELETE FROM menu WHERE menu_id = :menu_id");
    $stmt_delete->execute(['menu_id' => $menu_id]);

    echo json_encode([
        'succes' => "Le menu \"{$menu['nom']}\" a été supprimé avec succès."
    ]);

} catch (PDOException $e) {
    echo json_encode(['erreur' => "Erreur lors de la suppression du menu."]);
}
?>