// =============================================
// CONFIGURATION ET VARIABLES GLOBALES
// =============================================
const API_BASE_URL = '/admin/api/demandes';
let currentFilter = 'all';
let currentDepartmentFilter = 'all';
let currentEmployeeFilter = 'all';
let selectedRequests = new Set();
let currentRequestId = 0;
let currentAction = '';
let currentViewModeApproved = 'grid';
let currentViewModeRejected = 'grid';
let requestsData = [];
let departmentsData = [];
let employeesData = [];

// =============================================
// CHARGEMENT INITIAL DES DONNÉES
// =============================================
async function loadDepartments() {
    try {
        const response = await fetch(`${API_BASE_URL}/departements`);
        const data = await response.json();

        if (data.success) {
            departmentsData = data.departements;
            populateDepartmentFilter();
        }
    } catch (error) {
        console.error('Erreur lors du chargement des départements:', error);
        showToast('Erreur', 'Impossible de charger les départements', 'error');
    }
}

async function loadEmployees(departmentId = 'all') {
    try {
        const response = await fetch(`${API_BASE_URL}/departements/${departmentId}/employees`);
        const data = await response.json();

        if (data.success) {
            employeesData = data.employees;
            populateEmployeeFilter();
        }
    } catch (error) {
        console.error('Erreur lors du chargement des employés:', error);
        showToast('Erreur', 'Impossible de charger les employés', 'error');
    }
}

async function loadRequests() {
    try {
        showLoadingSpinner();

        const params = new URLSearchParams({
            filter: currentFilter,
            department_id: currentDepartmentFilter,
            employee_id: currentEmployeeFilter
        });

        const response = await fetch(`${API_BASE_URL}?${params}`);
        const data = await response.json();

        if (data.success) {
            requestsData = data.demandes;
            renderFilteredContent();
        } else {
            showToast('Erreur', data.message || 'Erreur lors du chargement', 'error');
        }
    } catch (error) {
        console.error('Erreur lors du chargement des demandes:', error);
        showToast('Erreur', 'Impossible de charger les demandes', 'error');
    } finally {
        hideLoadingSpinner();
    }
}

// =============================================
// POPULATION DES FILTRES
// =============================================
function populateDepartmentFilter() {
    const departmentFilter = document.getElementById('departmentFilter');
    if (!departmentFilter) return;

    // Vider le select (garder "Tous les départements")
    departmentFilter.innerHTML = '<option value="all">Tous les départements</option>';

    // Ajouter les départements
    departmentsData.forEach(dept => {
        const option = document.createElement('option');
        option.value = dept.id;
        option.textContent = dept.nom;
        departmentFilter.appendChild(option);
    });
}

function populateEmployeeFilter() {
    const employeeFilter = document.getElementById('employeeFilter');
    if (!employeeFilter) return;

    // Vider le select
    employeeFilter.innerHTML = '<option value="all">Tous les employés</option>';

    // Ajouter les employés
    employeesData.forEach(emp => {
        const option = document.createElement('option');
        option.value = emp.id;
        option.textContent = `${emp.prenom} ${emp.nom} (${emp.matricule})`;
        employeeFilter.appendChild(option);
    });
}

// =============================================
// INITIALISATION DES FILTRES
// =============================================
function setupFilterButtons() {
    const filterButtons = document.querySelectorAll('.filter-button');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            applyFilter(filter);
        });
    });
}

function initializeDepartmentFilter() {
    const departmentFilter = document.getElementById('departmentFilter');
    if (!departmentFilter) return;

    departmentFilter.addEventListener('change', async function() {
        currentDepartmentFilter = this.value;
        currentEmployeeFilter = 'all'; // Réinitialiser le filtre employé

        // Recharger les employés du département
        await loadEmployees(currentDepartmentFilter);

        // Recharger les demandes
        await loadRequests();
    });
}

function initializeEmployeeFilter() {
    const employeeFilter = document.getElementById('employeeFilter');
    if (!employeeFilter) return;

    employeeFilter.addEventListener('change', async function() {
        currentEmployeeFilter = this.value;
        await loadRequests();
    });
}

function applyFilter(filter) {
    currentFilter = filter;

    // Mettre à jour l'apparence des boutons
    const filterButtons = document.querySelectorAll('.filter-button');
    filterButtons.forEach(button => {
        if (button.getAttribute('data-filter') === filter) {
            button.className = 'filter-button active bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg px-6 py-3 font-medium rounded-xl transition-all duration-300 hover-lift click-scale';
        } else {
            button.className = 'filter-button bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-3 font-medium rounded-xl transition-all duration-300 hover-lift click-scale';
        }
    });

    // Recharger les demandes
    loadRequests();
}

