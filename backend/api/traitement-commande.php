<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/email-functions.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erreurs' => ['Méthode non autorisée.']]);
    exit;
}

// Vérifier le token et récupérer l'utilisateur
$user_connecte  = verifierToken($pdo);
$utilisateur_id = $user_connecte['utilisateur_id'];

// Récupération des données
$menu_id           = (int)($_POST['menu_id']          ?? 0);
$date_prestation   = trim($_POST['date_prestation']   ?? '');
$heure_livraison   = trim($_POST['heure_livraison']   ?? '');
$nombre_personnes  = (int)($_POST['nombre_personnes'] ?? 0);
$adresse_livraison = trim($_POST['adresse_livraison'] ?? '');
$code_postal       = trim($_POST['code_postal']       ?? '');
$ville             = trim($_POST['ville']             ?? '');
$commentaire       = trim($_POST['commentaire']       ?? '');
$hors_bordeaux     = isset($_POST['hors_bordeaux']) ? 1 : 0;
$kilometres        = isset($_POST['kilometres']) ? (float)$_POST['kilometres'] : 0;

$erreurs = [];

if (empty($menu_id) || empty($date_prestation) || empty($heure_livraison) ||
    empty($nombre_personnes) || empty($adresse_livraison) || empty($code_postal) || empty($ville)) {
    $erreurs[] = "Tous les champs obligatoires doivent être remplis.";
}

$date_min = date('Y-m-d', strtotime('+7 days'));
if ($date_prestation < $date_min) {
    $erreurs[] = "La date de prestation doit être au minimum 7 jours après aujourd'hui.";
}

if ($hors_bordeaux && $kilometres <= 0) {
    $erreurs[] = "Veuillez indiquer la distance en kilomètres pour une livraison hors Bordeaux.";
}

$stmt_menu = $pdo->prepare("SELECT * FROM menu WHERE menu_id = :menu_id");
$stmt_menu->execute(['menu_id' => $menu_id]);
$menu = $stmt_menu->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
    $erreurs[] = "Ce menu n'existe pas.";
}

if ($menu && $menu['quantite_restante'] <= 0) {
    $erreurs[] = "Ce menu n'est plus disponible.";
}

if ($menu && $nombre_personnes < $menu['personne_minimum']) {
    $erreurs[] = "Le nombre minimum de personnes est de " . $menu['personne_minimum'] . ".";
}

if (!empty($erreurs)) {
    echo json_encode(['erreurs' => $erreurs]);
    exit;
}

// CALCUL DU PRIX
$prix_menu = $menu['prix_par_personne'] * $nombre_personnes;

$reduction = 0;
if ($nombre_personnes >= ($menu['personne_minimum'] + 5)) {
    $reduction = $prix_menu * 0.10;
    $prix_menu -= $reduction;
}

$frais_livraison = 0;
if ($hors_bordeaux) {
    $frais_livraison = 5 + ($kilometres * 0.59);
}

$prix_total = $prix_menu + $frais_livraison;

try {
    $pdo->beginTransaction();

    $sql = "INSERT INTO commande (
                utilisateur_id, menu_id, date_prestation, heure_livraison,
                nombre_personnes, prix_total, hors_bordeaux, kilometres,
                frais_livraison, reduction, adresse_livraison, code_postal,
                ville, commentaire, statut, date_commande
            ) VALUES (
                :utilisateur_id, :menu_id, :date_prestation, :heure_livraison,
                :nombre_personnes, :prix_total, :hors_bordeaux, :kilometres,
                :frais_livraison, :reduction, :adresse_livraison, :code_postal,
                :ville, :commentaire, 'en attente', NOW()
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'utilisateur_id'    => $utilisateur_id,
        'menu_id'           => $menu_id,
        'date_prestation'   => $date_prestation,
        'heure_livraison'   => $heure_livraison,
        'nombre_personnes'  => $nombre_personnes,
        'prix_total'        => $prix_total,
        'hors_bordeaux'     => $hors_bordeaux,
        'kilometres'        => $kilometres,
        'frais_livraison'   => $frais_livraison,
        'reduction'         => $reduction,
        'adresse_livraison' => $adresse_livraison,
        'code_postal'       => $code_postal,
        'ville'             => $ville,
        'commentaire'       => $commentaire
    ]);

    $stmt_stock = $pdo->prepare("UPDATE menu SET quantite_restante = quantite_restante - 1 WHERE menu_id = :menu_id");
    $stmt_stock->execute(['menu_id' => $menu_id]);

    $stmt_user = $pdo->prepare("SELECT email, prenom, nom FROM utilisateur WHERE utilisateur_id = :utilisateur_id");
    $stmt_user->execute(['utilisateur_id' => $utilisateur_id]);
    $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

    $message = "Bonjour {$user['prenom']} {$user['nom']},

Nous avons bien reçu votre commande !

Détails de votre commande :
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Menu : {$menu['nom']}
Date de prestation : " . date('d/m/Y', strtotime($date_prestation)) . "
Heure de livraison : " . date('H:i', strtotime($heure_livraison)) . "
Nombre de personnes : $nombre_personnes

Adresse de livraison :
$adresse_livraison
$code_postal $ville

Prix détaillé :
- Prix menu : " . number_format(($prix_menu + $reduction), 2, ',', ' ') . " €";

    if ($reduction > 0) {
        $message .= "\n- Réduction 10% : -" . number_format($reduction, 2, ',', ' ') . " €";
    }
    if ($frais_livraison > 0) {
        $message .= "\n- Frais de livraison : " . number_format($frais_livraison, 2, ',', ' ') . " €";
    }

    $message .= "
TOTAL : " . number_format($prix_total, 2, ',', ' ') . " €

Votre commande sera traitée dans les plus brefs délais.
Vous recevrez une notification dès que votre commande sera validée.

Vous pouvez suivre l'état de votre commande dans votre espace client :
→ https://vite-et-gourmand-alex.netlify.app/pages/utilisateur/mes-commandes.html

Merci de votre confiance !

L'équipe Vite & Gourmand
05 56 00 00 00
contact@vitegourmand.fr";

    envoyerEmail(
        $user['email'],
        $user['prenom'] . ' ' . $user['nom'],
        'Confirmation de votre commande - Vite & Gourmand',
        $message
    );

    $pdo->commit();

    echo json_encode([
        'succes'   => "Votre commande a bien été enregistrée ! Un email de confirmation vous a été envoyé.",
        'redirect' => 'index.html'
    ]);
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['erreurs' => ["Une erreur est survenue. Veuillez réessayer."]]);
    exit;
}
?>