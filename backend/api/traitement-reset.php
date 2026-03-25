<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/email-functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erreurs' => ['Méthode non autorisée.']]);
    exit;
}

$email   = trim($_POST['email'] ?? '');
$erreurs = [];

if (empty($email)) {
    $erreurs[] = "L'adresse email est obligatoire.";
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}

if (!empty($erreurs)) {
    echo json_encode(['erreurs' => $erreurs]);
    exit;
}

$stmt = $pdo->prepare("SELECT utilisateur_id, prenom, nom FROM utilisateur WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $token  = bin2hex(random_bytes(32));
    $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $stmt_update = $pdo->prepare("UPDATE utilisateur SET reset_token = :token, reset_token_expire = :expire WHERE utilisateur_id = :id");
    $stmt_update->execute([
        'token'  => $token,
        'expire' => $expire,
        'id'     => $user['utilisateur_id']
    ]);

    // Lien vers le frontend Netlify
    $lien = "https://vite-et-gourmand-alex-a85135b73360.herokuapp.com/pages/nouveau-mot-de-passe.html?token=$token";

    $message = "Bonjour {$user['prenom']} {$user['nom']},

Vous avez demandé à réinitialiser votre mot de passe sur Vite & Gourmand.

Cliquez sur le lien ci-dessous pour définir un nouveau mot de passe :
→ $lien

⚠️ Ce lien est valable pendant 1 heure seulement.

Si vous n'avez pas demandé cette réinitialisation, ignorez simplement cet email.

L'équipe Vite & Gourmand
05 56 00 00 00
contact@vitegourmand.fr";

    envoyerEmail(
        $email,
        $user['prenom'] . ' ' . $user['nom'],
        'Réinitialisation de votre mot de passe - Vite & Gourmand',
        $message
    );
}

echo json_encode([
    'succes' => "Si cette adresse email existe dans notre base, vous allez recevoir un lien de réinitialisation."
]);
?>