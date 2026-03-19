<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$messages = [];

if (isset($_SESSION['succes_commande'])) {
    $messages['succes'] = $_SESSION['succes_commande'];
    unset($_SESSION['succes_commande']);
}

if (isset($_SESSION['error'])) {
    $messages['erreur'] = $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_SESSION['first_login']) && $_SESSION['first_login'] === true) {
    $messages['bienvenue'] = $_SESSION['user_prenom'];
    unset($_SESSION['first_login']);
}

echo json_encode($messages);
?>