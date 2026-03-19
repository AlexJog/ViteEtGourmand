async function chargerFooter() {
    const reponse = await fetch(`${BASE_URL}/frontend/components/footer.html`);
    const html    = await reponse.text();
    document.getElementById('footer').innerHTML = html;
}

chargerFooter();