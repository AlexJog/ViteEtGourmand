<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/json-config.php';

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

// Compter le nombre total dans le JSON
$data_json = lireStatsJSON();
$nb_total  = count($data_json);

// Récupérer les filtres
$menu_filtre = isset($_GET['menu_id'])    && !empty($_GET['menu_id'])    ? (int)$_GET['menu_id']    : null;
$date_debut  = isset($_GET['date_debut']) && !empty($_GET['date_debut']) ? $_GET['date_debut']       : null;
$date_fin    = isset($_GET['date_fin'])   && !empty($_GET['date_fin'])   ? $_GET['date_fin']         : null;

$filtres = [];
if ($menu_filtre) $filtres['menu_id']    = $menu_filtre;
if ($date_debut)  $filtres['date_debut'] = $date_debut;
if ($date_fin)    $filtres['date_fin']   = $date_fin;

// Calculer les stats
$stats_menus = calculerStatsParMenu($filtres);

// Récupérer la liste des menus pour le filtre
$menus = $pdo->query("SELECT menu_id, nom FROM menu ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'nb_total'    => $nb_total,
    'stats_menus' => $stats_menus,
    'menus'       => $menus,
    'filtres'     => [
        'menu_id'    => $menu_filtre,
        'date_debut' => $date_debut,
        'date_fin'   => $date_fin
    ]
]);
?>