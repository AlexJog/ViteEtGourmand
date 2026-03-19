<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erreurs' => ['Méthode non autorisée.']]);
    exit;
}

$token            = trim($_POST['token']            ?? '');
$password         = $_POST['password']              ?? '';
$password_confirm = $_POST['password_confirm']      ?? '';

$erreurs = [];

if (empty($token) || empty($password)) {
    $erreurs[] = "Tous les champs sont obligatoires.";
}

if ($password !== $password_confirm) {
    $erreurs[] = "Les mots de passe ne correspondent pas.";
}

// Validation détaillée du mot de passe
if (strlen($password) < 10) {
    $erreurs[] = "Le mot de passe doit contenir au minimum 10 caractères.";
}
if (!preg_match('/[A-Z]/', $password)) {
    $erreurs[] = "Le mot de passe doit contenir au moins une majuscule.";
}
if (!preg_match('/[a-z]/', $password)) {
    $erreurs[] = "Le mot de passe doit contenir au moins une minuscule.";
}
if (!preg_match('/[0-9]/', $password)) {
    $erreurs[] = "Le mot de passe doit contenir au moins un chiffre.";
}
if (!preg_match('/[@#$%&*!?.,;:_\-]/', $password)) {
    $erreurs[] = "Le mot de passe doit contenir au moins un caractère spécial (@#$%&*!?.,;:_-).";
}

// Vérifier le token
$stmt = $pdo->prepare("SELECT utilisateur_id, reset_token_expire FROM utilisateur WHERE reset_token = :token");
$stmt->execute(['token' => $token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $erreurs[] = "Ce lien de réinitialisation n'est pas valide.";
}

if ($user && strtotime($user['reset_token_expire']) < time()) {
    $erreurs[] = "Ce lien de réinitialisation a expiré.";
}

if (!empty($erreurs)) {
    echo json_encode(['erreurs' => $erreurs]);
    exit;
}

// Mettre à jour le mot de passe
try {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt_update = $pdo->prepare("UPDATE utilisateur 
                                   SET password = :password, reset_token = NULL, reset_token_expire = NULL 
                                   WHERE utilisateur_id = :id");
    $stmt_update->execute([
        'password' => $password_hash,
        'id'       => $user['utilisateur_id']
    ]);

    echo json_encode([
        'succes'   => "Votre mot de passe a été réinitialisé avec succès ! Vous pouvez maintenant vous connecter.",
        'redirect' => 'connexion.html'
    ]);

} catch (PDOException $e) {
    echo json_encode(['erreurs' => ["Une erreur est survenue. Veuillez réessayer."]]);
}
?>