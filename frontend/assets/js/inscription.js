const URL_INSCRIPTION = `${BASE_URL}/api/traitement-inscription.php`;

const zoneMessages = document.getElementById('zone-messages');
const form         = document.getElementById('form-inscription');

form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const body = new URLSearchParams();
    body.append('nom',              document.getElementById('nom').value);
    body.append('prenom',           document.getElementById('prenom').value);
    body.append('email',            document.getElementById('email').value);
    body.append('telephone',        document.getElementById('telephone').value);
    body.append('adresse_postale',  document.getElementById('adresse').value);
    body.append('code_postal',      document.getElementById('code_postal').value);
    body.append('ville',            document.getElementById('ville').value);
    body.append('password',         document.getElementById('password').value);
    body.append('password_confirm', document.getElementById('password_confirm').value);

    const reponse = await fetch(URL_INSCRIPTION, {
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
        window.scrollTo(0, 0);
    }

    if (data.succes) {
        window.location.href = '/pages/connexion.html';
    }
});