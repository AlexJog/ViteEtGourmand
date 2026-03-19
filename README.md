# 🍽️ Vite & Gourmand

## 📖 Présentation
Vite & Gourmand est un site web de traiteur réalisé dans le cadre de l'ECF du Titre Professionnel Développeur Web et Web Mobile.

Le projet est séparé en deux parties :
- **Frontend** : Interface HTML/CSS/JS qui communique avec le backend via des appels `fetch()` vers l'API REST
- **Backend** : API REST en PHP pur qui gère l'authentification, les menus, les commandes, les avis et les statistiques

**Technologies utilisées :** HTML, CSS, JavaScript, PHP, MySQL, PDO, PHPMailer

---

## 📁 Structure du projet
```
ViteEtGourmand/
├── backend/
│   ├── api/
│   │   ├── admin/
│   │   ├── employe/
│   │   ├── utilisateur/
│   │   └── *.php
│   ├── data/
│   └── includes/
│       ├── config.example.php
│       ├── config-heroku.php
│       ├── email-functions.php
│       ├── json-config.php
│       └── mailer.php
├── frontend/
│   ├── assets/
│   │   ├── css/
│   │   ├── images/
│   │   └── js/
│   ├── components/
│   └── pages/
├── vendor/
├── composer.json
├── composer.lock
├── Procfile
└── .gitignore
```

---

## 📋 Prérequis
- PHP (version 8 minimum)
- MySQL
- Composer
- Un navigateur web

---

## 🚀 Installation en local

### 1. Cloner le dépôt
```bash
git clone https://github.com/AlexJog/ViteEtGourmand.git
cd ViteEtGourmand
```

### 2. Installer les dépendances
```bash
composer install
```

### 3. Base de données
1. Créez une base de données `vite_gourmand`
2. Importez le fichier `vite_gourmand.sql`

### 4. Configuration du backend
Copiez `backend/includes/config.example.php` en `backend/includes/config.php` et remplissez vos identifiants :
```php
define('BASE_URL', '/ViteEtGourmand/');
define('DB_HOST', 'localhost');
define('DB_NAME', 'vite_gourmand');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 5. Configuration du frontend
Dans `frontend/assets/js/config.js`, renseignez l'URL de votre backend :
```javascript
const BASE_URL = 'http://localhost:8000';
```

### 6. Lancer le backend
```bash
php -S localhost:8000 -t backend/
```

### 7. Lancer le frontend
```bash
php -S localhost:3000 -t frontend/
```
Puis accédez à : `http://localhost:3000/pages/index.html`

---

## 👤 Comptes de test

**Administrateur**
- Email : `admin@vitegourmand.fr`
- Mot de passe : `Test12345.`

**Employé**
- Email : `employe@vitegourmand.fr`
- Mot de passe : `Test12345.`

**Client**
- Email : `client@client.fr`
- Mot de passe : `Test12345.`

---

## ⚠️ Remarques
- Le frontend ne fonctionne pas sans le backend
- Les emails ne fonctionnent qu'avec les variables SMTP configurées
- Le dossier `backend/data/` doit exister pour les statistiques
- Le dossier `frontend/assets/images/menus/` doit exister pour les uploads d'images
- Modifier `frontend/assets/js/config.js` si le backend tourne sur un port différent