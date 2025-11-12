// ==================== FONCTION MINI POPUP TOAST ====================
function showMiniToast(message, type = 'success') {
    // Supprimer les anciens toasts
    const existingToasts = document.querySelectorAll('.custom-toast');
    existingToasts.forEach(toast => toast.remove());

    // Créer le nouveau toast
    const toast = document.createElement('div');
    toast.className = `custom-toast ${type}`;

    // Définir l'icône et le titre selon le type
    let icon, title;
    if (type === 'success') {
        icon = 'fa-check-circle';
        title = 'Succès';
    } else {
        icon = 'fa-exclamation-circle';
        title = 'Erreur';
    }

    toast.innerHTML = `
        <div class="custom-toast-icon">
            <i class="fas ${icon}"></i>
        </div>
        <div class="custom-toast-content">
            <div class="custom-toast-title">${title}</div>
            <div class="custom-toast-message">${message}</div>
        </div>
        <button class="custom-toast-close">
            <i class="fas fa-times"></i>
        </button>
    `;

    document.body.appendChild(toast);

    // Bouton de fermeture
    const closeBtn = toast.querySelector('.custom-toast-close');
    closeBtn.addEventListener('click', () => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 500);
    });

    // Afficher le toast avec animation
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);

    // Retirer automatiquement après 4 secondes
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 500);
    }, 4000);
}

// ==================== GESTION DU LOADER ====================
window.addEventListener('load', function() {
    const loader = document.getElementById('loader');
    const mainContent = document.getElementById('mainContent');

    let progress = 0;
    const progressBar = document.querySelector('.loader-progress-bar');
    const interval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress >= 100) {
            progress = 100;
            clearInterval(interval);

            setTimeout(() => {
                loader.classList.add('hide');
                setTimeout(() => {
                    loader.style.display = 'none';
                    mainContent.classList.add('show');
                }, 500);
            }, 500);
        }
        progressBar.style.width = `${progress}%`;
    }, 200);
});

// ==================== GESTION DU THÈME ====================
const themeToggle = document.getElementById('themeToggle');
const body = document.body;
let currentTheme = localStorage.getItem('theme') || 'dark';

if (currentTheme === 'light') {
    body.setAttribute('data-theme', 'light');
    themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
}

themeToggle.addEventListener('click', () => {
    if (currentTheme === 'light') {
        body.removeAttribute('data-theme');
        themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        currentTheme = 'dark';
    } else {
        body.setAttribute('data-theme', 'light');
        themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        currentTheme = 'light';
    }
    localStorage.setItem('theme', currentTheme);
});

// ==================== GESTION DU MOT DE PASSE ====================
const passwordToggle = document.getElementById('passwordToggle');
const passwordInput = document.getElementById('password');

if (passwordToggle && passwordInput) {
    passwordToggle.addEventListener('click', () => {
        const icon = passwordToggle.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            icon.className = 'fas fa-eye';
        }
    });
}

// ==================== GESTION DU POPUP MOT DE PASSE OUBLIÉ ====================
const forgotPasswordBtn = document.getElementById('forgotPasswordBtn');
const forgotPasswordPopup = document.getElementById('forgotPasswordPopup');
const closePopup = document.getElementById('closePopup');
const forgotPasswordForm = document.getElementById('forgotPasswordForm');

if (forgotPasswordBtn && forgotPasswordPopup) {
    forgotPasswordBtn.addEventListener('click', () => {
        forgotPasswordPopup.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    });
}

if (closePopup && forgotPasswordPopup) {
    closePopup.addEventListener('click', () => {
        forgotPasswordPopup.style.display = 'none';
        document.body.style.overflow = 'auto';
    });
}

if (forgotPasswordPopup) {
    forgotPasswordPopup.addEventListener('click', (e) => {
        if (e.target === forgotPasswordPopup) {
            forgotPasswordPopup.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
}

// ==================== FORMULAIRE DE RÉINITIALISATION (AVEC LARAVEL) ====================
if (forgotPasswordForm) {
    forgotPasswordForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const resetEmail = document.getElementById('resetEmail').value;
        const btnContent = document.querySelector('#resetSubmitBtn .btn-content');
        const btnLoader = document.querySelector('#resetSubmitBtn .btn-loader');

        if (!resetEmail) {
            showMiniToast("Veuillez saisir votre adresse email", 'error');
            return;
        }

        if (!resetEmail.includes('@')) {
            showMiniToast("Veuillez utiliser une adresse email valide", 'error');
            return;
        }

        // Afficher le loader
        if (btnContent) btnContent.style.opacity = '0';
        if (btnLoader) btnLoader.style.display = 'block';

        try {
            // ✅ CORRECTION: Utiliser la bonne route Laravel
            const response = await fetch('/auth/forgot-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email: resetEmail })
            });

            const data = await response.json();

            // Masquer le loader
            if (btnContent) btnContent.style.opacity = '1';
            if (btnLoader) btnLoader.style.display = 'none';

            if (data.success) {
                showMiniToast(data.message, 'success');

                // Fermer le popup après un court délai
                setTimeout(() => {
                    forgotPasswordPopup.style.display = 'none';
                    document.body.style.overflow = 'auto';
                    document.getElementById('resetEmail').value = '';
                }, 2000);
            } else {
                showMiniToast(data.message, 'error');
            }
        } catch (error) {
            // Masquer le loader en cas d'erreur
            if (btnContent) btnContent.style.opacity = '1';
            if (btnLoader) btnLoader.style.display = 'none';

            console.error('Erreur:', error);
            showMiniToast("Une erreur s'est produite. Veuillez réessayer.", 'error');
        }
    });
}

// ==================== FORMULAIRE DE CONNEXION ====================
const loginForm = document.getElementById('loginForm');

if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const remember = document.getElementById('remember') ? document.getElementById('remember').checked : false;
        const btnContent = document.querySelector('#submitBtn .btn-content');
        const btnLoader = document.querySelector('#submitBtn .btn-loader');

        // Validation basique
        if (!email || !password) {
            showMiniToast('Veuillez remplir tous les champs', 'error');
            return;
        }

        // Afficher le loader
        if (btnContent) btnContent.style.opacity = '0';
        if (btnLoader) btnLoader.style.display = 'block';

        try {
            // ✅ CORRECTION: La route est bien '/login'
            const response = await fetch('/logi', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    password: password,
                    remember: remember
                })
            });

            const data = await response.json();

            if (data.success) {
                showMiniToast(data.message, 'success');

                // Redirection après 1.5 secondes
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                // Masquer le loader et afficher l'erreur
                if (btnContent) btnContent.style.opacity = '1';
                if (btnLoader) btnLoader.style.display = 'none';
                showMiniToast(data.message, 'error');
            }
        } catch (error) {
            // Masquer le loader en cas d'erreur
            if (btnContent) btnContent.style.opacity = '1';
            if (btnLoader) btnLoader.style.display = 'none';

            console.error('Erreur:', error);
            showMiniToast("Une erreur s'est produite lors de la connexion", 'error');
        }
    });
}

// ==================== ANIMATIONS SUPPLÉMENTAIRES ====================
const inputs = document.querySelectorAll('.form-input');
inputs.forEach(input => {
    input.addEventListener('focus', () => {
        input.style.boxShadow = '0 0 0 4px rgba(79, 70, 229, 0.2)';
    });

    input.addEventListener('blur', () => {
        input.style.boxShadow = 'none';
    });
});
