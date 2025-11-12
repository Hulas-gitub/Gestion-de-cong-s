// ==================== FONCTION DE DÉCONNEXION AVEC LARAVEL (SANS CHANGER VOS TOASTS) ====================

// Fonction pour ouvrir le modal de déconnexion
function openLogoutModal() {
    const modal = document.getElementById('logoutConfirmModal');
    if (!modal) {
        console.error('Le modal de déconnexion (logoutConfirmModal) n\'existe pas dans le DOM');
        // Fallback : déconnexion directe si le modal n'existe pas
        if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
            executeLogout();
        }
        return;
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Animation d'ouverture
    setTimeout(() => {
        const modalContent = modal.querySelector('.modal');
        if (modalContent) {
            modalContent.style.opacity = '1';
            modalContent.style.transform = 'scale(1)';
        }
    }, 10);
}

// Fonction pour fermer le modal de déconnexion
function closeLogoutModal() {
    const modal = document.getElementById('logoutConfirmModal');
    if (!modal) {
        return;
    }

    const modalContent = modal.querySelector('.modal');
    if (modalContent) {
        modalContent.style.opacity = '0';
        modalContent.style.transform = 'scale(0.95)';
    }

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }, 200);
}

// Fonction d'exécution de la déconnexion avec Laravel
function executeLogout() {
    try {
        // Fermer le modal
        closeLogoutModal();

        // Soumettre le formulaire de déconnexion
        setTimeout(() => {
            const logoutForm = document.getElementById('logout-form');
            if (logoutForm) {
                logoutForm.submit();
            } else {
                console.error('Formulaire de déconnexion introuvable');
                // Fallback : créer un formulaire dynamique avec CSRF
                createAndSubmitLogoutForm();
            }
        }, 300);

    } catch (error) {
        console.error('Erreur lors de la déconnexion:', error);

        // Utiliser VOTRE système de toast existant
        if (typeof showMiniToast === 'function') {
            showMiniToast("Une erreur s'est produite lors de la déconnexion", 'error');
        } else {
            alert("Une erreur s'est produite lors de la déconnexion");
        }
    }
}

// Fonction de secours pour créer un formulaire avec CSRF
function createAndSubmitLogoutForm() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/logout';

    // Ajouter le token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.content;
        form.appendChild(csrfInput);
    }

    document.body.appendChild(form);
    form.submit();
}

// Event listener pour le bouton de déconnexion
document.addEventListener('DOMContentLoaded', function() {
    // Chercher le bouton de déconnexion
    const logoutBtn = document.getElementById('logoutBtn');

    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openLogoutModal();
        });
    }

    // Fermer le modal en appuyant sur Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('logoutConfirmModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeLogoutModal();
            }
        }
    });

    // Initialisation des styles du modal
    const logoutModal = document.getElementById('logoutConfirmModal');
    if (logoutModal) {
        const modal = logoutModal.querySelector('.modal');
        if (modal) {
            modal.style.opacity = '0';
            modal.style.transform = 'scale(0.95)';
            modal.style.transition = 'all 0.2s ease-out';
        }
    }

    // Fermer le modal si on clique en dehors
    const modal = document.getElementById('logoutConfirmModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeLogoutModal();
            }
        });
    }
});

// ==================== GESTION DES ONGLETS ====================

document.addEventListener('DOMContentLoaded', function () {
    initTabNavigation();

    const profileTab = document.getElementById('profile-tab');
    if (profileTab) {
        switchTab('profile');
    }
});

function initTabNavigation() {
    const tabButtons = document.querySelectorAll('.tab-button');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            if (tabName) {
                switchTab(tabName);
            }
        });
    });
}

function switchTab(tabName) {
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
        button.classList.remove('bg-gradient-to-r', 'from-blue-500', 'to-purple-600', 'text-white', 'shadow-lg');
        button.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    });

    document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.add('hidden');
        pane.classList.remove('active');
    });

    const activeButton = document.querySelector(`.tab-button[data-tab="${tabName}"]`);
    if (activeButton) {
        activeButton.classList.add('active');
        activeButton.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
        activeButton.classList.add('bg-gradient-to-r', 'from-blue-500', 'to-purple-600', 'text-white', 'shadow-lg');
    }

    const activePane = document.getElementById(`${tabName}-tab`);
    if (activePane) {
        activePane.classList.remove('hidden');
        activePane.classList.add('active');
    }
}
