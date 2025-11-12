// =============================================
// VARIABLES GLOBALES
// =============================================
let currentDeleteId = null;
let currentDeleteType = null;
let currentEditId = null;
let employeCounter = 4;
let chefCounter = 2;

// =============================================
// INITIALISATION AU CHARGEMENT DE LA PAGE
// =============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation de la page Administration...');
    updateCurrentDate();
    initTheme();
    initTabs();
    initEventListeners();
    loadInitialData();
    console.log('✅ Page Administration initialisée avec succès');
});

// =============================================
// CHARGEMENT DES DONNÉES INITIALES
// =============================================
function loadInitialData() {
    // Charger les employés
    fetch('/api/users?role=employe')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderEmployes(data.users);
            }
        })
        .catch(error => console.error('Erreur chargement employés:', error));

    // Charger les chefs
    fetch('/api/users?role=chef')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderChefs(data.users);
            }
        })
        .catch(error => console.error('Erreur chargement chefs:', error));

    // Charger les départements
    fetch('/api/departements')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderDepartements(data.departements);
            }
        })
        .catch(error => console.error('Erreur chargement départements:', error));

    // Remplir les selects des modals
    fillModalSelects();
}

// =============================================
// REMPLIR LES SELECTS DES MODALS
// =============================================
function fillModalSelects() {
    // Remplir les rôles
    const employeRoleSelect = document.getElementById('employe-role');
    const chefRoleSelect = document.getElementById('chef-role');
    if (employeRoleSelect) {
        employeRoleSelect.innerHTML = '<option value="">Sélectionner un rôle</option>';
        window.laravelData.roles.forEach(role => {
            const option = document.createElement('option');
            option.value = role.id_role;
            option.textContent = role.nom_role;
            employeRoleSelect.appendChild(option);
        });
    }
    if (chefRoleSelect) {
        chefRoleSelect.innerHTML = '<option value="">Sélectionner un rôle</option>';
        window.laravelData.roles.forEach(role => {
            if (role.nom_role.toLowerCase().includes('chef')) {
                const option = document.createElement('option');
                option.value = role.id_role;
                option.textContent = role.nom_role;
                chefRoleSelect.appendChild(option);
            }
        });
    }

    // Remplir les départements
    const employeDepartementSelect = document.getElementById('employe-departement');
    const chefDepartementSelect = document.getElementById('chef-departement');
    if (employeDepartementSelect) {
        employeDepartementSelect.innerHTML = '<option value="">Sélectionner un département</option>';
        window.laravelData.departements.forEach(departement => {
            const option = document.createElement('option');
            option.value = departement.id_departement;
            option.textContent = departement.nom_departement;
            employeDepartementSelect.appendChild(option);
        });
    }
    if (chefDepartementSelect) {
        chefDepartementSelect.innerHTML = '<option value="">Sélectionner un département</option>';
        window.laravelData.departements.forEach(departement => {
            const option = document.createElement('option');
            option.value = departement.id_departement;
            option.textContent = departement.nom_departement;
            chefDepartementSelect.appendChild(option);
        });
    }

    // Remplir les chefs pour le modal département
    const departementChefSelect = document.getElementById('departement-chef');
    if (departementChefSelect) {
        departementChefSelect.innerHTML = '<option value="">Sélectionner un chef</option>';
        window.laravelData.allChefs.forEach(chef => {
            const option = document.createElement('option');
            option.value = chef.id_user;
            option.textContent = `${chef.nom} ${chef.prenom}`;
            departementChefSelect.appendChild(option);
        });
    }
}

