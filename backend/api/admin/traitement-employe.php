<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erreurs' => ['Méthode non autorisée.']]);
    exit;
}

// Vérifier le token
$user_connecte = verifierToken($pdo);

if ($user_connecte['role_nom'] !== 'admin') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

$nom       = trim($_POST['nom']       ?? '');
$prenom    = trim($_POST['prenom']    ?? '');
$email     = trim($_POST['email']     ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$password  = $_POST['password']       ?? '';

$erreurs = [];

if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || empty($password)) {
    $erreurs[] = "Tous les champs sont obligatoires.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}

if (strlen($password) < 10)                         $erreurs[] = "Le mot de passe doit contenir au minimum 10 caractères.";
if (!preg_match('/[A-Z]/', $password))              $erreurs[] = "Le mot de passe doit contenir au moins une majuscule.";
if (!preg_match('/[a-z]/', $password))              $erreurs[] = "Le mot de passe doit contenir au moins une minuscule.";
if (!preg_match('/[0-9]/', $password))              $erreurs[] = "Le mot de passe doit contenir au moins un chiffre.";
if (!preg_match('/[@#$%&*!?.,;:_\-]/', $password)) $erreurs[] = "Le mot de passe doit contenir au moins un caractère spécial (@#$%&*!?.,;:_-).";

if (empty($erreurs)) {
    $stmt_check = $pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE email = :email");
    $stmt_check->execute(['email' => $email]);
    if ($stmt_check->fetch()) {
        $erreurs[] = "Cette adresse email est déjà utilisée.";
    }
}

if (!empty($erreurs)) {
    echo json_encode(['erreurs' => $erreurs]);
    exit;
}

try {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt_role = $pdo->query("SELECT role_id FROM role WHERE libelle = 'employe'");
    $role_id   = $stmt_role->fetch()['role_id'];

    $stmt = $pdo->prepare("INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, code_postal, ville, pays, role_id)
                            VALUES (:email, :password, :nom, :prenom, :telephone, '', '', '', 'France', :role_id)");
    $stmt->execute([
        'email'     => $email,
        'password'  => $password_hash,
        'nom'       => $nom,
        'prenom'    => $prenom,
        'telephone' => $telephone,
        'role_id'   => $role_id
    ]);

    // Email avec lien Netlify
    $sujet   = "Votre compte employé - Vite & Gourmand";
    $message = "Bonjour $prenom $nom,

Un compte employé a été créé pour vous sur la plateforme Vite & Gourmand.

Informations de connexion :
- Email : $email
- Mot de passe : Pour des raisons de sécurité, le mot de passe n'est pas communiqué par email. Veuillez contacter l'administrateur pour l'obtenir.

Vous pouvez vous connecter à l'adresse suivante :
→ https://vite-et-gourmand-alex.netlify.app/pages/connexion.html

Une fois connecté, vous aurez accès à votre espace employé pour gérer les commandes et les avis clients.

Bienvenue dans l'équipe !

L'équipe Vite & Gourmand";

    $headers  = "From: noreply@vitegourmand.fr\r\n";
    $headers .= "Reply-To: contact@vitegourmand.fr\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    @mail($email, $sujet, $message, $headers);

    echo json_encode([
        'succes' => "Le compte employé a été créé avec succès. Un email a été envoyé à $email."
    ]);

} catch (PDOException $e) {
    echo json_encode(['erreurs' => ["Une erreur est survenue lors de la création du compte."]]);
}
?>