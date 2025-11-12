// =============================================
// CONFIGURATION
// =============================================
const API_BASE_URL = '/api/administration'; // Ajustez selon votre configuration Laravel
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
            loadUsers(),
            loadRoles(),
            loadDepartements()
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
        // Les rôles sont déjà disponibles dans la vue Laravel
        // Sinon, créer un endpoint pour les récupérer
        allRoles = window.roles || [];
    } catch (error) {
        console.error('Erreur loadRoles:', error);
    }
}

async function loadDepartements() {
    try {
        // Les départements sont déjà disponibles dans la vue Laravel
        // Sinon, créer un endpoint pour les récupérer
        allDepartements = window.departements || [];
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

    // Filtrer les employés et chefs
    const employes = allUsers.filter(u => u.role?.nom_role !== 'Admin' && u.role?.nom_role !== 'Chef de Département');
    const chefs = allUsers.filter(u => u.role?.nom_role === 'Chef de Département');

    // Rendre les employés
    employesTbody.innerHTML = employes.length === 0
        ? '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Aucun employé trouvé</td></tr>'
        : employes.map(user => createUserRow(user, 'employe')).join('');

    // Rendre les chefs
    chefsTbody.innerHTML = chefs.length === 0
        ? '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun chef de département trouvé</td></tr>'
        : chefs.map(user => createChefRow(user)).join('');

    // Réattacher les événements
    attachUserActions();
}

function createUserRow(user, type) {
    const statusClass = user.actif
        ? 'bg-green-100 text-green-800'
        : 'bg-red-100 text-red-800';
    const statusText = user.actif ? 'Actif' : 'Inactif';

    return `
        <tr data-user-id="${user.id_user}">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.matricule}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.nom} ${user.prenom}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${user.email}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.departement?.nom_departement || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.profession || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">${statusText}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button title="Voir détails" class="view-btn text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                    <i class="fas fa-eye"></i>
                </button>
                <button title="${user.actif ? 'Bloquer' : 'Débloquer'}" class="block-btn text-${user.actif ? 'red' : 'green'}-600 hover:text-${user.actif ? 'red' : 'green'}-900 dark:text-${user.actif ? 'red' : 'green'}-400 dark:hover:text-${user.actif ? 'red' : 'green'}-300 mr-3">
                    <i class="fas fa-${user.actif ? 'lock' : 'unlock'}"></i>
                </button>
                ${!user.actif ? `
                <button title="Renvoyer email d'activation" class="resend-btn text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
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
    return `
        <tr data-user-id="${user.id_user}">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.matricule}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.nom} ${user.prenom}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${user.email}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.departement?.nom_departement || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${formatDate(user.date_embauche)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button title="Voir détails" class="view-btn text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                    <i class="fas fa-eye"></i>
                </button>
                <button title="${user.actif ? 'Bloquer' : 'Débloquer'}" class="block-btn text-${user.actif ? 'red' : 'green'}-600 hover:text-${user.actif ? 'red' : 'green'}-900 mr-3">
                    <i class="fas fa-${user.actif ? 'lock' : 'unlock'}"></i>
                </button>
                ${!user.actif ? `
                <button title="Renvoyer email d'activation" class="resend-btn text-blue-600 hover:text-blue-900 mr-3">
                    <i class="fas fa-envelope"></i>
                </button>
                ` : ''}
                <button title="Supprimer" class="delete-btn text-gray-600 hover:text-gray-900">
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
    setTimeout(() => showTab('employes'), 0);
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

    // Formulaires
    document.getElementById('employe-form')?.addEventListener('submit', handleEmployeSubmit);
    document.getElementById('chef-form')?.addEventListener('submit', handleChefSubmit);

    // Bouton de confirmation de suppression
    document.getElementById('confirm-delete-btn')?.addEventListener('click', confirmDelete);
}

// =============================================
// GESTION DES MODALS
// =============================================
async function openAddModal(type) {
    const modal = document.getElementById(`${type}-modal`);
    const title = document.getElementById(`${type}-modal-title`);
    const form = document.getElementById(`${type}-form`);

    if (!modal || !title || !form) return;

    title.textContent = type === 'employe' ? 'Ajouter un employé' : 'Ajouter un chef de département';
    currentEditId = null;

    // Réinitialiser le formulaire
    form.reset();

    // Charger les dropdowns
    await loadFormDropdowns(type);

    // Générer un matricule
    if (type === 'employe') {
        await generateMatricule('employe');
        // Définir la date d'embauche à aujourd'hui
        document.getElementById('employe-date-embauche').value = new Date().toISOString().split('T')[0];
    } else if (type === 'chef') {
        await generateMatricule('chef');
        // Définir la date d'embauche à aujourd'hui
        document.getElementById('chef-date-embauche').value = new Date().toISOString().split('T')[0];
        // Trouver le rôle "Chef de Département"
        const chefRole = allRoles.find(r => r.nom_role === 'Chef de Département');
        if (chefRole) {
            document.getElementById('chef-role').value = chefRole.id_role;
        }
    }

    // Afficher le modal
    showModal(modal);
}

async function loadFormDropdowns(type) {
    // Charger les rôles
    const roleSelect = document.getElementById(`${type}-role`);
    if (roleSelect && type === 'employe') {
        roleSelect.innerHTML = '<option value="">Sélectionner un rôle</option>' +
            allRoles.map(role => `<option value="${role.id_role}">${role.nom_role}</option>`).join('');
    }

    // Charger les départements
    const deptSelect = document.getElementById(`${type}-departement`);
    if (deptSelect) {
        deptSelect.innerHTML = '<option value="">Sélectionner un département</option>' +
            allDepartements.map(dept => `<option value="${dept.id_departement}">${dept.nom_departement}</option>`).join('');
    }
}

async function generateMatricule(type) {
    try {
        const roleId = type === 'chef'
            ? allRoles.find(r => r.nom_role === 'Chef de Département')?.id_role
            : allRoles.find(r => r.nom_role === 'Employé')?.id_role;

        if (!roleId) return;

        const response = await fetch(`${API_BASE_URL}/generate-matricule?role_id=${roleId}`, {
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Erreur génération matricule');

        const data = await response.json();
        document.getElementById(`${type}-matricule`).value = data.matricule;
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
        nom: document.getElementById('employe-nom').value,
        prenom: document.getElementById('employe-prenom').value,
        email: document.getElementById('employe-email').value,
        telephone: document.getElementById('employe-telephone').value,
        profession: document.getElementById('employe-profession').value,
        matricule: document.getElementById('employe-matricule').value,
        date_embauche: document.getElementById('employe-date-embauche').value,
        role_id: document.getElementById('employe-role').value,
        departement_id: document.getElementById('employe-departement').value || null,
        solde_conges_annuel: document.getElementById('employe-solde-conges').value
    };

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
        nom: document.getElementById('chef-nom').value,
        prenom: document.getElementById('chef-prenom').value,
        email: document.getElementById('chef-email').value,
        telephone: document.getElementById('chef-telephone').value,
        profession: document.getElementById('chef-profession').value,
        matricule: document.getElementById('chef-matricule').value,
        date_embauche: document.getElementById('chef-date-embauche').value,
        role_id: document.getElementById('chef-role').value,
        departement_id: document.getElementById('chef-departement').value,
        solde_conges_annuel: document.getElementById('chef-solde-conges').value
    };

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
        // Afficher les détails (à implémenter selon vos besoins)
        showToast('Détails', `Utilisateur: ${data.user.nom} ${data.user.prenom}`, 'info');
    } catch (error) {
        console.error('Erreur viewUser:', error);
        showToast('Erreur', 'Impossible de charger les détails', 'error');
    }
}

async function blockUser(userId) {
    if (!confirm('Êtes-vous sûr de vouloir bloquer cet utilisateur ?')) return;

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
    if (!confirm('Êtes-vous sûr de vouloir débloquer cet utilisateur ?')) return;

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
    if (!confirm('Renvoyer l\'email d\'activation à cet utilisateur ?')) return;

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

    title.textContent = 'Supprimer l\'utilisateur';
    message.textContent = 'Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.';

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
    // Implémenter votre loader
    document.body.style.cursor = 'wait';
}

function hideLoader() {
    document.body.style.cursor = 'default';
}

function showToast(title, message, type = 'success') {
    // Utiliser la fonction existante ou créer un toast simple
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

    console.log(`[${type.toUpperCase()}] ${title}: ${message}`);
    alert(`${title}\n${message}`);
}
