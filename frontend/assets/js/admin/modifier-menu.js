const URL_API        = `${BASE_URL}/api/admin/modifier-menu.php`;
const URL_TRAITEMENT = `${BASE_URL}/api/admin/traitement-menu.php`;

const zoneMessages = document.getElementById('zone-messages');
const form         = document.getElementById('form-modifier-menu');
const params       = new URLSearchParams(window.location.search);
const menu_id      = params.get('id');

if (!menu_id) window.location.href = 'gestion-menus.html';

async function chargerMenu() {
    const reponse = await fetchAvecToken(`${URL_API}?id=${menu_id}`);
    const data    = await reponse.json();

    if (data.erreur === 'non_connecte') {
        window.location.href = '../connexion.html';
        return;
    }

    if (data.erreur === 'non_autorise' || data.erreur === 'redirect') {
        window.location.href = 'gestion-menus.html';
        return;
    }

    const menu = data.menu;

    document.getElementById('menu_id').value           = menu.menu_id;
    document.getElementById('menu-nom').textContent    = menu.nom;
    document.getElementById('nom').value               = menu.nom;
    document.getElementById('description').value       = menu.description;
    document.getElementById('prix_par_personne').value = menu.prix_par_personne;
    document.getElementById('personne_minimum').value  = menu.personne_minimum;
    document.getElementById('quantite_restante').value = menu.quantite_restante;
    document.getElementById('service').value           = menu.service;

    if (menu.image_url) {
        document.getElementById('image-actuelle').src                  = menu.image_url;
        document.getElementById('zone-image-actuelle').style.display   = 'block';
    }

    const selectRegime = document.getElementById('regime_id');
    const selectTheme  = document.getElementById('theme_id');

    data.regimes.forEach(r => {
        const option = document.createElement('option');
        option.value       = r.regime_id;
        option.textContent = r.libelle;
        if (r.regime_id == menu.regime_id) option.selected = true;
        selectRegime.appendChild(option);
    });

    data.themes.forEach(t => {
        const option = document.createElement('option');
        option.value       = t.theme_id;
        option.textContent = t.libelle;
        if (t.theme_id == menu.theme_id) option.selected = true;
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

chargerMenu();