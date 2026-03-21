const URL_API     = `${BASE_URL}/api/employe/gestion-avis.php`;
const URL_TRAITER = `${BASE_URL}/api/employe/traiter-avis.php`;

const zoneMessages = document.getElementById('zone-messages');
const zoneStats    = document.getElementById('zone-stats');
const zoneAvis     = document.getElementById('zone-avis');

const params = new URLSearchParams(window.location.search);
const filtre = params.get('statut') || 'tous';

const statutsBadge = {
    'en attente' : { couleur: '#FFC107', texte: '⏳ En attente' },
    'validé'     : { couleur: '#28A745', texte: '✅ Validé' },
    'refusé'     : { couleur: '#DC3545', texte: '❌ Refusé' }
};

function genererEtoiles(note) {
    let etoiles = '';
    for (let i = 1; i <= 5; i++) {
        etoiles += i <= note ? '★' : '☆';
    }
    return etoiles;
}

function genererCarteAvis(avis) {
    const badge = statutsBadge[avis.statut] || { couleur: '#6C757D', texte: avis.statut };
    const date  = new Date(avis.date_avis).toLocaleString('fr-FR', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });

    const boutons = avis.statut === 'en attente' ? `
        <div class="avis-actions">
            <button class="btn-avis-valider" onclick="traiterAvis(${avis.avis_id}, 'valider')">✅ Valider</button>
            <button class="btn-avis-refuser" onclick="traiterAvis(${avis.avis_id}, 'refuser')">❌ Refuser</button>
        </div>` : `<div class="avis-statut-final"><p>Avis ${avis.statut}</p></div>`;

    return `
        <div class="avis-carte-gestion">
            <div class="avis-gestion-header">
                <div>
                    <h3 class="avis-gestion-nom">${avis.prenom} ${avis.nom}</h3>
                    <p class="avis-gestion-info">Menu : ${avis.menu_nom} • ${date}</p>
                </div>
                <div class="badge-statut" style="background-color: ${badge.couleur};">${badge.texte}</div>
            </div>

            <div class="avis-note-section">
                <p><strong>Note :</strong></p>
                <div class="avis-etoiles">
                    ${genererEtoiles(avis.note)}
                    <span class="avis-note-texte">(${avis.note}/5)</span>
                </div>
            </div>

            <div class="avis-commentaire-box">
                <p><strong>💬 Commentaire :</strong><br>
                ${avis.commentaire.replace(/\n/g, '<br>')}</p>
            </div>

            ${boutons}
        </div>`;
}

async function traiterAvis(avis_id, action) {
    const formData = new FormData();
    formData.append('avis_id', avis_id);
    formData.append('action',  action);

    const reponse = await fetchAvecToken(URL_TRAITER, { method: 'POST', body: formData });
    const data    = await reponse.json();

    if (data.succes) {
        chargerAvis();
    }

    if (data.erreur) {
        zoneMessages.innerHTML = `<div class="alert alert-error"><p>${data.erreur}</p></div>`;
        window.scrollTo(0, 0);
    }
}

async function chargerAvis() {
    const reponse = await fetchAvecToken(`${URL_API}?statut=${encodeURIComponent(filtre)}`);
    const data    = await reponse.json();

    if (data.erreur === 'non_connecte') {
        window.location.href = '../connexion.html';
        return;
    }

    if (data.erreur === 'non_autorise') {
        window.location.href = '../index.html';
        return;
    }

    const total = Object.values(data.stats).reduce((a, b) => a + b, 0);

    zoneStats.innerHTML = `
        <a href="?statut=tous" class="stat-card-link">
            <div class="dashboard-card stat-card ${filtre === 'tous' ? 'active-tous' : ''}">
                <h3>📝 Tous</h3>
                <p class="stat-card-number stat-number-all">${total}</p>
            </div>
        </a>
        <a href="?statut=en attente" class="stat-card-link">
            <div class="dashboard-card stat-card ${filtre === 'en attente' ? 'active-attente' : ''}">
                <h3>⏳ En attente</h3>
                <p class="stat-card-number stat-number-attente">${data.stats['en attente'] ?? 0}</p>
            </div>
        </a>
        <a href="?statut=validé" class="stat-card-link">
            <div class="dashboard-card stat-card ${filtre === 'validé' ? 'active-livre' : ''}">
                <h3>✅ Validés</h3>
                <p class="stat-card-number stat-number-livre">${data.stats['validé'] ?? 0}</p>
            </div>
        </a>
        <a href="?statut=refusé" class="stat-card-link">
            <div class="dashboard-card stat-card ${filtre === 'refusé' ? 'active-refusee' : ''}">
                <h3>❌ Refusés</h3>
                <p class="stat-card-number stat-number-refusee">${data.stats['refusé'] ?? 0}</p>
            </div>
        </a>`;

    if (data.avis.length === 0) {
        const msg = filtre === 'tous'
            ? 'Aucun avis pour le moment.'
            : `Aucun avis avec le statut "${filtre}".`;
        zoneAvis.innerHTML = `<div class="message-aucune-commande-gestion"><p>${msg}</p></div>`;
        return;
    }

    zoneAvis.innerHTML = `
        <div class="liste-avis-gestion">
            ${data.avis.map(genererCarteAvis).join('')}
        </div>`;
}

chargerAvis();