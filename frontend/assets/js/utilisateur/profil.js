const URL_API        = `${BASE_URL}/api/utilisateur/profil.php`;
const URL_TRAITEMENT = `${BASE_URL}/api/utilisateur/traitement-profil.php`;

const zoneMessages = document.getElementById('zone-messages');
const form         = document.getElementById('form-profil');

async function chargerProfil() {
    const reponse = await fetchAvecToken(URL_API);
    const data    = await reponse.json();

    if (data.erreur === 'non_connecte') {
        window.location.href = '../connexion.html';
        return;
    }

    if (data.erreur === 'non_autorise') {
        window.location.href = '../index.html';
        return;
    }

    const user = data.user;

    document.getElementById('nom').value             = user.nom;
    document.getElementById('prenom').value          = user.prenom;
    document.getElementById('email').value           = user.email;
    document.getElementById('telephone').value       = user.telephone;
    document.getElementById('adresse_postale').value = user.adresse_postale;
    document.getElementById('code_postal').value     = user.code_postal;
    document.getElementById('ville').value           = user.ville;
    document.getElementById('pays').value            = user.pays;
}

form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(form);

    const reponse = await fetchAvecToken(URL_TRAITEMENT, { method: 'POST', body: formData });
    const data    = await reponse.json();

    if (data.erreurs) {
        zoneMessages.innerHTML = `
            <div class="alert alert-error">
                ${data.erreurs.map(e => `<p>• ${e}</p>`).join('')}
            </div>`;
        window.scrollTo(0, 0);
    }

    if (data.succes) {
        // Mettre à jour le prenom dans localStorage si changé
        if (data.prenom) {
            localStorage.setItem('prenom', data.prenom);
        }
        window.location.href = data.redirect;
    }
});

chargerProfil();