const URL_API       = `${BASE_URL}/api/admin/gestion-menus.php`;
const URL_SUPPRIMER = `${BASE_URL}/api/admin/supprimer-menu.php`;

const zoneMessages = document.getElementById('zone-messages');
const listeMenus   = document.getElementById('liste-menus');

function afficherPopup(menu_id) {
    document.getElementById('popupSuppression').style.display = 'flex';
    document.getElementById('lienSuppression').onclick = function(e) {
        e.preventDefault();
        supprimerMenu(menu_id);
    };
}

function fermerPopup() {
    document.getElementById('popupSuppression').style.display = 'none';
}

async function supprimerMenu(menu_id) {
    const formData = new FormData();
    formData.append('menu_id', menu_id);

    const reponse = await fetchAvecToken(URL_SUPPRIMER, { method: 'POST', body: formData });
    const data    = await reponse.json();

    fermerPopup();

    if (data.succes) {
        zoneMessages.innerHTML = `<div class="alert alert-success"><p>${data.succes}</p></div>`;
        chargerMenus();
    }

    if (data.erreur) {
        zoneMessages.innerHTML = `<div class="alert alert-error"><p>${data.erreur}</p></div>`;
    }

    window.scrollTo(0, 0);
}

function genererCarteMenu(menu) {
    const image = menu.image_url || '../../assets/images/menus/menu-default.jpg';
    const prix  = parseFloat(menu.prix_par_personne).toFixed(2).replace('.', ',');

    return `
        <div class="menu-admin-carte">
            <img src="${image}" alt="${menu.nom}" class="menu-admin-image">
            <div class="menu-admin-infos">
                <h3 class="menu-admin-titre">${menu.nom}</h3>
                <p class="menu-admin-details">
                    <strong>Service :</strong> ${menu.service} |
                    <strong>Régime :</strong> ${menu.regime_nom} |
                    <strong>Thème :</strong> ${menu.theme_nom}
                </p>
                <p class="menu-admin-prix">
                    <strong>Prix :</strong> ${prix}€/pers |
                    <strong>Min :</strong> ${menu.personne_minimum} pers |
                    <strong>Stock :</strong> ${menu.quantite_restante}
                </p>
            </div>
            <div class="menu-admin-actions">
                <a href="modifier-menu.html?id=${menu.menu_id}" class="btn-menu-modifier">✏️ Modifier</a>
                <button onclick="afficherPopup(${menu.menu_id})" class="btn-menu-supprimer">🗑️ Supprimer</button>
            </div>
        </div>`;
}

async function chargerMenus() {
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

    if (data.menus.length === 0) {
        listeMenus.innerHTML = `<p class="message-aucun-menu">Aucun menu disponible.</p>`;
        return;
    }

    listeMenus.innerHTML = data.menus.map(genererCarteMenu).join('');
}

chargerMenus();