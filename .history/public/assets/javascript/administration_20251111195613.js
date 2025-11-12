
// =============================================
// VARIABLES GLOBALES
// =============================================
let currentDeleteId = null;
let currentDeleteType = null;
let adminRoutes = {};
let adminRoles = {};
let adminDepartements = [];
let adminChefs = [];

// =============================================
// INITIALISATION AU CHARGEMENT DE LA PAGE
// =============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation de la page Administration...');

    // Récupérer les données depuis window.adminData
    if (window.adminData) {
        adminRoutes = window.adminData.routes || {};
        adminRoles = window.adminData.roles || {};
        adminDepartements = window.adminData.departements || [];
        adminChefs = window.adminData.chefs || [];

        // Récupérer le token CSRF depuis le meta tag si non défini
        if (!window.adminData.csrfToken) {
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) {
                window.adminData.csrfToken = csrfMeta.getAttribute('content');
            }
        }

        console.log('✅ Données admin chargées:', window.adminData);
    } else {
        console.error('❌ window.adminData non trouvé');
    }

    // Initialiser le thème
    initTheme();

    // Initialiser la gestion des onglets
    initTabs();

    // Initialiser les écouteurs d'événements
    initEventListeners();

    console.log('✅ Page Administration initialisée avec succès');
});

// =============================================
// GESTION DES ONGLETS
// =============================================
function initTabs() {
    // Attacher les événements aux boutons d'onglets
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            showTab(tabName);
        });
    });

    // Afficher l'onglet par défaut
    setTimeout(() => showTab('employes'), 0);
}

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

    console.log(`📄 Onglet affiché: ${tabName}`);
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

    if (addEmployeBtn) addEmployeBtn.addEventListener('click', () => openAddEmployeModal());
    if (addChefBtn) addChefBtn.addEventListener('click', () => openAddChefModal());
    if (addDepartementBtn) addDepartementBtn.addEventListener('click', () => openAddDepartementModal());

    // Formulaires avec soumission AJAX
    const employeForm = document.getElementById('employe-form');
    const chefForm = document.getElementById('chef-form');
    const departementForm = document.getElementById('departement-form');

    if (employeForm) employeForm.addEventListener('submit', handleEmployeSubmit);
    if (chefForm) chefForm.addEventListener('submit', handleChefSubmit);
    if (departementForm) departementForm.addEventListener('submit', handleDepartementSubmit);

    // Bouton de confirmation de suppression
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    if (confirmDeleteBtn) confirmDeleteBtn.addEventListener('click', confirmDelete);
}

// =============================================
// GESTION DE LA SIDEBAR (MOBILE)
// =============================================
function toggleSidebarMenu() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (sidebar) sidebar.classList.toggle('open');
    if (overlay) overlay.classList.toggle('open');
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
// GESTION DES MODALS - EMPLOYÉS
// =============================================
function openAddEmployeModal() {
    const modal = document.getElementById('employe-modal');
    const title = document.getElementById('employe-modal-title');
    const form = document.getElementById('employe-form');

    if (!modal || !title || !form) {
        console.error('Modal employé non trouvé');
        return;
    }

    title.textContent = 'Ajouter un employé';
    form.reset();
    document.getElementById('employe-id').value = '';

    showModal('employe-modal');
}

function editEmploye(id) {
    const url = adminRoutes.usersShow + id;

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.adminData.csrfToken
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Erreur réseau');
        return response.json();
    })
    .then(data => {
        const modal = document.getElementById('employe-modal');
        const title = document.getElementById('employe-modal-title');

        title.textContent = 'Modifier un employé';

        document.getElementById('employe-id').value = data.id_user || '';
        document.getElementById('employe-nom').value = data.nom || '';
        document.getElementById('employe-prenom').value = data.prenom || '';
        document.getElementById('employe-solde-conges').value = data.solde_conges_annuel || 30;

        showModal('employe-modal');
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur', 'Impossible de charger les données de l\'employé', 'error');
    });
}

function handleEmployeSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const employeId = document.getElementById('employe-id').value;
    const isEdit = employeId !== '';

    const url = isEdit ? adminRoutes.usersUpdate + employeId : adminRoutes.usersStore;
    const method = isEdit ? 'PUT' : 'POST';

    // Convertir FormData en JSON
    const data = {};
    formData.forEach((value, key) => {
        if (key !== '_token') {
            data[key] = value;
        }
    });

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': window.adminData.csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(
                isEdit ? 'Employé modifié' : 'Employé ajouté',
                data.message || 'Opération effectuée avec succès',
                'success'
            );
            closeModal('employe-modal');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('Erreur', data.message || data.error || 'Une erreur est survenue', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur', 'Une erreur est survenue lors de l\'enregistrement', 'error');
    });
}

