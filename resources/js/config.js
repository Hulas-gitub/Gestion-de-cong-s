"use strict";

try {
    // Initialize Notyf (avec types additionnels pour 'error')
    const notyf = new Notyf({
        duration: 4000,
        position: { x: 'right', y: 'top' },
        dismissible: true,
        types: [
            {
                type: 'success',
                background: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                icon: { className: 'fas fa-check', tagName: 'i' }
            },
            {
                type: 'info',
                background: 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)',
                icon: { className: 'fas fa-info-circle', tagName: 'i' }
            },
            {
                type: 'warning',
                background: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                icon: { className: 'fas fa-exclamation-triangle', tagName: 'i' }
            },
            {
                type: 'error',
                background: 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)',
                icon: { className: 'fas fa-times-circle', tagName: 'i' }
            }
        ]
    });
} catch (err) {
    console.warn('Notyf non disponible ou erreur d\'initialisation:', err);
    // fallback simple pour éviter que le script plante
    window.notyf = {
        success: (m) => console.log('SUCCESS:', m),
        info: (m) => console.log('INFO:', m),
        error: (m) => console.log('ERROR:', m),
    };
}

// JSON Data for dynamic content
const appData = {
    notifications: [
        { id: 1, type: 'info', message: 'Nouvelle demande de Pierre Durant', time: 'Il y a 2h', icon: 'fas fa-user-plus', color: 'blue' },
        { id: 2, type: 'success', message: 'Votre demande de congé approuvée', time: 'Il y a 1j', icon: 'fas fa-check-circle', color: 'green' },
        { id: 3, type: 'warning', message: 'Rappel: Réunion équipe demain', time: 'Il y a 3h', icon: 'fas fa-calendar', color: 'yellow' },
        { id: 4, type: 'info', message: 'Mise à jour du système', time: 'Il y a 5h', icon: 'fas fa-cog', color: 'purple' },
        { id: 5, type: 'error', message: 'Demande refusée - Marie L.', time: 'Il y a 1j', icon: 'fas fa-times-circle', color: 'red' }
    ],
    demands: [
        { id: 1, type: 'Congés payés', dates: '2024-01-15 - 2024-01-19', days: 5, status: 'pending', icon: 'fas fa-umbrella-beach' },
        { id: 2, type: 'Congé maladie', dates: '2024-01-10 - 2024-01-12', days: 3, status: 'approved', icon: 'fas fa-notes-medical' },
        { id: 3, type: 'RTT', dates: '2024-01-08 - 2024-01-08', days: 1, status: 'rejected', icon: 'fas fa-business-time' }
    ]
};

// Variable pour tracker si c'est la première visite
let isFirstVisit = true;

// Utility Functions
function updateDateTime() {
    const now = new Date();
    const options = {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit'
    };
    const el = document.getElementById('current-date');
    if (el) el.textContent = now.toLocaleString('fr-FR', options);
}

function showNotificationToken(message, icon = 'fas fa-check', type = 'success') {
    const token = document.getElementById('notification-token');
    if (!token) return;
    const tokenText = document.getElementById('token-text');
    const tokenIcon = document.getElementById('token-icon');

    if (tokenText) tokenText.textContent = message;
    if (tokenIcon) tokenIcon.className = icon;

    const colors = {
        success: 'from-green-500 to-emerald-500',
        info: 'from-blue-500 to-cyan-500',
        warning: 'from-yellow-500 to-orange-500',
        error: 'from-red-500 to-pink-500'
    };

    token.className = `notification-token glass-effect text-white px-6 py-3 rounded-2xl shadow-2xl bg-gradient-to-r ${colors[type] || colors.success}`;
    token.classList.add('show');

    setTimeout(() => {
        token.classList.remove('show');
    }, 4000);
}

// Theme Management
function initTheme() {
    const theme = localStorage.getItem('theme') || 'light';
    document.documentElement.className = theme === 'dark' ? 'dark h-full' : 'h-full';
}

function toggleTheme() {
    const isDark = document.documentElement.classList.contains('dark');
    const newTheme = isDark ? 'light' : 'dark';
    document.documentElement.className = newTheme === 'dark' ? 'dark h-full' : 'h-full';
    localStorage.setItem('theme', newTheme);

    // Pas de notification pour le changement de thème
}

// Sidebar Management
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (!sidebar || !overlay) return;
    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (!sidebar || !overlay) return;
    sidebar.classList.remove('show');
    overlay.classList.remove('show');
}

