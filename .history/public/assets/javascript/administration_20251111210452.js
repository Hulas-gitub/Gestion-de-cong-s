// =============================================
// VARIABLES GLOBALES
// =============================================
let currentDeleteId = null;
let currentDeleteType = null;
let currentEditId = null;
let currentData = window.laravelData || {};

// =============================================
// CONSTANTES DES ROUTES API
// =============================================
const API_ROUTES = {
    users: {
        index: '/api/users',
        store: '/api/users',
        show: (id) => `/api/users/${id}`,
        update: (id) => `/api/users/${id}`,
        destroy: (id) => `/api/users/${id}`,
        block: (id) => `/api/users/${id}/block`,
        unblock: (id) => `/api/users/${id}/unblock`,
        resendActivation: (id) => `/api/users/${id}/resend-activation`,
        generateMatricule: '/api/users/generate-matricule'
    },
    departements: {
        index: '/api/departements',
        store: '/api/departements',
        show: (id) => `/api/departements/${id}`,
        update: (id) => `/api/departements/${id}`,
        destroy: (id) => `/api/departements/${id}`
    }
};

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
    try {
        showLoading('Chargement des données...');

        // Charger les utilisateurs
        await loadUsers();

        // Charger les départements
        await loadDepartements();

        // Mettre à jour les selects des formulaires
        updateFormSelects();

        hideLoading();

    } catch (error) {
        console.error('Erreur lors du chargement des données:', error);
        showToast('Erreur', 'Impossible de charger les données', 'error');
        hideLoading();
    }
}

