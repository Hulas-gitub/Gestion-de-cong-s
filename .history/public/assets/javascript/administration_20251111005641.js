// =============================================
// CONFIGURATION administration.js - ROUTES CORRIGÉES
// =============================================
const API_BASE_URL = '/admin/api/administration'; // ✅ CORRIGÉ : ajout de /api/
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
let currentPage = 1;
let itemsPerPage = 10;

// =============================================
// INITIALISATION AU CHARGEMENT DE LA PAGE
// =============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation de la page Administration...');
    console.log('📍 API Base URL:', API_BASE_URL);
    console.log('🔑 CSRF Token:', CSRF_TOKEN ? 'Présent' : '❌ MANQUANT');

    updateCurrentDate();
    initTheme();
    initTabsWithPagination(); // ✅ Utiliser la nouvelle fonction
    initEventListeners();
    loadInitialData();

    console.log('✅ Page Administration initialisée avec succès');
});

// =============================================
// CHARGEMENT DES DONNÉES
// =============================================

/**
 * Charger toutes les données initiales
 */
async function loadInitialData() {
    showLoader();
    try {
        console.log('📥 Chargement des données initiales...');
        await Promise.all([
            loadRoles(),
            loadDepartements(),
            loadUsers()
        ]);
        console.log('✅ Toutes les données chargées avec succès');
    } catch (error) {
        console.error('❌ Erreur lors du chargement des données:', error);
        showToast('Erreur', 'Impossible de charger les données', 'error');
    } finally {
        hideLoader();
    }
}

/**
 * Charger tous les utilisateurs
 */
