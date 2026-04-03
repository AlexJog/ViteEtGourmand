<?php
class Menu
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT m.*, r.libelle AS regime_nom, t.libelle AS theme_nom
            FROM menu m
            INNER JOIN regime r ON m.regime_id = r.regime_id
            INNER JOIN theme t ON m.theme_id = t.theme_id
            ORDER BY m.menu_id ASC
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT m.*, r.libelle AS regime_nom, t.libelle AS theme_nom
            FROM menu m
            INNER JOIN regime r ON m.regime_id = r.regime_id
            INNER JOIN theme t ON m.theme_id = t.theme_id
            WHERE m.menu_id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO menu (nom, service, personne_minimum, prix_par_personne, description, image_url, regime_id, theme_id)
            VALUES (:nom, :service, :personne_minimum, :prix_par_personne, :description, :image_url, :regime_id, :theme_id)
        ");
        return $stmt->execute($data);
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        $stmt = $this->pdo->prepare("
            UPDATE menu SET
                nom               = :nom,
                service           = :service,
                personne_minimum  = :personne_minimum,
                prix_par_personne = :prix_par_personne,
                description       = :description,
                image_url         = :image_url,
                regime_id         = :regime_id,
                theme_id          = :theme_id
            WHERE menu_id = :id
        ");
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM menu WHERE menu_id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>