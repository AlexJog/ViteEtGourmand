<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Menu.php';

header('Content-Type: application/json');

$filtre_prix   = isset($_GET['prixMax'])     && $_GET['prixMax']     !== '' ? (float)$_GET['prixMax']  : null;
$filtre_regime = isset($_GET['regime'])      && $_GET['regime']      !== '' ? $_GET['regime']           : null;
$filtre_theme  = isset($_GET['theme'])       && $_GET['theme']       !== '' ? $_GET['theme']            : null;
$filtre_nb     = isset($_GET['nbPersonnes']) && $_GET['nbPersonnes'] !== '' ? (int)$_GET['nbPersonnes'] : null;

// Pas de filtres — on utilise la classe Menu
if ($filtre_prix === null && $filtre_regime === null && $filtre_theme === null && $filtre_nb === null) {
    $menu  = new Menu();
    $menus = $menu->getAll();
    echo json_encode($menus);
    exit;
}

// Avec filtres — requête dynamique
$pdo = Database::getConnection();

$sql = "SELECT m.*, r.libelle AS regime_nom, t.libelle AS theme_nom
        FROM menu m
        LEFT JOIN regime r ON m.regime_id = r.regime_id
        LEFT JOIN theme t ON m.theme_id = t.theme_id
        WHERE 1=1";

if ($filtre_prix   !== null) $sql .= " AND m.prix_par_personne <= :prix";
if ($filtre_regime !== null) $sql .= " AND LOWER(r.libelle) = :regime";
if ($filtre_theme  !== null) $sql .= " AND LOWER(t.libelle) = :theme";
if ($filtre_nb     !== null) $sql .= " AND m.personne_minimum <= :nb_personnes";

$sql .= " ORDER BY m.menu_id ASC";

$stmt = $pdo->prepare($sql);

if ($filtre_prix   !== null) $stmt->bindValue(':prix',         $filtre_prix,              PDO::PARAM_STR);
if ($filtre_regime !== null) $stmt->bindValue(':regime',       strtolower($filtre_regime), PDO::PARAM_STR);
if ($filtre_theme  !== null) $stmt->bindValue(':theme',        strtolower($filtre_theme),  PDO::PARAM_STR);
if ($filtre_nb     !== null) $stmt->bindValue(':nb_personnes', $filtre_nb,                 PDO::PARAM_INT);

$stmt->execute();
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($menus);