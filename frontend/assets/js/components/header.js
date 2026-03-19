async function chargerHeader() {
    // Charger le HTML du header
    const reponse = await fetch(`/components/header.html`);
    const html    = await reponse.text();
    document.getElementById('header').innerHTML = html;

    // Récupérer la session
    const sessionReponse = await fetch(`${BASE_URL}/api/session.php`);
    const session        = await sessionReponse.json();

    const navEspace    = document.getElementById('nav-espace');
    const navConnexion = document.getElementById('nav-connexion');
    const subNav       = document.getElementById('sub-nav');
    const subNavLiens  = document.getElementById('sub-nav-liens');
    const mobileUser   = document.getElementById('mobile-user-menu');
    const mobileConnex = document.getElementById('mobile-connexion');

    if (session.connecte) {
        // Lien espace selon rôle
        const espaces = {
            'utilisateur' : { lien: '/pages/utilisateur/dashboard.html', texte: 'Mon espace' },
            'employe'     : { lien: '/pages/employe/dashboard.html',     texte: 'Dashboard' },
            'admin'       : { lien: '/pages/admin/dashboard.html',       texte: 'Dashboard Admin' }
        };

        const espace = espaces[session.role];
        navEspace.innerHTML = `
            <span class="user-greeting">Bonjour ${session.prenom}</span>
            <a href="${espace.lien}">${espace.texte}</a>
            <a href="#" id="btn-deconnexion" class="btn-deconnexion">Déconnexion</a>`;

        // Sous-menu employé
        if (session.role === 'employe') {
            subNav.style.display = 'block';
            subNavLiens.innerHTML = `
                <li><a href="/pages/employe/dashboard.html">📊 Dashboard</a></li>
                <li><a href="/pages/employe/gestion-commandes.html">📦 Commandes</a></li>
                <li><a href="/pages/employe/gestion-avis.html">⭐ Avis clients</a></li>`;
        }

        // Sous-menu admin
        if (session.role === 'admin') {
            subNav.style.display = 'block';
            subNavLiens.innerHTML = `
                <li><a href="/pages/admin/dashboard.html">📊 Dashboard</a></li>
                <li><a href="/pages/admin/gestion-menus.html">🍽️ Menus</a></li>
                <li><a href="/pages/employe/gestion-commandes.html">📦 Commandes</a></li>
                <li><a href="/pages/employe/gestion-avis.html">⭐ Avis clients</a></li>
                <li><a href="/pages/admin/gestion-employes.html">👥 Employés</a></li>
                <li><a href="/pages/admin/statistiques.html">📈 Statistiques</a></li>
                <li><a href="/pages/admin/creer-employe.html">➕ Créer employé</a></li>`;
        }

        // Menu mobile connecté
        mobileUser.innerHTML = `
            <div class="mobile-user-menu">
                <span class="mobile-user-greeting">Bonjour ${session.prenom}</span>
                ${session.role === 'utilisateur' ? `<a href="/pages/utilisateur/dashboard.html" class="mobile-link">Mon espace</a>` : ''}
                ${session.role === 'employe' ? `
                    <a href="/pages/employe/dashboard.html" class="mobile-link">📊 Dashboard</a>
                    <a href="/pages/employe/gestion-commandes.html" class="mobile-link">📦 Commandes</a>
                    <a href="/pages/employe/gestion-avis.html" class="mobile-link">⭐ Avis clients</a>` : ''}
                ${session.role === 'admin' ? `
                    <a href="/pages/admin/dashboard.html" class="mobile-link">📊 Dashboard</a>
                    <a href="/pages/admin/gestion-menus.html" class="mobile-link">🍽️ Menus</a>
                    <a href="/pages/employe/gestion-commandes.html" class="mobile-link">📦 Commandes</a>
                    <a href="/pages/employe/gestion-avis.html" class="mobile-link">⭐ Avis clients</a>
                    <a href="/pages/admin/gestion-employes.html" class="mobile-link">👥 Employés</a>
                    <a href="/pages/admin/statistiques.html" class="mobile-link">📈 Statistiques</a>
                    <a href="/pages/admin/creer-employe.html" class="mobile-link">➕ Créer employé</a>` : ''}
                <a href="#" id="btn-deconnexion-mobile" class="mobile-deconnexion">Déconnexion</a>
            </div>`;

        // Déconnexion
        document.getElementById('btn-deconnexion')?.addEventListener('click', async function(e) {
            e.preventDefault();
            await fetch(`${BASE_URL}/api/deconnexion.php`);
            window.location.href = '/pages/connexion.html';
        });

        document.getElementById('btn-deconnexion-mobile')?.addEventListener('click', async function(e) {
            e.preventDefault();
            await fetch(`${BASE_URL}/api/deconnexion.php`);
            window.location.href = '/pages/connexion.html';
        });

    } else {
        // Non connecté
        navConnexion.innerHTML = `<a href="/pages/connexion.html">Connexion</a>`;
        mobileConnex.innerHTML = `<a href="/pages/connexion.html" class="mobile-link">Connexion</a>`;
    }

    // Burger menu
    const burgerMenu = document.getElementById('burgerMenu');
    const mobileMenu = document.getElementById('mobileMenu');

    if (burgerMenu && mobileMenu) {
        burgerMenu.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            this.classList.toggle('active');
        });

        // Fermer au clic sur un lien
        document.querySelectorAll('.mobile-link').forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                burgerMenu.classList.remove('active');
            });
        });
    }
}

chargerHeader();