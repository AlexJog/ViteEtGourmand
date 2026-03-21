<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/mailer.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erreurs' => ['Méthode non autorisée.']]);
    exit;
}

$nom              = trim($_POST['nom']              ?? '');
$prenom           = trim($_POST['prenom']           ?? '');
$email            = trim($_POST['email']            ?? '');
$telephone        = trim($_POST['telephone']        ?? '');
$adresse          = trim($_POST['adresse']          ?? '');
$code_postal      = trim($_POST['code_postal']      ?? '');
$ville            = trim($_POST['ville']            ?? '');
$password         = $_POST['password']              ?? '';
$password_confirm = $_POST['password_confirm']      ?? '';

$erreurs = [];

// VALIDATION
if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) ||
    empty($adresse) || empty($code_postal) || empty($ville) || empty($password)) {
    $erreurs[] = "Tous les champs sont obligatoires.";
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}

if ($password !== $password_confirm) {
    $erreurs[] = "Les mots de passe ne correspondent pas.";
}

if (!empty($password)) {
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
}

// Vérifier que l'email n'existe pas déjà
if (empty($erreurs)) {
    $stmt = $pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        $erreurs[] = "Cette adresse email est déjà utilisée.";
    }
}

if (!empty($erreurs)) {
    echo json_encode([
        'erreurs'   => $erreurs,
        'form_data' => compact('nom', 'prenom', 'email', 'telephone', 'adresse', 'code_postal', 'ville')
    ]);
    exit;
}

// INSÉRER L'UTILISATEUR
try {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Récupérer le role_id dynamiquement
    $stmt_role = $pdo->query("SELECT role_id FROM role WHERE libelle = 'utilisateur'");
    $role      = $stmt_role->fetch();
    $role_id   = $role['role_id'];

    $stmt = $pdo->prepare("INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, code_postal, ville, pays, role_id)
                            VALUES (:email, :password, :nom, :prenom, :telephone, :adresse, :code_postal, :ville, 'France', :role_id)");
    $stmt->execute([
        'email'       => $email,
        'password'    => $password_hash,
        'nom'         => $nom,
        'prenom'      => $prenom,
        'telephone'   => $telephone,
        'adresse'     => $adresse,
        'code_postal' => $code_postal,
        'ville'       => $ville,
        'role_id'     => $role_id
    ]);

    // Email de bienvenue
    $sujet   = "Bienvenue chez Vite & Gourmand !";
    $message = "Bonjour $prenom $nom,

Nous sommes ravis de vous accueillir chez Vite & Gourmand !

Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter et découvrir nos délicieux menus pour vos événements.

Informations de votre compte :
- Email : $email
- Nom : $prenom $nom
- Téléphone : $telephone

N'hésitez pas à nous contacter si vous avez la moindre question.

À très bientôt,
L'équipe Vite & Gourmand";

    sendWelcomeMail($email, $sujet, $message);

    echo json_encode([
        'succes'   => "Votre compte a été créé avec succès ! Un email de bienvenue vous a été envoyé.",
        'redirect' => 'connexion.html'
    ]);

} catch (PDOException $e) {
    echo json_encode(['erreurs' => ["Une erreur est survenue lors de la création de votre compte. Veuillez réessayer."]]);
}
?>