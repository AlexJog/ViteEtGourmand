const URL_API = `${BASE_URL}/api/menu-detail.php`;
const params  = new URLSearchParams(window.location.search);
const menu_id = params.get('id');

if (!menu_id) window.location.href = 'menus.html';

const labels = ['Entrée', 'Plat', 'Dessert'];

async function chargerMenuDetail() {
    const reponse = await fetch(`${URL_API}?id=${menu_id}`);
    const data    = await reponse.json();

    if (data.erreur) {
        window.location.href = 'menus.html';
        return;
    }

    const { menu, plats, allergenes } = data;

    // Remplir les zones une par une
    document.getElementById('menu-image').src             = menu.image_url || '/assets/images/menus/default.jpg';
    document.getElementById('menu-image').alt             = menu.nom;
    document.getElementById('menu-nom').textContent       = menu.nom;
    document.getElementById('menu-description').textContent = menu.description;
    document.getElementById('menu-service').textContent   = menu.service;
    document.getElementById('menu-prix').textContent      = `${parseFloat(menu.prix_par_personne).toFixed(2).replace('.', ',')}€ / Personne`;
    document.getElementById('menu-personnes').textContent = `Minimum : ${menu.personne_minimum} personnes`;
    document.getElementById('menu-stock').textContent     = `Stock : ${menu.quantite_restante} commandes disponibles`;
    document.getElementById('menu-theme').textContent     = menu.theme_nom ? `Thème : ${menu.theme_nom}` : '';
    document.getElementById('menu-regime').textContent    = menu.regime_nom ? `Régime : ${menu.regime_nom}` : '';
    document.getElementById('btn-commander').href         = `commander.html?menu=${menu.menu_id}`;

    // Remplir les plats
    plats.forEach((plat, index) => {
        const li = document.createElement('li');
        li.innerHTML = `${labels[index] ? `<strong>${labels[index]} :</strong>` : ''} ${plat.nom_plat}`;
        document.getElementById('menu-plats').appendChild(li);
    });

    // Remplir les allergènes
    if (allergenes.length === 0) {
        document.getElementById('menu-allergenes').innerHTML = '<p><em>Aucun allergène majeur déclaré.</em></p>';
    } else {
        allergenes.forEach(a => {
            const li = document.createElement('li');
            li.textContent = a.libelle;
            document.getElementById('menu-allergenes').appendChild(li);
        });
    }
}

chargerMenuDetail();