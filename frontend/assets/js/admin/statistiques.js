const URL_API  = `${BASE_URL}/api/admin/statistiques.php`;
const URL_SYNC = `${BASE_URL}/api/admin/sync-json.php`;

const zoneMessages  = document.getElementById('zone-messages');
const zoneStats     = document.getElementById('zone-stats');
const zoneGraphique = document.getElementById('zone-graphique');
const form          = document.getElementById('form-filtres');

let monChart = null;

function genererCarteStats(stat) {
    const ca       = parseFloat(stat.chiffre_affaires).toFixed(2).replace('.', ',');
    const ca_moyen = (stat.chiffre_affaires / stat.nb_commandes).toFixed(2).replace('.', ',');

    return `
        <div class="commande-carte">
            <h3 class="commande-titre">${stat.menu_nom}</h3>
            <div class="commande-details">
                <div>
                    <p><strong>📦 Nombre de commandes :</strong><br>
                    <span class="stat-nombre-commandes">${stat.nb_commandes}</span></p>
                </div>
                <div>
                    <p><strong>💰 Chiffre d'affaires :</strong><br>
                    <span class="stat-nombre-ca">${ca}€</span></p>
                </div>
            </div>
            <div class="commande-details">
                <div>
                    <p><strong>👥 Personnes servies :</strong><br>${stat.nb_personnes_total} personnes</p>
                </div>
                <div>
                    <p><strong>📊 CA moyen par commande :</strong><br>${ca_moyen}€</p>
                </div>
            </div>
        </div>`;
}

async function chargerStats(params = '') {
    const reponse = await fetchAvecToken(`${URL_API}${params ? '?' + params : ''}`);
    const data    = await reponse.json();

    if (data.erreur === 'non_connecte') {
        window.location.href = '../connexion.html';
        return;
    }

    if (data.erreur === 'non_autorise') {
        window.location.href = '../index.html';
        return;
    }

    document.getElementById('nb-total').textContent = data.nb_total;

    const selectMenu = document.getElementById('menu_id');
    if (selectMenu.options.length === 1) {
        data.menus.forEach(menu => {
            const option = document.createElement('option');
            option.value       = menu.menu_id;
            option.textContent = menu.nom;
            if (data.filtres.menu_id == menu.menu_id) option.selected = true;
            selectMenu.appendChild(option);
        });
    }

    if (data.filtres.date_debut) document.getElementById('date_debut').value = data.filtres.date_debut;
    if (data.filtres.date_fin)   document.getElementById('date_fin').value   = data.filtres.date_fin;

    if (data.stats_menus.length === 0) {
        zoneStats.innerHTML = `
            <div class="message-aucune-commande">
                <p>Aucune donnée disponible. Veuillez synchroniser les données.</p>
                <a href="#" onclick="synchroniser()" class="btn-hero">🔄 Synchroniser maintenant</a>
            </div>`;
        zoneGraphique.style.display = 'none';
        return;
    }

    zoneStats.innerHTML = `
        <div class="liste-commandes">
            ${data.stats_menus.map(genererCarteStats).join('')}
        </div>`;

    zoneGraphique.style.display = 'block';
    const labels = data.stats_menus.map(s => s.menu_nom);
    const values = data.stats_menus.map(s => s.nb_commandes);

    if (monChart) monChart.destroy();
    monChart = new Chart(document.getElementById('chartCommandes'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nombre de commandes',
                data: values,
                backgroundColor: 'rgba(255, 193, 7, 0.7)'
            }]
        }
    });
}

form.addEventListener('submit', function(e) {
    e.preventDefault();
    const params     = new URLSearchParams();
    const menu_id    = document.getElementById('menu_id').value;
    const date_debut = document.getElementById('date_debut').value;
    const date_fin   = document.getElementById('date_fin').value;
    if (menu_id)    params.append('menu_id',    menu_id);
    if (date_debut) params.append('date_debut', date_debut);
    if (date_fin)   params.append('date_fin',   date_fin);
    chargerStats(params.toString());
});

document.getElementById('btnSync').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('popupSync').style.display = 'flex';
});

document.getElementById('btnCancelSync').addEventListener('click', function() {
    document.getElementById('popupSync').style.display = 'none';
});

document.getElementById('btnConfirmSync').addEventListener('click', function() {
    document.getElementById('popupSync').style.display = 'none';
    synchroniser();
});

async function synchroniser() {
    const reponse = await fetchAvecToken(URL_SYNC, { method: 'POST' });
    const data    = await reponse.json();

    if (data.succes) {
        zoneMessages.innerHTML = `<div class="alert alert-success"><p>${data.succes}</p></div>`;
        chargerStats();
    }
    if (data.erreur) {
        zoneMessages.innerHTML = `<div class="alert alert-error"><p>${data.erreur}</p></div>`;
    }
    window.scrollTo(0, 0);
}

chargerStats();