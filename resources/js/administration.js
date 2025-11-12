// =============================================
// CONFIGURATION & VARIABLES GLOBALES
// =============================================
const API_BASE_URL = '/admin/api';
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
                <button onclick="toggleBlockUser(${user.id_user}, ${user.actif})" title="${user.actif ? 'Bloquer' : 'Débloquer'}" class="mr-3">
                    <i class="fas fa-${user.actif ? 'lock' : 'unlock'}" style="color: ${user.actif ? '#10B981' : '#EF4444'}"></i>
                </button>
                <button onclick="deleteUser(${user.id_user})" title="Supprimer" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('') : '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucun employé trouvé</td></tr>';
}

function renderChefsTable() {
    const tbody = document.querySelector('#chefs-tab tbody');
    if (!tbody) return;

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
                <button onclick="toggleBlockUser(${user.id_user}, ${user.actif})" title="${user.actif ? 'Bloquer' : 'Débloquer'}" class="mr-3">
                    <i class="fas fa-${user.actif ? 'lock' : 'unlock'}" style="color: ${user.actif ? '#10B981' : '#EF4444'}"></i>
                </button>
                <button onclick="deleteUser(${user.id_user})" title="Supprimer" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('') : '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucun chef de département trouvé</td></tr>';
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
    `).join('') : '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucun département trouvé</td></tr>';
}

// =============================================
// ACTIONS UTILISATEURS AVEC SWEETALERT2
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
        <div class="space-y-4 text-left">
            <div class="text-gray-900 dark:text-white"><strong>Matricule:</strong> ${user.matricule || 'N/A'}</div>
            <div class="text-gray-900 dark:text-white"><strong>Nom complet:</strong> ${user.prenom} ${user.nom}</div>
            <div class="text-gray-900 dark:text-white"><strong>Email:</strong> ${user.email}</div>
            <div class="text-gray-900 dark:text-white"><strong>Téléphone:</strong> ${user.telephone || 'N/A'}</div>
            <div class="text-gray-900 dark:text-white"><strong>Département:</strong> ${user.departement?.nom_departement || 'Non assigné'}</div>
            <div class="text-gray-900 dark:text-white"><strong>Poste:</strong> ${user.profession || 'N/A'}</div>
            <div class="text-gray-900 dark:text-white"><strong>Rôle:</strong> ${user.role?.nom_role || 'N/A'}</div>
            <div class="text-gray-900 dark:text-white"><strong>Date d'embauche:</strong> ${user.date_embauche ? new Date(user.date_embauche).toLocaleDateString('fr-FR') : 'N/A'}</div>
            <div class="text-gray-900 dark:text-white"><strong>Solde congés:</strong> ${user.solde_conges_annuel || 0} jours</div>
            <div class="text-gray-900 dark:text-white"><strong>Statut:</strong> <span class="px-2 py-1 rounded text-xs ${user.actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">${user.actif ? 'Actif' : 'Inactif'}</span></div>
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
    const actionText = isActive ? 'Bloquer' : 'Débloquer';

    const result = await Swal.fire({
        title: `${actionText} cet utilisateur ?`,
        text: `Voulez-vous vraiment ${action} cet utilisateur ?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isActive ? '#EF4444' : '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: `Oui, ${action}`,
        cancelButtonText: 'Annuler',
        background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
        color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
    });

    if (!result.isConfirmed) return;

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
            // ✅ SUCCÈS : Pas de bouton, refresh après 2s
            Swal.fire({
                title: 'Succès !',
                text: data.message,
                icon: 'success',
                showConfirmButton: false,
                timer: 2000,
                background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
                color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
            }).then(() => {
                window.location.reload();
            });
        } else {
            // ❌ ERREUR : Afficher le bouton D'accord
            await Swal.fire({
                title: 'Erreur !',
                text: data.message || 'Une erreur est survenue',
                icon: 'error',
                confirmButtonText: 'D\'accord',
                confirmButtonColor: '#EF4444',
                background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
                color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
            });
        }
    } catch (error) {
        hideLoader();
        console.error('Erreur:', error);
        await Swal.fire({
            title: 'Erreur !',
            text: 'Une erreur est survenue',
            icon: 'error',
            confirmButtonText: 'D\'accord',
            confirmButtonColor: '#EF4444',
            background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
            color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
        });
    }
}