// =============================================
// RENDU DU CONTENU
// =============================================
function renderFilteredContent() {
    const container = document.getElementById('dynamicContent');
    if (!container) return;

    let title = '';
    let subtitle = '';

    switch (currentFilter) {
        case 'all':
            title = 'Liste de toutes les demandes de congés';
            subtitle = getDynamicSubtitle();
            container.innerHTML = createPendingRequestsContent(requestsData, title, subtitle);
            break;
        case 'pending':
            title = 'Liste des congés en attente de traitement';
            subtitle = getDynamicSubtitle();
            container.innerHTML = createPendingRequestsContent(requestsData, title, subtitle);
            break;
        case 'approved':
            title = 'Liste des Congés Approuvés';
            subtitle = getDynamicSubtitle();
            container.innerHTML = createApprovedContent(requestsData, title, subtitle);
            break;
        case 'rejected':
            title = 'Liste des Congés Refusés';
            subtitle = getDynamicSubtitle();
            container.innerHTML = createRejectedContent(requestsData, title, subtitle);
            break;
    }

    // Réinitialiser la sélection
    selectedRequests.clear();

    // Réinitialiser les écouteurs d'événements
    setTimeout(() => {
        setupViewButtons();
        setupLeaveClickEvents();
    }, 100);
}

function getDynamicSubtitle() {
    let parts = [];

    // Département
    if (currentDepartmentFilter === 'all') {
        parts.push('tous les départements');
    } else {
        const dept = departmentsData.find(d => d.id == currentDepartmentFilter);
        parts.push(`département ${dept ? dept.nom : 'inconnu'}`);
    }

    // Employé
    if (currentEmployeeFilter !== 'all') {
        const emp = employeesData.find(e => e.id == currentEmployeeFilter);
        if (emp) {
            parts.push(`employé ${emp.prenom} ${emp.nom}`);
        }
    }

    return parts.join(' - ');
}

// =============================================
// CRÉATION DES CONTENUS HTML
// =============================================
function createPendingRequestsContent(requests, title, subtitle) {
    const hasPendingRequests = requests.some(req => req.status === 'pending');

    return `
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-4 gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">${title}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">${subtitle}</p>
            </div>
            ${hasPendingRequests ? `
                <div class="flex items-center space-x-3">
                    <button id="selectAllBtn" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors text-sm font-medium" onclick="toggleSelectAll()">
                        <i class="fas fa-check-square mr-2"></i>Tout sélectionner
                    </button>
                    <div id="bulkActionsContainer" class="hidden space-x-2">
                        <span id="selectedCount" class="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium">
                            0 sélectionné(s)
                        </span>
                        <button onclick="bulkApprove()" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors text-sm font-medium">
                            <i class="fas fa-check mr-2"></i>Approuver
                        </button>
                        <button onclick="bulkReject()" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors text-sm font-medium">
                            <i class="fas fa-times mr-2"></i>Refuser
                        </button>
                    </div>
                </div>
            ` : ''}
        </div>

        <div id="requestsContainer" class="space-y-4">
            ${requests.length === 0 ? `
                <div class="text-center py-12">
                    <i class="fas fa-calendar-check text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Aucune demande trouvée</p>
                </div>
            ` : requests.map(request => createRequestCard(request)).join('')}
        </div>
    `;
}

function createApprovedContent(requests, title, subtitle) {
    return `
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">${title}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">${subtitle}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button id="gridViewBtnApproved" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 text-sm font-medium">
                    <i class="fas fa-th"></i> Grille
                </button>
                <button id="listViewBtnApproved" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors duration-200 text-sm font-medium">
                    <i class="fas fa-list"></i> Liste
                </button>
            </div>
        </div>

        <div id="approvedLeavesContainer">
            ${requests.length === 0 ? `
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Aucun congé approuvé</p>
                </div>
            ` : renderApprovedLeavesView(requests)}
        </div>
    `;
}

function createRejectedContent(requests, title, subtitle) {
    return `
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">${title}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">${subtitle}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button id="gridViewBtnRejected" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 text-sm font-medium">
                    <i class="fas fa-th"></i> Grille
                </button>
                <button id="listViewBtnRejected" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors duration-200 text-sm font-medium">
                    <i class="fas fa-list"></i> Liste
                </button>
            </div>
        </div>

        <div id="rejectedLeavesContainer">
            ${requests.length === 0 ? `
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Aucun congé refusé</p>
                </div>
            ` : renderRejectedLeavesView(requests)}
        </div>
    `;
}

