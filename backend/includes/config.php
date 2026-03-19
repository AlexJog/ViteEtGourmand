<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (getenv("JAWSDB_URL")) {
    require_once __DIR__ . '/config-heroku.php';
} else {
    define('BASE_URL', '/ViteEtGourmand/');

    define('DB_HOST', 'localhost');
    define('DB_NAME', 'vite_gourmand');
    define('DB_USER', 'root');
    define('DB_PASS', '');

    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}
?>