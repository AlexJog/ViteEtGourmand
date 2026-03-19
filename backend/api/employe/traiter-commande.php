<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erreur' => 'non_connecte']);
    exit;
}

if ($_SESSION['user_role'] !== 'employe' && $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['erreur' => 'non_autorise']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erreur' => 'Méthode non autorisée.']);
    exit;
}

$commande_id = (int)($_POST['commande_id'] ?? 0);

// Déterminer le nouveau statut
if (isset($_POST['nouveau_statut'])) {
    $nouveau_statut = $_POST['nouveau_statut'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'valider')      $nouveau_statut = 'accepté';
    elseif ($action === 'refuser')  $nouveau_statut = 'refusée';
    elseif ($action === 'livrer')   $nouveau_statut = 'livré';
    else {
        echo json_encode(['erreur' => 'Action invalide.']);
        exit;
    }
} else {
    echo json_encode(['erreur' => 'Statut manquant.']);
    exit;
}

$pret_materiel        = !empty($_POST['pret_materiel'])        ? 1 : 0;
$restitution_materiel = !empty($_POST['restitution_materiel']) ? 1 : 0;

try {
    if ($pret_materiel) {
        $sql = "UPDATE commande SET statut = :statut, pret_materiel = 1 WHERE commande_id = :commande_id";
    } elseif ($restitution_materiel) {
        $sql = "UPDATE commande SET statut = :statut, restitution_materiel = 1 WHERE commande_id = :commande_id";
    } else {
        $sql = "UPDATE commande SET statut = :statut WHERE commande_id = :commande_id";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['statut' => $nouveau_statut, 'commande_id' => $commande_id]);

    // EMAIL : commande terminée
    if ($nouveau_statut === 'terminée') {
        $stmt_info = $pdo->prepare("SELECT u.email, u.prenom, u.nom, m.nom AS menu_nom
                                    FROM commande c
                                    INNER JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
                                    INNER JOIN menu m ON c.menu_id = m.menu_id
                                    WHERE c.commande_id = :commande_id");
        $stmt_info->execute(['commande_id' => $commande_id]);
        $info = $stmt_info->fetch(PDO::FETCH_ASSOC);

        if ($info) {
            $sujet   = "Votre commande est terminée - Laissez-nous un avis !";
            $message = "Bonjour {$info['prenom']} {$info['nom']},

Votre commande du menu \"{$info['menu_nom']}\" est maintenant terminée !

Nous espérons que tout s'est bien passé et que vous avez apprécié nos services.

Nous serions ravis d'avoir votre retour ! Connectez-vous à votre espace client pour laisser un avis :
→ https://vite-et-gourmand-alex-a85135b73360.herokuapp.com/utilisateur/mes-commandes.html

Merci de votre confiance !

L'équipe Vite & Gourmand
05 56 00 00 00
contact@vitegourmand.fr";

            $headers  = "From: noreply@vitegourmand.fr\r\n";
            $headers .= "Reply-To: contact@vitegourmand.fr\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            @mail($info['email'], $sujet, $message, $headers);
        }
    }

    // EMAIL : attente matériel
    if ($nouveau_statut === 'attente matériel') {
        $stmt_info = $pdo->prepare("SELECT u.email, u.prenom, u.nom
                                    FROM commande c
                                    INNER JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
                                    WHERE c.commande_id = :commande_id");
        $stmt_info->execute(['commande_id' => $commande_id]);
        $info = $stmt_info->fetch(PDO::FETCH_ASSOC);

        if ($info) {
            $sujet   = "Important : Restitution du matériel - Vite & Gourmand";
            $message = "Bonjour {$info['prenom']} {$info['nom']},

Votre commande a bien été livrée. Nous vous rappelons que du matériel vous a été prêté.

⚠️ IMPORTANT : Vous disposez de 10 jours ouvrés pour nous restituer le matériel prêté.

Passé ce délai, des frais de 600€ vous seront facturés conformément à nos conditions générales de vente.

Pour organiser la restitution du matériel, merci de nous contacter :
- Par téléphone : 05 56 00 00 00
- Par email : contact@vitegourmand.fr

Merci de votre compréhension.

L'équipe Vite & Gourmand";

            $headers  = "From: noreply@vitegourmand.fr\r\n";
            $headers .= "Reply-To: contact@vitegourmand.fr\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            @mail($info['email'], $sujet, $message, $headers);
        }
    }

    echo json_encode([
        'succes' => "La commande #$commande_id a été mise à jour : \"$nouveau_statut\"."
    ]);

} catch (PDOException $e) {
    echo json_encode(['erreur' => "Une erreur est survenue lors de la mise à jour."]);
}
?>