function createRequestCard(request) {
    const statusEmoji = request.status === 'pending' ? '🟡' : request.status === 'approved' ? '🟢' : '🔴';
    const statusClass = request.status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' :
                        request.status === 'approved' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                        'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300';
    const statusText = request.status === 'pending' ? 'En attente' : request.status === 'approved' ? 'Approuvée' : 'Refusée';

    const showActions = request.status === 'pending';
    const showRevalidate = request.status === 'rejected';
    const showCheckbox = request.status === 'pending';

    // Boutons pour les documents
    const hasDocument = request.document_justificatif && request.document_justificatif !== '';
    const documentButtons = hasDocument ? `
        <button onclick="event.stopPropagation(); openDocument(${request.id})"
                class="text-pink-500 hover:text-pink-700 p-2 rounded-lg hover:bg-pink-50 dark:hover:bg-pink-900/30 transition-colors"
                title="Voir le document">
            <i class="fas fa-eye"></i>
        </button>
        <button onclick="event.stopPropagation(); downloadDocument(${request.id})"
                class="text-blue-500 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                title="Télécharger le document">
            <i class="fas fa-download"></i>
        </button>
    ` : '';

    return `
        <div class="demand-item w-full flex flex-col md:flex-row items-start md:items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4" data-id="${request.id}">
            <div class="flex items-center space-x-4 w-full md:w-auto">
                ${showCheckbox ? `
                    <input type="checkbox"
                           class="request-checkbox w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer"
                           data-request-id="${request.id}"
                           onchange="updateBulkActions()">
                ` : ''}
                <div class="w-12 h-12 bg-gradient-to-r ${request.avatar} rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2">
                        <h4 class="font-semibold text-gray-900 dark:text-white truncate">${request.employeeName} - ${request.leaveType}</h4>
                        <span class="text-yellow-500">${statusEmoji}</span>
                        ${hasDocument ? '<i class="fas fa-paperclip text-blue-500" title="Document joint"></i>' : ''}
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 mr-2">
                            ${request.department}
                        </span>
                        ${formatDate(request.startDate)} - ${formatDate(request.endDate)} (${request.duration} jours)
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Demande soumise ${request.submittedTime}</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">
                <span class="status-badge px-4 py-2 ${statusClass} text-xs font-semibold rounded-full">${statusText}</span>

                ${documentButtons}

                ${showActions ? `
                    <button title="Approuver" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3" onclick="showConfirmModal('approve', ${request.id})">
                        <i class="fa fa-check"></i>
                    </button>
                    <button title="Rejeter" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 mr-3" onclick="showConfirmModal('reject', ${request.id})">
                        <i class="fas fa-times"></i>
                    </button>
                ` : ''}
                ${showRevalidate ? `
                    <button title="Revalider" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3" onclick="showConfirmModal('revalidate', ${request.id})">
                        <i class="fas fa-sync-alt mr-1"></i>Revalider
                    </button>
                ` : ''}
                <button title="Voir les détails" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3" onclick="showDetailsModal(${request.id})">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
    `;
}

// =============================================
// VUES GRILLE ET LISTE
// =============================================
function renderApprovedLeavesView(requests) {
    if (currentViewModeApproved === 'grid') {
        return `
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                ${requests.map(leave => createApprovedLeaveCardGrid(leave)).join('')}
            </div>
        `;
    } else {
        return `
            <div class="space-y-4">
                ${requests.map(leave => createApprovedLeaveCardList(leave)).join('')}
            </div>
        `;
    }
}

function renderRejectedLeavesView(requests) {
    if (currentViewModeRejected === 'grid') {
        return `
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                ${requests.map(leave => createRejectedLeaveCardGrid(leave)).join('')}
            </div>
        `;
    } else {
        return `
            <div class="space-y-4">
                ${requests.map(leave => createRejectedLeaveCardList(leave)).join('')}
            </div>
        `;
    }
}

