<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['connecte' => false]);
    exit;
}

echo json_encode([
    'connecte' => true,
    'prenom'   => $_SESSION['user_prenom'],
    'role'     => $_SESSION['user_role']
]);
?>