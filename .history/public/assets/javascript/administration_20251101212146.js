// =============================================
// VARIABLES GLOBALES
// =============================================
let currentDeleteId = null;
let currentDeleteType = null;
let currentEditId = null;
let employeCounter = 4; // Compteur pour générer les matricules employés (commence à 4)
let chefCounter = 2; // Compteur pour générer les matricules chefs (commence à 2)

// =============================================
// INITIALISATION AU CHARGEMENT DE LA PAGE
// =============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation de la page Administration...');

    // Mettre à jour la date actuelle
    updateCurrentDate();
    
    // Initialiser le thème
    initTheme();
    
    // Initialiser la gestion des onglets
    initTabs();
    
    // Initialiser les écouteurs d'événements
    initEventListeners();
    
    // Initialiser tous les boutons d'action des tableaux
    setupViewButtons();
    setupBlockButtons();
    setupDeleteButtons();

    console.log('✅ Page Administration initialisée avec succès');
});

// =============================================
// GESTION DES ONGLETS
// =============================================
function initTabs() {
    // Fonction pour afficher un onglet
    function showTab(tabName) {
        // Cacher tous les onglets
        const allTabs = document.querySelectorAll('.tab-pane');
        allTabs.forEach(tab => {
            tab.classList.add('hidden');
            tab.classList.remove('active');
        });

        // Retirer la classe active de tous les boutons
        const allButtons = document.querySelectorAll('.tab-button');
        allButtons.forEach(button => {
            button.classList.remove('active');
            button.classList.remove('bg-gradient-to-r', 'from-blue-500', 'to-purple-500', 'text-white');
            button.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
        });

        // Afficher l'onglet sélectionné
        const targetTab = document.getElementById(tabName + '-tab');
        if (targetTab) {
            targetTab.classList.remove('hidden');
            targetTab.classList.add('active');
        }

        // Activer le bouton correspondant
        const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
        if (activeButton) {
            activeButton.classList.add('active', 'bg-gradient-to-r', 'from-blue-500', 'to-purple-500', 'text-white');
            activeButton.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
        }

        console.log(`📄 Onglet affiché: ${tabName}`);
    }

    // Attacher les événements aux boutons d'onglets
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            showTab(tabName);
        });
    });

    // Afficher l'onglet "employes" par défaut au chargement
    setTimeout(() => {
        showTab('employes');
    }, 0);
}

// =============================================
// INITIALISATION DES ÉCOUTEURS D'ÉVÉNEMENTS
// =============================================
function initEventListeners() {
    // Navigation de la sidebar
    const toggleSidebar = document.getElementById('toggle-sidebar');
    const closeSidebar = document.getElementById('close-sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    
    if (toggleSidebar) toggleSidebar.addEventListener('click', toggleSidebarMenu);
    if (closeSidebar) closeSidebar.addEventListener('click', toggleSidebarMenu);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebarMenu);
    
    // Bouton de thème
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) themeToggle.addEventListener('click', toggleTheme);
    
    // Boutons d'ajout
    const addEmployeBtn = document.getElementById('add-employe-btn');
    const addChefBtn = document.getElementById('add-chef-btn');
    const addDepartementBtn = document.getElementById('add-departement-btn');
    
    if (addEmployeBtn) {
        addEmployeBtn.addEventListener('click', () => openAddModal('employe'));
    }
    
    if (addChefBtn) {
        addChefBtn.addEventListener('click', () => openAddModal('chef'));
    }
    
    if (addDepartementBtn) {
        addDepartementBtn.addEventListener('click', () => openAddModal('departement'));
    }
    
    // Formulaires
    const employeForm = document.getElementById('employe-form');
    const chefForm = document.getElementById('chef-form');
    const departementForm = document.getElementById('departement-form');
    
    if (employeForm) employeForm.addEventListener('submit', handleEmployeSubmit);
    if (chefForm) chefForm.addEventListener('submit', handleChefSubmit);
    if (departementForm) departementForm.addEventListener('submit', handleDepartementSubmit);
    
    // Bouton de confirmation de suppression
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (currentDeleteId && currentDeleteType) {
                // Simuler la suppression
                showToast(`${getTypeLabel(currentDeleteType)} supprimé`, `L'élément a été supprimé avec succès`, 'success');
                
                // Fermer le modal
                closeModal('delete-confirm-modal');
            }
        });
    }
    
    // Écouteur pour le champ contact employé - mettre à jour le mot de passe par défaut
    const employeContact = document.getElementById('employe-contact');
    if (employeContact) {
        employeContact.addEventListener('input', function() {
            const passwordField = document.getElementById('employe-password');
            if (passwordField && (!passwordField.value || !currentEditId)) {
                passwordField.value = this.value;
            }
        });
    }

    // Écouteur pour le champ contact chef - mettre à jour le mot de passe par défaut
    const chefContact = document.getElementById('chef-contact');
    if (chefContact) {
        chefContact.addEventListener('input', function() {
            const passwordField = document.getElementById('chef-password');
            if (passwordField && (!passwordField.value || !currentEditId)) {
                passwordField.value = this.value;
            }
        });
    }
}

