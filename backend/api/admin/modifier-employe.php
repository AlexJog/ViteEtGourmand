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

if ($user_connecte['role_nom'] !== 'admin') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

$employe_id = (int)($_POST['employe_id'] ?? 0);
$action     = $_POST['action'] ?? '';

$stmt_check = $pdo->prepare("SELECT u.*, r.libelle
                              FROM utilisateur u
                              INNER JOIN role r ON u.role_id = r.role_id
                              WHERE u.utilisateur_id = :employe_id
                              AND r.libelle = 'employe'");
$stmt_check->execute(['employe_id' => $employe_id]);
$employe = $stmt_check->fetch(PDO::FETCH_ASSOC);

if (!$employe) {
    echo json_encode(['erreur' => "Cet employé n'existe pas."]);
    exit;
}

try {
    if ($action === 'desactiver') {
        $stmt   = $pdo->prepare("UPDATE utilisateur SET actif = 0 WHERE utilisateur_id = :employe_id");
        $message = "Le compte de {$employe['prenom']} {$employe['nom']} a été désactivé.";
    } elseif ($action === 'activer') {
        $stmt   = $pdo->prepare("UPDATE utilisateur SET actif = 1 WHERE utilisateur_id = :employe_id");
        $message = "Le compte de {$employe['prenom']} {$employe['nom']} a été réactivé.";
    } else {
        echo json_encode(['erreur' => 'Action invalide.']);
        exit;
    }

    $stmt->execute(['employe_id' => $employe_id]);

    echo json_encode(['succes' => $message]);

} catch (PDOException $e) {
    echo json_encode(['erreur' => "Une erreur est survenue."]);
}
?>