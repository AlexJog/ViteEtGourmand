-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: vite_gourmand
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `allergene`
--

DROP TABLE IF EXISTS `allergene`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `allergene` (
  `allergene_id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`allergene_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `allergene`
--

LOCK TABLES `allergene` WRITE;
/*!40000 ALTER TABLE `allergene` DISABLE KEYS */;
INSERT INTO `allergene` VALUES (1,'Gluten'),(2,'Lactose'),(3,'Œufs'),(4,'Fruits à coque'),(5,'Arachides');
/*!40000 ALTER TABLE `allergene` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `avis`
--

DROP TABLE IF EXISTS `avis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avis` (
  `avis_id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `commande_id` int NOT NULL,
  `note` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `statut` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_avis` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`avis_id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `commande_id` (`commande_id`),
  CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`) ON DELETE CASCADE,
  CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`commande_id`) REFERENCES `commande` (`commande_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `avis`
--

LOCK TABLES `avis` WRITE;
/*!40000 ALTER TABLE `avis` DISABLE KEYS */;
INSERT INTO `avis` VALUES (1,14,5,'5','Excellent service ! Les plats étaient délicieux et la présentation soignée.','validé','2026-01-30 12:09:07'),(2,17,6,'5','Une qualité irréprochable, nos invités ont adoré. Merci !','validé','2026-02-04 14:51:07');
/*!40000 ALTER TABLE `avis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commande`
--

DROP TABLE IF EXISTS `commande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commande` (
  `commande_id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `menu_id` int NOT NULL,
  `date_prestation` date NOT NULL,
  `heure_livraison` time NOT NULL,
  `nombre_personnes` int NOT NULL,
  `prix_total` decimal(10,2) NOT NULL,
  `hors_bordeaux` tinyint(1) DEFAULT '0',
  `kilometres` decimal(10,2) DEFAULT '0.00',
  `frais_livraison` decimal(10,2) DEFAULT '0.00',
  `reduction` decimal(10,2) DEFAULT '0.00',
  `adresse_livraison` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_postal` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `statut` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'en attente',
  `pret_materiel` tinyint(1) DEFAULT '0',
  `restitution_materiel` tinyint(1) DEFAULT '0',
  `date_commande` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`commande_id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `menu_id` (`menu_id`),
  CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`) ON DELETE CASCADE,
  CONSTRAINT `commande_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commande`
--

LOCK TABLES `commande` WRITE;
/*!40000 ALTER TABLE `commande` DISABLE KEYS */;
INSERT INTO `commande` VALUES (5,14,1,'2026-02-07','13:54:00',16,720.00,0,0.00,0.00,0.00,'2 rue de Noel','33000','Bordeaux','','terminée',0,0,'2026-01-30 11:53:04'),(6,17,3,'2026-02-14','20:38:00',10,252.00,0,0.00,0.00,28.00,'1 rue du rosier','33000','Bordeaux','','terminée',0,0,'2026-02-04 14:42:34'),(7,18,5,'2026-02-19','19:45:00',4,103.49,1,11.00,11.49,0.00,'12 rue du cailloux','33290','Blanquefort','','en attente',0,0,'2026-02-04 14:57:33'),(8,18,2,'2026-02-28','15:06:00',15,472.50,0,0.00,0.00,52.50,'12 rue du cailloux','33000','Bordeaux','','en livraison',0,0,'2026-02-04 15:05:05');
/*!40000 ALTER TABLE `commande` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact` (
  `contact_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_envoi` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact`
--

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horaire`
--

DROP TABLE IF EXISTS `horaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horaire` (
  `horaire_id` int NOT NULL AUTO_INCREMENT,
  `jour` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `heure_ouverture` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `heure_fermeture` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ferme` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`horaire_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horaire`
--

LOCK TABLES `horaire` WRITE;
/*!40000 ALTER TABLE `horaire` DISABLE KEYS */;
INSERT INTO `horaire` VALUES (1,'Lundi','09:00','18:00',0),(2,'Mardi','09:00','18:00',0),(3,'Mercredi','09:00','18:00',0),(4,'Jeudi','09:00','18:00',0),(5,'Vendredi','09:00','18:00',0),(6,'Samedi','10:00','16:00',0),(7,'Dimanche',NULL,NULL,1);
/*!40000 ALTER TABLE `horaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu` (
  `menu_id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `personne_minimum` int NOT NULL,
  `prix_par_personne` decimal(10,0) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `quantite_restante` int DEFAULT NULL,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `regime_id` int NOT NULL,
  `theme_id` int NOT NULL,
  PRIMARY KEY (`menu_id`),
  KEY `regime_id` (`regime_id`),
  KEY `theme_id` (`theme_id`),
  CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`regime_id`) REFERENCES `regime` (`regime_id`),
  CONSTRAINT `menu_ibfk_2` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`theme_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,'Menu Noël','Formule complète',10,45,'Un menu festif pour les fêtes de fin d\'année avec des saveurs authentiques',26,'/assets/images/menus/menu-noel.jpg',1,1),(2,'Menu Pâques','Formule complète',8,35,'Un buffet printanier et gourmand pour célébrer Pâques en famille ou entre amis.',23,'/assets/images/menus/menu-paques.jpg',1,2),(3,'Menu Classique','Plateau-repas',4,28,'Un menu traditionnel et généreux, idéal pour vos réunions de famille ou événements professionnels.',49,'/assets/images/menus/menu-classique.jpg',1,3),(4,'Menu Végétarien','Plateau-repas',8,25,'Des saveurs végétales délicieuses et équilibrées.',20,'/assets/images/menus/menu-vegetarien.jpg',2,3),(5,'Menu Vegan','Plateau-repas',4,23,'Un menu 100% végétal, créatif et savoureux pour tous vos événements.',7,'/assets/images/menus/menu-vegan.jpg',3,3),(6,'Menu Mariage','Formule complète',20,55,'Un menu raffiné et élégant pour célébrer le plus beau jour de votre vie. Installation et service inclus.',50,'/assets/images/menus/menu-mariage.jpg',1,4);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_plat`
--

DROP TABLE IF EXISTS `menu_plat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_plat` (
  `menu_id` int NOT NULL,
  `plat_id` int NOT NULL,
  PRIMARY KEY (`menu_id`,`plat_id`),
  KEY `plat_id` (`plat_id`),
  CONSTRAINT `menu_plat_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`),
  CONSTRAINT `menu_plat_ibfk_2` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`plat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_plat`
--

LOCK TABLES `menu_plat` WRITE;
/*!40000 ALTER TABLE `menu_plat` DISABLE KEYS */;
INSERT INTO `menu_plat` VALUES (1,1),(1,2),(1,3),(2,4),(2,5),(2,6),(3,7),(3,8),(3,9),(4,10),(4,11),(4,12),(5,13),(5,14),(5,15),(6,16),(6,17),(6,18);
/*!40000 ALTER TABLE `menu_plat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plat`
--

DROP TABLE IF EXISTS `plat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plat` (
  `plat_id` int NOT NULL AUTO_INCREMENT,
  `nom_plat` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`plat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plat`
--

LOCK TABLES `plat` WRITE;
/*!40000 ALTER TABLE `plat` DISABLE KEYS */;
INSERT INTO `plat` VALUES (1,'Foie gras maison sur toast'),(2,'Chapon rôti aux marrons et légumes de saison'),(3,'Bûche de Noël chocolat-framboise'),(4,'Terrine de saumon aux herbes'),(5,'Magret de canard, gratin dauphinois'),(6,'Tarte au citron meringuée'),(7,'Salade de chèvre chaud'),(8,'Pavé de saumon grillé, écrasé de pommes de terre'),(9,'Crème brûlée vanille'),(10,'Velouté de potimarron'),(11,'Assortiment de tartines gourmandes'),(12,'Fondant au chocolat'),(13,'Houmous et crudités'),(14,'Salade Caesar vegan, croûtons dorés'),(15,'Mousse au chocolat à l\'aquafaba'),(16,'Jambon cru d’exception'),(17,'Filet de bœuf sauce au poivre, légumes fins'),(18,'Pièce montée de choux à la crème');
/*!40000 ALTER TABLE `plat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plat_allergene`
--

DROP TABLE IF EXISTS `plat_allergene`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plat_allergene` (
  `plat_id` int NOT NULL,
  `allergene_id` int NOT NULL,
  PRIMARY KEY (`plat_id`,`allergene_id`),
  KEY `allergene_id` (`allergene_id`),
  CONSTRAINT `plat_allergene_ibfk_1` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`plat_id`),
  CONSTRAINT `plat_allergene_ibfk_2` FOREIGN KEY (`allergene_id`) REFERENCES `allergene` (`allergene_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plat_allergene`
--

LOCK TABLES `plat_allergene` WRITE;
/*!40000 ALTER TABLE `plat_allergene` DISABLE KEYS */;
INSERT INTO `plat_allergene` VALUES (1,1),(3,1),(5,1),(6,1),(7,1),(11,1),(12,1),(14,1),(17,1),(18,1),(1,2),(2,2),(3,2),(4,2),(5,2),(6,2),(7,2),(8,2),(9,2),(10,2),(11,2),(12,2),(17,2),(18,2),(1,3),(3,3),(4,3),(6,3),(9,3),(11,3),(12,3),(18,3),(2,4),(3,4),(7,4),(10,4),(11,4),(12,4),(15,4),(13,5);
/*!40000 ALTER TABLE `plat_allergene` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `regime`
--

DROP TABLE IF EXISTS `regime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `regime` (
  `regime_id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`regime_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regime`
--

LOCK TABLES `regime` WRITE;
/*!40000 ALTER TABLE `regime` DISABLE KEYS */;
INSERT INTO `regime` VALUES (1,'Classique'),(2,'Végétarien'),(3,'Vegan');
/*!40000 ALTER TABLE `regime` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'admin'),(2,'employe'),(3,'utilisateur');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `theme`
--

DROP TABLE IF EXISTS `theme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `theme` (
  `theme_id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `theme`
--

LOCK TABLES `theme` WRITE;
/*!40000 ALTER TABLE `theme` DISABLE KEYS */;
INSERT INTO `theme` VALUES (1,'Noël'),(2,'Pâques'),(3,'Classique'),(4,'Événement');
/*!40000 ALTER TABLE `theme` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilisateur` (
  `utilisateur_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reset_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token_expire` datetime DEFAULT NULL,
  `nom` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_postal` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pays` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse_postale` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` int NOT NULL,
  `actif` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`utilisateur_id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateur`
--

LOCK TABLES `utilisateur` WRITE;
/*!40000 ALTER TABLE `utilisateur` DISABLE KEYS */;
INSERT INTO `utilisateur` VALUES (1,'admin@vitegourmand.fr','$2y$12$RntR5Vl98LrU5npi4P.9F.WSuAu6rRIzZfvhBiOP0VSYMG4xBUX8y',NULL,NULL,'Admin','Jose','05 56 00 00 00','Bordeaux','33000','France','1 rue du gourmand',1,1),(2,'employe@vitegourmand.fr','$2y$12$mC9kHn3v/OA9kyF9cc3y9.d3a1W09tsNeubLHEOkMJcAm2gDOHYn2',NULL,NULL,'Employe','Francis','06 12 34 56 32','Bordeaux','33000','France','12 rue du Gourmand',2,1),(14,'client@client.fr','$2y$12$Js/iaemw2vjsH4IewRDy3u8.MbpGhbQ.wnQojPxB1XMNP.Lxav1na',NULL,NULL,'Client','Sabino','08 36 75 75 75','Bordeaux','33000','France','2 rue de Noel',3,1),(16,'annalise@cree.com','$2y$12$rUKMsjq/k7hbVyNHyo7HNuAtNtMXqUbcXHZRlJmPuzpynzdq9PkOu',NULL,NULL,'Durine','Anna-lise','0606060606','','','France','',2,1),(17,'rosine@client.fr','$2y$12$PXoKBHnVoYDEbpZ0GSiMOOMr4Nv0BL6temGVwKMhd5ESrcg2HEPJK',NULL,NULL,'Client','Rosine','06','Bordeaux','33000','France','1 rue du rosier',3,1),(18,'pierre@client.fr','$2y$12$h9MSMwQf/kLGYwitmAoOJ./7doyeNKRWR2z.aJt7YJs5MN.QXbtkq',NULL,NULL,'Juser','Pierre','08 36 65 65 65','Bordeaux','33000','France','12 rue du cailloux',3,1);
/*!40000 ALTER TABLE `utilisateur` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateur_avis`
--

DROP TABLE IF EXISTS `utilisateur_avis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilisateur_avis` (
  `utilisateur_id` int NOT NULL,
  `avis_id` int NOT NULL,
  PRIMARY KEY (`utilisateur_id`,`avis_id`),
  KEY `avis_id` (`avis_id`),
  CONSTRAINT `utilisateur_avis_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`),
  CONSTRAINT `utilisateur_avis_ibfk_2` FOREIGN KEY (`avis_id`) REFERENCES `avis` (`avis_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateur_avis`
--

LOCK TABLES `utilisateur_avis` WRITE;
/*!40000 ALTER TABLE `utilisateur_avis` DISABLE KEYS */;
/*!40000 ALTER TABLE `utilisateur_avis` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

ALTER TABLE utilisateur ADD COLUMN IF NOT EXISTS api_token VARCHAR(64) NULL;