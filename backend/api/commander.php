<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erreur' => 'non_connecte']);
    exit;
}

// Vérifier que l'ID du menu est passé
if (!isset($_GET['menu']) || empty($_GET['menu'])) {
    echo json_encode(['erreur' => 'ID manquant.']);
    exit;
}

$menu_id = (int)$_GET['menu'];

// Récupérer les infos du menu
$sql = "SELECT m.*, r.libelle AS regime_nom, t.libelle AS theme_nom 
        FROM menu m
        LEFT JOIN regime r ON m.regime_id = r.regime_id
        LEFT JOIN theme t ON m.theme_id = t.theme_id
        WHERE m.menu_id = :menu_id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['menu_id' => $menu_id]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

// Menu introuvable
if (!$menu) {
    echo json_encode(['erreur' => 'Menu introuvable.']);
    exit;
}

// Stock épuisé
if ($menu['quantite_restante'] <= 0) {
    echo json_encode(['erreur' => 'stock_epuise', 'menu_id' => $menu_id]);
    exit;
}

// Retourner le menu + l'adresse de l'utilisateur connecté
echo json_encode([
    'menu'          => $menu,
    'user_adresse'  => $_SESSION['user_adresse'] ?? ''
]);
?>