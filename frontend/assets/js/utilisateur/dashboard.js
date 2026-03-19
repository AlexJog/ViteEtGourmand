const URL_API = `${BASE_URL}/api/utilisateur/dashboard.php`;

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
    document.getElementById('nb-commandes').textContent = data.nb_commandes;
    document.getElementById('pluriel').textContent      = data.nb_commandes > 1 ? 's' : '';
}

chargerDashboard();