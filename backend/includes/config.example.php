<?php
// RENOMMER CE FICHIER EN config.php
// ET REMPLIR AVEC VOS IDENTIFIANTS

// Démarrage de la session
session_start();

// Définir l'URL de base du projet
define('BASE_URL', '/ViteEtGourmand/');

// Informations de connexion à la Base de Données
define('DB_HOST', 'localhost');
define('DB_NAME', 'vite_gourmand');
define('DB_USER', 'root');
define('DB_PASS', '');  // Votre mot de passe MySQL

// Connexion à la base de données avec PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>