async function loadUsers() {
    try {
        console.log('📥 Chargement des utilisateurs...');
        const response = await fetch(`${API_BASE_URL}/users`, {
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        allUsers = data.users || [];
        console.log(`✅ ${allUsers.length} utilisateurs chargés`);
        renderUsers();
    } catch (error) {
        console.error('❌ Erreur loadUsers:', error);
        showToast('Erreur', 'Impossible de charger les utilisateurs', 'error');
        throw error;
    }
}

/**
 * Charger tous les rôles
 */
async function loadRoles() {
    try {
        console.log('📥 Chargement des rôles...');
        allRoles = window.laravelData?.roles || [];
        console.log(`✅ ${allRoles.length} rôles chargés:`, allRoles);
    } catch (error) {
        console.error('❌ Erreur loadRoles:', error);
    }
}

/**
 * Charger tous les départements
 */
async function loadDepartements() {
    try {
        console.log('📥 Chargement des départements...');
        const response = await fetch(`${API_BASE_URL}/departements`, {
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        allDepartements = data.departements || [];
        console.log(`✅ ${allDepartements.length} départements chargés`);
        renderDepartements();
    } catch (error) {
        console.error('❌ Erreur loadDepartements:', error);
        showToast('Erreur', 'Impossible de charger les départements', 'error');
    }
}

// =============================================
// RENDU DES DONNÉES
// =============================================

/**
 * Afficher les utilisateurs filtrés par rôle
 */
function renderUsers() {
    const employesTbody = document.getElementById('employes-tbody');
    const chefsTbody = document.getElementById('chefs-tbody');

    if (!employesTbody || !chefsTbody) {
        console.error('❌ Tableaux tbody introuvables');
        return;
    }

    // Filtrer uniquement les employés
    const employes = allUsers.filter(u => {
        const roleName = u.role?.nom_role;
        return roleName && roleName.toLowerCase() === 'employé';
    });

    // Filtrer uniquement les chefs de département
    const chefs = allUsers.filter(u => {
        const roleName = u.role?.nom_role;
        return roleName && (
            roleName === 'Chef de Département' ||
            roleName === 'Chef de département' ||
            roleName.toLowerCase() === 'chef de departement'
        );
    });

    console.log(`👥 Employés: ${employes.length}, Chefs: ${chefs.length}`);

    // Pagination pour employés
    const employesPaginated = paginate(employes, currentPage, itemsPerPage);

    // Rendre les employés
    employesTbody.innerHTML = employesPaginated.length === 0
        ? '<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucun employé trouvé</td></tr>'
        : employesPaginated.map(user => createUserRow(user, 'employe')).join('');

    // Rendre les chefs
    chefsTbody.innerHTML = chefs.length === 0
        ? '<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucun chef de département trouvé</td></tr>'
        : chefs.map(user => createChefRow(user)).join('');

    // Pagination
    renderPagination(employes.length, 'employes');

    // Réattacher les événements
    attachUserActions();
}

/**
 * Créer une ligne de tableau pour un employé
 */
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
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${user.telephone || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.departement?.nom_departement || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.profession || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">${statusText}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button title="Modifier" class="edit-btn text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                    <i class="fas fa-edit"></i>
                </button>
                <button title="Voir détails" class="view-btn text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                    <i class="fas fa-eye"></i>
                </button>
                <button title="${user.actif ? 'Bloquer' : 'Débloquer'}" class="block-btn text-${user.actif ? 'red' : 'green'}-600 hover:text-${user.actif ? 'red' : 'green'}-900">
                    <i class="fas fa-${user.actif ? 'lock' : 'unlock'}"></i>
                </button>
                ${!user.actif ? `
                <button title="Renvoyer email d'activation" class="resend-btn text-blue-600 hover:text-blue-900">
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

/**
 * Créer une ligne de tableau pour un chef de département
 */
function createChefRow(user) {
    const statusClass = user.actif
        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
        : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300';
    const statusText = user.actif ? 'Actif' : 'Inactif';

    return `
        <tr data-user-id="${user.id_user}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">${user.matricule}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.nom} ${user.prenom}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${user.email}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${user.telephone || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${user.departement?.nom_departement || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${formatDate(user.date_embauche)}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">${statusText}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button title="Modifier" class="edit-btn text-blue-600 hover:text-blue-900">
                    <i class="fas fa-edit"></i>
                </button>
                <button title="Voir détails" class="view-btn text-indigo-600 hover:text-indigo-900">
                    <i class="fas fa-eye"></i>
                </button>
                <button title="${user.actif ? 'Bloquer' : 'Débloquer'}" class="block-btn text-${user.actif ? 'red' : 'green'}-600">
                    <i class="fas fa-${user.actif ? 'lock' : 'unlock'}"></i>
                </button>
                ${!user.actif ? `
                <button title="Renvoyer email d'activation" class="resend-btn text-blue-600 hover:text-blue-900">
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

/**
 * Afficher les départements
 */
function renderDepartements() {
    const tbody = document.getElementById('departements-tbody');
    if (!tbody) {
        console.error('❌ Tableau départements tbody introuvable');
        return;
    }

    if (allDepartements.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucun département trouvé</td></tr>';
        return;
    }

    tbody.innerHTML = allDepartements.map(dept => `
        <tr data-dept-id="${dept.id_departement}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">${dept.id_departement}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${dept.nom_departement}</td>
            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">${dept.description || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                ${dept.chef_departement ? dept.chef_departement.prenom + ' ' + dept.chef_departement.nom : '-'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full text-xs font-medium">
                    ${dept.employes_count || 0} employé(s)
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 rounded border border-gray-300 dark:border-gray-600" style="background-color: ${dept.couleur_calendrier || '#3b82f6'}"></div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">${dept.couleur_calendrier || '#3b82f6'}</span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button title="Modifier" class="edit-dept-btn text-blue-600 hover:text-blue-900">
                    <i class="fas fa-edit"></i>
                </button>
                <button title="Supprimer" class="delete-dept-btn text-gray-600 hover:text-gray-900">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');

    attachDepartementActions();
}

/**
 * Attacher les événements aux boutons des utilisateurs
 */
function attachUserActions() {
    // Boutons Modifier
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const userId = row.dataset.userId;
            editUser(userId);
        });
    });

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

/**
 * Attacher les événements aux boutons des départements
 */
function attachDepartementActions() {
    // Boutons Modifier
    document.querySelectorAll('.edit-dept-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const deptId = row.dataset.deptId;
            editDepartement(deptId);
        });
    });

    // Boutons Supprimer
    document.querySelectorAll('.delete-dept-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const deptId = row.dataset.deptId;
            openDeleteModal('departement', deptId);
        });
    });
}

