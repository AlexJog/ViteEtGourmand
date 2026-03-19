const URL_MESSAGES = `${BASE_URL}/api/accueil.php`;
const URL_AVIS     = `${BASE_URL}/api/avis.php`;

const zoneMessages   = document.getElementById('zone-messages');
const zoneBienvenue  = document.getElementById('message-bienvenue');
const zoneAvis       = document.getElementById('zone-avis');

// Avis par défaut si aucun en BDD
const avisParDefaut = [
    { prenom: 'Sabino', nom: '',  note: 5, commentaire: 'Excellent service ! Les plats étaient délicieux et la présentation soignée.', date_avis: null },
    { prenom: 'Pierre', nom: '',  note: 5, commentaire: 'Très professionnels, à l\'écoute. Je recommande vivement pour vos événements.', date_avis: null },
    { prenom: 'Rosine', nom: '',  note: 5, commentaire: 'Une qualité irréprochable, nos invités ont adoré. Merci !', date_avis: null }
];

// Générer les étoiles
function genererEtoiles(note) {
    let etoiles = '';
    for (let i = 1; i <= 5; i++) {
        etoiles += i <= note
            ? '<span class="etoile-pleine">★</span>'
            : '<span class="etoile-vide">☆</span>';
    }
    return etoiles;
}

// Générer une carte avis
function genererCarteAvis(avis) {
    const auteur = avis.nom
        ? `${avis.prenom} ${avis.nom.charAt(0)}.`
        : avis.prenom;

    const date = avis.date_avis
        ? new Date(avis.date_avis).toLocaleDateString('fr-FR')
        : '';

    return `
        <div class="avis-carte">
            <div class="avis-etoiles">${genererEtoiles(avis.note)}</div>
            <p class="avis-commentaire">"${avis.commentaire}"</p>
            <p class="avis-auteur">— ${auteur}</p>
            ${date ? `<p class="avis-date">${date}</p>` : ''}
        </div>
    `;
}

// Charger les messages de session
async function chargerMessages() {
    const reponse = await fetch(URL_MESSAGES);
    const data    = await reponse.json();

    if (data.succes) {
        zoneMessages.innerHTML = `<div class="alert alert-success"><p>${data.succes}</p></div>`;
    }

    if (data.erreur) {
        zoneMessages.innerHTML = `<div class="alert alert-error"><p>${data.erreur}</p></div>`;
    }

    if (data.bienvenue) {
        zoneBienvenue.innerHTML = `
            <div class="message-bienvenue">
                <h2>Bienvenue ${data.bienvenue} !</h2>
                <p>Nous sommes ravis de vous revoir sur Vite & Gourmand.</p>
            </div>`;
    }
}

// Charger les avis
async function chargerAvis() {
    const reponse = await fetch(URL_AVIS);
    const avis    = await reponse.json();

    const liste = avis.length > 0 ? avis : avisParDefaut;
    const classe = avis.length > 0 ? 'avis-grid' : 'div-avis';

    zoneAvis.innerHTML = `
        <div class="${classe}">
            ${liste.map(genererCarteAvis).join('')}
        </div>`;
}

// Lancement
chargerMessages();
chargerAvis();