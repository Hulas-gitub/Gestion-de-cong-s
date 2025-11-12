// =============================================
// CONFIGURATION
// =============================================
const API_BASE_URL = '/admin/administration';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// =============================================
// VARIABLES GLOBALES
// =============================================
let currentDeleteId = null;
let currentDeleteType = null;
let currentEditId = null;
let allUsers = [];
let allRoles = [];
let allDepartements = [];

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

    // Charger les données initiales
    loadInitialData();

    console.log('✅ Page Administration initialisée avec succès');
});

// =============================================
// CHARGEMENT DES DONNÉES
// =============================================
async function loadInitialData() {
    showLoader();
    try {
        await Promise.all([
            loadRoles(),
            loadDepartements(),
            loadUsers()
        ]);
    } catch (error) {
        console.error('Erreur lors du chargement des données:', error);
        showToast('Erreur', 'Impossible de charger les données', 'error');
    } finally {
        hideLoader();
    }
}

async function loadUsers() {
    try {
        const response = await fetch(`${API_BASE_URL}/users`, {
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Erreur lors du chargement des utilisateurs');

        const data = await response.json();
        allUsers = data.users || [];
        renderUsers();
    } catch (error) {
        console.error('Erreur loadUsers:', error);
        throw error;
    }
}

async function loadRoles() {
    try {
        // Récupérer les rôles depuis les données Laravel injectées
        allRoles = window.laravelData?.roles || [];
        console.log('Rôles chargés:', allRoles);
    } catch (error) {
        console.error('Erreur loadRoles:', error);
    }
}

async function loadDepartements() {
    try {
        // Récupérer les départements depuis les données Laravel injectées
        allDepartements = window.laravelData?.departements || [];
        console.log('Départements chargés:', allDepartements);
    } catch (error) {
        console.error('Erreur loadDepartements:', error);
    }
}

// =============================================
// RENDU DES DONNÉES
// =============================================
function renderUsers() {
    const employesTbody = document.getElementById('employes-tbody');
    const chefsTbody = document.getElementById('chefs-tbody');

    if (!employesTbody || !chefsTbody) return;

    // Filtrer les utilisateurs par rôle
    const employes = allUsers.filter(u => {
        const roleName = u.role?.nom_role;
        return roleName && roleName !== 'Admin' && roleName !== 'Chef de Département' && roleName !== 'Chef de département';
    });

    const chefs = allUsers.filter(u => {
        const roleName = u.role?.nom_role;
        return roleName === 'Chef de Département' || roleName === 'Chef de département';
    });

    console.log('Employés filtrés:', employes.length);
    console.log('Chefs filtrés:', chefs.length);

    // Rendre les employés
    employesTbody.innerHTML = employes.length === 0
        ? '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucun employé trouvé</td></tr>'
        : employes.map(user => createUserRow(user, 'employe')).join('');

    // Rendre les chefs
    chefsTbody.innerHTML = chefs.length === 0
        ? '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucun chef de département trouvé</td></tr>'
        : chefs.map(user => createChefRow(user)).join('');

    // Réattacher les événements
    attachUserActions();
}

function createUserRow(user, type) {
    const statusClass = user.actif
        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
        : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300';
    const statusText = user.actif ? 'Actif' : 'Inactif';

    return `
        <tr data-user-id="${user.id_user}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">${user.matricule}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.nom} ${user.prenom}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${user.email}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.departement?.nom_departement || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.profession || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">${statusText}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button title="Voir détails" class="view-btn text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                    <i class="fas fa-eye"></i>
                </button>
                <button title="${user.actif ? 'Bloquer' : 'Débloquer'}" class="block-btn text-${user.actif ? 'red' : 'green'}-600 hover:text-${user.actif ? 'red' : 'green'}-900 dark:text-${user.actif ? 'red' : 'green'}-400 dark:hover:text-${user.actif ? 'red' : 'green'}-300">
                    <i class="fas fa-${user.actif ? 'lock' : 'unlock'}"></i>
                </button>
                ${!user.actif ? `
                <button title="Renvoyer email d'activation" class="resend-btn text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                    <i class="fas fa-envelope"></i>
                </button>
                ` : ''}
                <button title="Supprimer" class="delete-btn text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
}

function createChefRow(user) {
    const statusClass = user.actif
        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
        : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300';

    return `
        <tr data-user-id="${user.id_user}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">${user.matricule}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.nom} ${user.prenom}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${user.email}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.departement?.nom_departement || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${formatDate(user.date_embauche)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button title="Voir détails" class="view-btn text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                    <i class="fas fa-eye"></i>
                </button>
                <button title="${user.actif ? 'Bloquer' : 'Débloquer'}" class="block-btn text-${user.actif ? 'red' : 'green'}-600 hover:text-${user.actif ? 'red' : 'green'}-900 dark:text-${user.actif ? 'red' : 'green'}-400 dark:hover:text-${user.actif ? 'red' : 'green'}-300">
                    <i class="fas fa-${user.actif ? 'lock' : 'unlock'}"></i>
                </button>
                ${!user.actif ? `
                <button title="Renvoyer email d'activation" class="resend-btn text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                    <i class="fas fa-envelope"></i>
                </button>
                ` : ''}
                <button title="Supprimer" class="delete-btn text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
}

function attachUserActions() {
    // Boutons Voir détails
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const userId = row.dataset.userId;
            viewUser(userId);
        });
    });

    // Boutons Bloquer/Débloquer
    document.querySelectorAll('.block-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const userId = row.dataset.userId;
            const user = allUsers.find(u => u.id_user == userId);
            if (user) {
                user.actif ? blockUser(userId) : unblockUser(userId);
            }
        });
    });

    // Boutons Renvoyer email
    document.querySelectorAll('.resend-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const userId = row.dataset.userId;
            resendActivation(userId);
        });
    });

    // Boutons Supprimer
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const userId = row.dataset.userId;
            openDeleteModal('user', userId);
        });
    });
}