async function deleteUser(userId) {
    const result = await Swal.fire({
        title: 'Supprimer cet utilisateur ?',
        text: "⚠️ Cette action est irréversible !",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler',
        background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
        color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
    });

    if (!result.isConfirmed) return;

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
            // ✅ SUCCÈS : Pas de bouton, refresh après 2s
            Swal.fire({
                title: 'Supprimé !',
                text: data.message,
                icon: 'success',
                showConfirmButton: false,
                timer: 2000,
                background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
                color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
            }).then(() => {
                window.location.reload();
            });
        } else {
            // ❌ ERREUR : Afficher le bouton D'accord
            await Swal.fire({
                title: 'Erreur !',
                text: data.message || 'Impossible de supprimer l\'utilisateur',
                icon: 'error',
                confirmButtonText: 'D\'accord',
                confirmButtonColor: '#EF4444',
                background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
                color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
            });
        }
    } catch (error) {
        hideLoader();
        console.error('Erreur:', error);
        await Swal.fire({
            title: 'Erreur !',
            text: 'Une erreur est survenue',
            icon: 'error',
            confirmButtonText: 'D\'accord',
            confirmButtonColor: '#EF4444',
            background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
            color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
        });
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

    const matriculeField = document.getElementById(`${type}-matricule`);
    if (matriculeField) {
        matriculeField.value = 'AUTO';
        matriculeField.closest('div').style.display = 'none';
    }

    populateRoleSelect(type);
    populateDepartementSelect(type);

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

    const matriculeField = document.getElementById(`${type}-matricule`);
    if (matriculeField) {
        matriculeField.value = userData.matricule || '';
        matriculeField.closest('div').style.display = 'block';
    }

    document.getElementById(`${type}-nom`).value = userData.nom || '';
    document.getElementById(`${type}-prenom`).value = userData.prenom || '';
    document.getElementById(`${type}-contact`).value = userData.telephone || '';
    document.getElementById(`${type}-email`).value = userData.email || '';
    document.getElementById(`${type}-poste`).value = userData.profession || '';

    if (type === 'chef') {
        document.getElementById(`${type}-date-nomination`).value = userData.date_embauche || '';
    }

    populateRoleSelect(type, userData.role_id);
    populateDepartementSelect(type, userData.departement_id);

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

    const filteredRoles = type === 'employe'
        ? roles.filter(r => {
            const nom = r.nom_role.toLowerCase();
            return nom.includes('employe') || nom.includes('employé');
        })
        : roles.filter(r => {
            const nom = r.nom_role.toLowerCase();
            return nom.includes('chef') || nom.includes('manager');
        });

    if (filteredRoles.length === 0) {
        console.warn('⚠️ Aucun rôle trouvé pour le type:', type);
        select.innerHTML = '<option value="">Aucun rôle disponible</option>';
        return;
    }

    select.innerHTML = filteredRoles.map(role =>
        `<option value="${role.id_role}" ${selectedId == role.id_role ? 'selected' : ''}>${role.nom_role}</option>`
    ).join('');

    if (!selectedId && filteredRoles.length > 0) {
        select.value = filteredRoles[0].id_role;
    }
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
    populateDepartementSelect('employe');
    populateDepartementSelect('chef');

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
            // ✅ SUCCÈS : Pas de bouton, refresh après 2s
            closeModal(`${type}-modal`);
            Swal.fire({
                title: 'Succès !',
                text: data.message,
                icon: 'success',
                showConfirmButton: false,
                timer: 2000,
                background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
                color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
            }).then(() => {
                window.location.reload();
            });
        } else {
            // ❌ ERREUR : Afficher le bouton D'accord
            if (data.errors) {
                const errorMsg = Object.values(data.errors).flat().join('<br>');
                await Swal.fire({
                    title: 'Erreur de validation',
                    html: errorMsg,
                    icon: 'error',
                    confirmButtonText: 'D\'accord',
                    confirmButtonColor: '#EF4444',
                    background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
                    color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
                });
            } else {
                await Swal.fire({
                    title: 'Erreur !',
                    text: data.message || 'Une erreur est survenue',
                    icon: 'error',
                    confirmButtonText: 'D\'accord',
                    confirmButtonColor: '#EF4444',
                    background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
                    color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
                });
            }
        }
    } catch (error) {
        hideLoader();
        console.error('❌ Erreur:', error);
        await Swal.fire({
            title: 'Erreur !',
            text: 'Une erreur est survenue',
            icon: 'error',
            confirmButtonText: 'D\'accord',
            confirmButtonColor: '#EF4444',
            background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
            color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
        });
    }
}

