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

$nb_menus        = $pdo->query("SELECT COUNT(*) as nb FROM menu")->fetch()['nb'];
$nb_commandes    = $pdo->query("SELECT COUNT(*) as nb FROM commande")->fetch()['nb'];
$nb_users        = $pdo->query("SELECT COUNT(*) as nb FROM utilisateur WHERE role_id = 3")->fetch()['nb'];
$nb_avis_attente = $pdo->query("SELECT COUNT(*) as nb FROM avis WHERE statut = 'en attente'")->fetch()['nb'];
$nb_employes     = $pdo->query("SELECT COUNT(*) as nb FROM utilisateur WHERE role_id = 2")->fetch()['nb'];

echo json_encode([
    'prenom'          => $_SESSION['user_prenom'],
    'nb_menus'        => $nb_menus,
    'nb_commandes'    => $nb_commandes,
    'nb_users'        => $nb_users,
    'nb_avis_attente' => $nb_avis_attente,
    'nb_employes'     => $nb_employes
]);
?>