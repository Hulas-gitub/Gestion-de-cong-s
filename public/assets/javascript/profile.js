// ========== GESTION COMPLÈTE DU PROFIL ET MOT DE PASSE ==========

// Variables globales
let selectedFile = null;
let originalValues = {};

// Récupérer le rôle depuis le DOM ou le backend
const userRole = "{{ strtolower(str_replace(' ', '-', Auth::user()->role->nom_role ?? 'employes')) }}";

// Construire l'URL de base selon le rôle
let baseApiUrl = '';
if (userRole === 'admin') {
    baseApiUrl = '/admin/api/profile';
} else if (userRole === 'chef-de-departement') {
    baseApiUrl = '/chef-de-departement/api/profile';
} else {
    baseApiUrl = '/employes/api/profile';
}

// ========== ÉLÉMENTS DU DOM ==========

// Photo de profil
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
const popupDeleteBtn = document.getElementById('popup-delete-btn');

// Formulaire de profil
const editProfileBtn = document.getElementById('edit-profile-btn');
const cancelEditBtn = document.getElementById('cancel-edit-btn');
const formActions = document.getElementById('form-actions');
const profileForm = document.getElementById('profile-form');

// Formulaire de mot de passe
const passwordForm = document.getElementById('password-form');
const currentPasswordInput = document.getElementById('current-password');
const newPasswordInput = document.getElementById('new-password');
const confirmPasswordInput = document.getElementById('confirm-password');
const cancelPasswordBtn = document.getElementById('cancel-password-btn');
const savePasswordBtn = document.getElementById('save-password-btn');

// Indicateur de force du mot de passe
const passwordStrengthBar = document.getElementById('password-strength-bar');
const passwordStrengthText = document.getElementById('password-strength-text');
const lengthCheck = document.getElementById('length-check');
const uppercaseCheck = document.getElementById('uppercase-check');
const lowercaseCheck = document.getElementById('lowercase-check');
const numberCheck = document.getElementById('number-check');

// Champs éditables
const editableInputs = ['lastname', 'firstname', 'email', 'phone'];

// ========== FONCTIONS TOAST ==========
function showToast(type, title, message) {
    const toast = document.getElementById('toast');
    const toastIcon = document.getElementById('toastIcon');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');

    if (!toast) return;

    const config = {
        success: {
            bgColor: 'bg-green-500',
            icon: 'fa-check',
            borderColor: 'border-green-500'
        },
        error: {
            bgColor: 'bg-red-500',
            icon: 'fa-times',
            borderColor: 'border-red-500'
        },
        warning: {
            bgColor: 'bg-yellow-500',
            icon: 'fa-exclamation-triangle',
            borderColor: 'border-yellow-500'
        },
        info: {
            bgColor: 'bg-blue-500',
            icon: 'fa-info',
            borderColor: 'border-blue-500'
        }
    };

    const style = config[type] || config.info;

    toastIcon.className = `w-8 h-8 rounded-full flex items-center justify-center ${style.bgColor} text-white`;
    toastIcon.querySelector('i').className = `fas ${style.icon}`;
    toast.querySelector('.border-l-4').className = `bg-white dark:bg-gray-800 shadow-lg rounded-lg p-4 border-l-4 max-w-sm ${style.borderColor}`;

    toastTitle.textContent = title;
    toastMessage.textContent = message;

    toast.classList.remove('translate-x-full');
    toast.classList.add('translate-x-0');

    setTimeout(() => {
        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-full');
    }, 3000);
}

// ========== GESTION DU POPUP PHOTO ==========
function showPopup() {
    if (!popup || !popupContent) return;
    popup.classList.remove('hidden');
    popup.classList.add('flex');
    setTimeout(() => {
        popupContent.classList.remove('scale-95', 'opacity-0');
        popupContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function hidePopup() {
    if (!popup || !popupContent) return;
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
    if (popupFileInput) popupFileInput.value = '';
    if (popupConfirmBtn) {
        popupConfirmBtn.disabled = true;
        popupConfirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
}

function previewImage(file) {
    if (file && popupPreview) {
        const reader = new FileReader();
        reader.onload = function(e) {
            popupPreview.style.backgroundImage = `url(${e.target.result})`;
            popupPreview.style.backgroundSize = 'cover';
            popupPreview.style.backgroundPosition = 'center';
            popupPreview.textContent = '';
            if (popupConfirmBtn) {
                popupConfirmBtn.disabled = false;
                popupConfirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        };
        reader.readAsDataURL(file);
    }
}

function isValidImageFile(file) {
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    const maxSize = 2 * 1024 * 1024; // 2MB

    if (!validTypes.includes(file.type)) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Format non supporté',
                text: 'Veuillez choisir une image (JPG, PNG, GIF)',
                confirmButtonText: 'D\'accord',
                confirmButtonColor: '#3b82f6'
            });
        }
        return false;
    }

    if (file.size > maxSize) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Fichier trop volumineux',
                text: 'La taille maximum autorisée est de 2MB',
                confirmButtonText: 'D\'accord',
                confirmButtonColor: '#3b82f6'
            });
        }
        return false;
    }

    return true;
}