// =============================================
// GESTION DES MODALS - CHEFS
// =============================================
function openAddChefModal() {
    const modal = document.getElementById('chef-modal');
    const title = document.getElementById('chef-modal-title');
    const form = document.getElementById('chef-form');

    if (!modal || !title || !form) {
        console.error('Modal chef non trouvé');
        return;
    }

    title.textContent = 'Ajouter un chef de département';
    form.reset();
    document.getElementById('chef-id').value = '';

    // Définir la date d'aujourd'hui
    const dateField = document.getElementById('chef-date-embauche');
    if (dateField) {
        dateField.value = new Date().toISOString().split('T')[0];
    }

    showModal('chef-modal');
}

function editChef(id) {
    const url = adminRoutes.usersShow + id;

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.adminData.csrfToken
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Erreur réseau');
        return response.json();
    })
    .then(data => {
        const modal = document.getElementById('chef-modal');
        const title = document.getElementById('chef-modal-title');

        title.textContent = 'Modifier un chef de département';

        document.getElementById('chef-id').value = data.id_user || '';
        document.getElementById('chef-nom').value = data.nom || '';
        document.getElementById('chef-prenom').value = data.prenom || '';
        document.getElementById('chef-email').value = data.email || '';
        document.getElementById('chef-telephone').value = data.telephone || '';
        document.getElementById('chef-matricule').value = data.matricule || '';
        document.getElementById('chef-profession').value = data.profession || '';
        document.getElementById('chef-date-embauche').value = data.date_embauche || '';
        document.getElementById('chef-departement').value = data.departement_id || '';
        document.getElementById('chef-solde-conges').value = data.solde_conges_annuel || 30;

        showModal('chef-modal');
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur', 'Impossible de charger les données du chef', 'error');
    });
}

function handleChefSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const chefId = document.getElementById('chef-id').value;
    const isEdit = chefId !== '';

    const url = isEdit ? adminRoutes.usersUpdate + chefId : adminRoutes.usersStore;
    const method = isEdit ? 'PUT' : 'POST';

    // Convertir FormData en JSON
    const data = {};
    formData.forEach((value, key) => {
        if (key !== '_token') {
            data[key] = value;
        }
    });

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': window.adminData.csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(
                isEdit ? 'Chef modifié' : 'Chef ajouté',
                data.message || 'Opération effectuée avec succès',
                'success'
            );
            closeModal('chef-modal');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('Erreur', data.message || data.error || 'Une erreur est survenue', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur', 'Une erreur est survenue lors de l\'enregistrement', 'error');
    });
}

// =============================================
// GESTION DES MODALS - DÉPARTEMENTS
// =============================================
function openAddDepartementModal() {
    const modal = document.getElementById('departement-modal');
    const title = document.getElementById('departement-modal-title');
    const form = document.getElementById('departement-form');

    if (!modal || !title || !form) {
        console.error('Modal département non trouvé');
        return;
    }

    title.textContent = 'Ajouter un département';
    form.reset();
    document.getElementById('departement-id').value = '';

    showModal('departement-modal');
}

function editDepartement(id) {
    const url = adminRoutes.departementsShow + id;

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.adminData.csrfToken
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Erreur réseau');
        return response.json();
    })
    .then(data => {
        const modal = document.getElementById('departement-modal');
        const title = document.getElementById('departement-modal-title');

        title.textContent = 'Modifier un département';

        document.getElementById('departement-id').value = data.id_departement || '';
        document.getElementById('departement-nom').value = data.nom_departement || '';
        document.getElementById('departement-description').value = data.description || '';
        document.getElementById('departement-chef').value = data.chef_departement_id || '';
        document.getElementById('departement-couleur').value = data.couleur_calendrier || '#3b82f6';

        showModal('departement-modal');
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur', 'Impossible de charger les données du département', 'error');
    });
}

function handleDepartementSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const deptId = document.getElementById('departement-id').value;
    const isEdit = deptId !== '';

    const url = isEdit ? adminRoutes.departementsUpdate + deptId : adminRoutes.departementsStore;
    const method = isEdit ? 'PUT' : 'POST';

    // Convertir FormData en JSON
    const data = {};
    formData.forEach((value, key) => {
        if (key !== '_token') {
            data[key] = value;
        }
    });

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': window.adminData.csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(
                isEdit ? 'Département modifié' : 'Département ajouté',
                data.message || 'Opération effectuée avec succès',
                'success'
            );
            closeModal('departement-modal');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('Erreur', data.message || data.error || 'Une erreur est survenue', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur', 'Une erreur est survenue lors de l\'enregistrement', 'error');
    });
}

// =============================================
// ACTIONS - VISUALISATION
// =============================================
function viewUser(id) {
    const url = adminRoutes.usersShow + id;

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.adminData.csrfToken
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Erreur réseau');
        return response.json();
    })
    .then(user => {
        const content = document.getElementById('view-modal-content');
        const title = document.getElementById('view-modal-title');

        title.textContent = 'Détails de l\'utilisateur';

        content.innerHTML = `
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Matricule</p>
                    <p class="font-medium text-gray-900 dark:text-white">${user.matricule || 'N/A'}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nom complet</p>
                    <p class="font-medium text-gray-900 dark:text-white">${user.nom} ${user.prenom}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                    <p class="font-medium text-gray-900 dark:text-white">${user.email || 'N/A'}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Téléphone</p>
                    <p class="font-medium text-gray-900 dark:text-white">${user.telephone || 'N/A'}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Rôle</p>
                    <p class="font-medium text-gray-900 dark:text-white">${user.role?.nom_role || 'N/A'}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Profession</p>
                    <p class="font-medium text-gray-900 dark:text-white">${user.profession || 'N/A'}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Département</p>
                    <p class="font-medium text-gray-900 dark:text-white">${user.departement?.nom_departement || 'N/A'}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Statut</p>
                    <p class="font-medium text-gray-900 dark:text-white">${user.statut || 'N/A'}</p>
                </div>
                ${user.date_embauche ? `
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Date d'embauche</p>
                    <p class="font-medium text-gray-900 dark:text-white">${user.date_embauche}</p>
                </div>
                ` : ''}
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Solde congés</p>
                    <p class="font-medium text-gray-900 dark:text-white">${user.solde_conges_annuel || 0} jours</p>
                </div>
            </div>
        `;

        showModal('view-modal');
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur', 'Impossible de charger les détails de l\'utilisateur', 'error');
    });
}

function viewDepartement(id) {
    const url = adminRoutes.departementsShow + id;

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.adminData.csrfToken
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Erreur réseau');
        return response.json();
    })
    .then(dept => {
        const content = document.getElementById('view-modal-content');
        const title = document.getElementById('view-modal-title');

        title.textContent = 'Détails du département';

        content.innerHTML = `
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nom</p>
                    <p class="font-medium text-gray-900 dark:text-white">${dept.nom_departement}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                    <p class="font-medium text-gray-900 dark:text-white">${dept.description || 'Aucune description'}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Chef de département</p>
                    <p class="font-medium text-gray-900 dark:text-white">${dept.chef ? dept.chef.nom + ' ' + dept.chef.prenom : 'Aucun chef'}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nombre d'employés</p>
                    <p class="font-medium text-gray-900 dark:text-white">${dept.users_count || 0}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Couleur calendrier</p>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded" style="background-color: ${dept.couleur_calendrier || '#3b82f6'}"></div>
                        <p class="font-medium text-gray-900 dark:text-white">${dept.couleur_calendrier || '#3b82f6'}</p>
                    </div>
                </div>
            </div>
        `;

        showModal('view-modal');
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur', 'Impossible de charger les détails du département', 'error');
    });
}

// =============================================
// ACTIONS - CHANGEMENT DE STATUT
// =============================================
function toggleUserBlock(id) {
    const url = adminRoutes.usersBlock + id + '/toggle-block';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.adminData.csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Statut modifié', data.message || 'Le statut a été modifié avec succès', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('Erreur', data.message || data.error || 'Une erreur est survenue', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur', 'Une erreur est survenue', 'error');
    });
}