// =============================================
// CARTES D'AFFICHAGE - VUE GRILLE APPROUVÉES
// =============================================
function createApprovedLeaveCardGrid(leave) {
    return `
        <div id="leave-approved-${leave.id}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 cursor-pointer border-l-4 border-green-500 leave-card" data-leave-id="${leave.id}">
            <div class="p-4">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-12 h-12 bg-gradient-to-r ${leave.avatar} rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900 dark:text-white truncate">${leave.employeeName}</h4>
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                            ${leave.department}
                        </span>
                    </div>
                </div>
                <div class="mb-2">
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                        ${leave.leaveType}
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-calendar-day w-5"></i>
                        <span>${formatDate(leave.startDate)}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-calendar-check w-5"></i>
                        <span>${formatDate(leave.endDate)}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <button class="revalidate-btn px-3 py-1 text-xs bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors" onclick="event.stopPropagation(); showConfirmModal('revalidate', ${leave.id})">
                            <i class="fas fa-sync-alt mr-1"></i>Revalider
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// =============================================
// CARTES D'AFFICHAGE - VUE LISTE APPROUVÉES
// =============================================
function createApprovedLeaveCardList(leave) {
    return `
        <div id="leave-approved-${leave.id}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 cursor-pointer border-l-4 border-green-500 leave-card" data-leave-id="${leave.id}">
            <div class="p-4">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-gradient-to-r ${leave.avatar} rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">${leave.employeeName}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                    ${leave.department}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                    ${leave.leaveType}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-6 text-sm">
                        <div class="text-center">
                            <div class="text-gray-500 dark:text-gray-400 text-xs">Début</div>
                            <div class="font-medium text-gray-900 dark:text-white">${formatDate(leave.startDate)}</div>
                        </div>
                        <div class="text-gray-400 dark:text-gray-600">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="text-center">
                            <div class="text-gray-500 dark:text-gray-400 text-xs">Fin</div>
                            <div class="font-medium text-gray-900 dark:text-white">${formatDate(leave.endDate)}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-gray-500 dark:text-gray-400 text-xs">Durée</div>
                            <div class="font-medium text-gray-900 dark:text-white">${leave.duration} jour(s)</div>
                        </div>
                        <div class="text-center">
                            <span class="px-3 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                Approuvé
                            </span>
                        </div>
                    </div>
                    <button class="revalidate-btn px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors" onclick="event.stopPropagation(); showConfirmModal('revalidate', ${leave.id})">
                        <i class="fas fa-sync-alt mr-1"></i>Revalider
                    </button>
                </div>
            </div>
        </div>
    `;
}

// =============================================
// CARTES D'AFFICHAGE - VUE GRILLE REFUSÉES
// =============================================
function createRejectedLeaveCardGrid(leave) {
    return `
        <div id="leave-rejected-${leave.id}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 cursor-pointer border-l-4 border-red-500 leave-card" data-leave-id="${leave.id}">
            <div class="p-4">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-12 h-12 bg-gradient-to-r ${leave.avatar} rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900 dark:text-white truncate">${leave.employeeName}</h4>
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                            ${leave.department}
                        </span>
                    </div>
                </div>
                <div class="mb-2">
                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                        ${leave.leaveType}
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-calendar-day w-5"></i>
                        <span>${formatDate(leave.startDate)}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-calendar-check w-5"></i>
                        <span>${formatDate(leave.endDate)}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-clock w-5"></i>
                        <span>${leave.duration} jour(s)</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                            Refusé
                        </span>
                        <button class="revalidate-btn px-3 py-1 text-xs bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors" onclick="event.stopPropagation(); showConfirmModal('revalidate', ${leave.id})">
                            <i class="fas fa-sync-alt mr-1"></i>Revalider
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// =============================================
// CARTES D'AFFICHAGE - VUE LISTE REFUSÉES
// =============================================
function createRejectedLeaveCardList(leave) {
    return `
        <div id="leave-rejected-${leave.id}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 cursor-pointer border-l-4 border-red-500 leave-card" data-leave-id="${leave.id}">
            <div class="p-4">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-gradient-to-r ${leave.avatar} rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">${leave.employeeName}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                    ${leave.department}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                    ${leave.leaveType}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-6 text-sm">
                        <div class="text-center">
                            <div class="text-gray-500 dark:text-gray-400 text-xs">Début</div>
                            <div class="font-medium text-gray-900 dark:text-white">${formatDate(leave.startDate)}</div>
                        </div>
                        <div class="text-gray-400 dark:text-gray-600">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="text-center">
                            <div class="text-gray-500 dark:text-gray-400 text-xs">Fin</div>
                            <div class="font-medium text-gray-900 dark:text-white">${formatDate(leave.endDate)}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-gray-500 dark:text-gray-400 text-xs">Durée</div>
                            <div class="font-medium text-gray-900 dark:text-white">${leave.duration} jour(s)</div>
                        </div>
                        <div class="text-center">
                            <span class="px-3 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                Refusé
                            </span>
                        </div>
                    </div>
                    <button class="revalidate-btn px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors" onclick="event.stopPropagation(); showConfirmModal('revalidate', ${leave.id})">
                        <i class="fas fa-sync-alt mr-1"></i>Revalider
                    </button>
                </div>
            </div>
        </div>
    `;
}

// =============================================
// GESTION DES BOUTONS DE VUE
// =============================================
function setupViewButtons() {
    const gridBtnApproved = document.getElementById('gridViewBtnApproved');
    const listBtnApproved = document.getElementById('listViewBtnApproved');

    if (gridBtnApproved && listBtnApproved) {
        gridBtnApproved.onclick = function() {
            currentViewModeApproved = 'grid';
            updateViewButtons('gridApproved');
            const container = document.getElementById('approvedLeavesContainer');
            if (container) {
                container.innerHTML = renderApprovedLeavesView(requestsData);
                setupLeaveClickEvents();
            }
        };

        listBtnApproved.onclick = function() {
            currentViewModeApproved = 'list';
            updateViewButtons('listApproved');
            const container = document.getElementById('approvedLeavesContainer');
            if (container) {
                container.innerHTML = renderApprovedLeavesView(requestsData);
                setupLeaveClickEvents();
            }
        };

        updateViewButtons(currentViewModeApproved === 'grid' ? 'gridApproved' : 'listApproved');
    }

    const gridBtnRejected = document.getElementById('gridViewBtnRejected');
    const listBtnRejected = document.getElementById('listViewBtnRejected');

    if (gridBtnRejected && listBtnRejected) {
        gridBtnRejected.onclick = function() {
            currentViewModeRejected = 'grid';
            updateViewButtons('gridRejected');
            const container = document.getElementById('rejectedLeavesContainer');
            if (container) {
                container.innerHTML = renderRejectedLeavesView(requestsData);
                setupLeaveClickEvents();
            }
        };

        listBtnRejected.onclick = function() {
            currentViewModeRejected = 'list';
            updateViewButtons('listRejected');
            const container = document.getElementById('rejectedLeavesContainer');
            if (container) {
                container.innerHTML = renderRejectedLeavesView(requestsData);
                setupLeaveClickEvents();
            }
        };

        updateViewButtons(currentViewModeRejected === 'grid' ? 'gridRejected' : 'listRejected');
    }
}

function updateViewButtons(activeView) {
    const gridBtnApproved = document.getElementById('gridViewBtnApproved');
    const listBtnApproved = document.getElementById('listViewBtnApproved');
    const gridBtnRejected = document.getElementById('gridViewBtnRejected');
    const listBtnRejected = document.getElementById('listViewBtnRejected');

    const activeClass = 'px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 text-sm font-medium';
    const inactiveClass = 'px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors duration-200 text-sm font-medium';

    if (activeView === 'gridApproved') {
        if (gridBtnApproved) gridBtnApproved.className = activeClass;
        if (listBtnApproved) listBtnApproved.className = inactiveClass;
    } else if (activeView === 'listApproved') {
        if (gridBtnApproved) gridBtnApproved.className = inactiveClass;
        if (listBtnApproved) listBtnApproved.className = activeClass;
    } else if (activeView === 'gridRejected') {
        if (gridBtnRejected) gridBtnRejected.className = activeClass;
        if (listBtnRejected) listBtnRejected.className = inactiveClass;
    } else if (activeView === 'listRejected') {
        if (gridBtnRejected) gridBtnRejected.className = inactiveClass;
        if (listBtnRejected) listBtnRejected.className = activeClass;
    }
}

// =============================================
// GESTION DES CLICS SUR LES CARTES
// =============================================
function setupLeaveClickEvents() {
    const leaveCards = document.querySelectorAll('.leave-card');

    leaveCards.forEach(card => {
        const newCard = card.cloneNode(true);
        card.parentNode.replaceChild(newCard, card);

        newCard.addEventListener('click', function(e) {
            if (e.target.closest('.revalidate-btn')) {
                return;
            }

            const leaveId = parseInt(newCard.getAttribute('data-leave-id'));
            const request = requestsData.find(r => r.id === leaveId);
            if (request) {
                openLeaveDetailsModal(request);
            }
        });
    });
}

// =============================================
// FONCTIONS POUR GÉRER LES DOCUMENTS JUSTIFICATIFS
// =============================================
function openDocument(requestId) {
    const url = `${API_BASE_URL}/${requestId}/visualiser-document`;
    window.open(url, '_blank');
}

async function downloadDocument(requestId) {
    try {
        const url = `${API_BASE_URL}/${requestId}/telecharger-document`;

        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', '');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showToast('Téléchargement', 'Le document est en cours de téléchargement', 'success');
    } catch (error) {
        console.error('Erreur lors du téléchargement:', error);
        showToast('Erreur', 'Impossible de télécharger le document', 'error');
    }
}

// =============================================
// ACTIONS GROUPÉES
// =============================================
window.toggleSelectAll = function() {
    const checkboxes = document.querySelectorAll('.request-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);

    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });

    updateBulkActions();

    const selectAllBtn = document.getElementById('selectAllBtn');
    if (selectAllBtn) {
        if (!allChecked) {
            selectAllBtn.innerHTML = '<i class="fas fa-square mr-2"></i>Tout désélectionner';
        } else {
            selectAllBtn.innerHTML = '<i class="fas fa-check-square mr-2"></i>Tout sélectionner';
        }
    }
}