async function handleDepartementSubmit(e) {
    e.preventDefault();

    const formData = {
        nom_departement: document.getElementById('departement-nom').value,
        description: document.getElementById('departement-description').value,
        chef_departement_id: document.getElementById('departement-chef').value || null
    };

    console.log('📤 Données département envoyées:', formData);
    console.log('🔧 Mode:', currentEditId ? 'Modification' : 'Création');

    try {
        showLoader();

        const url = currentEditId
            ? `${API_BASE_URL}/departements/${currentEditId}`
            : `${API_BASE_URL}/departements`;

        let fetchOptions = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        };

        if (currentEditId) {
            fetchOptions.method = 'POST';
            formData._method = 'PUT';
            fetchOptions.body = JSON.stringify(formData);
        } else {
            fetchOptions.method = 'POST';
            fetchOptions.body = JSON.stringify(formData);
        }

        console.log('🌐 Requête envoyée:', url, fetchOptions);

        const response = await fetch(url, fetchOptions);
        const data = await response.json();

        console.log('📥 Réponse reçue:', data);
        hideLoader();

        if (data.success) {
            // ✅ SUCCÈS : Pas de bouton, refresh après 2s
            closeModal('departement-modal');
            Swal.fire({
                title: 'Succès !',
                text: data.message,
                icon: 'success',
                showConfirmButton: false,
                timer: 2000,
                background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
                color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
            }).then(() => {
                window.location.reload();
            });
        } else {
            // ❌ ERREUR : Afficher le bouton D'accord
            await Swal.fire({
                title: 'Erreur !',
                text: data.message || 'Une erreur est survenue',
                icon: 'error',
                confirmButtonText: 'D\'accord',
                confirmButtonColor: '#EF4444',
                background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
                color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
            });
        }
    } catch (error) {
        hideLoader();
        console.error('❌ Erreur:', error);
        await Swal.fire({
            title: 'Erreur !',
            text: 'Une erreur est survenue : ' + error.message,
            icon: 'error',
            confirmButtonText: 'D\'accord',
            confirmButtonColor: '#EF4444',
            background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
            color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
        });
    }
}

// =============================================
// ACTIONS DÉPARTEMENTS AVEC SWEETALERT2
// =============================================
async function viewDepartement(deptId) {
    const dept = departements.find(d => d.id_departement == deptId);
    if (!dept) return;

    const content = `
        <div class="space-y-4 text-left">
            <div class="text-gray-900 dark:text-white"><strong>Nom:</strong> ${dept.nom_departement}</div>
            <div class="text-gray-900 dark:text-white"><strong>Description:</strong> ${dept.description || 'Aucune description'}</div>
            <div class="text-gray-900 dark:text-white"><strong>Chef de département:</strong> ${dept.chef_departement ? `${dept.chef_departement.prenom} ${dept.chef_departement.nom}` : 'Non assigné'}</div>
            <div class="text-gray-900 dark:text-white"><strong>Nombre d'employés:</strong> ${dept.employes_count || 0}</div>
        </div>
    `;

    showModal('Détails du département', content);
}

async function editDepartement(deptId) {
    const dept = departements.find(d => d.id_departement == deptId);
    if (!dept) return;

    currentEditId = deptId;
    const modal = document.getElementById('departement-modal');
    const title = document.getElementById('departement-modal-title');

    if (!modal || !title) return;

    title.textContent = 'Modifier le département';

    document.getElementById('departement-nom').value = dept.nom_departement || '';
    document.getElementById('departement-description').value = dept.description || '';

    populateDepartementSelects();
    document.getElementById('departement-chef').value = dept.chef_departement_id || '';

    modal.classList.remove('hidden');
    setTimeout(() => {
        const modalContent = modal.querySelector('.modal');
        if (modalContent) modalContent.classList.add('open');
    }, 10);
}

async function deleteDepartement(deptId) {
    const result = await Swal.fire({
        title: 'Supprimer ce département ?',
        text: "⚠️ Cette action est irréversible !",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler',
        background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
        color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
    });

    if (!result.isConfirmed) return;

    try {
        showLoader();
        const response = await fetch(`${API_BASE_URL}/departements/${deptId}`, {
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
            // ✅ SUCCÈS : Pas de bouton, refresh après 2s
            Swal.fire({
                title: 'Supprimé !',
                text: data.message,
                icon: 'success',
                showConfirmButton: false,
                timer: 2000,
                background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
                color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
            }).then(() => {
                window.location.reload();
            });
        } else {
            // ❌ ERREUR : Afficher le bouton D'accord
            await Swal.fire({
                title: 'Erreur !',
                text: data.message || 'Impossible de supprimer le département',
                icon: 'error',
                confirmButtonText: 'D\'accord',
                confirmButtonColor: '#EF4444',
                background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
                color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
            });
        }
    } catch (error) {
        hideLoader();
        console.error('Erreur:', error);
        await Swal.fire({
            title: 'Erreur !',
            text: 'Une erreur est survenue',
            icon: 'error',
            confirmButtonText: 'D\'accord',
            confirmButtonColor: '#EF4444',
            background: document.documentElement.classList.contains('dark') ? '#1F2937' : '#FFFFFF',
            color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#000000'
        });
    }
}

// =============================================
// GESTION DES ONGLETS
// =============================================
function initTabs() {
    function showTab(tabName) {
        const allTabs = document.querySelectorAll('.tab-pane');
        allTabs.forEach(tab => {
            tab.classList.add('hidden');
            tab.classList.remove('active');
        });

        const allButtons = document.querySelectorAll('.tab-button');
        allButtons.forEach(button => {
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

        console.log(`📄 Onglet affiché: ${tabName}`);
    }

    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            showTab(tabName);
        });
    });

    setTimeout(() => showTab('employes'), 0);
}

