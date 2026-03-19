const URL_API      = `${BASE_URL}/api/traitement-reset.php`;
const zoneMessages = document.getElementById('zone-messages');
const form         = document.getElementById('form-reset');

form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('email', document.getElementById('email').value);

    const reponse = await fetch(URL_API, { method: 'POST', body: formData });
    const data    = await reponse.json();

    if (data.erreurs) {
        zoneMessages.innerHTML = `
            <div class="alert alert-error">
                ${data.erreurs.map(e => `<p>• ${e}</p>`).join('')}
            </div>`;
    }

    if (data.succes) {
        zoneMessages.innerHTML = `
            <div class="alert alert-success">
                <p>${data.succes}</p>
            </div>`;
        form.reset();
    }

    window.scrollTo(0, 0);
});