window.updateBulkActions = function() {
    const checkboxes = document.querySelectorAll('.request-checkbox:checked');
    const count = checkboxes.length;

    selectedRequests.clear();
    checkboxes.forEach(cb => {
        selectedRequests.add(parseInt(cb.dataset.requestId));
    });

    const bulkActionsContainer = document.getElementById('bulkActionsContainer');
    const selectedCountElement = document.getElementById('selectedCount');

    if (bulkActionsContainer && selectedCountElement) {
        if (count > 0) {
            bulkActionsContainer.classList.remove('hidden');
            bulkActionsContainer.classList.add('flex');
            selectedCountElement.textContent = `${count} sélectionné${count > 1 ? 's' : ''}`;
        } else {
            bulkActionsContainer.classList.add('hidden');
            bulkActionsContainer.classList.remove('flex');
        }
    }

    const allCheckboxes = document.querySelectorAll('.request-checkbox');
    const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
    const selectAllBtn = document.getElementById('selectAllBtn');

    if (selectAllBtn && allCheckboxes.length > 0) {
        if (allChecked) {
            selectAllBtn.innerHTML = '<i class="fas fa-square mr-2"></i>Tout désélectionner';
        } else {
            selectAllBtn.innerHTML = '<i class="fas fa-check-square mr-2"></i>Tout sélectionner';
        }
    }
}

window.bulkApprove = async function() {
    if (selectedRequests.size === 0) return;

    const selectedIds = Array.from(selectedRequests);
    const selectedRequestsData = requestsData.filter(r => selectedIds.includes(r.id));

    const requestsList = selectedRequestsData.map(r =>
        `• ${r.employeeName} (${r.department}) - ${r.leaveType}`
    ).join('\n');

    if (confirm(`Êtes-vous sûr de vouloir approuver ces ${selectedIds.length} demande(s) ?\n\n${requestsList}`)) {
        try {
            showLoadingSpinner();

            const response = await fetch(`${API_BASE_URL}/approuver-multiples`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    demande_ids: selectedIds
                })
            });

            const data = await response.json();

            if (data.success) {
                showToast(
                    'Demandes approuvées',
                    `${data.approved_count} demande(s) ont été approuvées avec succès.`,
                    'success'
                );

                selectedRequests.clear();
                await loadRequests();
            } else {
                showToast('Erreur', data.message || 'Erreur lors de l\'approbation', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showToast('Erreur', 'Erreur lors de l\'approbation des demandes', 'error');
        } finally {
            hideLoadingSpinner();
        }
    }
}