// =============================================
// INITIALISATION DES ÉCOUTEURS D'ÉVÉNEMENTS
// =============================================
function initEventListeners() {
    const toggleSidebar = document.getElementById('toggle-sidebar');
    const closeSidebar = document.getElementById('close-sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    if (toggleSidebar) toggleSidebar.addEventListener('click', toggleSidebarMenu);
    if (closeSidebar) closeSidebar.addEventListener('click', toggleSidebarMenu);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebarMenu);

    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) themeToggle.addEventListener('click', toggleTheme);

    const addEmployeBtn = document.getElementById('add-employe-btn');
    const addChefBtn = document.getElementById('add-chef-btn');
    const addDepartementBtn = document.getElementById('add-departement-btn');

    if (addEmployeBtn) addEmployeBtn.addEventListener('click', () => openAddModal('employe'));
    if (addChefBtn) addChefBtn.addEventListener('click', () => openAddModal('chef'));
    if (addDepartementBtn) addDepartementBtn.addEventListener('click', () => openAddModal('departement'));

    const employeForm = document.getElementById('employe-form');
    const chefForm = document.getElementById('chef-form');
    const departementForm = document.getElementById('departement-form');

    if (employeForm) employeForm.addEventListener('submit', handleEmployeSubmit);
    if (chefForm) chefForm.addEventListener('submit', handleChefSubmit);
    if (departementForm) departementForm.addEventListener('submit', handleDepartementSubmit);
}

// =============================================
// GESTION SIDEBAR & THÈME
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

// =============================================
// GESTION DES MODALS
// =============================================
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    const modalContent = modal.querySelector('.modal');
    if (modalContent) modalContent.classList.remove('open');

    setTimeout(() => {
        modal.classList.add('hidden');
        currentEditId = null;
    }, 300);
}

function showModal(title, content) {
    const modalHtml = `
        <div id="info-modal" class="fixed inset-0 z-50">
            <div class="backdrop absolute inset-0 bg-black bg-opacity-50" onclick="closeInfoModal()"></div>
            <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">${title}</h3>
                    </div>
                    <div class="p-6 text-gray-900 dark:text-white">
                        ${content}
                    </div>
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                        <button onclick="closeInfoModal()"
                            class="w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    const existingModal = document.getElementById('info-modal');
    if (existingModal) existingModal.remove();

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    setTimeout(() => {
        const modal = document.getElementById('info-modal');
        if (modal) modal.querySelector('.modal')?.classList.add('open');
    }, 10);
}

function closeInfoModal() {
    const modal = document.getElementById('info-modal');
    if (modal) {
        modal.querySelector('.modal')?.classList.remove('open');
        setTimeout(() => modal.remove(), 300);
    }
}

// =============================================
// UTILITAIRES
// =============================================
function updateCurrentDate() {
    const currentDateElement = document.getElementById('current-date');
    if (currentDateElement) {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        currentDateElement.textContent = now.toLocaleDateString('fr-FR', options);
    }
}

function getTypeLabel(type) {
    switch(type) {
        case 'employe': return 'Employé';
        case 'chef': return 'Chef de Département';
        case 'departement': return 'Département';
        default: return '';
    }
}

function showLoader() {
    let loader = document.getElementById('global-loader');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'global-loader';
        loader.innerHTML = `
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl">
                    <div class="flex items-center space-x-3">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                        <span class="text-gray-900 dark:text-white font-medium">Chargement...</span>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(loader);
    }
    loader.style.display = 'block';
}

function hideLoader() {
    const loader = document.getElementById('global-loader');
    if (loader) {
        loader.style.display = 'none';
    }
}

function showToast(title, message, type = 'success') {
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

    setTimeout(() => toast.style.transform = 'translateX(0)', 10);
    setTimeout(() => {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// =============================================
// EXPOSITION DES FONCTIONS AU SCOPE GLOBAL
// =============================================
window.viewUser = viewUser;
window.editUser = editUser;
window.deleteUser = deleteUser;
window.toggleBlockUser = toggleBlockUser;
window.viewDepartement = viewDepartement;
window.editDepartement = editDepartement;
window.deleteDepartement = deleteDepartement;
window.closeModal = closeModal;
window.closeInfoModal = closeInfoModal;

