// =============================================
// GESTION DE LA SIDEBAR (MOBILE)
// =============================================
function toggleSidebarMenu() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (sidebar) sidebar.classList.toggle('open');
    if (overlay) overlay.classList.toggle('open');
}

// =============================================
// GESTION DU THÈME
// =============================================
function initTheme() {
    // Récupérer le thème sauvegardé ou utiliser le thème par défaut
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark');
    }
}

function toggleTheme() {
    document.documentElement.classList.toggle('dark');
    const isDark = document.documentElement.classList.contains('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}

// =============================================
// GESTION DE LA DATE
// =============================================
function updateCurrentDate() {
    const currentDateElement = document.getElementById('current-date');
    if (currentDateElement) {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        currentDateElement.textContent = now.toLocaleDateString('fr-FR', options);
    }
}

// =============================================
// GESTION DES ACTIONS SUR LES LIGNES
// =============================================
function setupViewButtons() {
    const viewButtons = document.querySelectorAll('button[title="Voir détails"]');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            showToast('Voir détails', 'Affichage des détails de l\'élément', 'info');
        });
    });
}

function setupBlockButtons() {
    const blockButtons = document.querySelectorAll('button[title="Bloquer"]');
    blockButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const name = row.querySelector('td:nth-child(2)').textContent;
            
            if (confirm(`Êtes-vous sûr de vouloir bloquer ${name} ?`)) {
                showToast('Utilisateur bloqué', `${name} a été bloqué avec succès`, 'warning');
            }
        });
    });
}

function setupDeleteButtons() {
    const deleteButtons = document.querySelectorAll('button[title="Supprimer"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const name = row.querySelector('td:nth-child(2)').textContent;
            
            if (confirm(`⚠️ Êtes-vous sûr de vouloir supprimer ${name} ?\n\nCette action est irréversible !`)) {
                row.remove();
                showToast('Utilisateur supprimé', `${name} a été supprimé avec succès`, 'success');
            }
        });
    });
}

// =============================================
// GÉNÉRATION DE MATRICULES
// =============================================
function generateMatriculeEmploye() {
    employeCounter++;
    return `EMP${String(employeCounter).padStart(4, '0')}`;
}

function generateMatriculeChef() {
    chefCounter++;
    return `CHF${String(chefCounter).padStart(4, '0')}`;
}

// =============================================
// GESTION DES MODALS
// =============================================
function openAddModal(type) {
    const modal = document.getElementById(`${type}-modal`);
    const title = document.getElementById(`${type}-modal-title`);
    
    if (!modal || !title) return;
    
    title.textContent = `Ajouter un${type === 'employe' ? ' ' : type === 'chef' ? ' chef de ' : ' '}${getTypeLabel(type)}`;
    
    // Réinitialiser le formulaire
    const form = document.getElementById(`${type}-form`);
    if (form) form.reset();
    
    // Pour les employés, générer un nouveau matricule et définir le rôle par défaut
    if (type === 'employe') {
        const matriculeField = document.getElementById('employe-matricule');
        const roleField = document.getElementById('employe-role');
        
        if (matriculeField) matriculeField.value = generateMatriculeEmploye();
        if (roleField) roleField.value = 'Employé';
    }
    
    // Pour les chefs, générer un nouveau matricule et définir le rôle
    if (type === 'chef') {
        const matriculeField = document.getElementById('chef-matricule');
        const roleField = document.getElementById('chef-role');
        const dateNominationField = document.getElementById('chef-date-nomination');
        
        if (matriculeField) matriculeField.value = generateMatriculeChef();
        if (roleField) roleField.value = 'Chef de Département';
        
        // Définir la date de nomination à aujourd'hui par défaut
        if (dateNominationField) {
            const today = new Date().toISOString().split('T')[0];
            dateNominationField.value = today;
        }
    }
    
    // Afficher le modal
    modal.classList.remove('hidden');
    setTimeout(() => {
        const modalContent = modal.querySelector('.modal');
        if (modalContent) modalContent.classList.add('open');
    }, 10);
}