window.bulkReject = async function() {
    if (selectedRequests.size === 0) return;

    const selectedIds = Array.from(selectedRequests);
    const selectedRequestsData = requestsData.filter(r => selectedIds.includes(r.id));

    const requestsList = selectedRequestsData.map(r =>
        `• ${r.employeeName} (${r.department}) - ${r.leaveType}`
    ).join('\n');

    const commentaire = prompt(`Êtes-vous sûr de vouloir refuser ces ${selectedIds.length} demande(s) ?\n\n${requestsList}\n\nCommentaire de refus (optionnel):`);

    if (commentaire !== null) {
        try {
            showLoadingSpinner();

            const response = await fetch(`${API_BASE_URL}/refuser-multiples`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    demande_ids: selectedIds,
                    commentaire_refus: commentaire
                })
            });

            const data = await response.json();

            if (data.success) {
                showToast(
                    'Demandes refusées',
                    `${data.rejected_count} demande(s) ont été refusées.`,
                    'error'
                );

                selectedRequests.clear();
                await loadRequests();
            } else {
                showToast('Erreur', data.message || 'Erreur lors du refus', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showToast('Erreur', 'Erreur lors du refus des demandes', 'error');
        } finally {
            hideLoadingSpinner();
        }
    }
}

// =============================================
// ACTIONS INDIVIDUELLES
// =============================================
function showConfirmModal(action, requestId) {
    currentAction = action;
    currentRequestId = requestId;
    const request = requestsData.find(r => r.id === requestId);

    if (!request) return;

    const icon = document.getElementById('confirmIcon');
    const title = document.getElementById('confirmTitle');
    const message = document.getElementById('confirmMessage');
    const actionBtn = document.getElementById('confirmActionBtn');

    if (action === 'approve') {
        icon.className = 'w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center';
        icon.innerHTML = '<i class="fas fa-check text-green-500"></i>';
        title.textContent = 'Approuver la demande';
        message.textContent = 'Êtes-vous sûr de vouloir approuver cette demande de congés ?';
        actionBtn.className = 'flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors';
        actionBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Approuver';
    } else if (action === 'reject') {
        icon.className = 'w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center';
        icon.innerHTML = '<i class="fas fa-times text-red-500"></i>';
        title.textContent = 'Refuser la demande';
        message.textContent = 'Êtes-vous sûr de vouloir refuser cette demande de congés ?';
        actionBtn.className = 'flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors';
        actionBtn.innerHTML = '<i class="fas fa-times mr-2"></i>Refuser';
    } else if (action === 'revalidate') {
        icon.className = 'w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center';
        icon.innerHTML = '<i class="fas fa-sync-alt text-blue-500"></i>';
        title.textContent = 'Revalider la demande';
        message.textContent = 'Êtes-vous sûr de vouloir remettre cette demande en attente ?';
        actionBtn.className = 'flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors';
        actionBtn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Revalider';
    }

    document.getElementById('confirmDetails').textContent = `${request.employeeName} - ${request.leaveType} (${request.department})`;
    document.getElementById('confirmDates').textContent = `${formatDate(request.startDate)} - ${formatDate(request.endDate)} (${request.duration} jours)`;

    showModal('confirmModal');
}

async function executeAction() {
    const request = requestsData.find(r => r.id === currentRequestId);

    if (!request) return;

    closeModal('confirmModal');

    try {
        showLoadingSpinner();

        let url, body = {};

        if (currentAction === 'approve') {
            url = `${API_BASE_URL}/${currentRequestId}/approuver`;
        } else if (currentAction === 'reject') {
            url = `${API_BASE_URL}/${currentRequestId}/refuser`;
            const commentaire = prompt('Commentaire de refus (optionnel):');
            if (commentaire === null) {
                hideLoadingSpinner();
                return;
            }
            body.commentaire_refus = commentaire;
        } else if (currentAction === 'revalidate') {
            url = `${API_BASE_URL}/${currentRequestId}/revalider`;
        }

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(body)
        });

        const data = await response.json();

        if (data.success) {
            const actionText = currentAction === 'approve' ? 'approuvée' :
                             currentAction === 'reject' ? 'refusée' : 'revalidée';
            const toastType = currentAction === 'approve' ? 'success' :
                            currentAction === 'reject' ? 'error' : 'success';

            showToast(
                `Demande ${actionText}`,
                `La demande de ${request.employeeName} (${request.department}) a été ${actionText} avec succès.`,
                toastType
            );

            await loadRequests();
        } else {
            showToast('Erreur', data.message || 'Erreur lors du traitement', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur', 'Erreur lors du traitement de la demande', 'error');
    } finally {
        hideLoadingSpinner();
    }
}

