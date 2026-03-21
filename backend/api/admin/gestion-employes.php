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

$stmt = $pdo->query("SELECT u.*, r.libelle AS role_nom
                     FROM utilisateur u
                     INNER JOIN role r ON u.role_id = r.role_id
                     WHERE r.libelle = 'employe'
                     ORDER BY u.nom ASC");
$employes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'employes' => $employes,
    'messages' => [] // Plus de sessions
]);
?>