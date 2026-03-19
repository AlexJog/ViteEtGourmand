<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/email-functions.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erreurs' => ['Méthode non autorisée.']]);
    exit;
}

$email   = trim($_POST['email']   ?? '');
$titre   = trim($_POST['titre']   ?? '');
$message = trim($_POST['message'] ?? '');

$erreurs = [];

if (empty($email) || empty($titre) || empty($message)) {
    $erreurs[] = "Tous les champs sont obligatoires.";
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}

if (!empty($titre) && strlen($titre) > 255) {
    $erreurs[] = "Le titre ne peut pas dépasser 255 caractères.";
}

if (!empty($message) && strlen($message) < 10) {
    $erreurs[] = "Le message doit contenir au moins 10 caractères.";
}

if (!empty($erreurs)) {
    echo json_encode([
        'erreurs'      => $erreurs,
        'form_contact' => compact('email', 'titre', 'message')
    ]);
    exit;
}

// ENREGISTRER EN BDD + ENVOYER EMAIL
try {
    $stmt = $pdo->prepare("INSERT INTO contact (email, titre, message) VALUES (:email, :titre, :message)");
    $stmt->execute([
        'email'   => $email,
        'titre'   => $titre,
        'message' => $message
    ]);

    envoyerEmail(
        'contact@vitegourmand.fr',
        'Vite & Gourmand',
        $titre,
        "Message de : $email\n\n$message"
    );

    echo json_encode([
        'succes' => "Votre message a bien été envoyé ! Nous vous répondrons dans les plus brefs délais."
    ]);

} catch (PDOException $e) {
    echo json_encode(['erreurs' => ["Une erreur est survenue. Veuillez réessayer."]]);
}
?>