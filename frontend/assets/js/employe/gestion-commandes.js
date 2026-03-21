const URL_API   = `${BASE_URL}/api/employe/gestion-commandes.php`;
const URL_TRAITER = `${BASE_URL}/api/employe/traiter-commande.php`;

const zoneMessages  = document.getElementById('zone-messages');
const zoneStats     = document.getElementById('zone-stats');
const zoneCommandes = document.getElementById('zone-commandes');

const params  = new URLSearchParams(window.location.search);
const filtre  = params.get('statut') || 'tous';

const statuts = {
    'en attente'       : { couleur: '#FFC107', texte: '⏳ En attente' },
    'accepté'          : { couleur: '#17A2B8', texte: '✅ Accepté' },
    'en préparation'   : { couleur: '#FD7E14', texte: '👨‍🍳 En préparation' },
    'en livraison'     : { couleur: '#007BFF', texte: '🚚 En livraison' },
    'livré'            : { couleur: '#28A745', texte: '📦 Livré' },
    'attente matériel' : { couleur: '#FF9800', texte: '🔄 Attente matériel' },
    'terminée'         : { couleur: '#6B8E23', texte: '✅ Terminée' },
    'refusée'          : { couleur: '#DC3545', texte: '❌ Refusée' }
};

function formaterDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('fr-FR');
}

