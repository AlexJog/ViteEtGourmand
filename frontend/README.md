# 🍽️ Vite & Gourmand - Frontend

## 📖 Présentation
Frontend de Vite & Gourmand, site web de traiteur réalisé dans le cadre de l'ECF du Titre Professionnel Développeur Web et Web Mobile.

Interface HTML/CSS/JS qui communique avec le backend via des appels fetch() vers l'API REST.

**Technologies utilisées :** HTML, CSS, JavaScript

---

## 📋 Prérequis
- Un navigateur web
- Le backend doit être lancé et accessible

---

## 🚀 Installation du projet

### 1. Cloner le dépôt
```bash
git clone https://github.com/AlexJog/vite-et-gourmand-front.git
cd vite-et-gourmand-front
```

---

### 2. Configuration
Dans `assets/js/config.js`, renseignez l'URL de votre backend :
```javascript
const BASE_URL = 'http://localhost:8000';
```

---

### 3. Lancer le projet
Ouvrez simplement `pages/index.html` dans votre navigateur ou utilisez un serveur local :
```bash
php -S localhost:3000
```
Puis accédez à : `http://localhost:3000/pages/index.html`

---

## 📁 Structure

frontend/
    ── assets/
        ── css/
        ── images/
        ── js/
    ── components/
    ── pages/

---

## ⚠️ Remarques
- Le frontend ne fonctionne pas sans le backend
- Modifier `assets/js/config.js` si le backend tourne sur un port différent