<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/json-config.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

// Vérifier le token
$user_connecte = verifierToken($pdo);

if ($user_connecte['role_nom'] !== 'admin') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

$data_json = lireStatsJSON();
$nb_total  = count($data_json);

$menu_filtre = isset($_GET['menu_id'])    && !empty($_GET['menu_id'])    ? (int)$_GET['menu_id']    : null;
$date_debut  = isset($_GET['date_debut']) && !empty($_GET['date_debut']) ? $_GET['date_debut']       : null;
$date_fin    = isset($_GET['date_fin'])   && !empty($_GET['date_fin'])   ? $_GET['date_fin']         : null;

$filtres = [];
if ($menu_filtre) $filtres['menu_id']    = $menu_filtre;
if ($date_debut)  $filtres['date_debut'] = $date_debut;
if ($date_fin)    $filtres['date_fin']   = $date_fin;

$stats_menus = calculerStatsParMenu($filtres);

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