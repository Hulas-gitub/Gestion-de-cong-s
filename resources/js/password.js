
// Éléments du DOM pour les mots de passe
const passwordForm = document.getElementById('password-form');
const currentPasswordInput = document.getElementById('current-password');
const newPasswordInput = document.getElementById('new-password');
const confirmPasswordInput = document.getElementById('confirm-password');
const cancelPasswordBtn = document.getElementById('cancel-password-btn');
const savePasswordBtn = document.getElementById('save-password-btn');

// Éléments pour la validation du mot de passe
const strengthBar = document.getElementById('password-strength-bar');
const strengthText = document.getElementById('password-strength-text');
const lengthCheck = document.getElementById('length-check');
const uppercaseCheck = document.getElementById('uppercase-check');
const lowercaseCheck = document.getElementById('lowercase-check');
const numberCheck = document.getElementById('number-check');
const matchMessage = document.getElementById('password-match-message');
const matchIcon = document.getElementById('match-icon');
const matchText = document.getElementById('match-text');

// Fonction pour basculer la visibilité du mot de passe
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash w-4 h-4';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye w-4 h-4';
    }
}

// Fonction pour évaluer la force du mot de passe (sans caractères spéciaux)
function evaluatePasswordStrength(password) {
    let score = 0;
    
    // Critères de validation
    const hasLength = password.length >= 8;
    const hasUppercase = /[A-Z]/.test(password);
    const hasLowercase = /[a-z]/.test(password);
    const hasNumber = /\d/.test(password);
    
    // Mise à jour des indicateurs visuels
    updateCheck(lengthCheck, hasLength);
    updateCheck(uppercaseCheck, hasUppercase);
    updateCheck(lowercaseCheck, hasLowercase);
    updateCheck(numberCheck, hasNumber);
    
    // Calcul du score
    if (hasLength) score += 25;
    if (hasUppercase) score += 25;
    if (hasLowercase) score += 25;
    if (hasNumber) score += 25;
    
    // Mise à jour de la barre de progression
    strengthBar.style.width = score + '%';
    
    // Détermination du niveau de sécurité
    let level, color, text;
    if (score < 50) {
        level = 'weak';
        color = '#ef4444';
        text = 'Faible';
    } else if (score < 75) {
        level = 'fair';
        color = '#f59e0b';
        text = 'Moyen';
    } else if (score < 100) {
        level = 'good';
        color = '#3b82f6';
        text = 'Bon';
    } else {
        level = 'strong';
        color = '#10b981';
        text = 'Fort';
    }
    
    strengthBar.style.backgroundColor = color;
    strengthText.textContent = text;
    strengthText.style.color = color;
    
    // Retourne true seulement si tous les critères sont remplis
    return hasLength && hasUppercase && hasLowercase && hasNumber;
}

function updateCheck(element, isValid) {
    if (isValid) {
        element.className = 'fas fa-check text-green-500';
    } else {
        element.className = 'fas fa-times text-red-500';
    }
}

// Fonction pour vérifier la correspondance des mots de passe
function checkPasswordMatch() {
    const newPassword = newPasswordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    
    if (confirmPassword.length === 0) {
        matchMessage.classList.add('hidden');
        return;
    }
    
    matchMessage.classList.remove('hidden');
    
    if (newPassword === confirmPassword) {
        matchIcon.className = 'fas fa-check text-green-500';
        matchText.textContent = 'Les mots de passe correspondent';
        matchText.className = 'text-green-600 dark:text-green-400';
        return true;
    } else {
        matchIcon.className = 'fas fa-times text-red-500';
        matchText.textContent = 'Les mots de passe ne correspondent pas';
        matchText.className = 'text-red-600 dark:text-red-400';
        return false;
    }
}

// Fonction pour valider le formulaire
function validateForm() {
    const currentPassword = currentPasswordInput.value;
    const newPassword = newPasswordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    
    // Vérifications
    const hasCurrentPassword = currentPassword.length > 0;
    const isNewPasswordStrong = evaluatePasswordStrength(newPassword);
    const doPasswordsMatch = checkPasswordMatch();
    
    // Activation/désactivation du bouton de sauvegarde
    const isFormValid = hasCurrentPassword && isNewPasswordStrong && doPasswordsMatch;
    savePasswordBtn.disabled = !isFormValid;
    
    return isFormValid;
}

// Event Listeners pour les mots de passe
newPasswordInput.addEventListener('input', validateForm);
confirmPasswordInput.addEventListener('input', validateForm);
currentPasswordInput.addEventListener('input', validateForm);

// Bouton annuler
cancelPasswordBtn.addEventListener('click', () => {
    passwordForm.reset();
    matchMessage.classList.add('hidden');
    strengthBar.style.width = '0%';
    strengthText.textContent = 'Faible';
    strengthText.style.color = '#6b7280';
    
    // Réinitialiser les checks
    const checks = [lengthCheck, uppercaseCheck, lowercaseCheck, numberCheck];
    checks.forEach(check => {
        check.className = 'fas fa-times text-red-500';
    });
    
    savePasswordBtn.disabled = true;
});

// Soumission du formulaire
passwordForm.addEventListener('submit', (e) => {
    e.preventDefault();
    
    if (!validateForm()) {
        alert('Veuillez corriger les erreurs avant de soumettre le formulaire.');
        return;
    }
    
    // Récupérer les données du formulaire
    const formData = new FormData(passwordForm);
    const currentPassword = formData.get('current_password');
    const newPassword = formData.get('new_password');
    const confirmPassword = formData.get('confirm_password');
    
    console.log('Changement de mot de passe:', {
        currentPassword: '***', // Ne pas logger le mot de passe
        newPassword: '***',
        confirmPassword: '***'
    });
    
    // Simuler la sauvegarde
    savePasswordBtn.disabled = true;
    savePasswordBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Changement en cours...';
    
    setTimeout(() => {
        alert('Mot de passe changé avec succès !');
        passwordForm.reset();
        matchMessage.classList.add('hidden');
        strengthBar.style.width = '0%';
        strengthText.textContent = 'Faible';
        strengthText.style.color = '#6b7280';
        
        // Réinitialiser les checks
        const checks = [lengthCheck, uppercaseCheck, lowercaseCheck, numberCheck];
        checks.forEach(check => {
            check.className = 'fas fa-times text-red-500';
        });
        
        savePasswordBtn.disabled = true;
        savePasswordBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Changer le mot de passe';
    }, 2000);
});

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    savePasswordBtn.disabled = true;
});