async function loadUsers() {
    try {
        const response = await fetch(API_ROUTES.users.index);
        const data = await response.json();

        if (data.success) {
            currentData.users = data.users;
            updateUsersTables();
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Erreur chargement utilisateurs:', error);
        throw error;
    }
}

async function loadDepartements() {
    try {
        const response = await fetch(API_ROUTES.departements.index);
        const data = await response.json();

        if (data.success) {
            currentData.departements = data.departements;
            updateDepartementsTable();
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Erreur chargement départements:', error);
        throw error;
    }
}

// =============================================
// MISE À JOUR DES TABLES
// =============================================
function updateUsersTables() {
    if (!currentData.users) return;

    // Filtrer les employés (rôle Employé)
    const employes = currentData.users.filter(user =>
        user.role && user.role.nom_role.toLowerCase().includes('employé')
    );

    // Filtrer les chefs de département
    const chefs = currentData.users.filter(user =>
        user.role && (
            user.role.nom_role.toLowerCase().includes('chef') ||
            user.role.nom_role.toLowerCase().includes('manager')
        )
    );

    // Mettre à jour la table des employés
    updateEmployesTable(employes);

    // Mettre à jour la table des chefs
    updateChefsTable(chefs);
}

function updateEmployesTable(employes) {
    const tbody = document.querySelector('#employes-tab tbody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (employes.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    Aucun employé trouvé
                </td>
            </tr>
        `;
        return;
    }

    employes.forEach(employe => {
        const row = createEmployeRow(employe);
        tbody.appendChild(row);
    });

    // Réinitialiser les écouteurs d'événements
    setupActionButtons();
}

function updateChefsTable(chefs) {
    const tbody = document.querySelector('#chefs-tab tbody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (chefs.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    Aucun chef de département trouvé
                </td>
            </tr>
        `;
        return;
    }

    chefs.forEach(chef => {
        const row = createChefRow(chef);
        tbody.appendChild(row);
    });

    // Réinitialiser les écouteurs d'événements
    setupActionButtons();
}

function updateDepartementsTable() {
    const tbody = document.querySelector('#departements-tab tbody');
    if (!tbody || !currentData.departements) return;

    tbody.innerHTML = '';

    if (currentData.departements.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    Aucun département trouvé
                </td>
            </tr>
        `;
        return;
    }

    currentData.departements.forEach(departement => {
        const row = createDepartementRow(departement);
        tbody.appendChild(row);
    });

    // Réinitialiser les écouteurs d'événements
    setupActionButtons();
}

// =============================================
// CRÉATION DES LIGNES DE TABLEAU
// =============================================
function createEmployeRow(employe) {
    const row = document.createElement('tr');

    const statusClass = employe.actif ?
        'bg-green-100 text-green-800' :
        'bg-red-100 text-red-800';
    const statusText = employe.actif ? 'Actif' : 'Bloqué';

    row.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${employe.matricule}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${employe.prenom} ${employe.nom}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${employe.email}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${employe.departement ? employe.departement.nom_departement : 'Non assigné'}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${employe.profession || 'Non spécifié'}</td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">${statusText}</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <button title="Voir détails" data-id="${employe.id_user}" data-type="employe" class="view-btn text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                <i class="fas fa-eye"></i>
            </button>
            <button title="Modifier" data-id="${employe.id_user}" data-type="employe" class="edit-btn text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                <i class="fas fa-edit"></i>
            </button>
            <button title="${employe.actif ? 'Bloquer' : 'Débloquer'}" data-id="${employe.id_user}" data-type="employe" class="block-btn ${employe.actif ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'} dark:text-red-400 dark:hover:text-red-300 mr-3">
                <i class="fas ${employe.actif ? 'fa-lock' : 'fa-unlock'}"></i>
            </button>
            <button title="Supprimer" data-id="${employe.id_user}" data-type="employe" class="delete-btn text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;

    return row;
}

function createChefRow(chef) {
    const row = document.createElement('tr');

    const dateNomination = chef.date_embauche ?
        new Date(chef.date_embauche).toLocaleDateString('fr-FR') :
        'Non spécifiée';

    row.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${chef.matricule}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${chef.prenom} ${chef.nom}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${chef.email}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${chef.departement ? chef.departement.nom_departement : 'Non assigné'}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${dateNomination}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <button title="Voir détails" data-id="${chef.id_user}" data-type="chef" class="view-btn text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                <i class="fas fa-eye"></i>
            </button>
            <button title="Modifier" data-id="${chef.id_user}" data-type="chef" class="edit-btn text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                <i class="fas fa-edit"></i>
            </button>
            <button title="${chef.actif ? 'Bloquer' : 'Débloquer'}" data-id="${chef.id_user}" data-type="chef" class="block-btn ${chef.actif ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'} dark:text-red-400 dark:hover:text-red-300 mr-3">
                <i class="fas ${chef.actif ? 'fa-lock' : 'fa-unlock'}"></i>
            </button>
            <button title="Supprimer" data-id="${chef.id_user}" data-type="chef" class="delete-btn text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;

    return row;
}

function createDepartementRow(departement) {
    const row = document.createElement('tr');

    const chefNom = departement.chef_departement ?
        `${departement.chef_departement.prenom} ${departement.chef_departement.nom}` :
        'Non assigné';

    row.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${departement.id_departement}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${departement.nom_departement}</td>
        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">${departement.description || 'Aucune description'}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${chefNom}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${departement.employes_count || 0}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <button title="Voir détails" data-id="${departement.id_departement}" data-type="departement" class="view-btn text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                <i class="fas fa-eye"></i>
            </button>
            <button title="Modifier" data-id="${departement.id_departement}" data-type="departement" class="edit-btn text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                <i class="fas fa-edit"></i>
            </button>
            <button title="Supprimer" data-id="${departement.id_departement}" data-type="departement" class="delete-btn text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;

    return row;
}

// =============================================
// GESTION DES ÉVÉNEMENTS DES BOUTONS
// =============================================
function setupActionButtons() {
    setupViewButtons();
    setupEditButtons();
    setupBlockButtons();
    setupDeleteButtons();
}

function setupViewButtons() {
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const type = this.getAttribute('data-type');
            openViewModal(type, id);
        });
    });
}

function setupEditButtons() {
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const type = this.getAttribute('data-type');
            openEditModal(type, id);
        });
    });
}

function setupBlockButtons() {
    document.querySelectorAll('.block-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const type = this.getAttribute('data-type');
            const isCurrentlyActive = this.classList.contains('text-red-600');

            if (isCurrentlyActive) {
                openBlockModal(type, id);
            } else {
                openUnblockModal(type, id);
            }
        });
    });
}

function setupDeleteButtons() {
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const type = this.getAttribute('data-type');
            openDeleteModal(type, id);
        });
    });
}

// =============================================
// GESTION DES MODALS
// =============================================
async function openViewModal(type, id) {
    try {
        let response;
        if (type === 'departement') {
            response = await fetch(API_ROUTES.departements.show(id));
        } else {
            response = await fetch(API_ROUTES.users.show(id));
        }

        const data = await response.json();

        if (data.success) {
            const item = data[type] || data.user || data.departement;
            showViewDetails(type, item);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Erreur chargement détails:', error);
        showToast('Erreur', 'Impossible de charger les détails', 'error');
    }
}

function showViewDetails(type, item) {
    let modalId, content;

    switch(type) {
        case 'employe':
        case 'chef':
            modalId = type === 'employe' ? 'employe' : 'chef-departement';
            content = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom complet</label>
                        <input type="text" value="${item.prenom} ${item.nom}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" value="${item.email}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Téléphone</label>
                        <input type="text" value="${item.telephone || 'Non spécifié'}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Département</label>
                        <input type="text" value="${item.departement ? item.departement.nom_departement : 'Non assigné'}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                    </div>
                    ${type === 'chef' ? `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date de nomination</label>
                        <input type="text" value="${item.date_embauche ? new Date(item.date_embauche).toLocaleDateString('fr-FR') : 'Non spécifiée'}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                    </div>
                    ` : ''}
                </div>
            `;
            break;

        case 'departement':
            modalId = 'departement-detail';
            content = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom du département</label>
                        <input type="text" value="${item.nom_departement}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white" readonly>${item.description || 'Aucune description'}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Chef de département</label>
                        <input type="text" value="${item.chef_departement ? item.chef_departement.prenom + ' ' + item.chef_departement.nom : 'Non assigné'}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre d'employés</label>
                        <input type="text" value="${item.employes_count || 0}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                    </div>
                </div>
            `;
            break;
    }

    const modal = document.getElementById(modalId);
    const contentDiv = modal.querySelector('.p-6 > div');
    if (contentDiv) {
        contentDiv.innerHTML = content;
    }

    modal.classList.remove('hidden');
}

async function openEditModal(type, id) {
    try {
        let response;
        if (type === 'departement') {
            response = await fetch(API_ROUTES.departements.show(id));
        } else {
            response = await fetch(API_ROUTES.users.show(id));
        }

        const data = await response.json();

        if (data.success) {
            const item = data[type] || data.user || data.departement;
            fillEditForm(type, item);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Erreur chargement données édition:', error);
        showToast('Erreur', 'Impossible de charger les données', 'error');
    }
}

function fillEditForm(type, item) {
    currentEditId = item.id_user || item.id_departement;

    if (type === 'employe' || type === 'chef') {
        const modal = document.getElementById(`${type}-modal`);
        const title = document.getElementById(`${type}-modal-title`);

        if (title) title.textContent = `Modifier ${getTypeLabel(type)}`;

        // Remplir les champs du formulaire
        document.getElementById(`${type}-matricule`).value = item.matricule;
        document.getElementById(`${type}-nom`).value = item.nom;
        document.getElementById(`${type}-prenom`).value = item.prenom;
        document.getElementById(`${type}-contact`).value = item.telephone || '';
        document.getElementById(`${type}-email`).value = item.email;
        document.getElementById(`${type}-poste`).value = item.profession || '';

        if (type === 'employe') {
            document.getElementById('employe-role').value = item.role ? item.role.id_role : '';
            document.getElementById('employe-departement').value = item.departement ? item.departement.id_departement : '';
            document.getElementById('employe-date-embauche').value = item.date_embauche ? item.date_embauche.split('T')[0] : '';
        } else {
            document.getElementById('chef-departement').value = item.departement ? item.departement.id_departement : '';
            document.getElementById('chef-date-nomination').value = item.date_embauche ? item.date_embauche.split('T')[0] : '';
        }

        modal.classList.remove('hidden');

    } else if (type === 'departement') {
        const modal = document.getElementById('departement-modal');
        const title = document.getElementById('departement-modal-title');

        if (title) title.textContent = `Modifier le département`;

        document.getElementById('departement-nom').value = item.nom_departement;
        document.getElementById('departement-description').value = item.description || '';
        document.getElementById('departement-chef').value = item.chef_departement_id || '';

        modal.classList.remove('hidden');
    }
}

// =============================================
// GESTION DES FORMULAIRES (AVEC APPELS API)
// =============================================
async function handleEmployeSubmit(e) {
    e.preventDefault();

    const formData = {
        nom: document.getElementById('employe-nom').value,
        prenom: document.getElementById('employe-prenom').value,
        email: document.getElementById('employe-email').value,
        telephone: document.getElementById('employe-contact').value,
        profession: document.getElementById('employe-poste').value,
        matricule: document.getElementById('employe-matricule').value,
        date_embauche: document.getElementById('employe-date-embauche').value,
        role_id: document.getElementById('employe-role').value,
        departement_id: document.getElementById('employe-departement').value,
        solde_conges_annuel: 30
    };

    try {
        const url = currentEditId ?
            API_ROUTES.users.update(currentEditId) :
            API_ROUTES.users.store;

        const method = currentEditId ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.success) {
            showToast(
                currentEditId ? 'Employé modifié' : 'Employé ajouté',
                data.message,
                'success'
            );

            closeModal('employe-modal');
            await loadUsers();
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Erreur sauvegarde employé:', error);
        showToast('Erreur', error.message, 'error');
    }
}

async function handleChefSubmit(e) {
    e.preventDefault();

    const formData = {
        nom: document.getElementById('chef-nom').value,
        prenom: document.getElementById('chef-prenom').value,
        email: document.getElementById('chef-email').value,
        telephone: document.getElementById('chef-contact').value,
        profession: document.getElementById('chef-poste').value,
        matricule: document.getElementById('chef-matricule').value,
        date_embauche: document.getElementById('chef-date-nomination').value,
        role_id: getRoleIdForChef(),
        departement_id: document.getElementById('chef-departement').value,
        solde_conges_annuel: 30
    };

    try {
        const url = currentEditId ?
            API_ROUTES.users.update(currentEditId) :
            API_ROUTES.users.store;

        const method = currentEditId ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.success) {
            showToast(
                currentEditId ? 'Chef modifié' : 'Chef ajouté',
                data.message,
                'success'
            );

            closeModal('chef-modal');
            await loadUsers();
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Erreur sauvegarde chef:', error);
        showToast('Erreur', error.message, 'error');
    }
}

async function handleDepartementSubmit(e) {
    e.preventDefault();

    const formData = {
        nom_departement: document.getElementById('departement-nom').value,
        description: document.getElementById('departement-description').value,
        chef_departement_id: document.getElementById('departement-chef').value
    };

    try {
        const url = currentEditId ?
            API_ROUTES.departements.update(currentEditId) :
            API_ROUTES.departements.store;

        const method = currentEditId ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.success) {
            showToast(
                currentEditId ? 'Département modifié' : 'Département ajouté',
                data.message,
                'success'
            );

            closeModal('departement-modal');
            await loadDepartements();
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Erreur sauvegarde département:', error);
        showToast('Erreur', error.message, 'error');
    }
}

// =============================================
// GESTION DES ACTIONS (BLOQUER/DÉBLOQUER/SUPPRIMER)
// =============================================
async function openBlockModal(type, id) {
    if (confirm('Êtes-vous sûr de vouloir bloquer cet utilisateur ?')) {
        try {
            const response = await fetch(API_ROUTES.users.block(id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                showToast('Utilisateur bloqué', data.message, 'warning');
                await loadUsers();
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Erreur blocage utilisateur:', error);
            showToast('Erreur', error.message, 'error');
        }
    }
}

async function openUnblockModal(type, id) {
    if (confirm('Êtes-vous sûr de vouloir débloquer cet utilisateur ?')) {
        try {
            const response = await fetch(API_ROUTES.users.unblock(id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                showToast('Utilisateur débloqué', data.message, 'success');
                await loadUsers();
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Erreur déblocage utilisateur:', error);
            showToast('Erreur', error.message, 'error');
        }
    }
}

async function openDeleteModal(type, id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.')) {
        try {
            const route = type === 'departement' ?
                API_ROUTES.departements.destroy(id) :
                API_ROUTES.users.destroy(id);

            const response = await fetch(route, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                showToast('Suppression réussie', data.message, 'success');

                if (type === 'departement') {
                    await loadDepartements();
                } else {
                    await loadUsers();
                }
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Erreur suppression:', error);
            showToast('Erreur', error.message, 'error');
        }
    }
}

// =============================================
// FONCTIONS UTILITAIRES
// =============================================
function getRoleIdForChef() {
    const chefRole = currentData.roles?.find(role =>
        role.nom_role.toLowerCase().includes('chef') ||
        role.nom_role.toLowerCase().includes('manager')
    );
    return chefRole ? chefRole.id_role : null;
}

function updateFormSelects() {
    // Mettre à jour les selects des départements
    const departementSelects = document.querySelectorAll('select[id$="departement"]');
    departementSelects.forEach(select => {
        select.innerHTML = '<option value="">Sélectionner un département</option>';
        if (currentData.departements) {
            currentData.departements.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.id_departement;
                option.textContent = dept.nom_departement;
                select.appendChild(option);
            });
        }
    });

    // Mettre à jour les selects des rôles
    const roleSelects = document.querySelectorAll('select[id$="role"]');
    roleSelects.forEach(select => {
        select.innerHTML = '<option value="">Sélectionner un rôle</option>';
        if (currentData.roles) {
            currentData.roles.forEach(role => {
                const option = document.createElement('option');
                option.value = role.id_role;
                option.textContent = role.nom_role;
                select.appendChild(option);
            });
        }
    });

    // Mettre à jour les selects des chefs pour les départements
    const chefSelect = document.getElementById('departement-chef');
    if (chefSelect) {
        chefSelect.innerHTML = '<option value="">Sélectionner un chef</option>';
        if (currentData.users) {
            const chefs = currentData.users.filter(user =>
                user.role && (
                    user.role.nom_role.toLowerCase().includes('chef') ||
                    user.role.nom_role.toLowerCase().includes('manager')
                )
            );
            chefs.forEach(chef => {
                const option = document.createElement('option');
                option.value = chef.id_user;
                option.textContent = `${chef.prenom} ${chef.nom}`;
                chefSelect.appendChild(option);
            });
        }
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
    }

    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            showTab(tabName);
        });
    });

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

    // Génération automatique de matricule
    const roleSelects = document.querySelectorAll('select[id$="role"]');
    roleSelects.forEach(select => {
        select.addEventListener('change', async function() {
            if (!currentEditId) {
                const matriculeField = this.closest('form').querySelector('input[id$="matricule"]');
                if (matriculeField && this.value) {
                    const matricule = await generateMatricule(this.value);
                    if (matricule) {
                        matriculeField.value = matricule;
                    }
                }
            }
        });
    });
}

// =============================================
// GÉNÉRATION AUTOMATIQUE DE MATRICULE
// =============================================
async function generateMatricule(roleId) {
    try {
        const response = await fetch(API_ROUTES.users.generateMatricule, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ role_id: roleId })
        });

        const data = await response.json();

        if (data.success) {
            return data.matricule;
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Erreur génération matricule:', error);
        return null;
    }
}

// =============================================
// GESTION DES MODALS (OUVERTURE/FERMETURE)
// =============================================
function openAddModal(type) {
    const modal = document.getElementById(`${type}-modal`);
    const title = document.getElementById(`${type}-modal-title`);

    if (!modal || !title) return;

    title.textContent = `Ajouter un${type === 'employe' ? ' ' : type === 'chef' ? ' chef de ' : ' '}${getTypeLabel(type)}`;

    // Réinitialiser le formulaire
    const form = document.getElementById(`${type}-form`);
    if (form) form.reset();

    // Réinitialiser l'ID d'édition
    currentEditId = null;

    // Pour les employés, définir la date d'embauche à aujourd'hui
    if (type === 'employe') {
        const dateEmbaucheField = document.getElementById('employe-date-embauche');
        if (dateEmbaucheField) {
            const today = new Date().toISOString().split('T')[0];
            dateEmbaucheField.value = today;
        }
    }

    // Pour les chefs, définir la date de nomination à aujourd'hui
    if (type === 'chef') {
        const dateNominationField = document.getElementById('chef-date-nomination');
        if (dateNominationField) {
            const today = new Date().toISOString().split('T')[0];
            dateNominationField.value = today;
        }
    }

    // Afficher le modal
    modal.classList.remove('hidden');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.classList.add('hidden');

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
// FONCTIONS DE BASE (THÈME, DATE, etc.)
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

// =============================================
// FONCTIONS D'AFFICHAGE (TOAST, LOADING)
// =============================================
function showToast(title, message, type = 'success') {
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

function showLoading(message = 'Chargement...') {
    let loading = document.getElementById('loading-overlay');
    if (!loading) {
        loading = document.createElement('div');
        loading.id = 'loading-overlay';
        loading.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        `;
        loading.innerHTML = `
            <div style="background: white; padding: 20px; border-radius: 8px; display: flex; align-items: center; gap: 12px;">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(loading);
    }
}

function hideLoading() {
    const loading = document.getElementById('loading-overlay');
    if (loading) {
        loading.remove();
    }
}
