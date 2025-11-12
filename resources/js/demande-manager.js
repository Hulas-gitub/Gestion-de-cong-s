// =============================================
// DONNÉES SIMULÉES
// =============================================
const employeesData = [
    {
        id: 'jean-dupont',
        name: 'Jean Dupont',
        email: 'j.dupont@email.com',
        phone: '+241 07 45 23 12',
        position: 'developer',
        positionLabel: 'Développeur',
        remainingLeave: 25,
        status: 'available',
        blocked: false,
        photo: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face'
    },
    {
        id: 'marie-martin',
        name: 'Marie Martin',
        email: 'm.martin@email.com',
        phone: '+241 06 78 91 45',
        position: 'designer',
        positionLabel: 'Designer',
        remainingLeave: 18,
        status: 'on-leave',
        blocked: false,
        photo: 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=40&h=40&fit=crop&crop=face'
    },
    {
        id: 'paul-bernard',
        name: 'Paul Bernard',
        email: 'p.bernard@email.com',
        phone: '+241 05 23 67 89',
        position: 'manager',
        positionLabel: 'Manager',
        remainingLeave: 30,
        status: 'available',
        blocked: true,
        photo: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face'
    },
    {
        id: 'sophie-durand',
        name: 'Sophie Durand',
        email: 's.durand@email.com',
        phone: '+241 07 12 34 56',
        position: 'developer',
        positionLabel: 'Développeur',
        remainingLeave: 22,
        status: 'available',
        blocked: false,
        photo: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&crop=face'
    },
    {
        id: 'marc-petit',
        name: 'Marc Petit',
        email: 'm.petit@email.com',
        phone: '+241 06 89 45 23',
        position: 'designer',
        positionLabel: 'Designer',
        remainingLeave: 15,
        status: 'on-leave',
        blocked: false,
        photo: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=40&h=40&fit=crop&crop=face'
    },
    {
        id: 'claire-moreau',
        name: 'Claire Moreau',
        email: 'c.moreau@email.com',
        phone: '+241 05 67 89 12',
        position: 'manager',
        positionLabel: 'Manager',
        remainingLeave: 28,
        status: 'available',
        blocked: false,
        photo: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=40&h=40&fit=crop&crop=face'
    }
];

const leaveEvents = [
    {
        id: '1',
        title: 'Jean Dupont - Congés payés',
        start: '2025-10-15',
        end: '2025-10-21',
        backgroundColor: '#10b981',
        borderColor: '#10b981',
        extendedProps: {
            employeeId: 'jean-dupont',
            employeeName: 'Jean Dupont',
            leaveType: 'vacation',
            leaveTypeLabel: 'Congés payés',
            remainingLeave: 3,
            photo: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face'
        }
    },
    {
        id: '2',
        title: 'Marie Martin - Maladie',
        start: '2025-10-18',
        end: '2025-10-20',
        backgroundColor: '#ef4444',
        borderColor: '#ef4444',
        extendedProps: {
            employeeId: 'marie-martin',
            employeeName: 'Marie Martin',
            leaveType: 'sick',
            leaveTypeLabel: 'Maladie',
            remainingLeave: 2,
            photo: 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=40&h=40&fit=crop&crop=face'
        }
    },
    {
        id: '3',
        title: 'Paul Bernard - Paternité',
        start: '2025-10-22',
        end: '2025-10-25',
        backgroundColor: '#3b82f6',
        borderColor: '#3b82f6',
        extendedProps: {
            employeeId: 'paul-bernard',
            employeeName: 'Paul Bernard',
            leaveType: 'training',
            leaveTypeLabel: 'Paternité',
            remainingLeave: 2,
            photo: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face'
        }
    },
    {
        id: '4',
        title: 'Sophie Durand - Congés payés',
        start: '2025-10-10',
        end: '2025-10-14',
        backgroundColor: '#10b981',
        borderColor: '#10b981',
        extendedProps: {
            employeeId: 'sophie-durand',
            employeeName: 'Sophie Durand',
            leaveType: 'vacation',
            leaveTypeLabel: 'Congés payés',
            remainingLeave: 3,
            photo: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&crop=face'
        }
    },
    {
        id: '5',
        title: 'Marc Petit - Maternité',
        start: '2025-10-05',
        end: '2025-10-12',
        backgroundColor: '#ec4899',
        borderColor: '#ec4899',
        extendedProps: {
            employeeId: 'marc-petit',
            employeeName: 'Marc Petit',
            leaveType: 'maternity',
            leaveTypeLabel: 'Maternité',
            remainingLeave: 6,
            photo: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=40&h=40&fit=crop&crop=face'
        }
    }
];