function showDetailsModal(requestId) {
    currentRequestId = requestId;
    const request = requestsData.find(r => r.id === requestId);

    if (!request) return;

    document.getElementById('detailsName').textContent = request.employeeName;
    document.getElementById('detailsType').textContent = request.leaveType;
    document.getElementById('detailsDepartmentInfo').textContent = `Département: ${request.department}`;
    document.getElementById('detailsStartDate').textContent = formatDate(request.startDate);
    document.getElementById('detailsEndDate').textContent = formatDate(request.endDate);
    document.getElementById('detailsDuration').textContent = request.duration + ' jours';
    document.getElementById('detailsReason').textContent = request.reason;
    document.getElementById('detailsAvatar').className = `w-16 h-16 bg-gradient-to-r ${request.avatar} rounded-xl flex items-center justify-center`;

    const statusElement = document.getElementById('detailsStatus');
    if (request.status === 'approved') {
        statusElement.textContent = 'Approuvée';
        statusElement.className = 'text-green-600 dark:text-green-400';
    } else if (request.status === 'rejected') {
        statusElement.textContent = 'Refusée';
        statusElement.className = 'text-red-600 dark:text-red-400';
    } else {
        statusElement.textContent = 'En attente';
        statusElement.className = 'text-yellow-600 dark:text-yellow-400';
    }

    const modalActions = document.getElementById('detailsModalActions');
    if (modalActions) {
        const actionButtons = modalActions.querySelectorAll('button:not(:first-child)');
        actionButtons.forEach(btn => {
            if (request.status === 'approved' || request.status === 'rejected') {
                btn.style.display = 'none';
            } else {
                btn.style.display = 'inline-block';
            }
        });
    }

    showModal('detailsModal');
}

function openLeaveDetailsModal(leave) {
    const modal = document.getElementById('leaveDetailsModal');

    if (!modal) {
        console.error('Modal leaveDetailsModal introuvable');
        return;
    }

    document.getElementById('leaveDetailsName').textContent = leave.employeeName;
    document.getElementById('leaveDetailsType').textContent = leave.leaveType;
    document.getElementById('leaveDetailsDepartment').textContent = leave.department;
    document.getElementById('leaveDetailsStartDate').textContent = formatDate(leave.startDate);
    document.getElementById('leaveDetailsEndDate').textContent = formatDate(leave.endDate);
    document.getElementById('leaveDetailsDuration').textContent = leave.duration + ' jour(s)';

    const avatarDiv = document.getElementById('leaveDetailsAvatar');
    avatarDiv.className = `w-16 h-16 bg-gradient-to-r ${leave.avatar} rounded-full flex items-center justify-center flex-shrink-0`;

    const typeBadge = document.getElementById('leaveDetailsTypeBadge');
    let badgeColor = 'bg-blue-500';

    if (leave.leaveType.toLowerCase().includes('maladie')) {
        badgeColor = 'bg-red-500';
    } else if (leave.leaveType.toLowerCase().includes('maternité')) {
        badgeColor = 'bg-pink-500';
    } else if (leave.leaveType.toLowerCase().includes('paternité')) {
        badgeColor = 'bg-blue-500';
    } else if (leave.leaveType.toLowerCase().includes('formation')) {
        badgeColor = 'bg-purple-500';
    } else if (leave.leaveType.toLowerCase().includes('payés')) {
        badgeColor = 'bg-green-500';
    } else {
        badgeColor = 'bg-yellow-500';
    }

    typeBadge.className = `px-3 py-1 text-xs font-semibold rounded-full ${badgeColor} text-white`;
    typeBadge.textContent = leave.leaveType;

    document.getElementById('leaveDetailsReason').textContent = leave.reason || leave.motif;

    const statusBadge = document.getElementById('leaveDetailsStatusBadge');
    if (leave.status === 'approved') {
        statusBadge.className = 'px-4 py-2 text-sm font-semibold rounded-lg bg-green-500 text-white';
        statusBadge.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Approuvé';
    } else if (leave.status === 'rejected') {
        statusBadge.className = 'px-4 py-2 text-sm font-semibold rounded-lg bg-red-500 text-white';
        statusBadge.innerHTML = '<i class="fas fa-times-circle mr-1"></i>Refusé';
    } else {
        statusBadge.className = 'px-4 py-2 text-sm font-semibold rounded-lg bg-yellow-500 text-white';
        statusBadge.innerHTML = '<i class="fas fa-clock mr-1"></i>En attente';
    }

    const documentSection = document.getElementById('leaveDetailsDocumentSection');
    if (documentSection) {
        const hasDocument = leave.document_justificatif && leave.document_justificatif !== '';

        if (hasDocument) {
            const fileName = leave.pdfName || 'Document justificatif';
            documentSection.innerHTML = `
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-paperclip mr-2"></i>Document justificatif
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">${fileName}</p>
                    <div class="flex space-x-2">
                        <button onclick="openDocument(${leave.id})"
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors text-sm font-medium flex items-center space-x-2">
                            <i class="fas fa-eye"></i>
                            <span>Voir</span>
                        </button>
                        <button onclick="downloadDocument(${leave.id})"
                                class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors text-sm font-medium flex items-center space-x-2">
                            <i class="fas fa-download"></i>
                            <span>Télécharger</span>
                        </button>
                    </div>
                </div>
            `;
        } else {
            documentSection.innerHTML = `
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                        <i class="fas fa-file-slash mr-2"></i>Aucun document justificatif joint
                    </p>
                </div>
            `;
        }
    }

    const actionsDiv = document.getElementById('leaveDetailsActions');
    if (actionsDiv) {
        if (leave.status === 'approved' || leave.status === 'rejected') {
            actionsDiv.style.display = 'none';
        } else {
            actionsDiv.style.display = 'flex';
        }
    }

    currentRequestId = leave.id;

    showModal('leaveDetailsModal');
}

