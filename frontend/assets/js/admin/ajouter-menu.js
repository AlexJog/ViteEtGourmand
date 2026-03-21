const URL_API        = `${BASE_URL}/api/admin/ajouter-menu.php`;
const URL_TRAITEMENT = `${BASE_URL}/api/admin/traitement-menu.php`;

const zoneMessages = document.getElementById('zone-messages');
const form         = document.getElementById('form-ajouter-menu');

async function chargerOptions() {
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

    const selectRegime = document.getElementById('regime_id');
    const selectTheme  = document.getElementById('theme_id');

    data.regimes.forEach(r => {
        const option = document.createElement('option');
        option.value       = r.regime_id;
        option.textContent = r.libelle;
        selectRegime.appendChild(option);
    });

    data.themes.forEach(t => {
        const option = document.createElement('option');
        option.value       = t.theme_id;
        option.textContent = t.libelle;
        selectTheme.appendChild(option);
    });
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
        window.location.href = 'gestion-menus.html';
    }
});

chargerOptions();