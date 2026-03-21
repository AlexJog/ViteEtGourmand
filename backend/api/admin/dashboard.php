<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

// Vérifier le token
$user_connecte = verifierToken($pdo);

if ($user_connecte['role_nom'] !== 'admin') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

$nb_menus        = $pdo->query("SELECT COUNT(*) as nb FROM menu")->fetch()['nb'];
$nb_commandes    = $pdo->query("SELECT COUNT(*) as nb FROM commande")->fetch()['nb'];
$nb_users        = $pdo->query("SELECT COUNT(*) as nb FROM utilisateur WHERE role_id = 3")->fetch()['nb'];
$nb_avis_attente = $pdo->query("SELECT COUNT(*) as nb FROM avis WHERE statut = 'en attente'")->fetch()['nb'];
$nb_employes     = $pdo->query("SELECT COUNT(*) as nb FROM utilisateur WHERE role_id = 2")->fetch()['nb'];

echo json_encode([
    'prenom'          => $user_connecte['prenom'],
    'nb_menus'        => $nb_menus,
    'nb_commandes'    => $nb_commandes,
    'nb_users'        => $nb_users,
    'nb_avis_attente' => $nb_avis_attente,
    'nb_employes'     => $nb_employes
]);
?>