// =============================================
// SYSTÈME DE TOAST NOTIFICATIONS
// =============================================
function showToast(message, type = 'success') {
    // Créer le conteneur de toasts s'il n'existe pas
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(toastContainer);
    }

    // Définir les couleurs selon le type
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

    // Créer le toast
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

    // Animation d'entrée
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 10);

    // Auto-fermeture après 4 secondes
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
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
}

function getPositionColor(position) {
    const colors = {
        developer: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        designer: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
        manager: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
    };
    return colors[position] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
}

function getStatusColor(blocked) {
    return blocked
        ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
        : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
}

function getStatusLabel(blocked) {
    return blocked ? 'Bloqué' : 'Actif';
}

// =============================================
// VARIABLES GLOBALES POUR LE TABLEAU
// =============================================
let currentPage = 1;
const itemsPerPage = 5;
let filteredEmployees = [...employeesData];

// =============================================
// FONCTIONS POUR LE TABLEAU DES EMPLOYÉS
// =============================================

function renderEmployeeTable() {
    const tbody = document.getElementById('employeeTableBody');
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageEmployees = filteredEmployees.slice(startIndex, endIndex);

    tbody.innerHTML = pageEmployees.map(employee => `
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <img class="h-10 w-10 rounded-full object-cover" src="${employee.photo}" alt="Profile">
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
                        onclick="viewEmployee('${employee.id}')" title="Voir détails">
                        <i class="fas fa-eye"></i>
                </button>
                <button class="text-${employee.blocked ? 'green' : 'red'}-600 hover:text-${employee.blocked ? 'green' : 'red'}-900 dark:text-${employee.blocked ? 'green' : 'red'}-400 dark:hover:text-${employee.blocked ? 'green' : 'red'}-300"
                        onclick="toggleBlockEmployee('${employee.id}')" title="${employee.blocked ? 'Débloquer' : 'Bloquer'}">
                        <i class="fas fa-${employee.blocked ? 'unlock' : 'lock'}"></i>
                </button>
            </td>
        </tr>
    `).join('');

    updatePagination();
}

function updatePagination() {
    const totalPages = Math.ceil(filteredEmployees.length / itemsPerPage);
    const startItem = (currentPage - 1) * itemsPerPage + 1;
    const endItem = Math.min(currentPage * itemsPerPage, filteredEmployees.length);

    document.getElementById('paginationInfo').textContent =
        `Affichage de ${startItem} à ${endItem} sur ${filteredEmployees.length} employés`;

    const controls = document.getElementById('paginationControls');
    controls.innerHTML = `
        <button onclick="changePage(${currentPage - 1})"
                ${currentPage === 1 ? 'disabled' : ''}
                class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300
                       dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600
                       ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}">
            Précédent
        </button>
        ${Array.from({length: totalPages}, (_, i) => i + 1).map(page => `
            <button onclick="changePage(${page})"
                    class="px-3 py-1 text-sm rounded
                           ${page === currentPage ?
                               'bg-blue-500 text-white' :
                               'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600'}">
                ${page}
            </button>
        `).join('')}
        <button onclick="changePage(${currentPage + 1})"
                ${currentPage === totalPages ? 'disabled' : ''}
                class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300
                       dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600
                       ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}">
            Suivant
        </button>
    `;
}

function changePage(page) {
    const totalPages = Math.ceil(filteredEmployees.length / itemsPerPage);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderEmployeeTable();
    }
}