// =============================================
// GESTION DES ONGLETS
// =============================================
function initTabs() {
    function showTab(tabName) {
        // Cacher tous les onglets
        document.querySelectorAll('.tab-pane').forEach(tab => {
            tab.classList.add('hidden');
            tab.classList.remove('active');
        });

        // Retirer la classe active de tous les boutons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active', 'bg-gradient-to-r', 'from-blue-500', 'to-purple-500', 'text-white');
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
    }

    // Attacher les événements aux boutons d'onglets
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            showTab(tabName);
        });
    });

    // Afficher l'onglet "employes" par défaut
    setTimeout(() => showTab('employes'), 100);
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
    document.getElementById('add-employe-btn')?.addEventListener('click', () => openAddModal('employe'));
    document.getElementById('add-chef-btn')?.addEventListener('click', () => openAddModal('chef'));
    document.getElementById('add-departement-btn')?.addEventListener('click', () => openAddModal('departement'));

    // Formulaires
    document.getElementById('employe-form')?.addEventListener('submit', handleEmployeSubmit);
    document.getElementById('chef-form')?.addEventListener('submit', handleChefSubmit);
    document.getElementById('departement-form')?.addEventListener('submit', handleDepartementSubmit);

    // Bouton de confirmation de suppression
    document.getElementById('confirm-delete-btn')?.addEventListener('click', confirmDelete);

    // Événements de changement de rôle pour générer le matricule
    document.getElementById('employe-role')?.addEventListener('change', function() {
        if (this.value) {
            generateMatricule('employe', this.value);
        }
    });
}

// =============================================
// GESTION DES MODALS
// =============================================
async function openAddModal(type) {
    const modal = document.getElementById(`${type}-modal`);
    const title = document.getElementById(`${type}-modal-title`);
    const form = document.getElementById(`${type}-form`);

    if (!modal || !title || !form) {
        console.error(`Modal ${type} introuvable`);
        return;
    }

    title.textContent = type === 'employe' ? 'Ajouter un employé'
        : type === 'chef' ? 'Ajouter un chef de département'
        : 'Ajouter un département';

    currentEditId = null;

    // Réinitialiser le formulaire
    form.reset();

    // Charger les dropdowns pour employe et chef
    if (type === 'employe' || type === 'chef') {
        await loadFormDropdowns(type);

        // Définir la date d'embauche à aujourd'hui
        const dateInput = document.getElementById(`${type}-date-embauche`);
        if (dateInput) {
            dateInput.value = new Date().toISOString().split('T')[0];
        }

        if (type === 'chef') {
            // Trouver et définir le rôle "Chef de Département"
            const chefRole = allRoles.find(r =>
                r.nom_role === 'Chef de Département' || r.nom_role === 'Chef de département'
            );
            if (chefRole) {
                document.getElementById('chef-role').value = chefRole.id_role;
                await generateMatricule('chef', chefRole.id_role);
            }
        }
    }

    // Afficher le modal
    showModal(modal);
}