// ========== UPLOAD PHOTO ==========
async function uploadPhoto(file) {
    const formData = new FormData();
    formData.append('photo', file);

    try {
        const response = await fetch(baseApiUrl + '/photo', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showToast('success', 'Photo mise à jour', data.message);

            if (data.photo_url && avatarDisplay) {
                avatarDisplay.style.backgroundImage = `url(${data.photo_url})`;
                avatarDisplay.style.backgroundSize = 'cover';
                avatarDisplay.style.backgroundPosition = 'center';
                avatarDisplay.textContent = '';
            }

            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message,
                    confirmButtonText: 'D\'accord',
                    confirmButtonColor: '#3b82f6'
                });
            }
        }
    } catch (error) {
        console.error('Erreur:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors de l\'upload',
                confirmButtonText: 'D\'accord',
                confirmButtonColor: '#3b82f6'
            });
        }
    }
}

// ========== SUPPRESSION PHOTO ==========
async function deletePhoto() {
    if (typeof Swal !== 'undefined') {
        const result = await Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: 'Voulez-vous vraiment supprimer votre photo de profil ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        });

        if (!result.isConfirmed) {
            return;
        }
    } else {
        if (!confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')) {
            return;
        }
    }

    try {
        const response = await fetch(baseApiUrl + '/photo', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            showToast('success', 'Photo supprimée', data.message);
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message,
                    confirmButtonText: 'D\'accord',
                    confirmButtonColor: '#3b82f6'
                });
            }
        }
    } catch (error) {
        console.error('Erreur:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors de la suppression',
                confirmButtonText: 'D\'accord',
                confirmButtonColor: '#3b82f6'
            });
        }
    }
}

// ========== EVENT LISTENERS PHOTO ==========
if (changePhotoBtn) changePhotoBtn.addEventListener('click', showPopup);
if (photoOverlay) photoOverlay.addEventListener('click', showPopup);
if (closePopupBtn) closePopupBtn.addEventListener('click', hidePopup);
if (popupCancelBtn) popupCancelBtn.addEventListener('click', hidePopup);
if (popupDeleteBtn) popupDeleteBtn.addEventListener('click', () => {
    hidePopup();
    deletePhoto();
});

if (uploadZone) {
    uploadZone.addEventListener('click', () => {
        if (popupFileInput) popupFileInput.click();
    });

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
}

if (popupFileInput) {
    popupFileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file && isValidImageFile(file)) {
            selectedFile = file;
            previewImage(file);
        }
    });
}

if (popupConfirmBtn) {
    popupConfirmBtn.addEventListener('click', () => {
        if (selectedFile) {
            uploadPhoto(selectedFile);
            hidePopup();
        }
    });
}

if (popup) {
    popup.addEventListener('click', (e) => {
        if (e.target === popup) {
            hidePopup();
        }
    });
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && popup && !popup.classList.contains('hidden')) {
        hidePopup();
    }
});

// ========== GESTION DU MODE ÉDITION PROFIL ==========
function enterEditMode() {
    editableInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            originalValues[id] = input.value;
            input.removeAttribute('readonly');
            input.classList.remove('cursor-not-allowed', 'bg-gray-50', 'dark:bg-gray-700');
            input.classList.add('bg-white', 'dark:bg-gray-800');
        }
    });

    if (formActions) formActions.classList.remove('hidden');
    if (editProfileBtn) editProfileBtn.style.display = 'none';
}

function exitEditMode() {
    editableInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input && originalValues[id] !== undefined) {
            input.value = originalValues[id];
            input.setAttribute('readonly', 'true');
            input.classList.add('bg-gray-50', 'dark:bg-gray-700');
            input.classList.remove('bg-white', 'dark:bg-gray-800');
        }
    });

    if (formActions) formActions.classList.add('hidden');
    if (editProfileBtn) editProfileBtn.style.display = 'block';

    originalValues = {};
}

