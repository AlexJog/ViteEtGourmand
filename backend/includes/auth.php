<?php
function verifierToken($pdo) {
    $headers = getallheaders();
    
    // Chercher le token dans différentes casses
    $token = $headers['X-AUTH-TOKEN'] 
          ?? $headers['X-Auth-Token'] 
          ?? $headers['x-auth-token'] 
          ?? null;

    if (!$token) {
        echo json_encode(['erreur' => 'non_connecte']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT u.*, r.libelle as role_nom 
                            FROM utilisateur u
                            INNER JOIN role r ON u.role_id = r.role_id
                            WHERE u.api_token = :token");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['erreur' => 'non_connecte']);
        exit;
    }

    return $user;
}
?>