async function loadFormDropdowns(type) {
    // Charger les rôles pour les employés
    const roleSelect = document.getElementById(`${type}-role`);
    if (roleSelect && type === 'employe') {
        roleSelect.innerHTML = '<option value="">Sélectionner un rôle</option>' +
            allRoles.filter(r => r.nom_role !== 'Admin' && r.nom_role !== 'Chef de Département' && r.nom_role !== 'Chef de département')
                .map(role => `<option value="${role.id_role}">${role.nom_role}</option>`)
                .join('');
    }

    // Charger les départements
    const deptSelect = document.getElementById(`${type}-departement`);
    if (deptSelect) {
        deptSelect.innerHTML = '<option value="">Sélectionner un département</option>' +
            allDepartements.map(dept => `<option value="${dept.id_departement}">${dept.nom_departement}</option>`).join('');
    }
}

async function generateMatricule(type, roleId) {
    try {
        if (!roleId) {
            console.warn('Aucun rôle sélectionné pour générer le matricule');
            return;
        }

        const response = await fetch(`${API_BASE_URL}/generate-matricule?role_id=${roleId}`, {
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Erreur génération matricule');

        const data = await response.json();
        const matriculeInput = document.getElementById(`${type}-matricule`);
        if (matriculeInput) {
            matriculeInput.value = data.matricule;
        }
    } catch (error) {
        console.error('Erreur generateMatricule:', error);
    }
}

function showModal(modal) {
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

    // Réinitialiser les variables
    if (modalId === 'delete-confirm-modal') {
        currentDeleteId = null;
        currentDeleteType = null;
    }
    currentEditId = null;
}

// =============================================
// GESTION DES FORMULAIRES
// =============================================
async function handleEmployeSubmit(e) {
    e.preventDefault();

    const formData = {
        nom: document.getElementById('employe-nom').value.trim(),
        prenom: document.getElementById('employe-prenom').value.trim(),
        email: document.getElementById('employe-email').value.trim(),
        telephone: document.getElementById('employe-telephone').value.trim() || null,
        profession: document.getElementById('employe-profession').value.trim() || null,
        matricule: document.getElementById('employe-matricule').value.trim(),
        date_embauche: document.getElementById('employe-date-embauche').value,
        role_id: document.getElementById('employe-role').value,
        departement_id: document.getElementById('employe-departement').value || null,
        solde_conges_annuel: parseInt(document.getElementById('employe-solde-conges').value)
    };

    // Validation côté client
    if (!formData.nom || !formData.prenom || !formData.email || !formData.matricule || !formData.date_embauche || !formData.role_id) {
        showToast('Erreur', 'Veuillez remplir tous les champs obligatoires', 'error');
        return;
    }

    try {
        showLoader();
        const response = await fetch(`${API_BASE_URL}/users`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (!response.ok) {
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat().join('\n');
                throw new Error(errorMessages);
            }
            throw new Error(data.message || 'Erreur lors de la création');
        }

        showToast('Succès', data.message, 'success');
        closeModal('employe-modal');
        await loadUsers();
    } catch (error) {
        console.error('Erreur handleEmployeSubmit:', error);
        showToast('Erreur', error.message, 'error');
    } finally {
        hideLoader();
    }
}

async function handleChefSubmit(e) {
    e.preventDefault();

    const formData = {
        nom: document.getElementById('chef-nom').value.trim(),
        prenom: document.getElementById('chef-prenom').value.trim(),
        email: document.getElementById('chef-email').value.trim(),
        telephone: document.getElementById('chef-telephone').value.trim() || null,
        profession: document.getElementById('chef-profession').value.trim() || null,
        matricule: document.getElementById('chef-matricule').value.trim(),
        date_embauche: document.getElementById('chef-date-embauche').value,
        role_id: document.getElementById('chef-role').value,
        departement_id: document.getElementById('chef-departement').value,
        solde_conges_annuel: parseInt(document.getElementById('chef-solde-conges').value)
    };

    // Validation côté client
    if (!formData.nom || !formData.prenom || !formData.email || !formData.matricule || !formData.date_embauche || !formData.role_id || !formData.departement_id) {
        showToast('Erreur', 'Veuillez remplir tous les champs obligatoires', 'error');
        return;
    }

    try {
        showLoader();
        const response = await fetch(`${API_BASE_URL}/users`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (!response.ok) {
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat().join('\n');
                throw new Error(errorMessages);
            }
            throw new Error(data.message || 'Erreur lors de la création');
        }

        showToast('Succès', data.message, 'success');
        closeModal('chef-modal');
        await loadUsers();
    } catch (error) {
        console.error('Erreur handleChefSubmit:', error);
        showToast('Erreur', error.message, 'error');
    } finally {
        hideLoader();
    }
}

async function handleDepartementSubmit(e) {
    e.preventDefault();
    showToast('Info', 'Fonctionnalité de gestion des départements à implémenter', 'info');
    closeModal('departement-modal');
}

// =============================================
// ACTIONS UTILISATEURS
// =============================================
async function viewUser(userId) {
    try {
        const response = await fetch(`${API_BASE_URL}/users/${userId}`, {
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Erreur chargement utilisateur');

        const data = await response.json();
        const user = data.user;

        showToast('Détails', `${user.prenom} ${user.nom} - ${user.email}\nMatricule: ${user.matricule}\nRôle: ${user.role?.nom_role || '-'}`, 'info');
    } catch (error) {
        console.error('Erreur viewUser:', error);
        showToast('Erreur', 'Impossible de charger les détails', 'error');
    }
}

async function blockUser(userId) {
    const user = allUsers.find(u => u.id_user == userId);
    if (!user) return;

    if (!confirm(`Êtes-vous sûr de vouloir bloquer l'utilisateur ${user.prenom} ${user.nom} ?`)) return;

    try {
        showLoader();
        const response = await fetch(`${API_BASE_URL}/users/${userId}/block`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (!response.ok) throw new Error(data.message);

        showToast('Succès', data.message, 'success');
        await loadUsers();
    } catch (error) {
        console.error('Erreur blockUser:', error);
        showToast('Erreur', error.message, 'error');
    } finally {
        hideLoader();
    }
}

async function unblockUser(userId) {
    const user = allUsers.find(u => u.id_user == userId);
    if (!user) return;

    if (!confirm(`Êtes-vous sûr de vouloir débloquer l'utilisateur ${user.prenom} ${user.nom} ?`)) return;

    try {
        showLoader();
        const response = await fetch(`${API_BASE_URL}/users/${userId}/unblock`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (!response.ok) throw new Error(data.message);

        showToast('Succès', data.message, 'success');
        await loadUsers();
    } catch (error) {
        console.error('Erreur unblockUser:', error);
        showToast('Erreur', error.message, 'error');
    } finally {
        hideLoader();
    }
}

async function resendActivation(userId) {
    const user = allUsers.find(u => u.id_user == userId);
    if (!user) return;

    if (!confirm(`Renvoyer l'email d'activation à ${user.email} ?`)) return;

    try {
        showLoader();
        const response = await fetch(`${API_BASE_URL}/users/${userId}/resend-activation`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (!response.ok) throw new Error(data.message);

        showToast('Succès', data.message, 'success');
    } catch (error) {
        console.error('Erreur resendActivation:', error);
        showToast('Erreur', error.message, 'error');
    } finally {
        hideLoader();
    }
}

function openDeleteModal(type, id) {
    const modal = document.getElementById('delete-confirm-modal');
    const title = document.getElementById('delete-confirm-title');
    const message = document.getElementById('delete-confirm-message');

    if (!modal) return;

    currentDeleteId = id;
    currentDeleteType = type;

    const user = allUsers.find(u => u.id_user == id);
    const userName = user ? `${user.prenom} ${user.nom}` : 'cet utilisateur';

    title.textContent = 'Supprimer l\'utilisateur';
    message.textContent = `Êtes-vous sûr de vouloir supprimer ${userName} ? Cette action est irréversible.`;

    showModal(modal);
}

async function confirmDelete() {
    if (!currentDeleteId) return;

    try {
        showLoader();
        const response = await fetch(`${API_BASE_URL}/users/${currentDeleteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (!response.ok) throw new Error(data.message);

        showToast('Succès', data.message, 'success');
        closeModal('delete-confirm-modal');
        await loadUsers();
    } catch (error) {
        console.error('Erreur confirmDelete:', error);
        showToast('Erreur', error.message, 'error');
    } finally {
        hideLoader();
    }
}

// =============================================
// FONCTIONS UTILITAIRES
// =============================================
function toggleSidebarMenu() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (sidebar) sidebar.classList.toggle('open');
    if (overlay) overlay.classList.toggle('open');
}

function initTheme() {
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

function updateCurrentDate() {
    const currentDateElement = document.getElementById('current-date');
    if (currentDateElement) {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        currentDateElement.textContent = now.toLocaleDateString('fr-FR', options);
    }
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

function showLoader() {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.classList.remove('hidden');
    } else {
        document.body.style.cursor = 'wait';
    }
}

function hideLoader() {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.classList.add('hidden');
    } else {
        document.body.style.cursor = 'default';
    }
}

function showToast(title, message, type = 'success') {
    // Créer un toast moderne
    const toastContainer = document.getElementById('toast-container') || createToastContainer();

    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-times-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };

    const colors = {
        success: 'from-green-500 to-emerald-500',
        error: 'from-red-500 to-rose-500',
        warning: 'from-yellow-500 to-orange-500',
        info: 'from-blue-500 to-indigo-500'
    };

    const bgColors = {
        success: 'bg-green-50 dark:bg-green-900/20',
        error: 'bg-red-50 dark:bg-red-900/20',
        warning: 'bg-yellow-50 dark:bg-yellow-900/20',
        info: 'bg-blue-50 dark:bg-blue-900/20'
    };

    const borderColors = {
        success: 'border-green-200 dark:border-green-700',
        error: 'border-red-200 dark:border-red-700',
        warning: 'border-yellow-200 dark:border-yellow-700',
        info: 'border-blue-200 dark:border-blue-700'
    };

    const textColors = {
        success: 'text-green-800 dark:text-green-200',
        error: 'text-red-800 dark:text-red-200',
        warning: 'text-yellow-800 dark:text-yellow-200',
        info: 'text-blue-800 dark:text-blue-200'
    };

    const toast = document.createElement('div');
    toast.className = `toast-item ${bgColors[type]} ${borderColors[type]} border-l-4 p-4 rounded-lg shadow-lg mb-3 flex items-start space-x-3 animate-slide-in-right max-w-md`;
    toast.style.minWidth = '320px';

    toast.innerHTML = `
        <div class="flex-shrink-0">
            <div class="w-8 h-8 rounded-full bg-gradient-to-r ${colors[type]} flex items-center justify-center">
                <i class="${icons[type]} text-white text-sm"></i>
            </div>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold ${textColors[type]}">${title}</p>
            <p class="text-sm ${textColors[type]} opacity-90 mt-1">${message}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <i class="fas fa-times"></i>
        </button>
    `;

    toastContainer.appendChild(toast);

    // Auto-remove après 5 secondes
    setTimeout(() => {
        toast.style.animation = 'slide-out-right 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'fixed top-4 right-4 z-[9999] flex flex-col items-end';
    document.body.appendChild(container);
    return container;
}

// =============================================
// GESTION DE LA DÉCONNEXION
// =============================================
function openLogoutModal() {
    const modal = document.getElementById('logoutConfirmModal');
    if (modal) {
        showModal(modal);
    }
}

function closeLogoutModal() {
    closeModal('logoutConfirmModal');
}

function executeLogout() {
    // Soumettre le formulaire de déconnexion Laravel
    const logoutForm = document.getElementById('logout-form');
    if (logoutForm) {
        logoutForm.submit();
    } else {
        // Fallback: redirection vers la route de déconnexion
        window.location.href = '/logout';
    }
}

// Ajouter les styles d'animation CSS si nécessaire
if (!document.getElementById('toast-animations')) {
    const style = document.createElement('style');
    style.id = 'toast-animations';
    style.textContent = `
        @keyframes slide-in-right {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slide-out-right {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .animate-slide-in-right {
            animation: slide-in-right 0.3s ease-out;
        }

        .modal.open {
            transform: scale(1);
            opacity: 1;
        }

        .modal {
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.3s ease-out;
        }

        .backdrop.open {
            opacity: 1;
        }

        .backdrop {
            opacity: 0;
            transition: opacity 0.3s ease-out;
        }
    `;
    document.head.appendChild(style);
}

// =============================================
// EXPORT DES FONCTIONS GLOBALES
// =============================================
window.closeModal = closeModal;
window.openLogoutModal = openLogoutModal;
window.closeLogoutModal = closeLogoutModal;
window.executeLogout = executeLogout;

console.log('✅ Script administration.js chargé avec succès');
