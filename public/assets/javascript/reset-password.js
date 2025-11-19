// ==================== FONCTION MINI POPUP TOAST ====================
function showMiniToast(message, type = 'success') {
    const existingToasts = document.querySelectorAll('.custom-toast');
    existingToasts.forEach(toast => toast.remove());

    const toast = document.createElement('div');
    toast.className = `custom-toast ${type}`;

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

    const closeBtn = toast.querySelector('.custom-toast-close');
    closeBtn.addEventListener('click', () => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 500);
    });

    setTimeout(() => toast.classList.add('show'), 100);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 500);
    }, 4000);
}

// ==================== FONCTION CSRF TOKEN ====================
function getCsrfToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.getAttribute('content') : '';
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

// ==================== GESTION DE L'AFFICHAGE DU MOT DE PASSE ====================
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

const passwordConfirmToggle = document.getElementById('passwordConfirmToggle');
const passwordConfirmInput = document.getElementById('password_confirmation');

if (passwordConfirmToggle && passwordConfirmInput) {
    passwordConfirmToggle.addEventListener('click', () => {
        const icon = passwordConfirmToggle.querySelector('i');
        if (passwordConfirmInput.type === 'password') {
            passwordConfirmInput.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            passwordConfirmInput.type = 'password';
            icon.className = 'fas fa-eye';
        }
    });
}

// ==================== FORMULAIRE DE RÉINITIALISATION ====================
const resetPasswordForm = document.getElementById('resetPasswordForm');

if (resetPasswordForm) {
    resetPasswordForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const token = document.getElementById('resetToken').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        const btnContent = document.querySelector('#submitBtn .btn-content');
        const btnLoader = document.querySelector('#submitBtn .btn-loader');

        // Validations
        if (!email || !password || !passwordConfirmation) {
            showMiniToast('Veuillez remplir tous les champs', 'error');
            return;
        }

        if (!email.includes('@')) {
            showMiniToast('Veuillez saisir une adresse email valide', 'error');
            return;
        }

        if (password.length < 6) {
            showMiniToast('Le mot de passe doit contenir au moins 6 caractères', 'error');
            return;
        }

        if (password !== passwordConfirmation) {
            showMiniToast('Les mots de passe ne correspondent pas', 'error');
            return;
        }

        // Afficher le loader
        if (btnContent) btnContent.style.opacity = '0';
        if (btnLoader) btnLoader.style.display = 'block';

        try {
            const csrfToken = getCsrfToken();

            const response = await fetch('/auth/reset-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    token: token,
                    email: email,
                    password: password,
                    password_confirmation: passwordConfirmation
                })
            });

            const data = await response.json();

            // Masquer le loader
            if (btnContent) btnContent.style.opacity = '1';
            if (btnLoader) btnLoader.style.display = 'none';

            if (response.ok && data.success) {
                showMiniToast(data.message, 'success');

                // Rediriger vers la page de connexion après 2 secondes
                setTimeout(() => {
                    window.location.href = '/';
                }, 2000);
            } else {
                showMiniToast(data.message || 'Erreur lors de la réinitialisation', 'error');
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

// ==================== ANIMATIONS ====================
const inputs = document.querySelectorAll('.form-input');
inputs.forEach(input => {
    input.addEventListener('focus', () => {
        input.style.boxShadow = '0 0 0 4px rgba(79, 70, 229, 0.2)';
    });

    input.addEventListener('blur', () => {
        input.style.boxShadow = 'none';
    });
});
