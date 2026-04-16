// public/js/script.js
function closeFlashMessage() {
    const flashMessage = document.getElementById('flashMessage');
    if (flashMessage) {
        flashMessage.classList.add('hidden');
        setTimeout(() => flashMessage.style.display = 'none', 500);
    }
}

window.onload = function () {
    const flashMessage = document.getElementById('flashMessage');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.classList.add('hidden');
            setTimeout(() => flashMessage.style.display = 'none', 500);
        }, 15000);
    }
};