function formaterDateHeure(dateStr) {
    return new Date(dateStr).toLocaleString('fr-FR', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}

function formaterHeure(heureStr) {
    return heureStr.substring(0, 5);
}

function genererBoutons(commande) {
    const id = commande.commande_id;

    if (commande.statut === 'en attente') return `
        <button class="btn-action btn-accepter"  onclick="changerStatut(${id}, 'accepté')">✅ Accepter</button>
        <button class="btn-action btn-refuser"   onclick="changerStatut(${id}, 'refusée')">❌ Refuser</button>`;

    if (commande.statut === 'accepté') return `
        <button class="btn-action btn-preparation" onclick="changerStatut(${id}, 'en préparation')">👨‍🍳 Passer en préparation</button>`;

    if (commande.statut === 'en préparation') return `
        <button class="btn-action btn-livraison" onclick="changerStatut(${id}, 'en livraison')">🚚 Passer en livraison</button>`;

    if (commande.statut === 'en livraison') return `
        <button class="btn-action btn-livre"    onclick="changerStatut(${id}, 'livré')">📦 Marquer comme livré</button>
        <button class="btn-action btn-materiel" onclick="changerStatut(${id}, 'attente matériel', 1)">📦 Livré avec matériel prêté</button>`;

    if (commande.statut === 'livré') return `
        <button class="btn-action btn-terminer" onclick="changerStatut(${id}, 'terminée')">✅ Terminer la commande</button>`;

    if (commande.statut === 'attente matériel') return `
        <button class="btn-action btn-accepter" onclick="changerStatut(${id}, 'terminée', 0, 1)">✅ Matériel restitué - Terminer</button>`;

    return `<p class="commande-statut-final">Commande ${commande.statut}</p>`;
}

function genererCarteCommande(commande) {
    const badge = statuts[commande.statut] || { couleur: '#6C757D', texte: commande.statut };
    const prix  = parseFloat(commande.prix_total).toFixed(2).replace('.', ',');

    const commentaire = commande.commentaire ? `
        <div class="commande-commentaire-box">
            <p><strong>💬 Commentaire :</strong><br>
            <em>${commande.commentaire.replace(/\n/g, '<br>')}</em></p>
        </div>` : '';

    return `
        <div class="commande-carte-gestion">
            <div class="commande-gestion-header">
                <div>
                    <h3 class="commande-gestion-titre">${commande.menu_nom}</h3>
                    <p class="commande-gestion-info">Commande #${commande.commande_id} - ${formaterDateHeure(commande.date_commande)}</p>
                </div>
                <div class="badge-statut" style="background-color: ${badge.couleur};">${badge.texte}</div>
            </div>

            <div class="commande-info-grid">
                <div class="commande-info-section">
                    <h4>👤 Client</h4>
                    <p><strong>${commande.prenom} ${commande.user_nom}</strong></p>
                    <p><small>📧 ${commande.email}</small></p>
                    <p><small>📱 ${commande.telephone}</small></p>
                </div>
                <div class="commande-info-section">
                    <h4>📅 Prestation</h4>
                    <p><strong>Date :</strong> ${formaterDate(commande.date_prestation)}</p>
                    <p><strong>Heure :</strong> ${formaterHeure(commande.heure_livraison)}</p>
                    <p><strong>Personnes :</strong> ${commande.nombre_personnes}</p>
                </div>
                <div class="commande-info-section">
                    <h4>📍 Livraison</h4>
                    <p><small>${commande.adresse_livraison}<br>${commande.code_postal} ${commande.ville}</small></p>
                </div>
            </div>

            ${commentaire}

            <div class="commande-footer">
                <p class="commande-prix-total">Total : ${prix}€</p>
                <div class="commande-actions">${genererBoutons(commande)}</div>
            </div>
        </div>`;
}

async function changerStatut(commande_id, nouveau_statut, pret_materiel = 0, restitution_materiel = 0) {
    const formData = new FormData();
    formData.append('commande_id',          commande_id);
    formData.append('nouveau_statut',       nouveau_statut);
    formData.append('pret_materiel',        pret_materiel);
    formData.append('restitution_materiel', restitution_materiel);

    const reponse = await fetchAvecToken(URL_TRAITER, { method: 'POST', body: formData });
    const data    = await reponse.json();

    if (data.succes) {
        chargerCommandes();
    }

    if (data.erreur) {
        zoneMessages.innerHTML = `<div class="alert alert-error"><p>${data.erreur}</p></div>`;
        window.scrollTo(0, 0);
    }
}

async function chargerCommandes() {
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

    const totalCommandes = Object.values(data.stats).reduce((a, b) => a + b, 0);
    zoneStats.innerHTML = `
        <a href="?statut=tous" class="stat-card-link">
            <div class="dashboard-card stat-card ${filtre === 'tous' ? 'active-tous' : ''}">
                <h3>📦 Toutes</h3>
                <p class="stat-card-number stat-number-all">${totalCommandes}</p>
            </div>
        </a>
        <a href="?statut=en attente" class="stat-card-link">
            <div class="dashboard-card stat-card ${filtre === 'en attente' ? 'active-attente' : ''}">
                <h3>⏳ En attente</h3>
                <p class="stat-card-number stat-number-attente">${data.stats['en attente'] ?? 0}</p>
            </div>
        </a>
        <a href="?statut=accepté" class="stat-card-link">
            <div class="dashboard-card stat-card ${filtre === 'accepté' ? 'active-accepte' : ''}">
                <h3>✅ Acceptées</h3>
                <p class="stat-card-number stat-number-accepte">${data.stats['accepté'] ?? 0}</p>
            </div>
        </a>
        <a href="?statut=en préparation" class="stat-card-link">
            <div class="dashboard-card stat-card ${filtre === 'en préparation' ? 'active-preparation' : ''}">
                <h3>👨‍🍳 Préparation</h3>
                <p class="stat-card-number stat-number-preparation">${data.stats['en préparation'] ?? 0}</p>
            </div>
        </a>
        <a href="?statut=en livraison" class="stat-card-link">
            <div class="dashboard-card stat-card ${filtre === 'en livraison' ? 'active-livraison' : ''}">
                <h3>🚚 Livraison</h3>
                <p class="stat-card-number stat-number-livraison">${data.stats['en livraison'] ?? 0}</p>
            </div>
        </a>
        <a href="?statut=livré" class="stat-card-link">
            <div class="dashboard-card stat-card ${filtre === 'livré' ? 'active-livre' : ''}">
                <h3>📦 Livrées</h3>
                <p class="stat-card-number stat-number-livre">${data.stats['livré'] ?? 0}</p>
            </div>
        </a>
        <a href="?statut=attente matériel" class="stat-card-link">
            <div class="dashboard-card stat-card ${filtre === 'attente matériel' ? 'active-materiel' : ''}">
                <h3>🔄 Attente mat.</h3>
                <p class="stat-card-number stat-number-materiel">${data.stats['attente matériel'] ?? 0}</p>
            </div>
        </a>
        <a href="?statut=refusée" class="stat-card-link">
            <div class="dashboard-card stat-card ${filtre === 'refusée' ? 'active-refusee' : ''}">
                <h3>❌ Refusées</h3>
                <p class="stat-card-number stat-number-refusee">${data.stats['refusée'] ?? 0}</p>
            </div>
        </a>`;

    if (data.commandes.length === 0) {
        const msg = filtre === 'tous'
            ? 'Aucune commande pour le moment.'
            : `Aucune commande avec le statut "${filtre}".`;
        zoneCommandes.innerHTML = `<div class="message-aucune-commande-gestion"><p>${msg}</p></div>`;
        return;
    }

    zoneCommandes.innerHTML = `
        <div class="liste-commandes-gestion">
            ${data.commandes.map(genererCarteCommande).join('')}
        </div>`;
}

chargerCommandes();