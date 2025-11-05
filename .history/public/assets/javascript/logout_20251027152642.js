 
        // Fonction pour ouvrir le modal de déconnexion
        function openLogoutModal() {
            const modal = document.getElementById('logoutConfirmModal');
            modal.classList.remove('hidden');
            // Animation d'ouverture
            setTimeout(() => {
                modal.querySelector('.modal').style.opacity = '1';
                modal.querySelector('.modal').style.transform = 'scale(1)';
            }, 10);
        }

        // Fonction pour fermer le modal de déconnexion
        function closeLogoutModal() {
            const modal = document.getElementById('logoutConfirmModal');
            modal.querySelector('.modal').style.opacity = '0';
            modal.querySelector('.modal').style.transform = 'scale(0.95)';
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        // Fonction pour afficher le toast de déconnexion
        function showLogoutToast() {
            const toast = document.getElementById('logoutToast');
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
            
            // Masquer automatiquement après 3 secondes
            setTimeout(() => {
                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-full');
            }, 3000);
        }

        // Fonction d'exécution de la déconnexion
        function executeLogout() {
            // Fermer le modal
            closeLogoutModal();
            
            // Afficher le toast
            setTimeout(() => {
                showLogoutToast();
            }, 300);
            
            // Rediriger vers la page d'accueil après 2 secondes
            setTimeout(() => {
                window.location.href = '../index.html';
            }, 2000);
        }

        // Event listener pour le bouton de déconnexion
        document.getElementById('logoutBtn').addEventListener('click', function(e) {
            e.preventDefault();
            openLogoutModal();
        });

        // Fermer le modal en appuyant sur Échap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogoutModal();
            }
        });

        // Initialisation des styles du modal
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('logoutConfirmModal').querySelector('.modal');
            modal.style.opacity = '0';
            modal.style.transform = 'scale(0.95)';
            modal.style.transition = 'all 0.2s ease-out';
        });

        // Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function () {
    // Initialiser les écouteurs d'événements pour les onglets uniquement
    initTabNavigation();
    
    // Afficher l'onglet profile par défaut au chargement
    switchTab('profile');
});

// Initialiser la navigation par onglets
function initTabNavigation() {
    const tabButtons = document.querySelectorAll('.tab-button');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
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








