<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

// Vérifier que l'ID est passé
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['erreur' => 'ID manquant.']);
    exit;
}

$menu_id = (int)$_GET['id'];

// RÉCUPÉRER LES INFOS DU MENU
$sql = "SELECT m.*, r.libelle AS regime_nom, t.libelle AS theme_nom 
        FROM menu m
        LEFT JOIN regime r ON m.regime_id = r.regime_id
        LEFT JOIN theme t ON m.theme_id = t.theme_id
        WHERE m.menu_id = :menu_id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['menu_id' => $menu_id]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
    echo json_encode(['erreur' => 'Menu introuvable.']);
    exit;
}

// RÉCUPÉRER LES PLATS
$sql_plats = "SELECT p.* 
              FROM plat p
              INNER JOIN menu_plat mp ON p.plat_id = mp.plat_id
              WHERE mp.menu_id = :menu_id
              ORDER BY p.plat_id";

$stmt_plats = $pdo->prepare($sql_plats);
$stmt_plats->execute(['menu_id' => $menu_id]);
$plats = $stmt_plats->fetchAll(PDO::FETCH_ASSOC);

// RÉCUPÉRER LES ALLERGÈNES
$sql_allergenes = "SELECT DISTINCT a.libelle
                   FROM allergene a
                   INNER JOIN plat_allergene pa ON a.allergene_id = pa.allergene_id
                   INNER JOIN menu_plat mp ON pa.plat_id = mp.plat_id
                   WHERE mp.menu_id = :menu_id
                   ORDER BY a.libelle";

$stmt_allergenes = $pdo->prepare($sql_allergenes);
$stmt_allergenes->execute(['menu_id' => $menu_id]);
$allergenes = $stmt_allergenes->fetchAll(PDO::FETCH_ASSOC);

// TOUT RETOURNER EN UNE FOIS
echo json_encode([
    'menu'      => $menu,
    'plats'     => $plats,
    'allergenes' => $allergenes
]);
?>