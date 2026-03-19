const URL_TRAITEMENT = `${BASE_URL}/api/admin/traitement-employe.php`;

const zoneMessages = document.getElementById('zone-messages');
const form         = document.getElementById('form-creer-employe');

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
        zoneMessages.innerHTML = `
            <div class="alert alert-success">
                <p>${data.succes}</p>
            </div>`;
        form.reset();
        window.scrollTo(0, 0);
    }
});