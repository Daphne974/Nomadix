// Nomadix/public/js/script.js

const FLASH_MESSAGE_SELECTOR = '.message, .flash, .alert';
const FLASH_MESSAGE_DELAY = 10000;
const FLASH_MESSAGE_FADE_DURATION = 500;

function dismissFlashMessage(flashMessage) {
    if (!flashMessage || flashMessage.classList.contains('hidden')) {
        return;
    }

    flashMessage.classList.add('hidden');
    setTimeout(() => {
        flashMessage.style.display = 'none';
    }, FLASH_MESSAGE_FADE_DURATION);
}

function closeFlashMessage() {
    const flashMessage = document.getElementById('flashMessage');
    dismissFlashMessage(flashMessage);
}

function setupFlashMessages() {
    document.querySelectorAll(FLASH_MESSAGE_SELECTOR).forEach((flashMessage) => {
        if (!flashMessage.querySelector('.close-btn')) {
            const closeButton = document.createElement('span');
            closeButton.className = 'close-btn';
            closeButton.setAttribute('aria-label', 'Fermer le message');
            closeButton.setAttribute('role', 'button');
            closeButton.textContent = 'x';
            closeButton.addEventListener('click', () => dismissFlashMessage(flashMessage));
            flashMessage.appendChild(closeButton);
        }

        if (!flashMessage.querySelector('.progress-bar')) {
            const progressBar = document.createElement('div');
            progressBar.className = 'progress-bar';
            flashMessage.appendChild(progressBar);
        }

        setTimeout(() => dismissFlashMessage(flashMessage), FLASH_MESSAGE_DELAY);
    });
}

document.addEventListener('DOMContentLoaded', setupFlashMessages);

/**
 * Valide que une note a été sélectionnée avant de soumettre le formulaire d'avis
 */
function validateRating(form) {
    const ratingInputs = form.querySelectorAll('input[name="note"]');
    const isChecked = Array.from(ratingInputs).some(input => input.checked);
    
    if (!isChecked) {
        alert('Veuillez sélectionner une note avant de soumettre votre avis.');
        return false;
    }
    
    return true;
}
