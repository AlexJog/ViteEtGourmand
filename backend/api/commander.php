<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// Vérifier le token
$user_connecte = verifierToken($pdo);

if (!isset($_GET['menu']) || empty($_GET['menu'])) {
    echo json_encode(['erreur' => 'ID manquant.']);
    exit;
}

$menu_id = (int)$_GET['menu'];

$stmt = $pdo->prepare("SELECT m.*, r.libelle AS regime_nom, t.libelle AS theme_nom 
                        FROM menu m
                        LEFT JOIN regime r ON m.regime_id = r.regime_id
                        LEFT JOIN theme t ON m.theme_id = t.theme_id
                        WHERE m.menu_id = :menu_id");
$stmt->execute(['menu_id' => $menu_id]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
    echo json_encode(['erreur' => 'Menu introuvable.']);
    exit;
}

if ($menu['quantite_restante'] <= 0) {
    echo json_encode(['erreur' => 'stock_epuise', 'menu_id' => $menu_id]);
    exit;
}

// Récupérer l'adresse depuis la BDD au lieu de la session
echo json_encode([
    'menu'         => $menu,
    'user_adresse' => $user_connecte['adresse_postale'] ?? ''
]);
?>