const URL_API      = `${BASE_URL}/api/traitement-contact.php`;
const zoneMessages = document.getElementById('zone-messages');
const form         = document.getElementById('form-contact');

form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('email',   document.getElementById('email').value);
    formData.append('titre',   document.getElementById('titre').value);
    formData.append('message', document.getElementById('message').value);

    const reponse = await fetch(URL_API, { method: 'POST', body: formData });
    const data    = await reponse.json();

    if (data.erreurs) {
        zoneMessages.innerHTML = `
            <div class="alert alert-error">
                ${data.erreurs.map(e => `<p>• ${e}</p>`).join('')}
            </div>`;

        // Repré-remplir les champs
        if (data.form_contact) {
            document.getElementById('email').value   = data.form_contact.email;
            document.getElementById('titre').value   = data.form_contact.titre;
            document.getElementById('message').value = data.form_contact.message;
        }

        window.scrollTo(0, 0);
    }

    if (data.succes) {
        zoneMessages.innerHTML = `
            <div class="alert alert-success">
                <p>${data.succes}</p>
            </div>`;
        form.reset();
        window.scrollTo(0, 0);
    }
});