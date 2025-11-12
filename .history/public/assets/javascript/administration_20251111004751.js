// =============================================
// MISES À JOUR POUR administration.js
// =============================================

// =============================================
// GESTION DES ONGLETS
// =============================================

/**
 * Réinitialiser la pagination lors du changement d'onglet
 */
function resetPagination() {
    currentPage = 1;
}

/**
 * Initialiser la gestion des onglets (AJOUTER À L'INIT)
 */
function initTabsWithPagination() {
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');

            // Réinitialiser la pagination
            resetPagination();

            // Afficher l'onglet
            showTab(tabName);

            // Recharger les données si nécessaire
            if (tabName === 'employes' || tabName === 'chefs') {
                renderUsers();
            } else if (tabName === 'departements') {
                renderDepartements();
            }
        });
    });

    // Afficher l'onglet par défaut
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

/**
 * Afficher les départements (VERSION MISE À JOUR)
 */
function renderDepartements() {
    const tbody = document.getElementById('departements-tbody');
    if (!tbody) return;

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
                <button title="Modifier" class="edit-dept-btn text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                    <i class="fas fa-edit"></i>
                </button>
                <button title="Supprimer" class="delete-dept-btn text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');

    attachDepartementActions();
}

/**
 * Créer une ligne de tableau pour un employé (VERSION MISE À JOUR)
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

/**
 * Créer une ligne de tableau pour un chef de département (VERSION MISE À JOUR)
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
                <button title="Modifier" class="edit-btn text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                    <i class="fas fa-edit"></i>
                </button>
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

/**
 * Voir les détails d'un utilisateur (VERSION MISE À JOUR)
 */
