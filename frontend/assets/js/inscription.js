const URL_API = `${BASE_URL}/api/traitement-inscription.php`;

const zoneMessages = document.getElementById('zone-messages');
const form         = document.getElementById('form-inscription');

form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('nom',              document.getElementById('nom').value);
    formData.append('prenom',           document.getElementById('prenom').value);
    formData.append('email',            document.getElementById('email').value);
    formData.append('telephone',        document.getElementById('telephone').value);
    formData.append('adresse',          document.getElementById('adresse').value);
    formData.append('code_postal',      document.getElementById('code_postal').value);
    formData.append('ville',            document.getElementById('ville').value);
    formData.append('password',         document.getElementById('password').value);
    formData.append('password_confirm', document.getElementById('password_confirm').value);

    const reponse = await fetch(URL_API, { method: 'POST', body: formData });
    const data    = await reponse.json();

    if (data.erreurs) {
        zoneMessages.innerHTML = `
            <div class="alert alert-error">
                ${data.erreurs.map(e => `<p>• ${e}</p>`).join('')}
            </div>`;

        // Repré-remplir les champs si le back renvoie form_data
        if (data.form_data) {
            Object.keys(data.form_data).forEach(champ => {
                const input = document.getElementById(champ);
                if (input) input.value = data.form_data[champ];
            });
        }

        // Remonter en haut pour voir les erreurs
        window.scrollTo(0, 0);
    }

    if (data.succes) {
        window.location.href = data.redirect;
    }
});