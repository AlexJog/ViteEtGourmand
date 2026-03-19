<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Retourner les messages de session s'ils existent
$messages = [];

if (isset($_SESSION['succes_connexion'])) {
    $messages['succes'] = $_SESSION['succes_connexion'];
    unset($_SESSION['succes_connexion']);
}

if (isset($_SESSION['succes_inscription'])) {
    $messages['succes'] = $_SESSION['succes_inscription'];
    unset($_SESSION['succes_inscription']);
}

if (isset($_SESSION['erreurs_connexion'])) {
    $messages['erreurs'] = $_SESSION['erreurs_connexion'];
    unset($_SESSION['erreurs_connexion']);
}

if (isset($_SESSION['form_email'])) {
    $messages['form_email'] = $_SESSION['form_email'];
    unset($_SESSION['form_email']);
}

echo json_encode($messages);