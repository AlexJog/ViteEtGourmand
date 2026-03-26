<?php
require_once __DIR__ . '/../../vendor/autoload.php';

function getMongoCollection(): MongoDB\Collection
{
    $uri    = getenv('MONGODB_URI');
    $client = new MongoDB\Client($uri);
    return $client->selectDatabase('vite_gourmand')->selectCollection('stats_commandes');
}

function lireStatsJSON(): array
{
    try {
        $collection = getMongoCollection();
        $cursor     = $collection->find([]);
        $documents  = [];

        foreach ($cursor as $doc) {
            $documents[] = [
                'id'               => (int)($doc['id'] ?? 0),
                'date_commande'    => (string)($doc['date_commande'] ?? ''),
                'date_prestation'  => (string)($doc['date_prestation'] ?? ''),
                'nombre_personnes' => (int)($doc['nombre_personnes'] ?? 0),
                'prix_total'       => (float)($doc['prix_total'] ?? 0),
                'statut'           => (string)($doc['statut'] ?? ''),
                'menu'             => [
                    'id'                => (int)($doc['menu']['id'] ?? 0),
                    'nom'               => (string)($doc['menu']['nom'] ?? ''),
                    'prix_par_personne' => (float)($doc['menu']['prix_par_personne'] ?? 0)
                ],
                'mois'  => (int)($doc['mois'] ?? 0),
                'annee' => (int)($doc['annee'] ?? 0)
            ];
        }

        return $documents;

    } catch (Exception $e) {
        error_log("MongoDB lireStatsJSON error: " . $e->getMessage());
        return [];
    }
}

function synchroniserStatsJSON($pdo): int
{
    try {
        $sql = "SELECT 
                    c.commande_id,
                    c.date_commande,
                    c.date_prestation,
                    c.nombre_personnes,
                    c.prix_total,
                    c.statut,
                    m.menu_id,
                    m.nom AS menu_nom,
                    m.prix_par_personne,
                    MONTH(c.date_commande) AS mois,
                    YEAR(c.date_commande) AS annee
                FROM commande c
                INNER JOIN menu m ON c.menu_id = m.menu_id
                ORDER BY c.date_commande DESC";

        $stmt      = $pdo->query($sql);
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $collection = getMongoCollection();
        $collection->deleteMany([]);

        $documents = [];
        foreach ($commandes as $commande) {
            $documents[] = [
                'id'               => (int)$commande['commande_id'],
                'date_commande'    => $commande['date_commande'],
                'date_prestation'  => $commande['date_prestation'],
                'nombre_personnes' => (int)$commande['nombre_personnes'],
                'prix_total'       => (float)$commande['prix_total'],
                'statut'           => $commande['statut'],
                'menu'             => [
                    'id'                => (int)$commande['menu_id'],
                    'nom'               => $commande['menu_nom'],
                    'prix_par_personne' => (float)$commande['prix_par_personne']
                ],
                'mois'  => (int)$commande['mois'],
                'annee' => (int)$commande['annee']
            ];
        }

        if (!empty($documents)) {
            $collection->insertMany($documents);
        }

        return count($documents);

    } catch (Exception $e) {
        error_log("MongoDB synchroniserStatsJSON error: " . $e->getMessage());
        return false;
    }
}

function calculerStatsParMenu(array $filtres = []): array
{
    try {
        $collection = getMongoCollection();

        $filtre = [];

        if (!empty($filtres['menu_id'])) {
            $filtre['menu.id'] = (int)$filtres['menu_id'];
        }

        if (!empty($filtres['date_debut'])) {
            $filtre['date_commande']['$gte'] = $filtres['date_debut'];
        }

        if (!empty($filtres['date_fin'])) {
            $filtre['date_commande']['$lte'] = $filtres['date_fin'] . ' 23:59:59';
        }

        // (object) force un objet vide {} quand $filtre est vide
        $pipeline = [
            ['$match' => (object)$filtre],
            ['$group' => [
                '_id'                => '$menu.id',
                'menu_nom'           => ['$first' => '$menu.nom'],
                'menu_id'            => ['$first' => '$menu.id'],
                'nb_commandes'       => ['$sum' => 1],
                'chiffre_affaires'   => ['$sum' => '$prix_total'],
                'nb_personnes_total' => ['$sum' => '$nombre_personnes']
            ]],
            ['$sort' => ['nb_commandes' => -1]]
        ];

        $cursor = $collection->aggregate($pipeline);
        $stats  = [];

        foreach ($cursor as $doc) {
            $stats[] = [
                'menu_nom'           => (string)$doc['menu_nom'],
                'menu_id'            => (int)$doc['menu_id'],
                'nb_commandes'       => (int)$doc['nb_commandes'],
                'chiffre_affaires'   => (float)$doc['chiffre_affaires'],
                'nb_personnes_total' => (int)$doc['nb_personnes_total']
            ];
        }

        return $stats;

    } catch (Exception $e) {
        error_log("MongoDB calculerStatsParMenu error: " . $e->getMessage());
        return [];
    }
}
?>