// =============================================
// RENDU DES DONNÉES DANS LES TABLEAUX
// =============================================
function renderEmployes(employes) {
    const tbody = document.getElementById('employes-table-body');
    tbody.innerHTML = '';
    employes.forEach(employe => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${employe.matricule}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${employe.nom} ${employe.prenom}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${employe.email}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${employe.departement ? employe.departement.nom_departement : 'N/A'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${employe.profession}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${employe.actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${employe.actif ? 'Actif' : 'Inactif'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button title="Voir détails" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" onclick="openViewModal('employe', ${employe.id_user})">
                    <i class="fas fa-eye"></i>
                </button>
                <button title="Modifier" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" onclick="openEditModal('employe', ${employe.id_user})">
                    <i class="fas fa-edit"></i>
                </button>
                <button title="${employe.actif ? 'Bloquer' : 'Débloquer'}" class="text-${employe.actif ? 'red' : 'green'}-600 hover:text-${employe.actif ? 'red' : 'green'}-900 dark:text-${employe.actif ? 'red' : 'green'}-400 dark:hover:text-${employe.actif ? 'red' : 'green'}-300 mr-3" onclick="${employe.actif ? 'blockUser' : 'unblockUser'}(${employe.id_user})">
                    <i class="fas fa-${employe.actif ? 'lock' : 'unlock'}"></i>
                </button>
                <button title="Supprimer" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300" onclick="openDeleteModal('employe', ${employe.id_user})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function renderChefs(chefs) {
    const tbody = document.getElementById('chefs-table-body');
    tbody.innerHTML = '';
    chefs.forEach(chef => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${chef.matricule}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${chef.nom} ${chef.prenom}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${chef.email}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${chef.departement ? chef.departement.nom_departement : 'N/A'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${new Date(chef.date_embauche).toLocaleDateString('fr-FR')}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button title="Voir détails" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" onclick="openViewModal('chef', ${chef.id_user})">
                    <i class="fas fa-eye"></i>
                </button>
                <button title="Modifier" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" onclick="openEditModal('chef', ${chef.id_user})">
                    <i class="fas fa-edit"></i>
                </button>
                <button title="${chef.actif ? 'Bloquer' : 'Débloquer'}" class="text-${chef.actif ? 'red' : 'green'}-600 hover:text-${chef.actif ? 'red' : 'green'}-900 dark:text-${chef.actif ? 'red' : 'green'}-400 dark:hover:text-${chef.actif ? 'red' : 'green'}-300 mr-3" onclick="${chef.actif ? 'blockUser' : 'unblockUser'}(${chef.id_user})">
                    <i class="fas fa-${chef.actif ? 'lock' : 'unlock'}"></i>
                </button>
                <button title="Supprimer" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300" onclick="openDeleteModal('chef', ${chef.id_user})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function renderDepartements(departements) {
    const tbody = document.getElementById('departements-table-body');
    tbody.innerHTML = '';
    departements.forEach(departement => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${departement.id_departement}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${departement.nom_departement}</td>
            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">${departement.description}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${departement.chefDepartement ? `${departement.chefDepartement.nom} ${departement.chefDepartement.prenom}` : 'N/A'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${departement.employes_count}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button title="Voir détails" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" onclick="openViewModal('departement', ${departement.id_departement})">
                    <i class="fas fa-eye"></i>
                </button>
                <button title="Modifier" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" onclick="openEditModal('departement', ${departement.id_departement})">
                    <i class="fas fa-edit"></i>
                </button>
                <button title="Supprimer" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300" onclick="openDeleteModal('departement', ${departement.id_departement})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
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
    // Générer un matricule
    if (type === 'employe' || type === 'chef') {
        fetch('/api/users/generate-matricule', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ role_id: type === 'employe' ? 2 : 3 })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`${type}-matricule`).value = data.matricule;
            }
        });
    }
    // Afficher le modal
    modal.classList.remove('hidden');
}

function openEditModal(type, id) {
    const modal = document.getElementById(`${type}-modal`);
    const title = document.getElementById(`${type}-modal-title`);
    if (!modal || !title) return;
    title.textContent = `Modifier ${getTypeLabel(type)}`;
    currentEditId = id;
    // Charger les données depuis le backend
    const url = type === 'departement' ? `/api/departements/${id}` : `/api/users/${id}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = type === 'departement' ? data.departement : data.user;
                // Remplir le formulaire
                if (type === 'employe') {
                    document.getElementById('employe-matricule').value = item.matricule;
                    document.getElementById('employe-nom').value = item.nom;
                    document.getElementById('employe-prenom').value = item.prenom;
                    document.getElementById('employe-contact').value = item.telephone;
                    document.getElementById('employe-email').value = item.email;
                    document.getElementById('employe-role').value = item.role_id;
                    document.getElementById('employe-poste').value = item.profession;
                    document.getElementById('employe-departement').value = item.departement_id;
                } else if (type === 'chef') {
                    document.getElementById('chef-matricule').value = item.matricule;
                    document.getElementById('chef-nom').value = item.nom;
                    document.getElementById('chef-prenom').value = item.prenom;
                    document.getElementById('chef-contact').value = item.telephone;
                    document.getElementById('chef-email').value = item.email;
                    document.getElementById('chef-role').value = item.role_id;
                    document.getElementById('chef-poste').value = item.profession;
                    document.getElementById('chef-departement').value = item.departement_id;
                    document.getElementById('chef-date-nomination').value = item.date_embauche.split('T')[0];
                } else if (type === 'departement') {
                    document.getElementById('departement-nom').value = item.nom_departement;
                    document.getElementById('departement-description').value = item.description;
                    document.getElementById('departement-chef').value = item.chef_departement_id;
                }
            }
        });
    // Afficher le modal
    modal.classList.remove('hidden');
}

function openViewModal(type, id) {
    const url = type === 'departement' ? `/api/departements/${id}` : `/api/users/${id}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = type === 'departement' ? data.departement : data.user;
                let modal, title, content;
                if (type === 'employe') {
                    modal = document.getElementById('employe');
                    title = 'Détails de l’employé';
                    content = `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom complet</label>
                            <input type="text" value="${item.nom} ${item.prenom}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                            <input type="email" value="${item.email}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Téléphone</label>
                            <input type="text" value="${item.telephone}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Département</label>
                            <input type="text" value="${item.departement ? item.departement.nom_departement : 'N/A'}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                        </div>
                    `;
                } else if (type === 'chef') {
                    modal = document.getElementById('chef-departement');
                    title = 'Détails du chef de département';
                    content = `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom complet</label>
                            <input type="text" value="${item.nom} ${item.prenom}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                            <input type="email" value="${item.email}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Téléphone</label>
                            <input type="text" value="${item.telephone}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Département dirigé</label>
                            <input type="text" value="${item.departement ? item.departement.nom_departement : 'N/A'}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                        </div>
                    `;
                } else if (type === 'departement') {
                    modal = document.getElementById('departement-detail');
                    title = 'Détails du département';
                    content = `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom du département</label>
                            <input type="text" value="${item.nom_departement}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" readonly>${item.description}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Chef de département</label>
                            <input type="text" value="${item.chefDepartement ? `${item.chefDepartement.nom} ${item.chefDepartement.prenom}` : 'N/A'}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                        </div>
                    `;
                }
                // Mettre à jour le modal
                const modalTitle = modal.querySelector('h3');
                const modalContent = modal.querySelector('form');
                if (modalTitle) modalTitle.textContent = title;
                if (modalContent) modalContent.innerHTML = content;
                // Afficher le modal
                modal.classList.remove('hidden');
            }
        });
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
// GESTION DES FORMULAIRES
// =============================================
function handleEmployeSubmit(e) {
    e.preventDefault();
    const formData = {
        nom: document.getElementById('employe-nom').value,
        prenom: document.getElementById('employe-prenom').value,
        email: document.getElementById('employe-email').value,
        telephone: document.getElementById('employe-contact').value,
        profession: document.getElementById('employe-poste').value,
        matricule: document.getElementById('employe-matricule').value,
        date_embauche: new Date().toISOString().split('T')[0],
        role_id: document.getElementById('employe-role').value,
        departement_id: document.getElementById('employe-departement').value,
        solde_conges_annuel: 30,
    };
    const url = currentEditId ? `/api/users/${currentEditId}` : '/api/users';
    const method = currentEditId ? 'PUT' : 'POST';
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(currentEditId ? 'Employé modifié' : 'Employé ajouté', data.message, 'success');
            loadInitialData();
            closeModal('employe-modal');
        } else {
            showToast('Erreur', data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Erreur', 'Une erreur est survenue', 'error');
    });
}

function handleChefSubmit(e) {
    e.preventDefault();
    const formData = {
        nom: document.getElementById('chef-nom').value,
        prenom: document.getElementById('chef-prenom').value,
        email: document.getElementById('chef-email').value,
        telephone: document.getElementById('chef-contact').value,
        profession: document.getElementById('chef-poste').value,
        matricule: document.getElementById('chef-matricule').value,
        date_embauche: document.getElementById('chef-date-nomination').value,
        role_id: document.getElementById('chef-role').value,
        departement_id: document.getElementById('chef-departement').value,
        solde_conges_annuel: 30,
    };
    const url = currentEditId ? `/api/users/${currentEditId}` : '/api/users';
    const method = currentEditId ? 'PUT' : 'POST';
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(currentEditId ? 'Chef modifié' : 'Chef ajouté', data.message, 'success');
            loadInitialData();
            closeModal('chef-modal');
        } else {
            showToast('Erreur', data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Erreur', 'Une erreur est survenue', 'error');
    });
}

