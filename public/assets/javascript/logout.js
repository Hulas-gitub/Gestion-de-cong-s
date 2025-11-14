// ==================== FONCTION DE DÉCONNEXION AVEC LARAVEL (VERSION SÉCURISÉE) ====================

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
    document.body.style.overflow = 'hidden'; // Bloquer le scroll

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
        console.error('Le modal de déconnexion (logoutConfirmModal) n\'existe pas dans le DOM');
        return;
    }

    const modalContent = modal.querySelector('.modal');
    if (modalContent) {
        modalContent.style.opacity = '0';
        modalContent.style.transform = 'scale(0.95)';
    }

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Réactiver le scroll
    }, 200);
}

// ✅ Fonction pour afficher le toast de déconnexion
function showLogoutToast(message = 'Déconnexion réussie') {
    const toast = document.getElementById('logoutToast');
    if (!toast) {
        console.warn('Le toast de déconnexion (logoutToast) n\'existe pas dans le DOM');
        return;
    }

    // Mettre à jour le message si nécessaire
    const toastTitle = toast.querySelector('p.font-semibold');
    if (toastTitle && message !== 'Déconnexion réussie') {
        toastTitle.textContent = message;
    }

    // Afficher le toast avec animation
    toast.classList.remove('translate-x-full');
    toast.classList.add('translate-x-0');

    // Masquer automatiquement après 3 secondes
    setTimeout(() => {
        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-full');
    }, 3000);
}

// Fonction d'exécution de la déconnexion avec Laravel
function executeLogout() {
    try {
        // Créer un formulaire pour envoyer la requête POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/logout';

        // Ajouter le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('Token CSRF introuvable. Assurez-vous que <meta name="csrf-token"> existe dans votre page.');
            alert('Erreur: Token CSRF manquant');
            return;
        }

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.content;
        form.appendChild(csrfInput);

        // Ajouter le formulaire au body
        document.body.appendChild(form);

        // Fermer le modal avant la soumission
        closeLogoutModal();

        // ✅ Afficher le toast de déconnexion
        setTimeout(() => {
            showLogoutToast('Vous êtes déconnecté');
        }, 300);

        // ✅ Nettoyer l'historique et empêcher le retour
        if (window.history && window.history.pushState) {
            window.history.pushState(null, null, window.location.href);

            // Empêcher le retour arrière
            window.addEventListener('popstate', function(event) {
                window.history.pushState(null, null, window.location.href);
            });
        }

        // Soumettre le formulaire après un court délai
        setTimeout(() => {
            form.submit();
        }, 1000);

    } catch (error) {
        console.error('Erreur lors de la déconnexion:', error);

        // Fermer le modal en cas d'erreur
        closeLogoutModal();

        // Afficher un message d'erreur
        if (typeof showMiniToast === 'function') {
            showMiniToast("Une erreur s'est produite lors de la déconnexion", 'error');
        } else {
            alert("Une erreur s'est produite lors de la déconnexion");
        }
    }
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
        console.log('✅ Event listener de déconnexion initialisé');
    } else {
        console.warn('⚠️ Bouton de déconnexion (logoutBtn) non trouvé dans le DOM');
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
        console.log('✅ Modal de déconnexion initialisé');
    } else {
        console.warn('⚠️ Modal de déconnexion (logoutConfirmModal) non trouvé dans le DOM');
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

    // ✅ Initialiser le toast de déconnexion
    const logoutToast = document.getElementById('logoutToast');
    if (logoutToast) {
        console.log('✅ Toast de déconnexion initialisé');
    } else {
        console.warn('⚠️ Toast de déconnexion (logoutToast) non trouvé dans le DOM');
    }
});

// ==================== GESTION DES ONGLETS ====================

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function () {
    // Initialiser les écouteurs d'événements pour les onglets
    initTabNavigation();

    // Afficher l'onglet profile par défaut au chargement
    const profileTab = document.getElementById('profile-tab');
    if (profileTab) {
        switchTab('profile');
    }
});

// Initialiser la navigation par onglets
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

// Fonction pour changer d'onglet
function switchTab(tabName) {
    // Désactiver tous les boutons d'onglets
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
        // Retirer les classes de style actif
        button.classList.remove('bg-gradient-to-r', 'from-blue-500', 'to-purple-600', 'text-white', 'shadow-lg');
        // Ajouter les classes de style inactif
        button.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    });

    // Masquer tous les panneaux d'onglets
    document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.add('hidden');
        pane.classList.remove('active');
    });

    // Activer le bouton sélectionné
    const activeButton = document.querySelector(`.tab-button[data-tab="${tabName}"]`);
    if (activeButton) {
        activeButton.classList.add('active');
        // Retirer les classes inactives
        activeButton.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
        // Ajouter les classes actives
        activeButton.classList.add('bg-gradient-to-r', 'from-blue-500', 'to-purple-600', 'text-white', 'shadow-lg');
    }

    // Afficher le panneau correspondant
    const activePane = document.getElementById(`${tabName}-tab`);
    if (activePane) {
        activePane.classList.remove('hidden');
        activePane.classList.add('active');
    }
}
