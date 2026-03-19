# 🍽️ Vite & Gourmand - Backend

## 📖 Présentation
Backend de Vite & Gourmand, site web de traiteur réalisé dans le cadre de l'ECF du Titre Professionnel Développeur Web et Web Mobile.

API REST en PHP pur qui gère l'authentification, les menus, les commandes, les avis et les statistiques.

**Technologies utilisées :** PHP, MySQL, PDO, PHPMailer

---

## 📋 Prérequis
Pour installer et lancer le projet en local, il est nécessaire d’avoir :
- PHP (version 8 minimum)
- MySQL
- Composer

---

## 🚀 Installation

### 1. Cloner le dépôt
Téléchargez le projet ou clonez-le dans votre dossier de travail :
```bash
git clone https://github.com/AlexJog/vite-et-gourmand-back.git
cd vite-et-gourmand-back
```

---

### 2. Installer les dépendances
```bash
composer install
```

---

### 3. Base de données
1. Créez une base de données `vite_gourmand`
2. Importez le fichier `vite_gourmand.sql`

---

### 4. Configuration
Copiez `includes/config.example.php` en `includes/config.php` et remplissez vos identifiants :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'vite_gourmand');
define('DB_USER', 'root');
define('DB_PASS', '');
```

---

### 5. Lancer le serveur
```bash
php -S localhost:8000
```

---

## 📁 Structure

backend/
    ── api/
        ── admin/
        ── employe/
        ── utilisateur/
        ── *.php   -> tous les autres fichier .php
    ── data/
    ── includes/
        ── config.php
        ── config-heroku.php
        ── email-functions.php
        ── json-config.php
        ── mailer.php
    ── vendor/

---

## 👤 Comptes de test

**Administrateur**
- Email: `admin@vitegourmand.fr`
- Mot de passe: `Test12345.`

**Employé**
- Email: `employe@vitegourmand.fr`
- Mot de passe: `Test12345.`

**Client**
- Email: `client@client.fr`
- Mot de passe: `Test12345.`

---

## ⚠️ Remarques
- Les emails ne fonctionnent qu'avec les variables SMTP configurées
- Le dossier `data/` doit exister pour les statistiques
- Le dossier `assets/images/menus/` doit exister pour les uploads d'images