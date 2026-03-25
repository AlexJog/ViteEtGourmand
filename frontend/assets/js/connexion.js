const URL_CONNEXION = `${BASE_URL}/api/traitement-connexion.php`;

document.addEventListener('DOMContentLoaded', function() {
    const zoneMessages = document.getElementById('zone-messages');
    const form         = document.getElementById('form-connexion');

    // Message de succès après inscription
    const inscriptionSucces = sessionStorage.getItem('inscription_succes');
    if (inscriptionSucces === 'true') {
        zoneMessages.innerHTML = `
            <div class="alert alert-success">
                <p>✅ Votre compte a été créé avec succès ! Connectez-vous.</p>
            </div>`;
        sessionStorage.removeItem('inscription_succes');
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const body = new URLSearchParams();
        body.append('email',    document.getElementById('email').value);
        body.append('password', document.getElementById('password').value);

        const reponse = await fetch(URL_CONNEXION, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body
        });

        const data = await reponse.json();

        if (data.erreurs) {
            zoneMessages.innerHTML = `
                <div class="alert alert-error">
                    ${data.erreurs.map(e => `<p>• ${e}</p>`).join('')}
                </div>`;
        }

        if (data.succes) {
            localStorage.setItem('token',  data.token);
            localStorage.setItem('role',   data.role);
            localStorage.setItem('prenom', data.prenom);
            sessionStorage.setItem('first_login', 'true');
            window.location.href = '/pages/' + data.redirect;
        }
    });
});