<?php
// Configuration JSON (base NoSQL)
define('JSON_STATS_FILE', __DIR__ . '/../../data/stats.json');

// Fonction pour lire les données JSON
function lireStatsJSON() {
    if (!file_exists(JSON_STATS_FILE)) {
        return [];
    }
    
    $json = file_get_contents(JSON_STATS_FILE);
    $data = json_decode($json, true);
    
    return $data ?: [];
}

// Fonction pour écrire les données JSON
function ecrireStatsJSON($data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents(JSON_STATS_FILE, $json);
}

// Fonction pour synchroniser MySQL vers JSON
function synchroniserStatsJSON($pdo) {
    
    // Sur Heroku : simulation (pas d'écriture possible)
    if (getenv("JAWSDB_URL")) {
        error_log("Synchronisation JSON simulée sur Heroku (système de fichiers en lecture seule)");
        
        // Récupérer les commandes pour compter
        $sql = "SELECT COUNT(*) as total FROM commande";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch();
        
        // Retourner true pour simuler le succès
        return $result['total'];
    }
    
    // EN LOCAL : synchronisation réelle
    
    // Récupérer toutes les commandes depuis MySQL
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
    
    $stmt = $pdo->query($sql);
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convertir en format JSON NoSQL
    $documents = [];
    foreach ($commandes as $commande) {
        $documents[] = [
            'id' => $commande['commande_id'],
            'date_commande' => $commande['date_commande'],
            'date_prestation' => $commande['date_prestation'],
            'nombre_personnes' => (int)$commande['nombre_personnes'],
            'prix_total' => (float)$commande['prix_total'],
            'statut' => $commande['statut'],
            'menu' => [
                'id' => (int)$commande['menu_id'],
                'nom' => $commande['menu_nom'],
                'prix_par_personne' => (float)$commande['prix_par_personne']
            ],
            'mois' => (int)$commande['mois'],
            'annee' => (int)$commande['annee']
        ];
    }
    
    // Écrire dans le fichier JSON
    return ecrireStatsJSON($documents);
}

// Fonction pour calculer les stats par menu
function calculerStatsParMenu($filtres = []) {
    global $pdo;
    
    // Sur Heroku : utiliser MySQL directement
    if (getenv("JAWSDB_URL")) {
        
        // Construire la requête SQL avec filtres
        $sql = "SELECT 
                    m.nom AS menu_nom,
                    m.menu_id,
                    COUNT(*) AS nb_commandes,
                    SUM(c.prix_total) AS chiffre_affaires,
                    SUM(c.nombre_personnes) AS nb_personnes_total
                FROM commande c
                INNER JOIN menu m ON c.menu_id = m.menu_id
                WHERE 1=1";
        
        $params = [];
        
        // Filtre par menu
        if (isset($filtres['menu_id']) && !empty($filtres['menu_id'])) {
            $sql .= " AND m.menu_id = :menu_id";
            $params['menu_id'] = $filtres['menu_id'];
        }
        
        // Filtre par date début
        if (isset($filtres['date_debut']) && !empty($filtres['date_debut'])) {
            $sql .= " AND c.date_commande >= :date_debut";
            $params['date_debut'] = $filtres['date_debut'];
        }
        
        // Filtre par date fin
        if (isset($filtres['date_fin']) && !empty($filtres['date_fin'])) {
            $sql .= " AND c.date_commande <= :date_fin";
            $params['date_fin'] = $filtres['date_fin'] . ' 23:59:59';
        }
        
        $sql .= " GROUP BY m.menu_id, m.nom
                  ORDER BY nb_commandes DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // EN LOCAL : utiliser le fichier JSON
    
    $data = lireStatsJSON();
    
    if (empty($data)) {
        return [];
    }
    
    // Appliquer les filtres
    if (!empty($filtres)) {
        $data = array_filter($data, function($commande) use ($filtres) {
            // Filtre par menu
            if (isset($filtres['menu_id']) && $commande['menu']['id'] != $filtres['menu_id']) {
                return false;
            }
            
            // Filtre par date début
            if (isset($filtres['date_debut']) && $commande['date_commande'] < $filtres['date_debut']) {
                return false;
            }
            
            // Filtre par date fin
            if (isset($filtres['date_fin']) && $commande['date_commande'] > $filtres['date_fin'] . ' 23:59:59') {
                return false;
            }
            
            return true;
        });
    }
    
    // Grouper par menu et calculer les stats
    $stats = [];
    
    foreach ($data as $commande) {
        $menu_nom = $commande['menu']['nom'];
        
        if (!isset($stats[$menu_nom])) {
            $stats[$menu_nom] = [
                'menu_nom' => $menu_nom,
                'menu_id' => $commande['menu']['id'],
                'nb_commandes' => 0,
                'chiffre_affaires' => 0,
                'nb_personnes_total' => 0
            ];
        }
        
        $stats[$menu_nom]['nb_commandes']++;
        $stats[$menu_nom]['chiffre_affaires'] += $commande['prix_total'];
        $stats[$menu_nom]['nb_personnes_total'] += $commande['nombre_personnes'];
    }
    
    // Trier par nombre de commandes (décroissant)
    usort($stats, function($a, $b) {
        return $b['nb_commandes'] - $a['nb_commandes'];
    });
    
    return $stats;
}
?>