const URL_API       = `${BASE_URL}/api/auth.php`;
const URL_CONNEXION = `${BASE_URL}/api/traitement-connexion.php`;

const zoneMessages = document.getElementById('zone-messages');
const form         = document.getElementById('form-connexion');

// Afficher les messages de session au chargement
async function chargerMessages() {
    const reponse = await fetch(URL_API);
    const data    = await reponse.json();

    if (data.succes) {
        zoneMessages.innerHTML = `
            <div class="alert alert-success">
                <p>${data.succes}</p>
            </div>`;
    }

    if (data.erreurs) {
        zoneMessages.innerHTML = `
            <div class="alert alert-error">
                ${data.erreurs.map(e => `<p>• ${e}</p>`).join('')}
            </div>`;
    }

    // Repré-remplir l'email si la session l'avait gardé
    if (data.form_email) {
        document.getElementById('email').value = data.form_email;
    }
}

// Soumettre le formulaire
form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('email',    document.getElementById('email').value);
    formData.append('password', document.getElementById('password').value);

    const reponse = await fetch(URL_CONNEXION, {
        method: 'POST',
        body: formData
    });

    const data = await reponse.json();

    if (data.succes) {
        // Rediriger selon le rôle retourné par le back
        window.location.href = data.redirect;
    }

    if (data.erreurs) {
        zoneMessages.innerHTML = `
            <div class="alert alert-error">
                ${data.erreurs.map(e => `<p>• ${e}</p>`).join('')}
            </div>`;
    }
});

chargerMessages();