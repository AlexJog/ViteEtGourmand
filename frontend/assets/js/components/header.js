async function chargerHeader() {
    const reponse = await fetch(`/components/header.html`);
    const html    = await reponse.text();
    document.getElementById('header').innerHTML = html;

    const token  = localStorage.getItem('token');
    const role   = localStorage.getItem('role');
    const prenom = localStorage.getItem('prenom');

    const navEspace    = document.getElementById('nav-espace');
    const navConnexion = document.getElementById('nav-connexion');
    const subNav       = document.getElementById('sub-nav');
    const subNavLiens  = document.getElementById('sub-nav-liens');
    const mobileUser   = document.getElementById('mobile-user-menu');
    const mobileConnex = document.getElementById('mobile-connexion');

    if (token && role && prenom) {
        const espaces = {
            'utilisateur' : { lien: '/pages/utilisateur/dashboard.html', texte: 'Mon espace' },
            'employe'     : { lien: '/pages/employe/dashboard.html',     texte: 'Dashboard' },
            'admin'       : { lien: '/pages/admin/dashboard.html',       texte: 'Dashboard Admin' }
        };

        const espace = espaces[role];

        // Appliquer le flex sur navEspace pour espacer les éléments
        navEspace.style.cssText = 'display: flex; align-items: center; gap: 15px;';
        navConnexion.style.display = 'none';

        navEspace.innerHTML = `
            <span class="user-greeting">Bonjour ${prenom}</span>
            <a href="${espace.lien}">${espace.texte}</a>
            <a href="#" id="btn-deconnexion" class="btn-deconnexion">Déconnexion</a>`;

        if (role === 'employe') {
            subNav.style.display = 'block';
            subNavLiens.innerHTML = `
                <li><a href="/pages/employe/dashboard.html">📊 Dashboard</a></li>
                <li><a href="/pages/employe/gestion-commandes.html">📦 Commandes</a></li>
                <li><a href="/pages/employe/gestion-avis.html">⭐ Avis clients</a></li>`;
        }

        if (role === 'admin') {
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

        mobileUser.innerHTML = `
            <div class="mobile-user-menu">
                <span class="mobile-user-greeting">Bonjour ${prenom}</span>
                ${role === 'utilisateur' ? `<a href="/pages/utilisateur/dashboard.html" class="mobile-link">Mon espace</a>` : ''}
                ${role === 'employe' ? `
                    <a href="/pages/employe/dashboard.html" class="mobile-link">📊 Dashboard</a>
                    <a href="/pages/employe/gestion-commandes.html" class="mobile-link">📦 Commandes</a>
                    <a href="/pages/employe/gestion-avis.html" class="mobile-link">⭐ Avis clients</a>` : ''}
                ${role === 'admin' ? `
                    <a href="/pages/admin/dashboard.html" class="mobile-link">📊 Dashboard</a>
                    <a href="/pages/admin/gestion-menus.html" class="mobile-link">🍽️ Menus</a>
                    <a href="/pages/employe/gestion-commandes.html" class="mobile-link">📦 Commandes</a>
                    <a href="/pages/employe/gestion-avis.html" class="mobile-link">⭐ Avis clients</a>
                    <a href="/pages/admin/gestion-employes.html" class="mobile-link">👥 Employés</a>
                    <a href="/pages/admin/statistiques.html" class="mobile-link">📈 Statistiques</a>
                    <a href="/pages/admin/creer-employe.html" class="mobile-link">➕ Créer employé</a>` : ''}
                <a href="#" id="btn-deconnexion-mobile" class="mobile-deconnexion">Déconnexion</a>
            </div>`;

        document.getElementById('btn-deconnexion')?.addEventListener('click', async function(e) {
            e.preventDefault();
            await fetch(`${BASE_URL}/api/deconnexion.php`, {
                headers: { 'X-AUTH-TOKEN': token }
            });
            localStorage.removeItem('token');
            localStorage.removeItem('role');
            localStorage.removeItem('prenom');
            window.location.href = '/pages/connexion.html';
        });

        document.getElementById('btn-deconnexion-mobile')?.addEventListener('click', async function(e) {
            e.preventDefault();
            await fetch(`${BASE_URL}/api/deconnexion.php`, {
                headers: { 'X-AUTH-TOKEN': token }
            });
            localStorage.removeItem('token');
            localStorage.removeItem('role');
            localStorage.removeItem('prenom');
            window.location.href = '/pages/connexion.html';
        });

    } else {
        navConnexion.innerHTML = `<a href="/pages/connexion.html">Connexion</a>`;
        mobileConnex.innerHTML = `<a href="/pages/connexion.html" class="mobile-link">Connexion</a>`;
        navEspace.style.display = 'none';
    }

    const burgerMenu = document.getElementById('burgerMenu');
    const mobileMenu = document.getElementById('mobileMenu');

    if (burgerMenu && mobileMenu) {
        burgerMenu.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            this.classList.toggle('active');
        });

        document.querySelectorAll('.mobile-link').forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                burgerMenu.classList.remove('active');
            });
        });
    }
}

chargerHeader();