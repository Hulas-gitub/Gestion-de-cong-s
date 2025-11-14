// =============================================
// VARIABLES GLOBALES
// =============================================
let currentPage = 1;
const itemsPerPage = 5;
let filteredEmployees = [];
let allEmployees = [];
let searchTimeout = null;

// =============================================
// SYSTÈME DE TOAST NOTIFICATIONS
// =============================================
function showToast(message, type = 'success') {
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(toastContainer);
    }

    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500',
        green: 'bg-green-500',
        red: 'bg-red-500'
    };

    const icons = {
        success: 'fa-check-circle',
        error: 'fa-times-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle',
        green: 'fa-check-circle',
        red: 'fa-lock'
    };

    const toast = document.createElement('div');
    const toastId = 'toast-' + Date.now();
    toast.id = toastId;
    toast.className = `${colors[type] || colors.success} text-white px-6 py-4 rounded-lg shadow-2xl flex items-center space-x-3 min-w-[320px] transform transition-all duration-300 ease-in-out translate-x-[400px]`;

    toast.innerHTML = `
        <i class="fas ${icons[type] || icons.success} text-xl"></i>
        <span class="flex-1 font-medium">${message}</span>
        <button onclick="closeToast('${toastId}')" class="text-white hover:text-gray-200 transition-colors">
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
        toast.style.opacity = '0';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
}

// =============================================
// FONCTIONS UTILITAIRES
// =============================================
function formatDate(date) {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function calculateDuration(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const diffTime = Math.abs(end - start);
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
}

function getPositionColor(position) {
    const colors = {
        'Développeur': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'Designer': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
        'Manager': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'default': 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
    };
    return colors[position] || colors['default'];
}

function getStatusColor(blocked) {
    return blocked
        ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
        : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
}

function getStatusLabel(blocked) {
    return blocked ? 'Inactif' : 'Actif';
}

function isDarkMode() {
    return document.documentElement.classList.contains('dark');
}

// =============================================
// GÉNÉRER LA PHOTO DE PROFIL
// =============================================
function generatePhotoHTML(photoData) {
    if (photoData.type === 'url') {
        return `<img class="h-10 w-10 rounded-full object-cover" src="${photoData.value}" alt="Profile">`;
    } else {
        // Type 'initials'
        return `
            <div class="h-10 w-10 bg-gradient-to-r ${photoData.gradient} rounded-full flex items-center justify-center text-white font-bold text-sm">
                ${photoData.initials}
            </div>
        `;
    }
}

function generateLargePhotoHTML(photoData, name) {
    if (photoData.type === 'url') {
        return `<img src="${photoData.value}" alt="${name}" class="w-24 h-24 rounded-full object-cover mb-3 border-4 border-blue-500">`;
    } else {
        // Type 'initials'
        return `
            <div class="w-24 h-24 bg-gradient-to-r ${photoData.gradient} rounded-full flex items-center justify-center text-white font-bold text-3xl mb-3 border-4 border-blue-500">
                ${photoData.initials}
            </div>
        `;
    }
}

// =============================================
// CHARGEMENT DES EMPLOYÉS
// =============================================
async function loadEmployees(page = 1) {
    try {
        const searchValue = document.getElementById('searchEmployee').value;
        const positionValue = document.getElementById('positionFilter').value;

        const params = new URLSearchParams({
            page: page,
            search: searchValue,
            position: positionValue
        });

        const response = await fetch(`/chef-de-departement/gestion-equipe/employees?${params}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            allEmployees = result.data;
            filteredEmployees = result.data;
            currentPage = result.pagination.current_page;
            renderEmployeeTable();
            updatePagination(result.pagination);
        } else {
            showToast(result.message || 'Erreur lors du chargement des employés', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur lors du chargement des employés', 'error');
    }
}

// =============================================
// CHARGEMENT DES POSTES
// =============================================
async function loadPositions() {
    try {
        const response = await fetch('/chef-de-departement/gestion-equipe/positions', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            const select = document.getElementById('positionFilter');
            const currentValue = select.value;

            select.innerHTML = '<option value="">Tous les postes</option>';

            result.data.forEach(position => {
                const option = document.createElement('option');
                option.value = position;
                option.textContent = position;
                select.appendChild(option);
            });

            select.value = currentValue;
        }
    } catch (error) {
        console.error('Erreur lors du chargement des postes:', error);
    }
}

