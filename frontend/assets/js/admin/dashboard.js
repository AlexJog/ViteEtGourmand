const URL_API = `${BASE_URL}/api/admin/dashboard.php`;

async function chargerDashboard() {
    const reponse = await fetch(URL_API);
    const data    = await reponse.json();

    if (data.erreur === 'non_connecte') {
        window.location.href = '../connexion.html';
        return;
    }

    if (data.erreur === 'non_autorise') {
        window.location.href = '../index.html';
        return;
    }

    document.getElementById('user-prenom').textContent  = data.prenom;
    document.getElementById('nb-menus').textContent     = data.nb_menus;
    document.getElementById('nb-commandes').textContent = data.nb_commandes;
    document.getElementById('nb-users').textContent     = data.nb_users;
    document.getElementById('nb-employes').textContent  = data.nb_employes;

    if (data.nb_avis_attente > 0) {
        document.getElementById('avis-attente').innerHTML =
            `<strong style="color: #FFC107;">⚠️ ${data.nb_avis_attente} avis en attente</strong>`;
    }
}

chargerDashboard();