// ========== SAUVEGARDE DU PROFIL ==========
async function saveProfile() {
    const formData = {
        nom: document.getElementById('lastname')?.value.trim(),
        prenom: document.getElementById('firstname')?.value.trim(),
        email: document.getElementById('email')?.value.trim(),
        telephone: document.getElementById('phone')?.value.trim()
    };

    if (!formData.nom || !formData.prenom || !formData.email) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Attention',
                text: 'Veuillez remplir tous les champs obligatoires',
                confirmButtonText: 'D\'accord',
                confirmButtonColor: '#3b82f6'
            });
        }
        return;
    }

    try {
        const response = await fetch(baseApiUrl + '/update', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.success) {
            showToast('success', 'Profil mis à jour', data.message);
            exitEditMode();
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message,
                    confirmButtonText: 'D\'accord',
                    confirmButtonColor: '#3b82f6'
                });
            }
        }
    } catch (error) {
        console.error('Erreur:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors de la sauvegarde',
                confirmButtonText: 'D\'accord',
                confirmButtonColor: '#3b82f6'
            });
        }
    }
}

// Event listeners profil
if (editProfileBtn) editProfileBtn.addEventListener('click', enterEditMode);
if (cancelEditBtn) cancelEditBtn.addEventListener('click', exitEditMode);

if (profileForm) {
    profileForm.addEventListener('submit', (e) => {
        e.preventDefault();
        saveProfile();
    });
}

// ========== GESTION DU MOT DE PASSE ==========

// Fonction pour basculer la visibilité du mot de passe
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');

    if (input && icon) {
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

window.togglePassword = togglePassword;

// Vérification de la force du mot de passe
function checkPasswordStrength(password) {
    let strength = 0;
    const checks = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password)
    };

    updateCheckIcon(lengthCheck, checks.length);
    updateCheckIcon(uppercaseCheck, checks.uppercase);
    updateCheckIcon(lowercaseCheck, checks.lowercase);
    updateCheckIcon(numberCheck, checks.number);

    Object.values(checks).forEach(check => {
        if (check) strength += 25;
    });

    updateStrengthBar(strength);

    return checks;
}

function updateCheckIcon(element, isValid) {
    if (!element) return;

    if (isValid) {
        element.classList.remove('fa-times', 'text-red-500');
        element.classList.add('fa-check', 'text-green-500');
    } else {
        element.classList.remove('fa-check', 'text-green-500');
        element.classList.add('fa-times', 'text-red-500');
    }
}

function updateStrengthBar(strength) {
    if (!passwordStrengthBar || !passwordStrengthText) return;

    passwordStrengthBar.style.width = `${strength}%`;

    if (strength === 0 || strength < 50) {
        passwordStrengthBar.classList.remove('bg-yellow-500', 'bg-green-500');
        passwordStrengthBar.classList.add('bg-red-500');
        passwordStrengthText.textContent = 'Faible';
        passwordStrengthText.classList.remove('text-yellow-600', 'text-green-600');
        passwordStrengthText.classList.add('text-red-600');
    } else if (strength < 75) {
        passwordStrengthBar.classList.remove('bg-red-500', 'bg-green-500');
        passwordStrengthBar.classList.add('bg-yellow-500');
        passwordStrengthText.textContent = 'Moyen';
        passwordStrengthText.classList.remove('text-red-600', 'text-green-600');
        passwordStrengthText.classList.add('text-yellow-600');
    } else if (strength < 100) {
        passwordStrengthBar.classList.remove('bg-red-500', 'bg-yellow-500');
        passwordStrengthBar.classList.add('bg-green-500');
        passwordStrengthText.textContent = 'Bon';
        passwordStrengthText.classList.remove('text-red-600', 'text-yellow-600');
        passwordStrengthText.classList.add('text-green-600');
    } else {
        passwordStrengthBar.classList.remove('bg-red-500', 'bg-yellow-500');
        passwordStrengthBar.classList.add('bg-green-500');
        passwordStrengthText.textContent = 'Excellent';
        passwordStrengthText.classList.remove('text-red-600', 'text-yellow-600');
        passwordStrengthText.classList.add('text-green-600');
    }
}

// Event listener pour vérifier la force en temps réel
if (newPasswordInput) {
    newPasswordInput.addEventListener('input', (e) => {
        checkPasswordStrength(e.target.value);
    });
}

// Vérification de la correspondance des mots de passe
if (confirmPasswordInput && newPasswordInput) {
    confirmPasswordInput.addEventListener('input', (e) => {
        const newPassword = newPasswordInput.value;
        const confirmPassword = e.target.value;

        if (confirmPassword.length > 0) {
            if (newPassword === confirmPassword) {
                confirmPasswordInput.classList.remove('border-red-500', 'focus:ring-red-500');
                confirmPasswordInput.classList.add('border-green-500', 'focus:ring-green-500');
            } else {
                confirmPasswordInput.classList.remove('border-green-500', 'focus:ring-green-500');
                confirmPasswordInput.classList.add('border-red-500', 'focus:ring-red-500');
            }
        } else {
            confirmPasswordInput.classList.remove('border-red-500', 'border-green-500', 'focus:ring-red-500', 'focus:ring-green-500');
        }
    });
}

