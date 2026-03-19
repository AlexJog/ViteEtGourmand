<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erreur' => 'non_connecte']);
    exit;
}

if ($_SESSION['user_role'] !== 'admin') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erreurs' => ['Méthode non autorisée.']]);
    exit;
}

$action = $_POST['action'] ?? '';

// FONCTION UPLOAD IMAGE
function uploadImage($file) {

    // Sur Heroku : utiliser une image par défaut
    if (getenv("JAWSDB_URL")) {
        return ['erreur' => false, 'chemin' => '/assets/images/menus/menu-default.jpg'];
    }

    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['erreur' => true, 'message' => "Aucune image n'a été uploadée."];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['erreur' => true, 'message' => "Erreur lors de l'upload de l'image."];
    }

    $max_size = 10 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        return ['erreur' => true, 'message' => "L'image est trop volumineuse (max 10 Mo)."];
    }

    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
    $finfo     = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        return ['erreur' => true, 'message' => "Format d'image non autorisé (JPG, JPEG, PNG uniquement)."];
    }

    $extension    = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nom_fichier  = 'menu-' . time() . '-' . uniqid() . '.' . $extension;
    $dossier_upload = '../../assets/images/menus/';
    $chemin_complet = $dossier_upload . $nom_fichier;

    if (!is_dir($dossier_upload)) {
        mkdir($dossier_upload, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $chemin_complet)) {
        return ['erreur' => false, 'chemin' => '/assets/images/menus/' . $nom_fichier];
    } else {
        return ['erreur' => true, 'message' => "Erreur lors de l'enregistrement de l'image."];
    }
}

// AJOUTER UN MENU
if ($action === 'ajouter') {

    $nom               = trim($_POST['nom']               ?? '');
    $description       = trim($_POST['description']       ?? '');
    $service           = trim($_POST['service']           ?? '');
    $regime_id         = (int)($_POST['regime_id']        ?? 0);
    $theme_id          = (int)($_POST['theme_id']         ?? 0);
    $prix_par_personne = (float)($_POST['prix_par_personne'] ?? 0);
    $personne_minimum  = (int)($_POST['personne_minimum'] ?? 0);
    $quantite_restante = (int)($_POST['quantite_restante'] ?? 0);

    $erreurs = [];

    if (empty($nom) || empty($description) || empty($service)) {
        $erreurs[] = "Tous les champs sont obligatoires.";
    }
    if ($prix_par_personne <= 0) {
        $erreurs[] = "Le prix doit être supérieur à 0.";
    }
    if ($personne_minimum <= 0) {
        $erreurs[] = "Le nombre minimum de personnes doit être supérieur à 0.";
    }

    $upload_result = uploadImage($_FILES['image']);
    if ($upload_result['erreur']) {
        $erreurs[] = $upload_result['message'];
    }

    if (!empty($erreurs)) {
        echo json_encode(['erreurs' => $erreurs]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO menu (nom, description, service, regime_id, theme_id, prix_par_personne, personne_minimum, quantite_restante, image_url)
                                VALUES (:nom, :description, :service, :regime_id, :theme_id, :prix_par_personne, :personne_minimum, :quantite_restante, :image_url)");
        $stmt->execute([
            'nom'               => $nom,
            'description'       => $description,
            'service'           => $service,
            'regime_id'         => $regime_id,
            'theme_id'          => $theme_id,
            'prix_par_personne' => $prix_par_personne,
            'personne_minimum'  => $personne_minimum,
            'quantite_restante' => $quantite_restante,
            'image_url'         => $upload_result['chemin']
        ]);

        echo json_encode(['succes' => "Le menu a été créé avec succès !"]);

    } catch (PDOException $e) {
        echo json_encode(['erreurs' => ["Une erreur est survenue lors de la création."]]);
    }

// MODIFIER UN MENU
} elseif ($action === 'modifier') {

    $menu_id           = (int)($_POST['menu_id']          ?? 0);
    $nom               = trim($_POST['nom']               ?? '');
    $description       = trim($_POST['description']       ?? '');
    $service           = trim($_POST['service']           ?? '');
    $regime_id         = (int)($_POST['regime_id']        ?? 0);
    $theme_id          = (int)($_POST['theme_id']         ?? 0);
    $prix_par_personne = (float)($_POST['prix_par_personne'] ?? 0);
    $personne_minimum  = (int)($_POST['personne_minimum'] ?? 0);
    $quantite_restante = (int)($_POST['quantite_restante'] ?? 0);

    $erreurs = [];

    if (empty($nom) || empty($description) || empty($service)) {
        $erreurs[] = "Tous les champs sont obligatoires.";
    }
    if ($prix_par_personne <= 0) {
        $erreurs[] = "Le prix doit être supérieur à 0.";
    }
    if ($personne_minimum <= 0) {
        $erreurs[] = "Le nombre minimum de personnes doit être supérieur à 0.";
    }

    // Nouvelle image ?
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = uploadImage($_FILES['image']);

        if ($upload_result['erreur']) {
            $erreurs[] = $upload_result['message'];
        } else {
            $image_url = $upload_result['chemin'];

            // Supprimer l'ancienne image
            $stmt_old = $pdo->prepare("SELECT image_url FROM menu WHERE menu_id = :menu_id");
            $stmt_old->execute(['menu_id' => $menu_id]);
            $old_image = $stmt_old->fetch()['image_url'];

            if (!empty($old_image) && file_exists('../../' . $old_image)) {
                unlink('../../' . $old_image);
            }
        }
    }

    if (!empty($erreurs)) {
        echo json_encode(['erreurs' => $erreurs]);
        exit;
    }

    try {
        if ($image_url) {
            $stmt = $pdo->prepare("UPDATE menu SET nom=:nom, description=:description, service=:service,
                                   regime_id=:regime_id, theme_id=:theme_id, prix_par_personne=:prix_par_personne,
                                   personne_minimum=:personne_minimum, quantite_restante=:quantite_restante,
                                   image_url=:image_url WHERE menu_id=:menu_id");
            $stmt->execute([
                'nom' => $nom, 'description' => $description, 'service' => $service,
                'regime_id' => $regime_id, 'theme_id' => $theme_id,
                'prix_par_personne' => $prix_par_personne, 'personne_minimum' => $personne_minimum,
                'quantite_restante' => $quantite_restante, 'image_url' => $image_url,
                'menu_id' => $menu_id
            ]);
        } else {
            $stmt = $pdo->prepare("UPDATE menu SET nom=:nom, description=:description, service=:service,
                                   regime_id=:regime_id, theme_id=:theme_id, prix_par_personne=:prix_par_personne,
                                   personne_minimum=:personne_minimum, quantite_restante=:quantite_restante
                                   WHERE menu_id=:menu_id");
            $stmt->execute([
                'nom' => $nom, 'description' => $description, 'service' => $service,
                'regime_id' => $regime_id, 'theme_id' => $theme_id,
                'prix_par_personne' => $prix_par_personne, 'personne_minimum' => $personne_minimum,
                'quantite_restante' => $quantite_restante, 'menu_id' => $menu_id
            ]);
        }

        echo json_encode(['succes' => "Le menu a été modifié avec succès !"]);

    } catch (PDOException $e) {
        echo json_encode(['erreurs' => ["Une erreur est survenue lors de la modification."]]);
    }

} else {
    echo json_encode(['erreurs' => ['Action invalide.']]);
}
?>