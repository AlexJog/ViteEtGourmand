<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erreur' => 'non_connecte']);
    exit;
}

if ($_SESSION['user_role'] !== 'employe' && $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erreur' => 'Méthode non autorisée.']);
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