// =============================================
// RENDU DU TABLEAU
// =============================================
function renderEmployeeTable() {
    const tbody = document.getElementById('employeeTableBody');

    if (filteredEmployees.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-users text-4xl mb-3"></i>
                    <p class="text-lg">Aucun employé trouvé</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = filteredEmployees.map(employee => `
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    ${generatePhotoHTML(employee.photo)}
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">${employee.name}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">${employee.email}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full ${getPositionColor(employee.position)}">
                    ${employee.positionLabel}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 dark:text-white">
                    <i class="fas fa-phone text-gray-400 mr-2"></i>${employee.phone}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(employee.blocked)}">
                    ${getStatusLabel(employee.blocked)}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3"
                        onclick="viewEmployee(${employee.id})" title="Voir détails">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="text-${employee.blocked ? 'green' : 'red'}-600 hover:text-${employee.blocked ? 'green' : 'red'}-900 dark:text-${employee.blocked ? 'green' : 'red'}-400 dark:hover:text-${employee.blocked ? 'green' : 'red'}-300"
                        onclick="confirmToggleBlock(${employee.id}, '${employee.name}', ${employee.blocked})"
                        title="${employee.blocked ? 'Débloquer' : 'Bloquer'}">
                    <i class="fas fa-${employee.blocked ? 'unlock' : 'lock'}"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// =============================================
// PAGINATION
// =============================================
function updatePagination(pagination) {
    const startItem = ((pagination.current_page - 1) * pagination.per_page) + 1;
    const endItem = Math.min(pagination.current_page * pagination.per_page, pagination.total);

    document.getElementById('paginationInfo').textContent =
        `Affichage de ${startItem} à ${endItem} sur ${pagination.total} employés`;

    const controls = document.getElementById('paginationControls');
    controls.innerHTML = `
        <button onclick="changePage(${pagination.current_page - 1})"
                ${pagination.current_page === 1 ? 'disabled' : ''}
                class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300
                       dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600
                       ${pagination.current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''}">
            Précédent
        </button>
        ${Array.from({length: pagination.total_pages}, (_, i) => i + 1).map(page => `
            <button onclick="changePage(${page})"
                    class="px-3 py-1 text-sm rounded
                           ${page === pagination.current_page ?
                               'bg-blue-500 text-white' :
                               'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600'}">
                ${page}
            </button>
        `).join('')}
        <button onclick="changePage(${pagination.current_page + 1})"
                ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}
                class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300
                       dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600
                       ${pagination.current_page === pagination.total_pages ? 'opacity-50 cursor-not-allowed' : ''}">
            Suivant
        </button>
    `;
}

function changePage(page) {
    loadEmployees(page);
}

// =============================================
// FILTRAGE
// =============================================
function filterEmployees() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentPage = 1;
        loadEmployees(1);
    }, 500);
}

// =============================================
// CONFIRMATION BLOCAGE/DÉBLOCAGE
// =============================================
function confirmToggleBlock(employeeId, employeeName, isBlocked) {
    const action = isBlocked ? 'débloquer' : 'bloquer';
    const actionCapitalized = isBlocked ? 'Débloquer' : 'Bloquer';

    Swal.fire({
        title: `${actionCapitalized} l'employé ?`,
        html: `Voulez-vous vraiment ${action} <strong>${employeeName}</strong> ?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isBlocked ? '#10b981' : '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Oui, ${action}`,
        cancelButtonText: 'Annuler',
        background: isDarkMode() ? '#1f2937' : '#ffffff',
        color: isDarkMode() ? '#f3f4f6' : '#1f2937',
        customClass: {
            popup: isDarkMode() ? 'dark-popup' : '',
            title: isDarkMode() ? 'text-gray-100' : 'text-gray-900',
            htmlContainer: isDarkMode() ? 'text-gray-300' : 'text-gray-600'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            toggleBlockEmployee(employeeId);
        }
    });
}

