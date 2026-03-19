const URL_API      = `${BASE_URL}/api/commander.php`;
const URL_COMMANDE = `${BASE_URL}/api/traitement-commande.php`;

const zoneMessages = document.getElementById('zone-messages');
const form         = document.getElementById('form-commande');
const params       = new URLSearchParams(window.location.search);
const menu_id      = params.get('menu');

if (!menu_id) window.location.href = 'menus.html';

// Charger les infos du menu
async function chargerCommande() {
    const reponse = await fetch(`${URL_API}?menu=${menu_id}`);
    const data    = await reponse.json();

    // Non connecté → rediriger vers connexion
    if (data.erreur === 'non_connecte') {
        window.location.href = 'connexion.html';
        return;
    }

    // Stock épuisé → retour au détail
    if (data.erreur === 'stock_epuise') {
        window.location.href = `menu-detail.html?id=${data.menu_id}`;
        return;
    }

    // Menu introuvable
    if (data.erreur) {
        window.location.href = 'menus.html';
        return;
    }

    const menu = data.menu;
    const prix = parseFloat(menu.prix_par_personne);

    // Remplir le formulaire
    document.getElementById('menu_id').value            = menu.menu_id;
    document.getElementById('menu-nom').textContent     = menu.nom;
    document.getElementById('recap-nom').textContent    = menu.nom;
    document.getElementById('recap-service').textContent = menu.service;
    document.getElementById('recap-regime').textContent  = menu.regime_nom;
    document.getElementById('recap-stock').textContent   = menu.quantite_restante;
    document.getElementById('hint-personnes').textContent = `Minimum : ${menu.personne_minimum} personnes`;
    document.getElementById('prix-par-personne').textContent = `${prix.toFixed(2).replace('.', ',')}€`;

    // Configurer le champ nombre de personnes
    const inputPersonnes = document.getElementById('nombre_personnes');
    inputPersonnes.min   = menu.personne_minimum;
    inputPersonnes.value = menu.personne_minimum;
    inputPersonnes.dataset.prix    = prix;
    inputPersonnes.dataset.minimum = menu.personne_minimum;

    // Date minimum (aujourd'hui + 7 jours)
    const dateMin = new Date();
    dateMin.setDate(dateMin.getDate() + 7);
    document.getElementById('date_prestation').min = dateMin.toISOString().split('T')[0];

    // Adresse utilisateur pré-remplie
    if (data.user_adresse) {
        document.getElementById('adresse_livraison').value = data.user_adresse;
    }

    // Calcul initial du prix
    calculerPrix();
}

// Calcul du prix (ton ancien commande.js, on garde la même logique)
function calculerPrix() {
    const inputPersonnes  = document.getElementById('nombre_personnes');
    const prix            = parseFloat(inputPersonnes.dataset.prix) || 0;
    const minimum         = parseInt(inputPersonnes.dataset.minimum) || 1;
    const nbPersonnes     = parseInt(inputPersonnes.value) || minimum;
    const horsbordeaux    = document.getElementById('hors_bordeaux').checked;
    const kilometres      = parseFloat(document.getElementById('kilometres').value) || 0;

    let prixMenu     = prix * nbPersonnes;
    let reduction    = 0;
    let fraisLivraison = 0;

    // Réduction 10% si plus de 50 personnes
    if (nbPersonnes >= 50) {
        reduction = prixMenu * 0.10;
        prixMenu  = prixMenu - reduction;
        document.getElementById('ligne_reduction').classList.remove('ligne-reduction-hidden');
        document.getElementById('montant_reduction').textContent = `${reduction.toFixed(2).replace('.', ',')}€`;
    } else {
        document.getElementById('ligne_reduction').classList.add('ligne-reduction-hidden');
    }

    // Frais livraison hors Bordeaux
    if (horsbordeaux) {
        fraisLivraison = 5 + (kilometres * 0.59);
    }

    const total = prixMenu + fraisLivraison;

    document.getElementById('prix_menu').textContent      = `${prixMenu.toFixed(2).replace('.', ',')}€`;
    document.getElementById('frais_livraison').textContent = `${fraisLivraison.toFixed(2).replace('.', ',')}€`;
    document.getElementById('prix_total').textContent      = `${total.toFixed(2).replace('.', ',')}€`;
}

// Événements pour recalculer le prix
document.getElementById('nombre_personnes').addEventListener('input', calculerPrix);
document.getElementById('hors_bordeaux').addEventListener('change', function() {
    document.getElementById('div_kilometres').style.display = this.checked ? 'block' : 'none';
    calculerPrix();
});
document.getElementById('kilometres').addEventListener('input', calculerPrix);

// Soumission du formulaire
form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(form);

    const reponse = await fetch(URL_COMMANDE, { method: 'POST', body: formData });
    const data    = await reponse.json();

    if (data.erreurs) {
        zoneMessages.innerHTML = `
            <div class="alert alert-error">
                ${data.erreurs.map(e => `<p>• ${e}</p>`).join('')}
            </div>`;
        window.scrollTo(0, 0);
    }

    if (data.succes) {
        window.location.href = `index.html`;
    }
});

chargerCommande();