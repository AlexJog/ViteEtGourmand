const BASE_URL = 'https://vite-et-gourmand-alex-a85135b73360.herokuapp.com';

// Fonction fetch avec token automatique
async function fetchAvecToken(url, options = {}) {
    const token = localStorage.getItem('token');
    if (token) {
        options.headers = { ...options.headers, 'X-AUTH-TOKEN': token };
    }
    return fetch(url, options);
}