function filterEmployees() {
    const searchTerm = document.getElementById('searchEmployee').value.toLowerCase();
    const positionFilter = document.getElementById('positionFilter').value;

    filteredEmployees = employeesData.filter(emp => {
        const matchesSearch = emp.name.toLowerCase().includes(searchTerm) ||
                             emp.email.toLowerCase().includes(searchTerm);
        const matchesPosition = !positionFilter || emp.position === positionFilter;
        return matchesSearch && matchesPosition;
    });

    currentPage = 1;
    renderEmployeeTable();
}

function toggleBlockEmployee(employeeId) {
    const employee = employeesData.find(emp => emp.id === employeeId);
    if (employee) {
        employee.blocked = !employee.blocked;
        renderEmployeeTable();
        
        // Toast notification
        const status = employee.blocked ? 'bloqué' : 'débloqué';
        const color = employee.blocked ? 'red' : 'green';
        showToast(`${employee.name} a été ${status} avec succès`, color);
    }
}

function viewEmployee(employeeId) {
    const employee = employeesData.find(emp => emp.id === employeeId);
    if (!employee) return;

    document.getElementById('employeeModalTitle').textContent = `Détails de ${employee.name}`;

    const modalContent = document.getElementById('employeeModalContent');
    modalContent.innerHTML = `
        <div class="flex flex-col items-center md:items-start">
            <img src="${employee.photo}" alt="${employee.name}"
                 class="w-24 h-24 rounded-full object-cover mb-4 border-2 border-blue-500">
            <div class="text-center md:text-left">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">${employee.name}</h4>
                <p class="text-gray-600 dark:text-gray-400">${employee.positionLabel}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h5 class="font-medium text-gray-900 dark:text-white mb-2">Informations personnelles</h5>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Email:</span>
                        <span class="font-medium text-gray-900 dark:text-white">${employee.email}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Téléphone:</span>
                        <span class="font-medium text-gray-900 dark:text-white">${employee.phone}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Poste:</span>
                        <span class="font-medium text-gray-900 dark:text-white">${employee.positionLabel}</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h5 class="font-medium text-gray-900 dark:text-white mb-2">Congés</h5>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Solde restant:</span>
                        <span class="font-medium text-gray-900 dark:text-white">${employee.remainingLeave} jours</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Statut:</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(employee.blocked)}">
                            ${getStatusLabel(employee.blocked)}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h5 class="font-medium text-gray-900 dark:text-white mb-2">Historique des congés</h5>
                <div class="space-y-3">
                    ${getEmployeeLeaveHistory(employee.id)}
                </div>
            </div>
        </div>
    `;

    document.getElementById('employeeModal').classList.remove('hidden');
}

function getEmployeeLeaveHistory(employeeId) {
    const employeeLeaves = leaveEvents.filter(event => event.extendedProps.employeeId === employeeId);

    if (employeeLeaves.length === 0) {
        return '<p class="text-sm text-gray-500 dark:text-gray-400">Aucun congé enregistré.</p>';
    }

    return employeeLeaves.map(leave => `
        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-start">
                <div>
                    <h6 class="font-medium text-gray-900 dark:text-white">${leave.extendedProps.leaveTypeLabel}</h6>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        ${formatDate(leave.start)} → ${formatDate(leave.end)}
                    </p>
                </div>
                <span class="px-2 py-1 text-xs rounded-full text-white"
                      style="background-color: ${leave.backgroundColor}">
                    ${calculateDuration(leave.start, leave.end)} jours
                </span>
            </div>
        </div>
    `).join('');
}

function closeEmployeeModal() {
    document.getElementById('employeeModal').classList.add('hidden');
}

// =============================================
// INITIALISATION
// =============================================
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation tableau employés
    renderEmployeeTable();

    document.getElementById('searchEmployee').addEventListener('input', filterEmployees);
    document.getElementById('positionFilter').addEventListener('change', filterEmployees);
    document.getElementById('closeEmployeeModal').addEventListener('click', closeEmployeeModal);

    document.getElementById('employeeModal').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) closeEmployeeModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (!document.getElementById('employeeModal').classList.contains('hidden')) {
                closeEmployeeModal();
            }
        }
    });
});