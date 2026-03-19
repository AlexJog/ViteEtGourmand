<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Vérifier que c'est bien un POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erreurs' => ['Méthode non autorisée.']]);
    exit;
}

// Récupération des données
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$erreurs = [];

// VALIDATION
if (empty($email) || empty($password)) {
    $erreurs[] = "Tous les champs sont obligatoires.";
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}

if (!empty($erreurs)) {
    echo json_encode([
        'erreurs'    => $erreurs,
        'form_email' => $email
    ]);
    exit;
}

// VÉRIFIER L'UTILISATEUR EN BDD
try {
    $sql = "SELECT u.*, r.libelle as role_nom 
            FROM utilisateur u
            INNER JOIN role r ON u.role_id = r.role_id
            WHERE u.email = :email";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        // COMPTE DÉSACTIVÉ
        if (isset($user['actif']) && $user['actif'] == 0) {
            echo json_encode([
                'erreurs'    => ["Votre compte a été désactivé. Veuillez contacter l'administrateur."],
                'form_email' => $email
            ]);
            exit;
        }

        // CONNEXION RÉUSSIE — créer la session
        $_SESSION['user_id']      = $user['utilisateur_id'];
        $_SESSION['user_email']   = $user['email'];
        $_SESSION['user_nom']     = $user['nom'];
        $_SESSION['user_prenom']  = $user['prenom'];
        $_SESSION['user_role']    = $user['role_nom'];
        $_SESSION['user_adresse'] = $user['adresse_postale'];
        $_SESSION['first_login']  = true;

        // Déterminer la redirection selon le rôle
        $redirect = 'index.html';
        if ($user['role_nom'] === 'admin') {
            $redirect = 'admin/dashboard.html';
        } elseif ($user['role_nom'] === 'employe') {
            $redirect = 'employe/dashboard.html';
        }

        echo json_encode([
            'succes'   => true,
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
    echo json_encode([
        'erreurs' => ["Une erreur est survenue. Veuillez réessayer."]
    ]);
    exit;
}
?>