// =============================================
// BLOQUER/DÉBLOQUER EMPLOYÉ
// =============================================
async function toggleBlockEmployee(employeeId) {
    try {
        const response = await fetch(`/chef-de-departement/gestion-equipe/employee/${employeeId}/toggle-block`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showToast(result.message, result.data.blocked ? 'red' : 'green');
            // Rafraîchir la page actuelle
            setTimeout(() => {
                loadEmployees(currentPage);
            }, 500);
        } else {
            Swal.fire({
                title: 'Erreur',
                text: result.message || 'Erreur lors de la modification du statut',
                icon: 'error',
                confirmButtonText: "D'accord",
                confirmButtonColor: '#ef4444',
                background: isDarkMode() ? '#1f2937' : '#ffffff',
                color: isDarkMode() ? '#f3f4f6' : '#1f2937',
                customClass: {
                    popup: isDarkMode() ? 'dark-popup' : '',
                    title: isDarkMode() ? 'text-gray-100' : 'text-gray-900',
                    htmlContainer: isDarkMode() ? 'text-gray-300' : 'text-gray-600'
                }
            });
        }
    } catch (error) {
        console.error('Erreur:', error);
        Swal.fire({
            title: 'Erreur',
            text: 'Une erreur est survenue lors de la modification du statut',
            icon: 'error',
            confirmButtonText: "D'accord",
            confirmButtonColor: '#ef4444',
            background: isDarkMode() ? '#1f2937' : '#ffffff',
            color: isDarkMode() ? '#f3f4f6' : '#1f2937',
            customClass: {
                popup: isDarkMode() ? 'dark-popup' : '',
                title: isDarkMode() ? 'text-gray-100' : 'text-gray-900',
                htmlContainer: isDarkMode() ? 'text-gray-300' : 'text-gray-600'
            }
        });
    }
}

