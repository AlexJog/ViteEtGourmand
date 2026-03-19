const URL_VERIFIER = `${BASE_URL}/api/verifier-token.php`;
const URL_API      = `${BASE_URL}/api/traitement-nouveau-mdp.php`;

const zoneMessages = document.getElementById('zone-messages');
const form         = document.getElementById('form-nouveau-mdp');
const params       = new URLSearchParams(window.location.search);
const token        = params.get('token');

// Vérifier le token au chargement
async function verifierToken() {
    if (!token) {
        window.location.href = 'connexion.html';
        return;
    }

    const reponse = await fetch(`${URL_VERIFIER}?token=${token}`);
    const data    = await reponse.json();

    if (data.erreur === 'expired') {
        window.location.href = 'mot-de-passe-oublie.html';
        return;
    }

    if (data.erreur) {
        window.location.href = 'connexion.html';
        return;
    }

    // Token valide — afficher le prénom et remplir le champ caché
    document.getElementById('user-prenom').textContent = data.prenom;
    document.getElementById('token').value             = token;
}

// Soumission du formulaire
form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('token',            document.getElementById('token').value);
    formData.append('password',         document.getElementById('password').value);
    formData.append('password_confirm', document.getElementById('password_confirm').value);

    const reponse = await fetch(URL_API, { method: 'POST', body: formData });
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

verifierToken();