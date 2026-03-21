<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erreurs' => ['Méthode non autorisée.']]);
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password']   ?? '';

$erreurs = [];

if (empty($email) || empty($password)) {
    $erreurs[] = "Tous les champs sont obligatoires.";
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}

if (!empty($erreurs)) {
    echo json_encode(['erreurs' => $erreurs, 'form_email' => $email]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT u.*, r.libelle as role_nom 
                            FROM utilisateur u
                            INNER JOIN role r ON u.role_id = r.role_id
                            WHERE u.email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        // COMPTE DÉSACTIVÉ — vérifier en premier
        if (isset($user['actif']) && $user['actif'] == 0) {
            echo json_encode([
                'erreurs'    => ["Votre compte a été désactivé. Veuillez contacter l'administrateur."],
                'form_email' => $email
            ]);
            exit;
        }

        // GÉNÉRER LE TOKEN
        $token = bin2hex(random_bytes(32));

        $stmt_token = $pdo->prepare("UPDATE utilisateur SET api_token = :token WHERE utilisateur_id = :id");
        $stmt_token->execute(['token' => $token, 'id' => $user['utilisateur_id']]);

        // DÉTERMINER LA REDIRECTION
        $redirect = 'index.html';
        if ($user['role_nom'] === 'admin') {
            $redirect = 'admin/dashboard.html';
        } elseif ($user['role_nom'] === 'employe') {
            $redirect = 'employe/dashboard.html';
        }

        // RETOURNER LE TOKEN AU FRONTEND
        echo json_encode([
            'succes'   => true,
            'token'    => $token,
            'role'     => $user['role_nom'],
            'prenom'   => $user['prenom'],
            'redirect' => $redirect
        ]);
        exit;

    } else {
        echo json_encode([
            'erreurs'    => ["Email ou mot de passe incorrect."],
            'form_email' => $email
        ]);
        exit;
    }

} catch (PDOException $e) {
    echo json_encode(['erreurs' => ["Une erreur est survenue. Veuillez réessayer."]]);
    exit;
}
?>