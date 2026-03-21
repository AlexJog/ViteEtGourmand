<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erreurs' => ['Méthode non autorisée.']]);
    exit;
}

// Vérifier le token
$user_connecte  = verifierToken($pdo);
$utilisateur_id = $user_connecte['utilisateur_id'];

if ($user_connecte['role_nom'] !== 'utilisateur') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

$commande_id = (int)($_POST['commande_id'] ?? 0);
$note        = (int)($_POST['note']        ?? 0);
$commentaire = trim($_POST['commentaire']  ?? '');

$erreurs = [];

if (empty($commande_id)) {
    $erreurs[] = "Commande invalide.";
}

if ($note < 1 || $note > 5) {
    $erreurs[] = "La note doit être entre 1 et 5.";
}

if (empty($commentaire)) {
    $erreurs[] = "Le commentaire est obligatoire.";
}

if (strlen($commentaire) < 10) {
    $erreurs[] = "Le commentaire doit contenir au minimum 10 caractères.";
}

if (!empty($erreurs)) {
    echo json_encode(['erreurs' => $erreurs]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO avis (commande_id, utilisateur_id, note, commentaire, statut, date_avis)
                            VALUES (:commande_id, :utilisateur_id, :note, :commentaire, 'en attente', NOW())");
    $stmt->execute([
        'commande_id'    => $commande_id,
        'utilisateur_id' => $utilisateur_id,
        'note'           => $note,
        'commentaire'    => $commentaire
    ]);

    echo json_encode([
        'succes'   => "Votre avis a bien été envoyé ! Il sera publié après validation.",
        'redirect' => 'mes-commandes.html'
    ]);

} catch (PDOException $e) {
    echo json_encode(['erreurs' => ["Une erreur est survenue. Veuillez réessayer."]]);
}
?>