function openEditModal(type, id) {
    const modal = document.getElementById(`${type}-modal`);
    const title = document.getElementById(`${type}-modal-title`);
    
    if (!modal || !title) return;
    
    title.textContent = `Modifier ${getTypeLabel(type)}`;
    currentEditId = id;
    
    // Remplir le formulaire avec les données existantes (simulées)
    if (type === 'employe') {
        const fields = {
            'employe-matricule': 'EMP0001',
            'employe-nom': 'Martin',
            'employe-prenom': 'Jean',
            'employe-contact': '+241 01 23 45 67',
            'employe-email': 'jean.martin@graxeltech.com',
            'employe-role': 'Employé',
            'employe-poste': 'Analyste financier',
            'employe-departement': 'Finance'
        };
        
        Object.keys(fields).forEach(key => {
            const field = document.getElementById(key);
            if (field) field.value = fields[key];
        });
    } else if (type === 'chef') {
        const fields = {
            'chef-matricule': 'CHF0001',
            'chef-nom': 'Dubois',
            'chef-prenom': 'Marie',
            'chef-contact': '+241 02 34 56 78',
            'chef-email': 'marie.dubois@graxeltech.com',
            'chef-role': 'Chef de Département',
            'chef-poste': 'Directrice des Ressources Humaines',
            'chef-departement': 'Ressources Humaines',
            'chef-date-nomination': '2023-03-15'
        };
        
        Object.keys(fields).forEach(key => {
            const field = document.getElementById(key);
            if (field) field.value = fields[key];
        });
    } else if (type === 'departement') {
        const fields = {
            'departement-nom': 'Finance',
            'departement-description': 'Gestion financière et comptable',
            'departement-chef': 'Jean Martin'
        };
        
        Object.keys(fields).forEach(key => {
            const field = document.getElementById(key);
            if (field) field.value = fields[key];
        });
    }
    
    // Afficher le modal
    modal.classList.remove('hidden');
    setTimeout(() => {
        const modalContent = modal.querySelector('.modal');
        if (modalContent) modalContent.classList.add('open');
    }, 10);
}

function openDeleteModal(type, id) {
    const modal = document.getElementById('delete-confirm-modal');
    const title = document.getElementById('delete-confirm-title');
    const message = document.getElementById('delete-confirm-message');
    
    if (!modal || !title || !message) return;
    
    currentDeleteId = id;
    currentDeleteType = type;
    
    title.textContent = `Supprimer ${getTypeLabel(type)}`;
    message.textContent = `Êtes-vous sûr de vouloir supprimer ce ${getTypeLabel(type).toLowerCase()} ? Cette action est irréversible.`;
    
    // Afficher le modal
    modal.classList.remove('hidden');
    setTimeout(() => {
        const modalContent = modal.querySelector('.modal');
        if (modalContent) modalContent.classList.add('open');
    }, 10);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    const modalContent = modal.querySelector('.modal');
    if (modalContent) modalContent.classList.remove('open');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
    
    // Réinitialiser les variables globales
    if (modalId === 'delete-confirm-modal') {
        currentDeleteId = null;
        currentDeleteType = null;
    }
    
    if (modalId.includes('-modal') && !modalId.includes('delete-confirm')) {
        currentEditId = null;
    }
}

// =============================================
// GESTION DES FORMULAIRES
// =============================================
function handleEmployeSubmit(e) {
    e.preventDefault();
    
    // Récupérer les données du formulaire
    const matricule = document.getElementById('employe-matricule')?.value;
    const nom = document.getElementById('employe-nom')?.value;
    const prenom = document.getElementById('employe-prenom')?.value;
    const contact = document.getElementById('employe-contact')?.value;
    const email = document.getElementById('employe-email')?.value;
    const password = document.getElementById('employe-password')?.value;
    const role = document.getElementById('employe-role')?.value;
    const poste = document.getElementById('employe-poste')?.value;
    const departement = document.getElementById('employe-departement')?.value;
    
    // Validation
    if (!matricule || !nom || !prenom || !contact || !email || !password || !role || !poste || !departement) {
        showToast('Erreur', 'Tous les champs sont obligatoires', 'error');
        return;
    }
    
    // Simuler l'enregistrement
    if (currentEditId) {
        showToast('Employé modifié', `${prenom} ${nom} (${matricule}) a été modifié avec succès`, 'success');
    } else {
        showToast('Employé ajouté', `${prenom} ${nom} (${matricule}) a été ajouté avec succès`, 'success');
    }
    
    // Fermer le modal
    closeModal('employe-modal');
}

