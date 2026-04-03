<?php
class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            if (getenv('JAWSDB_URL')) {
                $url    = parse_url(getenv('JAWSDB_URL'));
                $host   = $url['host'];
                $dbname = ltrim($url['path'], '/');
                $user   = $url['user'];
                $pass   = $url['pass'];
            } else {
                $host   = getenv('DB_HOST') ?: 'localhost';
                $dbname = getenv('DB_NAME') ?: 'vite_gourmand';
                $user   = getenv('DB_USER') ?: 'root';
                $pass   = getenv('DB_PASS') ?: '';
            }

            self::$instance = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }

        return self::$instance;
    }

    // Empêcher l'instanciation directe
    private function __construct() {}
    private function __clone() {}
}
?>