// =============================================
// GESTION DES MODALS
// =============================================
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.classList.remove('hidden');

    setTimeout(() => {
        const backdrop = modal.querySelector('.backdrop');
        const modalContent = modal.querySelector('.modal');

        if (backdrop) backdrop.classList.add('show');
        if (modalContent) modalContent.classList.add('show');
    }, 10);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    const backdrop = modal.querySelector('.backdrop');
    const modalContent = modal.querySelector('.modal');

    if (backdrop) backdrop.classList.remove('show');
    if (modalContent) modalContent.classList.remove('show');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('backdrop')) {
        const modals = ['confirmModal', 'detailsModal', 'leaveDetailsModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && !modal.classList.contains('hidden')) {
                closeModal(modalId);
            }
        });
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = ['confirmModal', 'detailsModal', 'leaveDetailsModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && !modal.classList.contains('hidden')) {
                closeModal(modalId);
            }
        });
    }
});

// =============================================
// SYSTÈME DE TOAST NOTIFICATIONS
// =============================================
function showToast(param1, param2, param3) {
    let title, message, type;

    if (param3 !== undefined) {
        title = param1;
        message = param2;
        type = param3 || 'success';
    } else {
        title = param2 === 'success' ? 'Succès' :
                param2 === 'error' ? 'Erreur' :
                param2 === 'warning' ? 'Attention' :
                param2 === 'info' ? 'Information' : 'Notification';
        message = param1;
        type = param2 || 'success';
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
        <button onclick="closeToast('${toastId}')" style="background: none; border: none; color: #999; cursor: pointer; font-size: 18px; padding: 0; width: 24px; height: 24px;">
            <i class="fas fa-times"></i>
        </button>
    `;

    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 10);

    setTimeout(() => {
        closeToast(toastId);
    }, 4000);
}

function closeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
}

// =============================================
// LOADING SPINNER
// =============================================
function showLoadingSpinner() {
    let spinner = document.getElementById('global-loading-spinner');
    if (!spinner) {
        spinner = document.createElement('div');
        spinner.id = 'global-loading-spinner';
        spinner.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        `;
        spinner.innerHTML = `
            <div style="background: white; padding: 20px; border-radius: 10px; text-align: center;">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-3"></div>
                <p style="color: #666;">Chargement...</p>
            </div>
        `;
        document.body.appendChild(spinner);
    }
    spinner.style.display = 'flex';
}

function hideLoadingSpinner() {
    const spinner = document.getElementById('global-loading-spinner');
    if (spinner) {
        spinner.style.display = 'none';
    }
}

// =============================================
// FONCTIONS UTILITAIRES
// =============================================
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function setupSidebar() {
    const toggleBtn = document.getElementById('toggle-sidebar');
    const closeBtn = document.getElementById('close-sidebar');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (sidebar) sidebar.classList.add('show');
            if (overlay) overlay.classList.add('show');
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            if (sidebar) sidebar.classList.remove('show');
            if (overlay) overlay.classList.remove('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            if (sidebar) sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }
}

function displayCurrentDate() {
    const dateElement = document.getElementById('current-date');
    if (dateElement) {
        const today = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        dateElement.textContent = today.toLocaleDateString('fr-FR', options);
    }
}

// =============================================
// INITIALISATION PRINCIPALE
// =============================================
document.addEventListener('DOMContentLoaded', async function() {
    console.log('🚀 Initialisation du Dashboard Admin Demandes...');

    // Animation d'entrée
    const elements = document.querySelectorAll('.animate-slide-up');
    elements.forEach((el, index) => {
        setTimeout(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Initialiser tous les composants
    setupFilterButtons();
    initializeDepartmentFilter();
    initializeEmployeeFilter();
    setupSidebar();
    displayCurrentDate();

    // Charger les données initiales
    await loadDepartments();
    await loadEmployees();
    await loadRequests();

    console.log('✅ Dashboard Admin Demandes initialisé avec succès');
    console.log('📊 Données chargées:', {
        total: requestsData.length,
        departments: departmentsData.length,
        employees: employeesData.length,
        pending: requestsData.filter(r => r.status === 'pending').length,
        approved: requestsData.filter(r => r.status === 'approved').length,
        rejected: requestsData.filter(r => r.status === 'rejected').length
    });
});
