const URL_API      = `${BASE_URL}/api/utilisateur/mes-commandes.php`;
const zoneMessages = document.getElementById('zone-messages');
const listeCommandes = document.getElementById('liste-commandes');

// Correspondance statut → badge
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

function genererCarteCommande(commande) {
    const badge = statuts[commande.statut] || { couleur: '#6C757D', texte: commande.statut };
    const prix  = parseFloat(commande.prix_total).toFixed(2).replace('.', ',');

    const commentaire = commande.commentaire ? `
        <div class="commande-commentaire">
            <p><strong>💬 Commentaire :</strong><br>
            <em>${commande.commentaire.replace(/\n/g, '<br>')}</em></p>
        </div>` : '';

    let boutonAvis = '';
    if (commande.statut === 'terminée') {
        boutonAvis = commande.avis_existe
            ? `<div class="commande-avis"><p class="message-avis-deja-laisse">✅ Vous avez laissé un avis pour cette commande</p></div>`
            : `<div class="commande-avis"><a href="laisser-avis.html?commande_id=${commande.commande_id}" class="btn-laisser-avis">⭐ Laisser un avis</a></div>`;
    }

    return `
        <div class="commande-carte">
            <div class="commande-header">
                <div>
                    <h3 class="commande-titre">${commande.menu_nom}</h3>
                    <p class="commande-date">Commandé le ${formaterDateHeure(commande.date_commande)}</p>
                </div>
                <div class="badge-statut" style="background-color: ${badge.couleur};">
                    ${badge.texte}
                </div>
            </div>

            <div class="commande-details">
                <div>
                    <p><strong>📅 Date de prestation :</strong><br>${formaterDate(commande.date_prestation)}</p>
                    <p><strong>🕐 Heure de livraison :</strong><br>${formaterHeure(commande.heure_livraison)}</p>
                    <p><strong>👥 Nombre de personnes :</strong><br>${commande.nombre_personnes} personnes</p>
                </div>
                <div>
                    <p><strong>📍 Adresse de livraison :</strong><br>
                    ${commande.adresse_livraison}<br>
                    ${commande.code_postal} ${commande.ville}</p>
                </div>
            </div>

            ${commentaire}

            <div class="commande-prix">
                <p>Total : ${prix}€</p>
            </div>

            ${boutonAvis}
        </div>`;
}

async function chargerCommandes() {
    const reponse = await fetch(URL_API);
    const data    = await reponse.json();

    if (data.erreur === 'non_connecte') {
        window.location.href = '../connexion.html';
        return;
    }

    if (data.erreur === 'non_autorise') {
        window.location.href = '../index.html';
        return;
    }

    // Afficher les messages de session
    if (data.messages.succes) {
        zoneMessages.innerHTML = `<div class="alert alert-success"><p>${data.messages.succes}</p></div>`;
    }
    if (data.messages.erreur) {
        zoneMessages.innerHTML = `<div class="alert alert-error"><p>${data.messages.erreur}</p></div>`;
    }

    if (data.commandes.length === 0) {
        listeCommandes.innerHTML = `
            <div class="message-aucune-commande">
                <p>Vous n'avez pas encore passé de commande.</p>
                <a href="../menus.html" class="btn-hero">Découvrir nos menus</a>
            </div>`;
        return;
    }

    listeCommandes.innerHTML = `
        <div class="liste-commandes">
            ${data.commandes.map(genererCarteCommande).join('')}
        </div>`;
}

chargerCommandes();