// Notifications Management
function toggleNotifications() {
    const dropdown = document.getElementById('notifications-dropdown');
    if (!dropdown) return;
    dropdown.classList.toggle('show');
    // Pas de notification pour l'ouverture des notifications
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize
    initTheme();
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // SEULE notification de bienvenue au premier chargement
    if (isFirstVisit) {
        setTimeout(() => {
            showNotificationToken('Bienvenue sur Graxel Tech', 'fas fa-rocket', 'success');
            if (window.notyf && notyf.success) notyf.success('Bienvenue Jean Martin ! Tableau de bord chargé avec succès.');
            isFirstVisit = false; // Marquer comme visité
        }, 1000);
    }

    // Safely attach listeners only if elements exist
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) themeToggle.addEventListener('click', toggleTheme);

    const toggleSidebarBtn = document.getElementById('toggle-sidebar');
    if (toggleSidebarBtn) toggleSidebarBtn.addEventListener('click', toggleSidebar);

    const closeSidebarBtn = document.getElementById('close-sidebar');
    if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeSidebar);

    const sidebarOverlay = document.getElementById('sidebar-overlay');
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

    const notificationsBtn = document.getElementById('notifications-btn');
    if (notificationsBtn) {
        notificationsBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleNotifications();
        });
    }

    // Close notifications dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('notifications-dropdown');
        const btn = document.getElementById('notifications-btn');
        if (!dropdown || !btn) return;
        if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });

    // Action buttons SANS notifications
    const newRequestBtn = document.getElementById('new-request-btn');
    if (newRequestBtn) newRequestBtn.addEventListener('click', function() {
        // Juste l'action, pas de notification
        console.log('Nouvelle demande cliquée');
    });

    const calendarBtn = document.getElementById('calendar-btn');
    if (calendarBtn) calendarBtn.addEventListener('click', function() {
        console.log('Calendrier cliqué');
    });

    const reportsBtn = document.getElementById('reports-btn');
    if (reportsBtn) reportsBtn.addEventListener('click', function() {
        console.log('Rapports cliqué');
    });

    const teamBtn = document.getElementById('team-btn');
    if (teamBtn) teamBtn.addEventListener('click', function() {
        console.log('Équipe cliquée');
    });

    // Navigation SANS notifications
    const navItems = document.querySelectorAll('.nav-item');
    if (navItems && navItems.length) {
        navItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const targetUrl = this.getAttribute('href');
                if (targetUrl && targetUrl !== "#") {
                    setTimeout(() => {
                        window.location.href = targetUrl;
                    }, 300);
                }
            });
        });
    }

    // Demand items SANS notifications
    const demandItems = document.querySelectorAll('.demand-item');
    if (demandItems && demandItems.length) {
        demandItems.forEach(item => {
            item.addEventListener('click', function() {
                console.log('Demande consultée');
            });
        });
    }

    // Status badges SANS notifications
    const badges = document.querySelectorAll('span[class*="bg-yellow-100"], span[class*="bg-green-500"], span[class*="bg-red-500"]');
    if (badges && badges.length) {
        badges.forEach(badge => {
            badge.addEventListener('click', function(e) {
                e.stopPropagation();
                console.log('Badge cliqué');
            });
        });
    }

    // Calendar dates SANS notifications
    const greenDates = document.querySelectorAll('[class*="bg-green-100"]');
    if (greenDates && greenDates.length) {
        greenDates.forEach(dateEl => {
            dateEl.addEventListener('click', function() {
                console.log('Date cliquée');
            });
        });
    }

    // Add smooth animations to cards on scroll
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = '0s';
                entry.target.classList.add('animate-fade-in');
            }
        });
    }, observerOptions);

    // Observe all cards
    const slideCards = document.querySelectorAll('[class*="animate-slide-up"]');
    if (slideCards && slideCards.length) {
        slideCards.forEach(card => {
            observer.observe(card);
        });
    }

    // Add hover effects enhancement
    const hoverLift = document.querySelectorAll('.hover-lift');
    if (hoverLift && hoverLift.length) {
        hoverLift.forEach(element => {
            element.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
                this.style.boxShadow = '0 25px 50px rgba(0,0,0,0.15)';
            });

            element.addEventListener('mouseleave', function() {
                this.style.transform = '';
                this.style.boxShadow = '';
            });
        });
    }

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + D = Toggle dark mode
        if ((e.ctrlKey || e.metaKey) && (e.key === 'd' || e.key === 'D')) {
            e.preventDefault();
            toggleTheme();
        }

        // Ctrl/Cmd + N = New request
        if ((e.ctrlKey || e.metaKey) && (e.key === 'n' || e.key === 'N')) {
            e.preventDefault();
            const btn = document.getElementById('new-request-btn');
            if (btn) btn.click();
        }

        // Ctrl/Cmd + K = Toggle notifications
        if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
            e.preventDefault();
            toggleNotifications();
        }

        // Escape = Close sidebar and notifications
        if (e.key === 'Escape') {
            closeSidebar();
            const dropdown = document.getElementById('notifications-dropdown');
            if (dropdown) dropdown.classList.remove('show');
        }
    });

    // Add ripple effect to buttons
    document.querySelectorAll('button').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
                z-index: 9999;
            `;

            this.style.position = this.style.position || 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Add CSS for ripple animation (once)
    if (!document.getElementById('ripple-style')) {
        const style = document.createElement('style');
        style.id = 'ripple-style';
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }

    // Progressive loading simulation (fade/slide)
    if (slideCards && slideCards.length) {
        slideCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    // Add smooth scrolling for all internal anchors
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Initialize basic tooltips for icons
    document.querySelectorAll('[class*="fas fa-"]').forEach(icon => {
        const parts = icon.className.split('fa-');
        icon.title = parts[1] ? parts[1].replace('-', ' ') : 'Action';
    });

    console.log('🚀 Graxel Tech Dashboard initialized successfully!');
});

// Handle window resize for responsive behavior
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (!sidebar || !overlay) return;
    if (window.innerWidth >= 768) {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    }
});

// Service Worker for offline functionality (optional)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        console.log('💡 Service Worker support detected');
    });
}

// Configuration Tailwind
tailwind.config = {
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                'poppins': ['Poppins', 'sans-serif'],
            },
            animation: {
                'fade-in': 'fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1)',
                'slide-up': 'slideUp 0.8s cubic-bezier(0.4, 0, 0.2, 1)',
                'slide-right': 'slideRight 0.6s cubic-bezier(0.4, 0, 0.2, 1)',
                'bounce-subtle': 'bounceSubtle 1.2s ease-out',
                'pulse-ring': 'pulseRing 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite',
                'float': 'float 6s ease-in-out infinite',
                'glow': 'glow 2s ease-in-out infinite alternate',
                'shimmer': 'shimmer 2s linear infinite',
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
            }
        }
    }
}

// Attendre que le DOM et config.js soient chargés
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du pop-up nouvelle demande
    const btnNouvelleDemande = document.getElementById('btn-nouvelle-demande');
    const popupNouvelleDemande = document.getElementById('popup-nouvelle-demande');
    const closePopup = document.getElementById('close-popup');
    const annulerDemande = document.getElementById('annuler-demande');
    const soumettreDemandeBtn = document.getElementById('soumettre-demande');

    // Ouvrir le pop-up SANS notification
    if (btnNouvelleDemande && popupNouvelleDemande) {
        btnNouvelleDemande.addEventListener('click', () => {
            popupNouvelleDemande.classList.remove('hidden');
        });
    }

    // Fermer le pop-up
    function fermerPopup() {
        if (popupNouvelleDemande) {
            popupNouvelleDemande.classList.add('hidden');
        }
    }

    if (closePopup) closePopup.addEventListener('click', fermerPopup);
    if (annulerDemande) annulerDemande.addEventListener('click', fermerPopup);

    // Fermer en cliquant en dehors
    if (popupNouvelleDemande) {
        popupNouvelleDemande.addEventListener('click', (e) => {
            if (e.target === popupNouvelleDemande) {
                fermerPopup();
            }
        });
    }

    // Soumettre la demande AVEC notification (car c'est important)
    if (soumettreDemandeBtn) {
        soumettreDemandeBtn.addEventListener('click', () => {
            showNotificationToken('Demande soumise avec succès', 'fas fa-check', 'success');
            if (window.notyf && notyf.success) {
                notyf.success('Votre demande de congé a été soumise pour validation');
            }
            fermerPopup();
        });
    }

    // Actions rapides SANS notifications
    const actionButtons = document.querySelectorAll('.gradient-bg, a[href*="calendrier"], a[href*="rapports"], a[href*="equipe"]');
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (!href || href === '#') {
                e.preventDefault();
            }
        });
    });
});

// Gestion du loader
window.addEventListener('load', function() {
    const loader = document.getElementById('loader');
    const mainContent = document.getElementById('mainContent');
    
    setTimeout(() => {
        loader.classList.add('hide');
        setTimeout(() => {
            loader.style.display = 'none';
            mainContent.classList.add('show');
        }, 500);
    }, 3000);
});

// Gestion du thème (utilisation de variables JS au lieu de localStorage pour éviter les erreurs)
const themeToggleLogin = document.getElementById('themeToggle');
if (themeToggleLogin) {
    const body = document.body;
    let currentTheme = 'dark';
    
    themeToggleLogin.addEventListener('click', () => {
        if (currentTheme === 'light') {
            body.removeAttribute('data-theme');
            themeToggleLogin.innerHTML = '<i class="fas fa-moon"></i>';
            currentTheme = 'dark';
        } else {
            body.setAttribute('data-theme', 'light');
            themeToggleLogin.innerHTML = '<i class="fas fa-sun"></i>';
            currentTheme = 'light';
        }
    });
}

// Gestion du mot de passe
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

// Gestion des rôles
const roleOptions = document.querySelectorAll('.role-option');
const roleRadios = document.querySelectorAll('.role-radio');

if (roleOptions.length && roleRadios.length) {
    roleOptions.forEach((option, index) => {
        option.addEventListener('click', () => {
            roleOptions.forEach(opt => opt.classList.remove('selected'));
            option.classList.add('selected');
            roleRadios[index].checked = true;
        });
    });

    roleRadios.forEach((radio, index) => {
        radio.addEventListener('change', () => {
            if (radio.checked) {
                roleOptions.forEach(opt => opt.classList.remove('selected'));
                roleOptions[index].classList.add('selected');
            }
        });
    });
}

// Soumission du formulaire
const loginForm = document.getElementById('loginForm');

if (loginForm) {
    loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const selectedRole = document.querySelector('input[name="role"]:checked');
        
        if (!selectedRole) {
            alert('Veuillez sélectionner un rôle');
            return;
        }
        
        console.log('Connexion:', {
            email: email,
            password: password,
            role: selectedRole.value
        });
        
        alert(`Connexion en cours...\nEmail: ${email}\nRôle: ${selectedRole.value}`);
    });
}

// Animations au focus
const inputs = document.querySelectorAll('.form-input');
inputs.forEach(input => {
    input.addEventListener('focus', () => {
        input.style.transform = 'translateY(-2px)';
    });
    
    input.addEventListener('blur', () => {
        input.style.transform = '';
    });
});

// Profile page specific functionality
document.addEventListener('DOMContentLoaded', function() {
    let isEditing = false;
    let selectedPhoto = null;

    const editProfileBtn = document.getElementById('edit-profile-btn');
    if (!editProfileBtn) return; // Sort si on n'est pas sur la page profil

    // Edit profile functionality
    editProfileBtn.addEventListener('click', function() {
        isEditing = true;
        toggleEditMode();
        // Pas de notification pour l'édition
    });

    // Cancel edit
    const cancelEditBtn = document.getElementById('cancel-edit-btn');
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', function() {
            isEditing = false;
            toggleEditMode();
            resetForm();
            // Pas de notification pour l'annulation
        });
    }

    // Save profile - FORM SUBMIT
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            if (selectedPhoto) {
                formData.append('profile_photo', selectedPhoto);
            }
            
            console.log('Données du profil à sauvegarder:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ':', value);
            }
            
            isEditing = false;
            toggleEditMode();
            showNotificationToken('Profil sauvegardé', 'fas fa-save', 'success');
            if (window.notyf && notyf.success) {
                notyf.success('Vos informations ont été mises à jour avec succès');
            }
        });
    }

    // Fonctions pour le profil...
    function toggleEditMode() {
        const editableFields = ['lastname', 'firstname', 'email', 'phone', 'profession'];
        const formActions = document.getElementById('form-actions');
        const editBtn = document.getElementById('edit-profile-btn');

        editableFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.readOnly = !isEditing;
                if (isEditing) {
                    field.classList.remove('bg-gray-50', 'dark:bg-gray-700');
                    field.classList.add('bg-white', 'dark:bg-gray-800');
                } else {
                    field.classList.add('bg-gray-50', 'dark:bg-gray-700');
                    field.classList.remove('bg-white', 'dark:bg-gray-800');
                }
            }
        });

        if (formActions) {
            if (isEditing) {
                formActions.classList.remove('hidden');
                if (editBtn) editBtn.style.display = 'none';
            } else {
                formActions.classList.add('hidden');
                if (editBtn) editBtn.style.display = 'block';
            }
        }
    }

    function resetForm() {
        const fields = {
            'lastname': 'Martin',
            'firstname': 'Jean',
            'email': 'jean.martin@graxel.com',
            'phone': '+33 6 12 34 56 78',
            'profession': 'Développeur Full Stack'
        };
        
        Object.entries(fields).forEach(([id, value]) => {
            const field = document.getElementById(id);
            if (field) field.value = value;
        });
    }
});

// Gestion des popups
document.addEventListener('DOMContentLoaded', function() {
    const popupElement = document.getElementById('popup-nouvelle-demande');
    if (popupElement) {
        // Méthodes pour ouvrir/fermer le popup
        window.openPopup = function() {
            popupElement.classList.add('show');
        };
        
        window.closePopup = function() {
            popupElement.classList.remove('show');
        };
    }
});
