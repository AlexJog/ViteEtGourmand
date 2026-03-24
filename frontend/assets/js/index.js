const URL_AVIS = `${BASE_URL}/api/avis.php`;

const zoneAvis = document.getElementById('zone-avis');

const avisParDefaut = [
    { prenom: 'Sabino', nom: '', note: 5, commentaire: 'Excellent service ! Les plats étaient délicieux et la présentation soignée.', date_avis: null },
    { prenom: 'Pierre', nom: '', note: 5, commentaire: 'Très professionnels, à l\'écoute. Je recommande vivement pour vos événements.', date_avis: null },
    { prenom: 'Rosine', nom: '', note: 5, commentaire: 'Une qualité irréprochable, nos invités ont adoré. Merci !', date_avis: null }
];

// Message de bienvenue après connexion
const firstLogin    = sessionStorage.getItem('first_login');
const zoneBienvenue = document.getElementById('message-bienvenue');

if (firstLogin === 'true' && zoneBienvenue) {
    const prenom = localStorage.getItem('prenom');
    zoneBienvenue.innerHTML = `
        <div class="message-bienvenue">
            <h2>Bienvenue ${prenom} !</h2>
            <p>Nous sommes ravis de vous revoir sur Vite & Gourmand.</p>
        </div>`;
    setTimeout(() => {
        zoneBienvenue.innerHTML = '';
    }, 3000);
    sessionStorage.removeItem('first_login');
}

function genererEtoiles(note) {
    let etoiles = '';
    for (let i = 1; i <= 5; i++) {
        etoiles += i <= note
            ? '<span class="etoile-pleine">★</span>'
            : '<span class="etoile-vide">☆</span>';
    }
    return etoiles;
}

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
        </div>`;
}

async function chargerAvis() {
    const reponse = await fetch(URL_AVIS);
    const avis    = await reponse.json();

    const liste  = avis.length > 0 ? avis : avisParDefaut;
    const classe = avis.length > 0 ? 'avis-grid' : 'div-avis';

    zoneAvis.innerHTML = `
        <div class="${classe}">
            ${liste.map(genererCarteAvis).join('')}
        </div>`;
}

chargerAvis();