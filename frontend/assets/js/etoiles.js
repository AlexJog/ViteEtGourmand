// Gestion des étoiles interactives pour les avis
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    const form = document.querySelector('.contact-form');
    let selectedRating = 0;

    if (stars.length > 0 && form) {
        stars.forEach(star => {
            // Survol
            star.addEventListener('mouseenter', function() {
                const value = parseInt(this.getAttribute('data-value'));
                highlightStars(value);
            });
            
            // Clic
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.getAttribute('data-value'));
                document.getElementById('note' + selectedRating).checked = true;
                highlightStars(selectedRating);
            });
        });

        // Réinitialiser au survol
        form.addEventListener('mouseleave', function() {
            if (selectedRating > 0) {
                highlightStars(selectedRating);
            } else {
                highlightStars(0);
            }
        });

        function highlightStars(count) {
            stars.forEach((star, index) => {
                if (index < count) {
                    star.style.color = '#FFD700';
                } else {
                    star.style.color = '#ddd';
                }
            });
        }
    }
});