function handleDepartementSubmit(e) {
    e.preventDefault();
    const formData = {
        nom_departement: document.getElementById('departement-nom').value,
        description: document.getElementById('departement-description').value,
        chef_departement_id: document.getElementById('departement-chef').value,
    };
    const url = currentEditId ? `/api/departements/${currentEditId}` : '/api/departements';
    const method = currentEditId ? 'PUT' : 'POST';
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(currentEditId ? 'Département modifié' : 'Département ajouté', data.message, 'success');
            loadInitialData();
            closeModal('departement-modal');
        } else {
            showToast('Erreur', data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Erreur', 'Une erreur est survenue', 'error');
    });
}

// =============================================
// GESTION DES ACTIONS (BLOQUER, SUPPRIMER)
// =============================================
function blockUser(id) {
    fetch(`/api/users/${id}/block`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Utilisateur bloqué', data.message, 'warning');
            loadInitialData();
        } else {
            showToast('Erreur', data.message, 'error');
        }
    });
}

function unblockUser(id) {
    fetch(`/api/users/${id}/unblock`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Utilisateur débloqué', data.message, 'success');
            loadInitialData();
        } else {
            showToast('Erreur', data.message, 'error');
        }
    });
}

function confirmDelete() {
    if (!currentDeleteId || !currentDeleteType) return;
    const url = currentDeleteType === 'departement'
        ? `/api/departements/${currentDeleteId}`
        : `/api/users/${currentDeleteId}`;
    fetch(url, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`${getTypeLabel(currentDeleteType)} supprimé`, data.message, 'success');
            loadInitialData();
            closeModal('delete-confirm-modal');
        } else {
            showToast('Erreur', data.message, 'error');
        }
    });
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
    setTimeout(() => {
        showTab('employes');
    }, 0);
}

// =============================================
// INITIALISATION DES ÉCOUTEURS D'ÉVÉNEMENTS
// =============================================
function initEventListeners() {
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
}

// =============================================
// GESTION DU THÈME
// =============================================
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
