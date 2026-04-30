// Nomadix/public/js/script.js

// Fonction pour fermer les messages flash
function closeFlashMessage() {
    const flashMessage = document.getElementById('flashMessage');
    if (flashMessage) {
        flashMessage.classList.add('hidden');
        setTimeout(() => flashMessage.style.display = 'none', 500);
    }
}

// Fermer automatiquement les messages flash après 15 secondes
window.onload = function () {
    const flashMessage = document.getElementById('flashMessage');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.classList.add('hidden');
            setTimeout(() => flashMessage.style.display = 'none', 500);
        }, 15000);
    }
};