<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erreur' => 'non_connecte']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erreurs' => ['Méthode non autorisée.']]);
    exit;
}

$nom                      = trim($_POST['nom']                     ?? '');
$prenom                   = trim($_POST['prenom']                  ?? '');
$email                    = trim($_POST['email']                   ?? '');
$telephone                = trim($_POST['telephone']               ?? '');
$adresse_postale          = trim($_POST['adresse_postale']         ?? '');
$code_postal              = trim($_POST['code_postal']             ?? '');
$ville                    = trim($_POST['ville']                   ?? '');
$pays                     = trim($_POST['pays']                    ?? '');
$password_actuel          = $_POST['password_actuel']              ?? '';
$nouveau_password         = $_POST['nouveau_password']             ?? '';
$nouveau_password_confirm = $_POST['nouveau_password_confirm']     ?? '';
$utilisateur_id           = $_SESSION['user_id'];

$erreurs = [];

// Validation champs obligatoires
if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) ||
    empty($adresse_postale) || empty($code_postal) || empty($ville) || empty($pays)) {
    $erreurs[] = "Tous les champs sont obligatoires.";
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}

// Vérifier que l'email n'est pas déjà utilisé par un autre compte
if (empty($erreurs)) {
    $stmt_check = $pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE email = :email AND utilisateur_id != :utilisateur_id");
    $stmt_check->execute(['email' => $email, 'utilisateur_id' => $utilisateur_id]);
    if ($stmt_check->fetch()) {
        $erreurs[] = "Cette adresse email est déjà utilisée par un autre compte.";
    }
}

// Changement de mot de passe
$change_password = !empty($password_actuel) || !empty($nouveau_password) || !empty($nouveau_password_confirm);

if ($change_password) {
    if (empty($password_actuel)) {
        $erreurs[] = "Le mot de passe actuel est requis.";
    }
    if (empty($nouveau_password)) {
        $erreurs[] = "Le nouveau mot de passe est requis.";
    }
    if (empty($nouveau_password_confirm)) {
        $erreurs[] = "La confirmation du nouveau mot de passe est requise.";
    }
    if ($nouveau_password !== $nouveau_password_confirm) {
        $erreurs[] = "Les nouveaux mots de passe ne correspondent pas.";
    }

    if (!empty($nouveau_password)) {
        if (strlen($nouveau_password) < 10)                          $erreurs[] = "Minimum 10 caractères.";
        if (!preg_match('/[A-Z]/', $nouveau_password))               $erreurs[] = "Au moins une majuscule.";
        if (!preg_match('/[a-z]/', $nouveau_password))               $erreurs[] = "Au moins une minuscule.";
        if (!preg_match('/[0-9]/', $nouveau_password))               $erreurs[] = "Au moins un chiffre.";
        if (!preg_match('/[@#$%&*!?.,;:_\-]/', $nouveau_password))  $erreurs[] = "Au moins un caractère spécial.";
    }

    if (!empty($password_actuel)) {
        $stmt_pwd = $pdo->prepare("SELECT password FROM utilisateur WHERE utilisateur_id = :id");
        $stmt_pwd->execute(['id' => $utilisateur_id]);
        $user_pwd = $stmt_pwd->fetch();
        if (!password_verify($password_actuel, $user_pwd['password'])) {
            $erreurs[] = "Le mot de passe actuel est incorrect.";
        }
    }
}

if (!empty($erreurs)) {
    echo json_encode(['erreurs' => $erreurs]);
    exit;
}

// MISE À JOUR
try {
    if ($change_password) {
        $password_hash = password_hash($nouveau_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE utilisateur SET nom=:nom, prenom=:prenom, email=:email, telephone=:telephone,
                                adresse_postale=:adresse_postale, code_postal=:code_postal, ville=:ville,
                                pays=:pays, password=:password WHERE utilisateur_id=:id");
        $stmt->execute([
            'nom' => $nom, 'prenom' => $prenom, 'email' => $email,
            'telephone' => $telephone, 'adresse_postale' => $adresse_postale,
            'code_postal' => $code_postal, 'ville' => $ville, 'pays' => $pays,
            'password' => $password_hash, 'id' => $utilisateur_id
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE utilisateur SET nom=:nom, prenom=:prenom, email=:email, telephone=:telephone,
                                adresse_postale=:adresse_postale, code_postal=:code_postal, ville=:ville,
                                pays=:pays WHERE utilisateur_id=:id");
        $stmt->execute([
            'nom' => $nom, 'prenom' => $prenom, 'email' => $email,
            'telephone' => $telephone, 'adresse_postale' => $adresse_postale,
            'code_postal' => $code_postal, 'ville' => $ville, 'pays' => $pays,
            'id' => $utilisateur_id
        ]);
    }

    // Mettre à jour la session
    $_SESSION['user_prenom']  = $prenom;
    $_SESSION['user_email']   = $email;
    $_SESSION['user_adresse'] = $adresse_postale;

    $succes = $change_password
        ? "Vos informations et votre mot de passe ont été mis à jour avec succès."
        : "Vos informations ont été mises à jour avec succès.";

    echo json_encode(['succes' => $succes, 'redirect' => 'dashboard.html']);

} catch (PDOException $e) {
    echo json_encode(['erreurs' => ["Une erreur est survenue. Veuillez réessayer."]]);
}
?>