// =============================================
// GESTION DES ONGLETS
// =============================================

/**
 * Réinitialiser la pagination
 */
function resetPagination() {
    currentPage = 1;
}

/**
 * Initialiser la gestion des onglets avec pagination
 */
function initTabsWithPagination() {
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            resetPagination();
            showTab(tabName);

            if (tabName === 'employes' || tabName === 'chefs') {
                renderUsers();
            } else if (tabName === 'departements') {
                renderDepartements();
            }
        });
    });

    setTimeout(() => showTab('employes'), 100);
}

function showTab(tabName) {
    document.querySelectorAll('.tab-pane').forEach(tab => {
        tab.classList.add('hidden');
        tab.classList.remove('active');
    });

    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'bg-gradient-to-r', 'from-blue-500', 'to-purple-500', 'text-white');
        button.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    });

    const targetTab = document.getElementById(tabName + '-tab');
    if (targetTab) {
        targetTab.classList.remove('hidden');
        targetTab.classList.add('active');
    }

    const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
    if (activeButton) {
        activeButton.classList.add('active', 'bg-gradient-to-r', 'from-blue-500', 'to-purple-500', 'text-white');
        activeButton.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
    }
}

// =============================================
// INITIALISATION DES ÉCOUTEURS D'ÉVÉNEMENTS
// =============================================

/**
 * Initialiser tous les écouteurs d'événements
 */
function initEventListeners() {
    console.log('🎯 Initialisation des écouteurs d\'événements...');

    const toggleSidebar = document.getElementById('toggle-sidebar');
    const closeSidebar = document.getElementById('close-sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    if (toggleSidebar) toggleSidebar.addEventListener('click', toggleSidebarMenu);
    if (closeSidebar) closeSidebar.addEventListener('click', toggleSidebarMenu);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebarMenu);

    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) themeToggle.addEventListener('click', toggleTheme);

    // Boutons d'ajout
    const addEmployeBtn = document.getElementById('add-employe-btn');
    const addChefBtn = document.getElementById('add-chef-btn');
    const addDeptBtn = document.getElementById('add-departement-btn');

    if (addEmployeBtn) {
        addEmployeBtn.addEventListener('click', () => {
            console.log('✅ Clic sur bouton ajouter employé');
            openAddModal('employe');
        });
    } else {
        console.error('❌ Bouton add-employe-btn introuvable');
    }

    if (addChefBtn) {
        addChefBtn.addEventListener('click', () => {
            console.log('✅ Clic sur bouton ajouter chef');
            openAddModal('chef');
        });
    } else {
        console.error('❌ Bouton add-chef-btn introuvable');
    }

    if (addDeptBtn) {
        addDeptBtn.addEventListener('click', () => {
            console.log('✅ Clic sur bouton ajouter département');
            openAddModal('departement');
        });
    } else {
        console.error('❌ Bouton add-departement-btn introuvable');
    }

    // Formulaires
    document.getElementById('employe-form')?.addEventListener('submit', handleEmployeSubmit);
    document.getElementById('chef-form')?.addEventListener('submit', handleChefSubmit);
    document.getElementById('departement-form')?.addEventListener('submit', handleDepartementSubmit);

    document.getElementById('confirm-delete-btn')?.addEventListener('click', confirmDelete);

    // Changement de rôle pour générer le matricule
    document.getElementById('employe-role')?.addEventListener('change', function() {
        if (this.value) {
            generateMatricule('employe', this.value);
        }
    });

    // Gestion couleur département
    const couleurInput = document.getElementById('departement-couleur');
    const couleurPreview = document.getElementById('couleur-preview');
    if (couleurInput && couleurPreview) {
        couleurInput.addEventListener('input', function() {
            couleurPreview.textContent = this.value;
        });
    }

    console.log('✅ Écouteurs d\'événements initialisés');
}

// =============================================
// SUITE DU FICHIER...
// (Ajouter ici toutes les autres fonctions de ton administration.js)
// =============================================

console.log('✅ Script administration.js chargé avec succès (routes corrigées)');
