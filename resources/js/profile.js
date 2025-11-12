
// Variables globales
let selectedFile = null;

// Éléments du DOM
const changePhotoBtn = document.getElementById('change-photo-btn');
const photoOverlay = document.getElementById('photo-overlay');
const profilePhotoInput = document.getElementById('profile-photo');
const popup = document.getElementById('photo-upload-popup');
const popupContent = document.getElementById('popup-content');
const closePopupBtn = document.getElementById('close-popup-btn');
const uploadZone = document.getElementById('upload-zone');
const popupFileInput = document.getElementById('popup-file-input');
const popupPreview = document.getElementById('popup-preview');
const avatarDisplay = document.getElementById('avatar-display');
const popupCancelBtn = document.getElementById('popup-cancel-btn');
const popupConfirmBtn = document.getElementById('popup-confirm-btn');

// Fonctions utilitaires
function showPopup() {
    popup.classList.remove('hidden');
    popup.classList.add('flex');
    setTimeout(() => {
        popupContent.classList.remove('scale-95', 'opacity-0');
        popupContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function hidePopup() {
    popupContent.classList.remove('scale-100', 'opacity-100');
    popupContent.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        popup.classList.add('hidden');
        popup.classList.remove('flex');
        resetPopup();
    }, 300);
}

function resetPopup() {
    selectedFile = null;
    popupFileInput.value = '';
    popupPreview.style.backgroundImage = '';
    popupPreview.textContent = 'JM';
    popupPreview.className = 'w-24 h-24 mx-auto bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg';
    popupConfirmBtn.disabled = true;
    popupConfirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
}

function previewImage(file) {
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            popupPreview.style.backgroundImage = `url(${e.target.result})`;
            popupPreview.style.backgroundSize = 'cover';
            popupPreview.style.backgroundPosition = 'center';
            popupPreview.textContent = '';
            popupConfirmBtn.disabled = false;
            popupConfirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        };
        reader.readAsDataURL(file);
    }
}

function updateProfilePhoto(file) {
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            avatarDisplay.style.backgroundImage = `url(${e.target.result})`;
            avatarDisplay.style.backgroundSize = 'cover';
            avatarDisplay.style.backgroundPosition = 'center';
            avatarDisplay.textContent = '';
            
            // Mettre à jour l'input file principal
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            profilePhotoInput.files = dataTransfer.files;
        };
        reader.readAsDataURL(file);
    }
}

function isValidImageFile(file) {
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/svg+xml'];
    const maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!validTypes.includes(file.type)) {
        alert('Format de fichier non supporté. Veuillez choisir une image (JPG, PNG, GIF, BMP, WEBP, SVG).');
        return false;
    }
    
    if (file.size > maxSize) {
        alert('Le fichier est trop volumineux. Taille maximum : 5MB.');
        return false;
    }
    
    return true;
}

// Event Listeners
changePhotoBtn.addEventListener('click', showPopup);
photoOverlay.addEventListener('click', showPopup);
closePopupBtn.addEventListener('click', hidePopup);
popupCancelBtn.addEventListener('click', hidePopup);

// Clic sur la zone d'upload
uploadZone.addEventListener('click', () => {
    popupFileInput.click();
});

// Changement de fichier
popupFileInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file && isValidImageFile(file)) {
        selectedFile = file;
        previewImage(file);
    }
});

// Drag & Drop
uploadZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadZone.classList.add('border-blue-500', 'dark:border-blue-400');
});

uploadZone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    uploadZone.classList.remove('border-blue-500', 'dark:border-blue-400');
});

uploadZone.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadZone.classList.remove('border-blue-500', 'dark:border-blue-400');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const file = files[0];
        if (isValidImageFile(file)) {
            selectedFile = file;
            previewImage(file);
        }
    }
});

// Confirmation de la photo
popupConfirmBtn.addEventListener('click', () => {
    if (selectedFile) {
        updateProfilePhoto(selectedFile);
        hidePopup();
    }
});

// Fermeture du popup en cliquant à l'extérieur
popup.addEventListener('click', (e) => {
    if (e.target === popup) {
        hidePopup();
    }
});

// Gestion des touches
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !popup.classList.contains('hidden')) {
        hidePopup();
    }
});

// Gestion du mode édition du profil
const editProfileBtn = document.getElementById('edit-profile-btn');
const cancelEditBtn = document.getElementById('cancel-edit-btn');
const formActions = document.getElementById('form-actions');
const profileForm = document.getElementById('profile-form');

// Éléments editables
const editableInputs = [
    'lastname', 'firstname', 'email', 'phone', 'profession'
];
const editableSelects = ['department'];

let originalValues = {};

function enterEditMode() {
    // Sauvegarder les valeurs originales
    editableInputs.forEach(id => {
        const input = document.getElementById(id);
        originalValues[id] = input.value;
        input.removeAttribute('readonly');
        input.classList.remove('cursor-not-allowed');
        input.classList.remove('bg-gray-50', 'dark:bg-gray-700');
        input.classList.add('bg-white', 'dark:bg-gray-800');
    });

    // Afficher les boutons d'action
    formActions.classList.remove('hidden');
    
    // Masquer le bouton modifier
    editProfileBtn.style.display = 'none';
}

function exitEditMode() {
    // Restaurer les valeurs originales
    editableInputs.forEach(id => {
        const input = document.getElementById(id);
        input.value = originalValues[id];
        input.setAttribute('readonly', 'true');
        input.classList.add('bg-gray-50', 'dark:bg-gray-700');
        input.classList.remove('bg-white', 'dark:bg-gray-800');
    });

    // Masquer les boutons d'action
    formActions.classList.add('hidden');
    
    // Afficher le bouton modifier
    editProfileBtn.style.display = 'block';
    
    // Vider les valeurs sauvegardées
    originalValues = {};
}

function saveProfile() {
    // Ici vous pouvez ajouter la logique de sauvegarde
    console.log('Sauvegarde du profil...');
    
    // Simuler la sauvegarde
    setTimeout(() => {
        alert('Profil mis à jour avec succès !');
        exitEditMode();
    }, 500);
}

// Event listeners pour la gestion du profil
editProfileBtn.addEventListener('click', enterEditMode);
cancelEditBtn.addEventListener('click', exitEditMode);

// Soumission du formulaire
profileForm.addEventListener('submit', (e) => {
    e.preventDefault();
    
    // Récupérer toutes les données du formulaire
    const formData = new FormData(profileForm);
    
    // Log des données pour debug
    console.log('Données du formulaire:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
    saveProfile();
});