function handleChefSubmit(e) {
    e.preventDefault();
    
    // Récupérer les données du formulaire
    const matricule = document.getElementById('chef-matricule')?.value;
    const nom = document.getElementById('chef-nom')?.value;
    const prenom = document.getElementById('chef-prenom')?.value;
    const contact = document.getElementById('chef-contact')?.value;
    const email = document.getElementById('chef-email')?.value;
    const password = document.getElementById('chef-password')?.value;
    const role = document.getElementById('chef-role')?.value;
    const poste = document.getElementById('chef-poste')?.value;
    const departement = document.getElementById('chef-departement')?.value;
    const dateNomination = document.getElementById('chef-date-nomination')?.value;
    
    // Validation
    if (!matricule || !nom || !prenom || !contact || !email || !password || !role || !poste || !departement || !dateNomination) {
        showToast('Erreur', 'Tous les champs sont obligatoires', 'error');
        return;
    }
    
    // Simuler l'enregistrement
    if (currentEditId) {
        showToast('Chef modifié', `${prenom} ${nom} (${matricule}) a été modifié avec succès`, 'success');
    } else {
        showToast('Chef ajouté', `${prenom} ${nom} (${matricule}) a été ajouté avec succès`, 'success');
    }
    
    // Fermer le modal
    closeModal('chef-modal');
}

function handleDepartementSubmit(e) {
    e.preventDefault();
    
    // Récupérer les données du formulaire
    const nom = document.getElementById('departement-nom')?.value;
    const description = document.getElementById('departement-description')?.value;
    const chef = document.getElementById('departement-chef')?.value;
    
    // Validation
    if (!nom) {
        showToast('Erreur', 'Le nom du département est obligatoire', 'error');
        return;
    }
    
    // Simuler l'enregistrement
    if (currentEditId) {
        showToast('Département modifié', `${nom} a été modifié avec succès`, 'success');
    } else {
        showToast('Département ajouté', `${nom} a été ajouté avec succès`, 'success');
    }
    
    // Fermer le modal
    closeModal('departement-modal');
}

// =============================================
// FONCTIONS UTILITAIRES
// =============================================
function getTypeLabel(type) {
    switch(type) {
        case 'employe': return 'Employé';
        case 'chef': return 'Chef de Département';
        case 'departement': return 'Département';
        default: return '';
    }
}

function showToast(title, message, type = 'success') {
    // Vérifier si la fonction showNotificationToken de config.js existe
    if (typeof showNotificationToken !== 'undefined') {
        const icons = {
            success: 'fas fa-check',
            error: 'fas fa-times',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };
        showNotificationToken(message, icons[type] || icons.info, type);
        return;
    }

    // Sinon, créer notre propre toast
    let toastContainer = document.getElementById('dynamic-toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'dynamic-toast-container';
        toastContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
        document.body.appendChild(toastContainer);
    }

    const colors = {
        success: { bg: '#10B981', icon: 'fa-check-circle' },
        error: { bg: '#EF4444', icon: 'fa-times-circle' },
        warning: { bg: '#F59E0B', icon: 'fa-exclamation-triangle' },
        info: { bg: '#3B82F6', icon: 'fa-info-circle' }
    };

    const config = colors[type] || colors.success;
    const toastId = 'toast-' + Date.now();

    const toast = document.createElement('div');
    toast.id = toastId;
    toast.style.cssText = `
        background: white;
        border-left: 4px solid ${config.bg};
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        padding: 16px;
        margin-bottom: 10px;
        min-width: 320px;
        display: flex;
        align-items: center;
        gap: 12px;
        transform: translateX(400px);
        transition: transform 0.3s ease;
    `;
    
    toast.innerHTML = `
        <div style="width: 32px; height: 32px; background: ${config.bg}20; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <i class="fas ${config.icon}" style="color: ${config.bg};"></i>
        </div>
        <div style="flex: 1;">
            <div style="font-weight: 600; color: #111; margin-bottom: 4px;">${title}</div>
            <div style="font-size: 14px; color: #666;">${message}</div>
        </div>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #999; cursor: pointer; font-size: 18px; padding: 0; width: 24px; height: 24px;">
            <i class="fas fa-times"></i>
        </button>
    `;

    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 10);

    setTimeout(() => {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}