// Validation du formulaire de mot de passe
function validatePasswordForm() {
    if (!currentPasswordInput || !newPasswordInput || !confirmPasswordInput) return false;

    const currentPassword = currentPasswordInput.value.trim();
    const newPassword = newPasswordInput.value.trim();
    const confirmPassword = confirmPasswordInput.value.trim();

    if (!currentPassword || !newPassword || !confirmPassword) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Attention',
                text: 'Veuillez remplir tous les champs',
                confirmButtonText: 'D\'accord',
                confirmButtonColor: '#3b82f6'
            });
        }
        return false;
    }

    const checks = checkPasswordStrength(newPassword);
    const allChecksPassed = Object.values(checks).every(check => check === true);

    if (!allChecksPassed) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Mot de passe faible',
                text: 'Le mot de passe doit respecter tous les critères de sécurité',
                confirmButtonText: 'D\'accord',
                confirmButtonColor: '#3b82f6'
            });
        }
        return false;
    }

    if (newPassword !== confirmPassword) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Les mots de passe ne correspondent pas',
                confirmButtonText: 'D\'accord',
                confirmButtonColor: '#3b82f6'
            });
        }
        return false;
    }

    if (currentPassword === newPassword) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Attention',
                text: 'Le nouveau mot de passe doit être différent de l\'ancien',
                confirmButtonText: 'D\'accord',
                confirmButtonColor: '#3b82f6'
            });
        }
        return false;
    }

    return true;
}

// Sauvegarde du nouveau mot de passe
async function savePassword() {
    if (!validatePasswordForm()) {
        return;
    }

    const passwordData = {
        current_password: currentPasswordInput.value.trim(),
        new_password: newPasswordInput.value.trim(),
        new_password_confirmation: confirmPasswordInput.value.trim()
    };

    try {
        const response = await fetch(baseApiUrl + '/password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(passwordData)
        });

        const data = await response.json();

        if (data.success) {
            showToast('success', 'Mot de passe modifié', data.message);

            if (passwordForm) passwordForm.reset();

            if (passwordStrengthBar) passwordStrengthBar.style.width = '0%';
            updateCheckIcon(lengthCheck, false);
            updateCheckIcon(uppercaseCheck, false);
            updateCheckIcon(lowercaseCheck, false);
            updateCheckIcon(numberCheck, false);

            // Rediriger vers la page de connexion après 2 secondes
            setTimeout(() => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.href = '/'; // Fallback
                }
            }, 2000);
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message,
                    confirmButtonText: 'D\'accord',
                    confirmButtonColor: '#3b82f6'
                });
            }
        }
    } catch (error) {
        console.error('Erreur:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors du changement de mot de passe',
                confirmButtonText: 'D\'accord',
                confirmButtonColor: '#3b82f6'
            });
        }
    }
}
// Event listeners pour le formulaire de mot de passe
if (passwordForm) {
    passwordForm.addEventListener('submit', (e) => {
        e.preventDefault();
        savePassword();
    });
}

if (cancelPasswordBtn) {
    cancelPasswordBtn.addEventListener('click', (e) => {
        e.preventDefault();

        if (passwordForm) passwordForm.reset();

        if (passwordStrengthBar) passwordStrengthBar.style.width = '0%';
        if (passwordStrengthText) {
            passwordStrengthText.textContent = 'Faible';
            passwordStrengthText.classList.remove('text-yellow-600', 'text-green-600');
            passwordStrengthText.classList.add('text-red-600');
        }
        if (passwordStrengthBar) {
            passwordStrengthBar.classList.remove('bg-yellow-500', 'bg-green-500');
            passwordStrengthBar.classList.add('bg-red-500');
        }

        updateCheckIcon(lengthCheck, false);
        updateCheckIcon(uppercaseCheck, false);
        updateCheckIcon(lowercaseCheck, false);
        updateCheckIcon(numberCheck, false);

        showToast('info', 'Annulé', 'Les modifications ont été annulées');
    });
}

// ========== INITIALISATION ==========
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Gestion du profil et mot de passe initialisée');
    console.log('🔗 API URL:', baseApiUrl);

    if (passwordStrengthBar) {
        passwordStrengthBar.style.width = '0%';
    }
});
