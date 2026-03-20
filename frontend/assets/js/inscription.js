const URL_CONNEXION = `${BASE_URL}/api/traitement-connexion.php`;

const zoneMessages = document.getElementById('zone-messages');
const form         = document.getElementById('form-connexion');

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

    if (data.erreurs) {
        zoneMessages.innerHTML = `
            <div class="alert alert-error">
                ${data.erreurs.map(e => `<p>• ${e}</p>`).join('')}
            </div>`;
    }

    if (data.succes) {
        // Stocker le token et les infos utilisateur
        localStorage.setItem('token',  data.token);
        localStorage.setItem('role',   data.role);
        localStorage.setItem('prenom', data.prenom);

        // Rediriger
        window.location.href = '/pages/' + data.redirect;
    }
});