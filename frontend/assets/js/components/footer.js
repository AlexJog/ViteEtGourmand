async function chargerFooter() {
   const reponse = await fetch(`/components/footer.html`);
    const html    = await reponse.text();
    document.getElementById('footer').innerHTML = html;
}

chargerFooter();