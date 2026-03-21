<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erreur' => 'Méthode non autorisée.']);
    exit;
}

// Vérifier le token
$user_connecte = verifierToken($pdo);

if ($user_connecte['role_nom'] !== 'employe' && $user_connecte['role_nom'] !== 'admin') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

$avis_id = (int)($_POST['avis_id'] ?? 0);
$action  = $_POST['action'] ?? '';

if ($action === 'valider') {
    $nouveau_statut = 'validé';
} elseif ($action === 'refuser') {
    $nouveau_statut = 'refusé';
} else {
    echo json_encode(['erreur' => 'Action invalide.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE avis SET statut = :statut WHERE avis_id = :avis_id");
    $stmt->execute([
        'statut'  => $nouveau_statut,
        'avis_id' => $avis_id
    ]);

    echo json_encode([
        'succes' => "L'avis #$avis_id a été $nouveau_statut avec succès."
    ]);

} catch (PDOException $e) {
    echo json_encode(['erreur' => "Une erreur est survenue lors du traitement de l'avis."]);
}
?>