function resendActivation(id) {
    const url = adminRoutes.usersResendActivation + id + '/resend-activation';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.adminData.csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Email envoyé', data.message || 'L\'email d\'activation a été renvoyé', 'success');
        } else {
            showToast('Erreur', data.message || data.error || 'Une erreur est survenue', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur', 'Une erreur est survenue', 'error');
    });
}

// =============================================
// ACTIONS - SUPPRESSION
// =============================================
function deleteUser(id) {
    currentDeleteId = id;
    currentDeleteType = 'user';

    const title = document.getElementById('delete-confirm-title');
    const message = document.getElementById('delete-confirm-message');

    title.textContent = 'Supprimer l\'utilisateur';
    message.textContent = 'Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.';

    showModal('delete-confirm-modal');
}

function deleteDepartement(id) {
    currentDeleteId = id;
    currentDeleteType = 'departement';

    const title = document.getElementById('delete-confirm-title');
    const message = document.getElementById('delete-confirm-message');

    title.textContent = 'Supprimer le département';
    message.textContent = 'Êtes-vous sûr de vouloir supprimer ce département ? Cette action est irréversible.';

    showModal('delete-confirm-modal');
}

function confirmDelete() {
    if (!currentDeleteId || !currentDeleteType) return;

    const url = currentDeleteType === 'user'
        ? adminRoutes.usersDelete + currentDeleteId
        : adminRoutes.departementsDelete + currentDeleteId;

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': window.adminData.csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Suppression réussie', data.message || 'L\'élément a été supprimé avec succès', 'success');
            closeModal('delete-confirm-modal');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('Erreur', data.message || data.error || 'Une erreur est survenue', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur', 'Une erreur est survenue lors de la suppression', 'error');
    })
    .finally(() => {
        currentDeleteId = null;
        currentDeleteType = null;
    });
}

// =============================================
// GÉNÉRATION DE MATRICULE
// =============================================
function generateMatricule(type) {
    const url = adminRoutes.generateMatricule + '?type=' + type;

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.adminData.csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.matricule) {
            const fieldId = type === 'chef' ? 'chef-matricule' : 'employe-matricule';
            const field = document.getElementById(fieldId);
            if (field) {
                field.value = data.matricule;
                showToast('Matricule généré', 'Le matricule a été généré automatiquement', 'success');
            }
        }
    })
    .catch(error => {
        console.error('Erreur génération matricule:', error);
        showToast('Erreur', 'Impossible de générer le matricule', 'error');
    });
}

// =============================================
// GESTION DES MODALS
// =============================================
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) {
        console.error('Modal non trouvé:', modalId);
        return;
    }

    modal.classList.remove('hidden');

    // Forcer le reflow pour l'animation
    setTimeout(() => {
        const backdrop = modal.querySelector('.backdrop');
        const modalContent = modal.querySelector('.modal');

        if (backdrop) backdrop.style.opacity = '1';
        if (modalContent) {
            modalContent.style.transform = 'scale(1)';
            modalContent.style.opacity = '1';
        }
    }, 10);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    const backdrop = modal.querySelector('.backdrop');
    const modalContent = modal.querySelector('.modal');

    if (backdrop) backdrop.style.opacity = '0';
    if (modalContent) {
        modalContent.style.transform = 'scale(0.95)';
        modalContent.style.opacity = '0';
    }

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);

    // Réinitialiser les variables de suppression
    if (modalId === 'delete-confirm-modal') {
        currentDeleteId = null;
        currentDeleteType = null;
    }
}

// =============================================
// SYSTÈME DE NOTIFICATIONS TOAST
// =============================================
function showToast(title, message, type = 'success') {
    // Vérifier si showNotificationToken existe (depuis config.js)
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

    // Toast personnalisé si showNotificationToken n'existe pas
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

// =============================================
// FONCTIONS GLOBALES (pour onclick dans HTML)
// =============================================
window.openAddEmployeModal = openAddEmployeModal;
window.openAddChefModal = openAddChefModal;
window.openAddDepartementModal = openAddDepartementModal;
window.editEmploye = editEmploye;
window.editChef = editChef;
window.editDepartement = editDepartement;
window.viewUser = viewUser;
window.viewDepartement = viewDepartement;
window.deleteUser = deleteUser;
window.deleteDepartement = deleteDepartement;
window.toggleUserBlock = toggleUserBlock;
window.resendActivation = resendActivation;
window.generateMatricule = generateMatricule;
window.showModal = showModal;
window.closeModal = closeModal;
window.showToast = showToast;
