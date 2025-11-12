// =============================================
// CONFIGURATION & VARIABLES GLOBALES
// =============================================
const API_BASE_URL = '/admin/api'; // Routes correctes selon web.php
let currentDeleteId = null;
let currentDeleteType = null;
let currentEditId = null;

// Pagination
let currentPageEmployes = 1;
let currentPageChefs = 1;
let currentPageDepartements = 1;
const itemsPerPage = 8;

// Données Laravel injectées
let roles = window.laravelData?.roles || [];
let departements = window.laravelData?.departements || [];
let users = window.laravelData?.users || [];

// =============================================
// INITIALISATION AU CHARGEMENT DE LA PAGE
// =============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation de la page Administration...');

    updateCurrentDate();
    initTheme();
    initTabs();
    initEventListeners();

    // Charger les données initiales
    loadAllData();

    console.log('✅ Page Administration initialisée avec succès');
    console.log('📋 Rôles disponibles:', roles);
    console.log('📋 Départements disponibles:', departements);
    console.log('📋 Utilisateurs disponibles:', users);
});

// =============================================
// CHARGEMENT DES DONNÉES
// =============================================
async function loadAllData() {
    try {
        showLoader();
        await Promise.all([
            loadUsers(),
            loadDepartements()
        ]);
        hideLoader();
    } catch (error) {
        console.error('Erreur chargement données:', error);
        hideLoader();
        showToast('Erreur', 'Erreur lors du chargement des données', 'error');
    }
}

async function loadUsers() {
    try {
        const response = await fetch(`${API_BASE_URL}/users`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });

        if (!response.ok) throw new Error('Erreur réseau');

        const data = await response.json();

        if (data.success) {
            users = data.users;
            renderEmployesTable();
            renderChefsTable();
        }
    } catch (error) {
        console.error('Erreur chargement utilisateurs:', error);
        throw error;
    }
}

async function loadDepartements() {
    try {
        const response = await fetch(`${API_BASE_URL}/departements`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });

        if (!response.ok) throw new Error('Erreur réseau');

        const data = await response.json();

        if (data.success) {
            departements = data.departements;
            renderDepartementsTable();
            populateDepartementSelects();
        }
    } catch (error) {
        console.error('Erreur chargement départements:', error);
        throw error;
    }
}