async function viewUser(userId) {
    try {
        showLoader();
        const response = await fetch(`${API_BASE_URL}/users/${userId}`, {
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Erreur chargement utilisateur');

        const data = await response.json();
        const user = data.user;

        // Afficher le modal de détails
        const modal = document.getElementById('user-details-modal');
        const content = document.getElementById('user-details-content');

        if (!modal || !content) return;

        const statusClass = user.actif
            ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300'
            : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300';
        const statusText = user.actif ? 'Actif' : 'Inactif';

        content.innerHTML = `
            <div class="space-y-6">
                <!-- En-tête avec photo/avatar -->
                <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-lg">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                        ${user.prenom.charAt(0)}${user.nom.charAt(0)}
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white">${user.prenom} ${user.nom}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">${user.role?.nom_role || 'Aucun rôle'}</p>
                        <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">${statusText}</span>
                    </div>
                </div>

                <!-- Informations personnelles -->
                <div>
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-500"></i>
                        Informations personnelles
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Matricule</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${user.matricule}</p>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Email</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${user.email}</p>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Téléphone</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${user.telephone || 'Non renseigné'}</p>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Profession</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${user.profession || 'Non renseignée'}</p>
                        </div>
                    </div>
                </div>

                <!-- Informations professionnelles -->
                <div>
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                        <i class="fas fa-briefcase mr-2 text-purple-500"></i>
                        Informations professionnelles
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Département</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${user.departement?.nom_departement || 'Aucun département'}</p>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Date d'embauche</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${formatDate(user.date_embauche)}</p>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Solde de congés</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${user.solde_conges_annuel || 0} jours</p>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Congés pris</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${user.conges_pris || 0} jours</p>
                        </div>
                    </div>
                </div>

                <!-- Dates système -->
                <div>
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                        <i class="fas fa-clock mr-2 text-gray-500"></i>
                        Informations système
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Compte créé le</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${formatDateTime(user.created_at)}</p>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Dernière modification</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${formatDateTime(user.updated_at)}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        showModal(modal);

    } catch (error) {
        console.error('Erreur viewUser:', error);
        showToast('Erreur', 'Impossible de charger les détails', 'error');
    } finally {
        hideLoader();
    }
}

/**
 * Ouvrir le modal de modification d'un département (VERSION MISE À JOUR)
 */
async function editDepartement(deptId) {
    const dept = allDepartements.find(d => d.id_departement == deptId);
    if (!dept) return;

    const modal = document.getElementById('departement-modal');
    const title = document.getElementById('departement-modal-title');
    const form = document.getElementById('departement-form');

    if (!modal || !title || !form) return;

    title.textContent = 'Modifier le département';
    currentEditId = deptId;

    await loadDepartementFormDropdowns();

    document.getElementById('departement-nom').value = dept.nom_departement;
    document.getElementById('departement-description').value = dept.description || '';
    document.getElementById('departement-chef').value = dept.chef_departement_id || '';
    document.getElementById('departement-couleur').value = dept.couleur_calendrier || '#3b82f6';

    // Mettre à jour l'aperçu de la couleur
    const couleurPreview = document.getElementById('couleur-preview');
    if (couleurPreview) {
        couleurPreview.textContent = dept.couleur_calendrier || '#3b82f6';
    }

    showModal(modal);
}

/**
 * Gérer la soumission du formulaire département (VERSION MISE À JOUR)
 */
async function handleDepartementSubmit(e) {
    e.preventDefault();

    const formData = {
        nom_departement: document.getElementById('departement-nom').value.trim(),
        description: document.getElementById('departement-description').value.trim() || null,
        chef_departement_id: document.getElementById('departement-chef').value || null,
        couleur_calendrier: document.getElementById('departement-couleur').value || '#3b82f6'
    };

    if (!formData.nom_departement) {
        showToast('Erreur', 'Le nom du département est obligatoire', 'error');
        return;
    }

    try {
        showLoader();
        const url = currentEditId
            ? `${API_BASE_URL}/departements/${currentEditId}`
            : `${API_BASE_URL}/departements`;

        const method = currentEditId ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
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
            throw new Error(data.message || 'Erreur lors de la sauvegarde');
        }

        showToast('Succès', data.message, 'success');
        closeModal('departement-modal');
        await loadDepartements();
    } catch (error) {
        console.error('Erreur handleDepartementSubmit:', error);
        showToast('Erreur', error.message, 'error');
    } finally {
        hideLoader();
    }
}

/**
 * Formater une date et heure
 */
function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR') + ' à ' + date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
}

// =============================================
// PAGINATION AMÉLIORÉE
// =============================================

/**
 * Afficher les utilisateurs filtrés par rôle (VERSION MISE À JOUR AVEC PAGINATION)
 */
function renderUsers() {
    const employesTbody = document.getElementById('employes-tbody');
    const chefsTbody = document.getElementById('chefs-tbody');

    if (!employesTbody || !chefsTbody) return;

    // Filtrer uniquement les employés (exclure Admin et Chef de Département)
    const employes = allUsers.filter(u => {
        const roleName = u.role?.nom_role;
        return roleName && roleName.toLowerCase() === 'employé';
    });

    // Filtrer uniquement les chefs de département
    const chefs = allUsers.filter(u => {
        const roleName = u.role?.nom_role;
        return roleName && (roleName === 'Chef de Département' || roleName === 'Chef de département' || roleName.toLowerCase() === 'chef de departement');
    });

    console.log('Employés filtrés:', employes.length);
    console.log('Chefs filtrés:', chefs.length);

    // Pagination pour employés
    const employesPaginated = paginate(employes, currentPage, itemsPerPage);

    // Rendre les employés
    employesTbody.innerHTML = employesPaginated.length === 0
        ? '<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucun employé trouvé</td></tr>'
        : employesPaginated.map(user => createUserRow(user, 'employe')).join('');

    // Rendre les chefs (sans pagination pour l'instant)
    chefsTbody.innerHTML = chefs.length === 0
        ? '<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucun chef de département trouvé</td></tr>'
        : chefs.map(user => createChefRow(user)).join('');

    // Afficher la pagination uniquement si nécessaire
    renderPagination(employes.length, 'employes');

    // Réattacher les événements
    attachUserActions();
}

/**
 * Afficher la pagination (VERSION MISE À JOUR)
 */
function renderPagination(totalItems, type) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const paginationContainer = document.getElementById(`${type}-pagination`);

    if (!paginationContainer) return;

    // Ne rien afficher si une seule page ou moins
    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }

    let html = '<div class="flex items-center justify-between">';

    // Info sur les résultats
    const start = (currentPage - 1) * itemsPerPage + 1;
    const end = Math.min(currentPage * itemsPerPage, totalItems);
    html += `
        <div class="text-sm text-gray-700 dark:text-gray-300">
            Affichage de <span class="font-medium">${start}</span> à <span class="font-medium">${end}</span> sur <span class="font-medium">${totalItems}</span> résultats
        </div>
    `;

    // Boutons de pagination
    html += '<div class="flex items-center space-x-2">';

    // Bouton Précédent
    html += `<button onclick="changePage(${currentPage - 1}, '${type}')"
        class="px-3 py-2 rounded-lg transition-colors ${currentPage === 1 ? 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed' : 'bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'}"
        ${currentPage === 1 ? 'disabled' : ''}>
        <i class="fas fa-chevron-left"></i>
    </button>`;

    // Numéros de pages
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            html += `<button onclick="changePage(${i}, '${type}')"
                class="px-4 py-2 rounded-lg transition-colors ${i === currentPage ? 'bg-gradient-to-r from-blue-500 to-purple-500 text-white shadow-lg' : 'bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'}">
                ${i}
            </button>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            html += '<span class="px-2 text-gray-500">...</span>';
        }
    }

    // Bouton Suivant
    html += `<button onclick="changePage(${currentPage + 1}, '${type}')"
        class="px-3 py-2 rounded-lg transition-colors ${currentPage === totalPages ? 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed' : 'bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'}"
        ${currentPage === totalPages ? 'disabled' : ''}>
        <i class="fas fa-chevron-right"></i>
    </button>`;

    html += '</div></div>';
    paginationContainer.innerHTML = html;
}

/**
 * Changer de page (VERSION MISE À JOUR)
 */
function changePage(page, type) {
    const totalItems = type === 'employes'
        ? allUsers.filter(u => u.role?.nom_role?.toLowerCase() === 'employé').length
        : 0;
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    if (page < 1 || page > totalPages) return;

    currentPage = page;
    renderUsers();

    // Scroll vers le haut du tableau
    const tab = document.getElementById(`${type}-tab`);
    if (tab) {
        tab.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// =============================================
// GESTION COMPLÈTE DES MODALS D'ÉDITION
// =============================================

/**
 * Ouvrir le modal d'ajout (VERSION COMPLÈTE)
 */
async function openAddModal(type) {
    const modal = document.getElementById(`${type}-modal`);
    const title = document.getElementById(`${type}-modal-title`);
    const form = document.getElementById(`${type}-form`);

    if (!modal || !title || !form) {
        console.error(`Modal ${type} introuvable`);
        return;
    }

    // Définir le titre selon le type
    title.textContent = type === 'employe' ? 'Ajouter un employé'
        : type === 'chef' ? 'Ajouter un chef de département'
        : 'Ajouter un département';

    // Réinitialiser
    currentEditId = null;
    form.reset();

    if (type === 'employe' || type === 'chef') {
        await loadFormDropdowns(type);

        // Date d'embauche par défaut
        const dateInput = document.getElementById(`${type}-date-embauche`);
        if (dateInput) {
            dateInput.value = new Date().toISOString().split('T')[0];
        }

        // Solde de congés par défaut
        const soldeInput = document.getElementById(`${type}-solde-conges`);
        if (soldeInput) {
            soldeInput.value = 30;
        }

        if (type === 'chef') {
            // Trouver le rôle "Chef de Département"
            const chefRole = allRoles.find(r =>
                r.nom_role === 'Chef de Département' ||
                r.nom_role === 'Chef de département' ||
                r.nom_role.toLowerCase() === 'chef de departement'
            );
            if (chefRole) {
                document.getElementById('chef-role').value = chefRole.id_role;
                await generateMatricule('chef', chefRole.id_role);
            }
        }
    } else if (type === 'departement') {
        await loadDepartementFormDropdowns();

        // Couleur par défaut
        const couleurInput = document.getElementById('departement-couleur');
        const couleurPreview = document.getElementById('couleur-preview');
        if (couleurInput && couleurPreview) {
            couleurInput.value = '#3b82f6';
            couleurPreview.textContent = '#3b82f6';
        }
    }

    showModal(modal);
}

/**
 * Ouvrir le modal de modification d'un utilisateur (VERSION COMPLÈTE)
 */
async function editUser(userId) {
    const user = allUsers.find(u => u.id_user == userId);
    if (!user) {
        showToast('Erreur', 'Utilisateur non trouvé', 'error');
        return;
    }

    const isChef = user.role?.nom_role === 'Chef de Département' ||
                   user.role?.nom_role === 'Chef de département' ||
                   user.role?.nom_role.toLowerCase() === 'chef de departement';
    const type = isChef ? 'chef' : 'employe';
    const modal = document.getElementById(`${type}-modal`);
    const title = document.getElementById(`${type}-modal-title`);
    const form = document.getElementById(`${type}-form`);

    if (!modal || !title || !form) {
        showToast('Erreur', 'Modal introuvable', 'error');
        return;
    }

    title.textContent = isChef ? 'Modifier le chef de département' : 'Modifier l\'employé';
    currentEditId = userId;

    // Charger les options des dropdowns
    await loadFormDropdowns(type);

    // Remplir le formulaire
    document.getElementById(`${type}-matricule`).value = user.matricule || '';
    document.getElementById(`${type}-nom`).value = user.nom || '';
    document.getElementById(`${type}-prenom`).value = user.prenom || '';
    document.getElementById(`${type}-email`).value = user.email || '';
    document.getElementById(`${type}-telephone`).value = user.telephone || '';
    document.getElementById(`${type}-profession`).value = user.profession || '';
    document.getElementById(`${type}-date-embauche`).value = user.date_embauche || '';
    document.getElementById(`${type}-solde-conges`).value = user.solde_conges_annuel || 30;

    // Gérer le rôle
    if (type === 'chef') {
        document.getElementById('chef-role').value = user.role_id || '';
    } else {
        const roleSelect = document.getElementById('employe-role');
        if (roleSelect) {
            roleSelect.value = user.role_id || '';
        }
    }

    // Gérer le département
    const deptSelect = document.getElementById(`${type}-departement`);
    if (deptSelect) {
        deptSelect.value = user.departement_id || '';
    }

    showModal(modal);
}

/**
 * Ouvrir le modal de modification d'un département (VERSION COMPLÈTE)
 */
async function editDepartement(deptId) {
    const dept = allDepartements.find(d => d.id_departement == deptId);
    if (!dept) {
        showToast('Erreur', 'Département non trouvé', 'error');
        return;
    }

    const modal = document.getElementById('departement-modal');
    const title = document.getElementById('departement-modal-title');
    const form = document.getElementById('departement-form');

    if (!modal || !title || !form) {
        showToast('Erreur', 'Modal introuvable', 'error');
        return;
    }

    title.textContent = 'Modifier le département';
    currentEditId = deptId;

    await loadDepartementFormDropdowns();

    document.getElementById('departement-nom').value = dept.nom_departement || '';
    document.getElementById('departement-description').value = dept.description || '';
    document.getElementById('departement-chef').value = dept.chef_departement_id || '';

    // Gérer la couleur
    const couleurInput = document.getElementById('departement-couleur');
    const couleurPreview = document.getElementById('couleur-preview');
    const couleur = dept.couleur_calendrier || '#3b82f6';

    if (couleurInput) couleurInput.value = couleur;
    if (couleurPreview) couleurPreview.textContent = couleur;

    showModal(modal);
}

/**
 * Charger les dropdowns pour les formulaires utilisateurs (VERSION COMPLÈTE)
 */
async function loadFormDropdowns(type) {
    const roleSelect = document.getElementById(`${type}-role`);
    if (roleSelect && type === 'employe') {
        // Pour les employés, afficher uniquement le rôle "Employé"
        const employeRole = allRoles.find(r => r.nom_role.toLowerCase() === 'employé');
        roleSelect.innerHTML = '<option value="">Sélectionner un rôle</option>';
        if (employeRole) {
            roleSelect.innerHTML += `<option value="${employeRole.id_role}">${employeRole.nom_role}</option>`;
        }
    }

    const deptSelect = document.getElementById(`${type}-departement`);
    if (deptSelect) {
        deptSelect.innerHTML = '<option value="">Sélectionner un département</option>' +
            allDepartements.map(dept => `<option value="${dept.id_departement}">${dept.nom_departement}</option>`).join('');
    }
}

/**
 * Charger les dropdowns pour le formulaire département (VERSION COMPLÈTE)
 */
async function loadDepartementFormDropdowns() {
    const chefSelect = document.getElementById('departement-chef');
    if (!chefSelect) return;

    // Filtrer uniquement les utilisateurs avec le rôle "Chef de Département"
    const chefs = allUsers.filter(u => {
        const roleName = u.role?.nom_role;
        return roleName && (
            roleName === 'Chef de Département' ||
            roleName === 'Chef de département' ||
            roleName.toLowerCase() === 'chef de departement'
        );
    });

    chefSelect.innerHTML = '<option value="">Sélectionner un chef (optionnel)</option>' +
        chefs.map(chef => `<option value="${chef.id_user}">${chef.prenom} ${chef.nom}</option>`).join('');
}

/**
 * Fermer un modal (VERSION AMÉLIORÉE)
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    const modalContent = modal.querySelector('.modal');
    if (modalContent) modalContent.classList.remove('open');

    setTimeout(() => {
        modal.classList.add('hidden');

        // Réinitialiser le formulaire si c'est un modal de formulaire
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }, 300);

    // Réinitialiser les variables globales
    if (modalId === 'delete-confirm-modal') {
        currentDeleteId = null;
        currentDeleteType = null;
    }

    if (modalId !== 'delete-confirm-modal' && modalId !== 'user-details-modal') {
        currentEditId = null;
    }
}

// =============================================
// REMPLACER CES FONCTIONS DANS administration.js
// =============================================
console.log('✅ Mises à jour JavaScript complètes chargées');