// =============================================
// VOIR DÉTAILS EMPLOYÉ
// =============================================
async function viewEmployee(employeeId) {
    try {
        const response = await fetch(`/chef-de-departement/gestion-equipe/employee/${employeeId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showEmployeeDetails(result.data);
        } else {
            Swal.fire({
                title: 'Erreur',
                text: result.message || 'Erreur lors du chargement des détails',
                icon: 'error',
                confirmButtonText: "D'accord",
                confirmButtonColor: '#ef4444',
                background: isDarkMode() ? '#1f2937' : '#ffffff',
                color: isDarkMode() ? '#f3f4f6' : '#1f2937',
                customClass: {
                    popup: isDarkMode() ? 'dark-popup' : '',
                    title: isDarkMode() ? 'text-gray-100' : 'text-gray-900',
                    htmlContainer: isDarkMode() ? 'text-gray-300' : 'text-gray-600'
                }
            });
        }
    } catch (error) {
        console.error('Erreur:', error);
        Swal.fire({
            title: 'Erreur',
            text: 'Une erreur est survenue lors du chargement des détails',
            icon: 'error',
            confirmButtonText: "D'accord",
            confirmButtonColor: '#ef4444',
            background: isDarkMode() ? '#1f2937' : '#ffffff',
            color: isDarkMode() ? '#f3f4f6' : '#1f2937',
            customClass: {
                popup: isDarkMode() ? 'dark-popup' : '',
                title: isDarkMode() ? 'text-gray-100' : 'text-gray-900',
                htmlContainer: isDarkMode() ? 'text-gray-300' : 'text-gray-600'
            }
        });
    }
}

// =============================================
// AFFICHER DÉTAILS DANS SWEETALERT
// =============================================
function showEmployeeDetails(employee) {
    const congesHtml = employee.conges && employee.conges.length > 0
        ? employee.conges.map(conge => {
            const statusColors = {
                'En attente': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                'Approuvé': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                'Refusé': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
            };

            return `
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 mb-2">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h6 class="font-medium text-gray-900 dark:text-white mb-1">${conge.type}</h6>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-calendar mr-1"></i>
                                ${formatDate(conge.date_debut)} → ${formatDate(conge.date_fin)}
                            </p>
                            ${conge.motif ? `<p class="text-sm text-gray-500 dark:text-gray-400 mt-1 italic">${conge.motif}</p>` : ''}
                        </div>
                        <div class="ml-3 flex flex-col items-end gap-2">
                            <span class="px-2 py-1 text-xs rounded-full ${statusColors[conge.statut] || statusColors['En attente']}">
                                ${conge.statut}
                            </span>
                            <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                ${conge.nb_jours} jour${conge.nb_jours > 1 ? 's' : ''}
                            </span>
                        </div>
                    </div>
                </div>
            `;
        }).join('')
        : '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Aucun congé enregistré</p>';

    const htmlContent = `
        <div class="text-left space-y-4">
            <!-- Photo et infos principales -->
            <div class="flex flex-col items-center pb-4 border-b border-gray-200 dark:border-gray-600">
                ${generateLargePhotoHTML(employee.photo, employee.name)}
                <h4 class="text-xl font-bold text-gray-900 dark:text-white">${employee.name}</h4>
                <p class="text-gray-600 dark:text-gray-400">${employee.positionLabel}</p>
                <span class="mt-2 px-3 py-1 text-xs font-medium rounded-full ${getStatusColor(employee.blocked)}">
                    ${getStatusLabel(employee.blocked)}
                </span>
            </div>

            <!-- Informations personnelles -->
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h5 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                    <i class="fas fa-user mr-2 text-blue-500"></i>
                    Informations personnelles
                </h5>
                <div class="space-y-2">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-gray-600 dark:text-gray-400 text-sm">
                            <i class="fas fa-envelope w-5"></i> Email
                        </span>
                        <span class="font-medium text-gray-900 dark:text-white text-sm">${employee.email}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-gray-600 dark:text-gray-400 text-sm">
                            <i class="fas fa-phone w-5"></i> Téléphone
                        </span>
                        <span class="font-medium text-gray-900 dark:text-white text-sm">${employee.phone}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-gray-600 dark:text-gray-400 text-sm">
                            <i class="fas fa-id-card w-5"></i> Matricule
                        </span>
                        <span class="font-medium text-gray-900 dark:text-white text-sm">${employee.matricule}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600 dark:text-gray-400 text-sm">
                            <i class="fas fa-calendar-check w-5"></i> Date d'embauche
                        </span>
                        <span class="font-medium text-gray-900 dark:text-white text-sm">${formatDate(employee.date_embauche)}</span>
                    </div>
                </div>
            </div>

            <!-- Informations congés -->
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h5 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                    <i class="fas fa-umbrella-beach mr-2 text-green-500"></i>
                    Congés
                </h5>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div class="bg-white dark:bg-gray-800 p-3 rounded-lg text-center border border-gray-200 dark:border-gray-600">
                        <p class="text-gray-600 dark:text-gray-400 text-xs mb-1">Solde restant</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">${employee.solde_conges}</p>
                        <p class="text-gray-500 dark:text-gray-400 text-xs">jours</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-3 rounded-lg text-center border border-gray-200 dark:border-gray-600">
                        <p class="text-gray-600 dark:text-gray-400 text-xs mb-1">Congés pris</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">${employee.conges_pris}</p>
                        <p class="text-gray-500 dark:text-gray-400 text-xs">jours</p>
                    </div>
                </div>
            </div>

            <!-- Historique des congés -->
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h5 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                    <i class="fas fa-history mr-2 text-purple-500"></i>
                    Historique des congés
                </h5>
                <div class="max-h-64 overflow-y-auto space-y-2">
                    ${congesHtml}
                </div>
            </div>
        </div>
    `;

    Swal.fire({
        title: `<span class="${isDarkMode() ? 'text-gray-100' : 'text-gray-900'}">Détails de ${employee.name}</span>`,
        html: htmlContent,
        width: '700px',
        showCloseButton: true,
        showConfirmButton: false,
        background: isDarkMode() ? '#1f2937' : '#ffffff',
        color: isDarkMode() ? '#f3f4f6' : '#1f2937',
        customClass: {
            popup: isDarkMode() ? 'dark-popup' : '',
            closeButton: isDarkMode() ? 'text-gray-400 hover:text-gray-200' : 'text-gray-600 hover:text-gray-800'
        }
    });
}

// =============================================
// FERMER MODAL
// =============================================
function closeEmployeeModal() {
    const modal = document.getElementById('employeeModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// =============================================
// DATE ACTUELLE
// =============================================
function updateCurrentDate() {
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    const date = new Date().toLocaleDateString('fr-FR', options);
    const dateElement = document.getElementById('current-date');
    if (dateElement) {
        dateElement.textContent = date.charAt(0).toUpperCase() + date.slice(1);
    }
}

// =============================================
// INITIALISATION
// =============================================
document.addEventListener('DOMContentLoaded', function() {
    // Mettre à jour la date
    updateCurrentDate();

    // Charger les postes pour le filtre
    loadPositions();

    // Charger les employés
    loadEmployees(1);

    // Event listeners
    const searchInput = document.getElementById('searchEmployee');
    if (searchInput) {
        searchInput.addEventListener('input', filterEmployees);
    }

    const positionFilter = document.getElementById('positionFilter');
    if (positionFilter) {
        positionFilter.addEventListener('change', filterEmployees);
    }

    const closeModalBtn = document.getElementById('closeEmployeeModal');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeEmployeeModal);
    }

    const modal = document.getElementById('employeeModal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === e.currentTarget) {
                closeEmployeeModal();
            }
        });
    }

    // Fermer les modals avec Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (modal && !modal.classList.contains('hidden')) {
                closeEmployeeModal();
            }
        }
    });
});
