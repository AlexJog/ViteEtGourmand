const URL_API      = `${BASE_URL}/api/admin/gestion-employes.php`;
const URL_MODIFIER = `${BASE_URL}/api/admin/modifier-employe.php`;

const zoneMessages  = document.getElementById('zone-messages');
const listeEmployes = document.getElementById('liste-employes');

function genererCarteEmploye(employe) {
    const badge = employe.actif
        ? `<div class="badge-statut" style="background-color: #28A745;">✅ Compte actif</div>`
        : `<div class="badge-statut" style="background-color: #DC3545;">❌ Compte désactivé</div>`;

    const dateCreation = employe.date_creation
        ? new Date(employe.date_creation).toLocaleDateString('fr-FR')
        : 'N/A';

    const boutonAction = employe.actif
        ? `<button class="btn-secondary" onclick="ouvrirPopup(${employe.utilisateur_id}, 'desactiver')">🔒 Désactiver le compte</button>`
        : `<button class="btn-laisser-avis" onclick="ouvrirPopup(${employe.utilisateur_id}, 'activer')">🔓 Réactiver le compte</button>`;

    return `
        <div class="commande-carte">
            <div class="commande-header">
                <div>
                    <h3 class="commande-titre">${employe.prenom} ${employe.nom}</h3>
                    <p class="commande-date">${employe.email}</p>
                </div>
                ${badge}
            </div>
            <div class="commande-details">
                <div>
                    <p><strong>📞 Téléphone :</strong><br>${employe.telephone}</p>
                </div>
                <div>
                    <p><strong>📅 Créé le :</strong><br>${dateCreation}</p>
                </div>
            </div>
            <div class="commande-avis">
                ${boutonAction}
            </div>
        </div>`;
}

let pendingEmployeId = null;
let pendingAction    = null;

function ouvrirPopup(employe_id, action) {
    pendingEmployeId = employe_id;
    pendingAction    = action;

    const estDesactivation = action === 'desactiver';
    document.getElementById('popup-titre').textContent   = estDesactivation ? '🔒 Désactiver le compte' : '🔓 Réactiver le compte';
    document.getElementById('popup-message').textContent = estDesactivation
        ? 'Voulez-vous vraiment désactiver ce compte employé ?'
        : 'Voulez-vous vraiment réactiver ce compte employé ?';

    const btnConfirmer = document.getElementById('popup-btn-confirmer');
    btnConfirmer.style.backgroundColor = estDesactivation ? '#DC3545' : '#28A745';

    document.getElementById('popupStatut').classList.add('active');
}

function fermerPopup() {
    document.getElementById('popupStatut').classList.remove('active');
    pendingEmployeId = null;
    pendingAction    = null;
}

async function confirmerChangement() {
    const employe_id = pendingEmployeId;
    const action     = pendingAction;
    fermerPopup();

    const formData = new FormData();
    formData.append('employe_id', employe_id);
    formData.append('action',     action);

    const reponse = await fetchAvecToken(URL_MODIFIER, { method: 'POST', body: formData });
    const data    = await reponse.json();

    if (data.succes) {
        zoneMessages.innerHTML = `<div class="alert alert-success"><p>${data.succes}</p></div>`;
        chargerEmployes();
    }

    if (data.erreur) {
        zoneMessages.innerHTML = `<div class="alert alert-error"><p>${data.erreur}</p></div>`;
    }

    window.scrollTo(0, 0);
}

async function chargerEmployes() {
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

    if (data.employes.length === 0) {
        listeEmployes.innerHTML = `
            <div class="message-aucune-commande">
                <p>Aucun employé enregistré.</p>
                <a href="creer-employe.html" class="btn-hero">Créer un employé</a>
            </div>`;
        return;
    }

    listeEmployes.innerHTML = `
        <div class="liste-commandes">
            ${data.employes.map(genererCarteEmploye).join('')}
        </div>
        <div class="btn-creer-employe-container">
            <a href="creer-employe.html" class="btn-hero">➕ Créer un nouvel employé</a>
        </div>`;
}

chargerEmployes();