// =============================================
// RENDU DES TABLEAUX
// =============================================
function renderEmployesTable() {
    const tbody = document.querySelector('#employes-tab tbody');
    if (!tbody) return;

    // Filtrer uniquement les employés
    const employes = users.filter(user => {
        const roleName = user.role?.nom_role?.toLowerCase() || '';
        return roleName.includes('employe') || roleName.includes('employé');
    });

    tbody.innerHTML = employes.length > 0 ? employes.map(user => `
        <tr data-user-id="${user.id_user}">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                ${user.matricule || 'N/A'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                ${user.prenom} ${user.nom}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                ${user.email}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                ${user.departement?.nom_departement || 'Non assigné'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                ${user.profession || 'N/A'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${user.actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${user.actif ? 'Actif' : 'Inactif'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="viewUser(${user.id_user})" title="Voir détails" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                    <i class="fas fa-eye"></i>
                </button>
                <button onclick="editUser(${user.id_user})" title="Modifier" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="toggleBlockUser(${user.id_user}, ${user.actif})" title="${user.actif ? 'Bloquer' : 'Débloquer'}" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 mr-3">
                    <i class="fas fa-${user.actif ? 'lock' : 'unlock'}"></i>
                </button>
                <button onclick="deleteUser(${user.id_user})" title="Supprimer" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('') : '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Aucun employé trouvé</td></tr>';
}

function renderChefsTable() {
    const tbody = document.querySelector('#chefs-tab tbody');
    if (!tbody) return;

    // Filtrer uniquement les chefs
    const chefs = users.filter(user => {
        const roleName = user.role?.nom_role?.toLowerCase() || '';
        return roleName.includes('chef') || roleName.includes('manager');
    });

    tbody.innerHTML = chefs.length > 0 ? chefs.map(user => `
        <tr data-user-id="${user.id_user}">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                ${user.matricule || 'N/A'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                ${user.prenom} ${user.nom}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                ${user.email}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                ${user.departement?.nom_departement || 'Non assigné'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                ${user.date_embauche ? new Date(user.date_embauche).toLocaleDateString('fr-FR') : 'N/A'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${user.actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${user.actif ? 'Actif' : 'Inactif'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="viewUser(${user.id_user})" title="Voir détails" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                    <i class="fas fa-eye"></i>
                </button>
                <button onclick="editUser(${user.id_user})" title="Modifier" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="toggleBlockUser(${user.id_user}, ${user.actif})" title="${user.actif ? 'Bloquer' : 'Débloquer'}" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 mr-3">
                    <i class="fas fa-${user.actif ? 'lock' : 'unlock'}"></i>
                </button>
                <button onclick="deleteUser(${user.id_user})" title="Supprimer" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('') : '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Aucun chef de département trouvé</td></tr>';
}

function renderDepartementsTable() {
    const tbody = document.querySelector('#departements-tab tbody');
    if (!tbody) return;

    tbody.innerHTML = departements.length > 0 ? departements.map(dept => `
        <tr data-dept-id="${dept.id_departement}">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                DEP${String(dept.id_departement).padStart(3, '0')}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                ${dept.nom_departement}
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                ${dept.description || 'Aucune description'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                ${dept.chef_departement ? `${dept.chef_departement.prenom} ${dept.chef_departement.nom}` : 'Non assigné'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                ${dept.employes_count || 0}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="viewDepartement(${dept.id_departement})" title="Voir détails" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                    <i class="fas fa-eye"></i>
                </button>
                <button onclick="editDepartement(${dept.id_departement})" title="Modifier" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteDepartement(${dept.id_departement})" title="Supprimer" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('') : '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun département trouvé</td></tr>';
}

// =============================================
// ACTIONS UTILISATEURS
// =============================================
async function viewUser(userId) {
    try {
        showLoader();
        const response = await fetch(`${API_BASE_URL}/users/${userId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });

        const data = await response.json();
        hideLoader();

        if (data.success) {
            showUserDetails(data.user);
        } else {
            showToast('Erreur', data.message || 'Impossible de charger les détails', 'error');
        }
    } catch (error) {
        hideLoader();
        console.error('Erreur:', error);
        showToast('Erreur', 'Une erreur est survenue', 'error');
    }
}

function showUserDetails(user) {
    const content = `
        <div class="space-y-4">
            <div><strong>Matricule:</strong> ${user.matricule || 'N/A'}</div>
            <div><strong>Nom complet:</strong> ${user.prenom} ${user.nom}</div>
            <div><strong>Email:</strong> ${user.email}</div>
            <div><strong>Téléphone:</strong> ${user.telephone || 'N/A'}</div>
            <div><strong>Département:</strong> ${user.departement?.nom_departement || 'Non assigné'}</div>
            <div><strong>Poste:</strong> ${user.profession || 'N/A'}</div>
            <div><strong>Rôle:</strong> ${user.role?.nom_role || 'N/A'}</div>
            <div><strong>Date d'embauche:</strong> ${user.date_embauche ? new Date(user.date_embauche).toLocaleDateString('fr-FR') : 'N/A'}</div>
            <div><strong>Solde congés:</strong> ${user.solde_conges_annuel || 0} jours</div>
            <div><strong>Statut:</strong> <span class="px-2 py-1 rounded text-xs ${user.actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">${user.actif ? 'Actif' : 'Inactif'}</span></div>
        </div>
    `;

    showModal('Détails de l\'utilisateur', content);
}

async function editUser(userId) {
    try {
        showLoader();
        const response = await fetch(`${API_BASE_URL}/users/${userId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });

        const data = await response.json();
        hideLoader();

        if (data.success) {
            currentEditId = userId;
            const user = data.user;
            const isChef = user.role?.nom_role?.toLowerCase().includes('chef');

            openEditModal(isChef ? 'chef' : 'employe', user);
        } else {
            showToast('Erreur', data.message || 'Impossible de charger les données', 'error');
        }
    } catch (error) {
        hideLoader();
        console.error('Erreur:', error);
        showToast('Erreur', 'Une erreur est survenue', 'error');
    }
}

async function toggleBlockUser(userId, isActive) {
    const action = isActive ? 'bloquer' : 'débloquer';

    if (!confirm(`Voulez-vous vraiment ${action} cet utilisateur ?`)) {
        return;
    }

    try {
        showLoader();
        const endpoint = isActive ? 'block' : 'unblock';
        const response = await fetch(`${API_BASE_URL}/users/${userId}/${endpoint}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });

        const data = await response.json();
        hideLoader();

        if (data.success) {
            showToast('Succès', data.message, 'success');
            await loadUsers();
        } else {
            showToast('Erreur', data.message || 'Une erreur est survenue', 'error');
        }
    } catch (error) {
        hideLoader();
        console.error('Erreur:', error);
        showToast('Erreur', 'Une erreur est survenue', 'error');
    }
}

async function deleteUser(userId) {
    if (!confirm('⚠️ Êtes-vous sûr de vouloir supprimer cet utilisateur ?\n\nCette action est irréversible !')) {
        return;
    }

    try {
        showLoader();
        const response = await fetch(`${API_BASE_URL}/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });

        const data = await response.json();
        hideLoader();

        if (data.success) {
            showToast('Succès', data.message, 'success');
            await loadUsers();
        } else {
            showToast('Erreur', data.message || 'Impossible de supprimer l\'utilisateur', 'error');
        }
    } catch (error) {
        hideLoader();
        console.error('Erreur:', error);
        showToast('Erreur', 'Une erreur est survenue', 'error');
    }
}

// =============================================
// GESTION DES MODALS & FORMULAIRES
// =============================================
function openAddModal(type) {
    const modal = document.getElementById(`${type}-modal`);
    const title = document.getElementById(`${type}-modal-title`);
    const form = document.getElementById(`${type}-form`);

    if (!modal || !title || !form) {
        console.error(`❌ Modal ${type} non trouvé`);
        return;
    }

    title.textContent = `Ajouter un${type === 'employe' ? ' ' : type === 'chef' ? ' chef de ' : ' '}${getTypeLabel(type)}`;
    form.reset();
    currentEditId = null;

    // CACHER LE CHAMP MATRICULE (généré automatiquement côté serveur)
    const matriculeField = document.getElementById(`${type}-matricule`);
    if (matriculeField) {
        matriculeField.value = 'AUTO';
        matriculeField.closest('div').style.display = 'none';
    }

    // Remplir les selects
    populateRoleSelect(type);
    populateDepartementSelect(type);

    // Afficher le modal
    modal.classList.remove('hidden');
    setTimeout(() => {
        const modalContent = modal.querySelector('.modal');
        if (modalContent) modalContent.classList.add('open');
    }, 10);
}

function openEditModal(type, userData) {
    const modal = document.getElementById(`${type}-modal`);
    const title = document.getElementById(`${type}-modal-title`);

    if (!modal || !title) {
        console.error(`❌ Modal ${type} non trouvé`);
        return;
    }

    title.textContent = `Modifier ${getTypeLabel(type)}`;

    // AFFICHER LE MATRICULE EN LECTURE SEULE
    const matriculeField = document.getElementById(`${type}-matricule`);
    if (matriculeField) {
        matriculeField.value = userData.matricule || '';
        matriculeField.closest('div').style.display = 'block';
    }

    // Remplir les champs
    document.getElementById(`${type}-nom`).value = userData.nom || '';
    document.getElementById(`${type}-prenom`).value = userData.prenom || '';
    document.getElementById(`${type}-contact`).value = userData.telephone || '';
    document.getElementById(`${type}-email`).value = userData.email || '';
    document.getElementById(`${type}-poste`).value = userData.profession || '';

    if (type === 'chef') {
        document.getElementById(`${type}-date-nomination`).value = userData.date_embauche || '';
    }

    // Remplir les selects
    populateRoleSelect(type, userData.role_id);
    populateDepartementSelect(type, userData.departement_id);

    // Afficher le modal
    modal.classList.remove('hidden');
    setTimeout(() => {
        const modalContent = modal.querySelector('.modal');
        if (modalContent) modalContent.classList.add('open');
    }, 10);
}

function populateRoleSelect(type, selectedId = null) {
    const select = document.getElementById(`${type}-role`);
    if (!select) {
        console.error(`❌ Select ${type}-role introuvable`);
        return;
    }

    console.log('🔍 Rôles disponibles:', roles);
    console.log('🔍 Type:', type);

    // Filtrer les rôles selon le type
    const filteredRoles = type === 'employe'
        ? roles.filter(r => {
            const nom = r.nom_role.toLowerCase();
            return nom.includes('employe') || nom.includes('employé');
        })
        : roles.filter(r => {
            const nom = r.nom_role.toLowerCase();
            return nom.includes('chef') || nom.includes('manager');
        });

    console.log('✅ Rôles filtrés:', filteredRoles);

    if (filteredRoles.length === 0) {
        console.warn('⚠️ Aucun rôle trouvé pour le type:', type);
        select.innerHTML = '<option value="">Aucun rôle disponible</option>';
        return;
    }

    // Remplir le select
    select.innerHTML = filteredRoles.map(role =>
        `<option value="${role.id_role}" ${selectedId == role.id_role ? 'selected' : ''}>${role.nom_role}</option>`
    ).join('');

    // Si pas de sélection, sélectionner le premier par défaut
    if (!selectedId && filteredRoles.length > 0) {
        select.value = filteredRoles[0].id_role;
    }

    console.log('✅ Select rempli avec', filteredRoles.length, 'rôles');
}

function populateDepartementSelect(type, selectedId = null) {
    const select = document.getElementById(`${type}-departement`);
    if (!select) return;

    select.innerHTML = '<option value="">Sélectionner un département</option>' +
        departements.map(dept =>
            `<option value="${dept.id_departement}" ${selectedId == dept.id_departement ? 'selected' : ''}>${dept.nom_departement}</option>`
        ).join('');
}

function populateDepartementSelects() {
    // Pour les modals
    populateDepartementSelect('employe');
    populateDepartementSelect('chef');

    // Pour le modal département (chef select)
    const chefSelect = document.getElementById('departement-chef');
    if (chefSelect) {
        const chefs = users.filter(u => {
            const nom = u.role?.nom_role?.toLowerCase() || '';
            return nom.includes('chef') || nom.includes('manager');
        });
        chefSelect.innerHTML = '<option value="">Sélectionner un chef</option>' +
            chefs.map(chef =>
                `<option value="${chef.id_user}">${chef.prenom} ${chef.nom}</option>`
            ).join('');
    }
}

// =============================================
// SOUMISSION DES FORMULAIRES
// =============================================
async function handleEmployeSubmit(e) {
    e.preventDefault();
    await handleUserSubmit('employe');
}

async function handleChefSubmit(e) {
    e.preventDefault();
    await handleUserSubmit('chef');
}

async function handleUserSubmit(type) {
    const formData = {
        nom: document.getElementById(`${type}-nom`).value,
        prenom: document.getElementById(`${type}-prenom`).value,
        email: document.getElementById(`${type}-email`).value,
        telephone: document.getElementById(`${type}-contact`).value,
        profession: document.getElementById(`${type}-poste`).value,
        role_id: document.getElementById(`${type}-role`).value,
        departement_id: document.getElementById(`${type}-departement`).value || null,
        date_embauche: type === 'chef' ? document.getElementById(`${type}-date-nomination`).value : new Date().toISOString().split('T')[0],
        solde_conges_annuel: 30
    };

    // Si modification, ajouter le matricule existant
    if (currentEditId) {
        const matriculeField = document.getElementById(`${type}-matricule`);
        if (matriculeField && matriculeField.value) {
            formData.matricule = matriculeField.value;
        }
    }

    console.log('📤 Données envoyées:', formData);

    try {
        showLoader();
        const url = currentEditId
            ? `${API_BASE_URL}/users/${currentEditId}`
            : `${API_BASE_URL}/users`;

        const method = currentEditId ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        console.log('📥 Réponse reçue:', data);
        hideLoader();

        if (data.success) {
            showToast('Succès', data.message, 'success');
            closeModal(`${type}-modal`);
            await loadUsers();
        } else {
            if (data.errors) {
                const errorMsg = Object.values(data.errors).flat().join('\n');
                showToast('Erreur de validation', errorMsg, 'error');
            } else {
                showToast('Erreur', data.message || 'Une erreur est survenue', 'error');
            }
        }
    } catch (error) {
        hideLoader();
        console.error('❌ Erreur:', error);
        showToast('Erreur', 'Une erreur est survenue', 'error');
    }
}

async function handleDepartementSubmit(e) {
    e.preventDefault();

    const formData = {
        nom_departement: document.getElementById('departement-nom').value,
        description: document.getElementById('departement-description').value,
        chef_departement_id: document.getElementById('departement-chef').value || null
    };

    try {
        showLoader();
        const url = currentEditId
            ? `${API_BASE_URL}/departements/${currentEditId}`
            : `${API_BASE_URL}/departements`;

        const method = currentEditId ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        hideLoader();

        if (data.success) {
            showToast('Succès', data.message, 'success');
            closeModal('departement-modal');
            await loadDepartements();
        } else {
            showToast('Erreur', data.message || 'Une erreur est survenue', 'error');
        }
    } catch (error) {
        hideLoader();
        console.error('Erreur:', error);
        showToast('Erreur', 'Une erreur est survenue', 'error');
    }
}

// =============================================
// ACTIONS DÉPARTEMENTS
// =============================================
async function viewDepartement(deptId) {
    const dept = departements.find(d => d.id_departement == deptId);
    if (!dept) return;

    const content = `
        <div class="space-y-4">
            <div><strong>Nom:</strong> ${dept.nom_departement}</div>
            <div><strong>Description:</strong> ${dept.description || 'Aucune description'}</div>
            <div><strong>Chef de département:</strong> ${dept.chef_departement ? `${dept.chef_departement.prenom} ${dept.chef_departement.nom}` : 'Non assigné'}</div>
