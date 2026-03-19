const URL_API       = `${BASE_URL}/api/utilisateur/laisser-avis.php`;
const URL_TRAITEMENT = `${BASE_URL}/api/utilisateur/traitement-avis.php`;

const zoneMessages = document.getElementById('zone-messages');
const form         = document.getElementById('form-avis');
const params       = new URLSearchParams(window.location.search);
const commande_id  = params.get('commande_id');

async function chargerAvis() {
    if (!commande_id) {
        window.location.href = 'mes-commandes.html';
        return;
    }

    const reponse = await fetch(`${URL_API}?commande_id=${commande_id}`);
    const data    = await reponse.json();

    if (data.erreur === 'non_connecte') {
        window.location.href = '../connexion.html';
        return;
    }

    if (data.erreur === 'non_autorise') {
        window.location.href = '../index.html';
        return;
    }

    if (data.erreur === 'avis_existe' || data.erreur === 'redirect_commandes') {
        window.location.href = 'mes-commandes.html';
        return;
    }

    document.getElementById('menu-nom').textContent  = data.menu_nom;
    document.getElementById('commande_id').value     = commande_id;
}

form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(form);

    const reponse = await fetch(URL_TRAITEMENT, { method: 'POST', body: formData });
    const data    = await reponse.json();

    if (data.erreurs) {
        zoneMessages.innerHTML = `
            <div class="alert alert-error">
                ${data.erreurs.map(e => `<p>• ${e}</p>`).join('')}
            </div>`;
        window.scrollTo(0, 0);
    }

    if (data.succes) {
        window.location.href = data.redirect;
    }
});

chargerAvis();