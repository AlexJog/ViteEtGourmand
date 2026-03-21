<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

$sql = "SELECT a.*, u.prenom, u.nom 
        FROM avis a
        INNER JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
        WHERE a.statut = 'validé'
        ORDER BY a.date_avis DESC
        LIMIT 6";

$stmt = $pdo->query($sql);
$avis = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($avis);
?>