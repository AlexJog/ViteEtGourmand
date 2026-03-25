const URL_API = `${BASE_URL}/api/menus.php`;

const grille        = document.getElementById('grille-menus');
const form          = document.getElementById('form-filtres');
const btnReinit     = document.getElementById('btn-reinitialiser');

// Charger les menus (avec ou sans filtres)
async function chargerMenus(params = '') {
    grille.innerHTML = '<p>Chargement...</p>';

    const reponse = await fetch(URL_API + (params ? '?' + params : ''));
    const menus   = await reponse.json();

    if (menus.length === 0) {
        grille.innerHTML = '<p class="message-aucun-resultat">Aucun menu ne correspond à vos critères.</p>';
        return;
    }

    grille.innerHTML = menus.map(menu => {
        const image = menu.image_url || '/assets/images/menus/menu-default.jpg';
        const prix  = parseFloat(menu.prix_par_personne).toFixed(2).replace('.', ',');

        return `
            <article class="menu-card">
                <div class="menu-image">
                    <img src="${image}" alt="${menu.nom}">
                </div>
                <div class="menu-content">
                    <h3>${menu.nom}</h3>
                    <p>${menu.description}</p>
                    <div class="menu-info">
                        <p class="menu-prix">${prix}€ / Pers</p>
                        <p class="menu-personnes">Min. ${menu.personne_minimum} personnes</p>
                    </div>
                    <a href="menu-detail.html?id=${menu.menu_id}" class="btn-voir-detail">Voir détail</a>
                </div>
            </article>
        `;
    }).join('');
}

// Soumission du formulaire de filtres
form.addEventListener('submit', function(e) {
    e.preventDefault();

    const params = new URLSearchParams();

    const prix  = document.getElementById('prixMax').value;
    const regime = document.getElementById('regime').value;
    const theme  = document.getElementById('theme').value;
    const nb     = document.getElementById('nbPersonnes').value;

    if (prix)   params.append('prixMax', prix);
    if (regime) params.append('regime', regime);
    if (theme)  params.append('theme', theme);
    if (nb)     params.append('nbPersonnes', nb);

    btnReinit.style.display = params.toString() ? 'inline' : 'none';

    chargerMenus(params.toString());
});

// Réinitialiser les filtres
btnReinit.addEventListener('click', function(e) {
    e.preventDefault();
    form.reset();
    btnReinit.style.display = 'none';
    chargerMenus();
});

// Chargement initial
chargerMenus();