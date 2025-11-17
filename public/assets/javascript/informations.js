// ============================================
// GESTION DES NOTES D'INFORMATION - VERSION AJAX DYNAMIQUE
// ============================================

let currentNoteType = 'note';
let currentNoteId = null;
let notesData = [];

// Configuration des icônes et couleurs par type de fichier
const fileTypeConfig = {
    'pdf': {
        icon: 'fas fa-file-pdf',
        bgColor: 'from-red-500 to-pink-500',
        badgeColor: 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
        type: 'PDF'
    },
    'xlsx': {
        icon: 'fas fa-file-excel',
        bgColor: 'from-green-500 to-teal-500',
        badgeColor: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
        type: 'XLSX'
    },
    'xls': {
        icon: 'fas fa-file-excel',
        bgColor: 'from-green-500 to-teal-500',
        badgeColor: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
        type: 'XLS'
    },
    'png': {
        icon: 'fas fa-file-image',
        bgColor: 'from-purple-500 to-pink-500',
        badgeColor: 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300',
        type: 'PNG'
    },
    'jpg': {
        icon: 'fas fa-file-image',
        bgColor: 'from-purple-500 to-pink-500',
        badgeColor: 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300',
        type: 'JPG'
    },
    'jpeg': {
        icon: 'fas fa-file-image',
        bgColor: 'from-purple-500 to-pink-500',
        badgeColor: 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300',
        type: 'JPEG'
    },
    'doc': {
        icon: 'fas fa-file-word',
        bgColor: 'from-blue-500 to-indigo-500',
        badgeColor: 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
        type: 'DOC'
    },
    'docx': {
        icon: 'fas fa-file-word',
        bgColor: 'from-blue-500 to-indigo-500',
        badgeColor: 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
        type: 'DOCX'
    },
    'note': {
        icon: 'fas fa-sticky-note',
        bgColor: 'from-blue-500 to-indigo-500',
        badgeColor: 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
        type: 'NOTE'
    }
};

// ============================================
// CHARGEMENT DES NOTES
// ============================================

async function loadNotes() {
    try {
        const response = await fetch('/chef-de-departement/informations/get-notes', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            notesData = data.notes || [];
            renderNotes();
        } else {
            console.error('Erreur API:', data.message);
            notesData = [];
            renderNotes();
            showNotification(data.message || 'Erreur lors du chargement des notes', 'error');
        }
    } catch (error) {
        console.error('Erreur loadNotes:', error);
        notesData = [];
        renderNotes();
        showNotification('Erreur lors du chargement des notes', 'error');
    }
}

// ============================================
// RENDU DU TABLEAU
// ============================================

function renderNotes() {
    const tbody = document.getElementById('notesTableBody');

    if (notesData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-lg font-medium text-gray-600 dark:text-gray-400 mb-2">Aucune note d'information</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500">Aucune note d'information n'a été publiée pour le moment</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = '';

    notesData.forEach(note => {
        const config = fileTypeConfig[note.file_type] || fileTypeConfig['note'];

        const row = document.createElement('tr');
        row.className = 'note-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors';
        row.setAttribute('data-id', note.id);

        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-r ${config.bgColor} rounded-lg flex items-center justify-center mr-3">
                        <i class="${config.icon} text-white text-sm"></i>
                    </div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white">${note.titre}</div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${note.date}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${config.badgeColor}">${config.type}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                ${note.has_file ? `
                    <button onclick="downloadNote(${note.id})" class="text-blue-500 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors" title="Télécharger">
                        <i class="fas fa-download"></i>
                    </button>
                ` : ''}
                <button onclick="viewNote(${note.id})" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" title="Visualiser">
                    <i class="fas fa-eye"></i>
                </button>
                ${note.is_owner ? `
                    <button onclick="editNote(${note.id})" class="text-orange-500 hover:text-orange-700 p-2 rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/30 transition-colors" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteNote(${note.id})" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                ` : ''}
            </td>
        `;

        tbody.appendChild(row);
    });
}

// ============================================
// MODAL MANAGEMENT
// ============================================

function showModal(modalId) {
    const modal = document.getElementById(modalId);
    const backdrop = modal.querySelector('.backdrop');
    const modalContent = modal.querySelector('.modal');

    modal.classList.remove('hidden');

    setTimeout(() => {
        backdrop.classList.add('show');
        modalContent.classList.add('show');
    }, 10);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const backdrop = modal.querySelector('.backdrop');
    const modalContent = modal.querySelector('.modal');

    backdrop.classList.remove('show');
    modalContent.classList.remove('show');

    setTimeout(() => {
        modal.classList.add('hidden');
        if (modalId === 'publishModal') {
            document.getElementById('noteForm').reset();
            document.getElementById('filePreview').classList.add('hidden');
            currentNoteId = null;
        }
    }, 300);
}

// ============================================
// GESTION DU FORMULAIRE
// ============================================

function showPublishModal() {
    currentNoteId = null;
    document.getElementById('noteForm').reset();
    document.getElementById('filePreview').classList.add('hidden');
    selectNoteType('note');
    showModal('publishModal');
}

function selectNoteType(type) {
    currentNoteType = type;
    const noteBtn = document.getElementById('noteTypeBtn');
    const fileBtn = document.getElementById('fileTypeBtn');
    const fileSection = document.getElementById('fileUploadSection');

    if (type === 'note') {
        noteBtn.className = 'flex-1 p-4 border-2 border-blue-500 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-center transition-all';
        fileBtn.className = 'flex-1 p-4 border-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/30 rounded-lg text-center transition-all';
        fileSection.classList.add('hidden');
    } else {
        noteBtn.className = 'flex-1 p-4 border-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/30 rounded-lg text-center transition-all';
        fileBtn.className = 'flex-1 p-4 border-2 border-blue-500 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-center transition-all';
        fileSection.classList.remove('hidden');
    }
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 10 * 1024 * 1024) {
            showNotification('Le fichier ne doit pas dépasser 10MB', 'error');
            event.target.value = '';
            return;
        }

        const fileName = document.getElementById('fileName');
        const fileIcon = document.getElementById('fileIcon');
        const filePreview = document.getElementById('filePreview');

        fileName.textContent = file.name;

        const extension = file.name.split('.').pop().toLowerCase();
        const iconMap = {
            'pdf': 'fas fa-file-pdf text-red-500 mr-3',
            'xlsx': 'fas fa-file-excel text-green-500 mr-3',
            'xls': 'fas fa-file-excel text-green-500 mr-3',
            'png': 'fas fa-file-image text-purple-500 mr-3',
            'jpg': 'fas fa-file-image text-purple-500 mr-3',
            'jpeg': 'fas fa-file-image text-purple-500 mr-3',
            'doc': 'fas fa-file-word text-blue-500 mr-3',
            'docx': 'fas fa-file-word text-blue-500 mr-3'
        };

        fileIcon.className = iconMap[extension] || 'fas fa-file text-gray-500 mr-3';
        filePreview.classList.remove('hidden');
    }
}

function removeFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').classList.add('hidden');
}

// ============================================
// SOUMISSION DU FORMULAIRE
// ============================================

// Gestionnaire de soumission du formulaire
function initFormHandler() {
    const form = document.getElementById('noteForm');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('titre', document.getElementById('noteTitle').value.trim());
            formData.append('message', document.getElementById('noteDescription').value.trim());

            const fileInput = document.getElementById('fileInput');
            if (fileInput.files.length > 0) {
                formData.append('document', fileInput.files[0]);
            }

            try {
                let url = '/chef-de-departement/informations/store';

                if (currentNoteId) {
                    url = `/chef-de-departement/informations/update/${currentNoteId}`;
                    formData.append('_method', 'POST');
                }

                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    closeModal('publishModal');
                    showNotification(data.message, 'success');
                    await loadNotes();
                } else {
                    showNotification(data.message || 'Une erreur est survenue', 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showNotification('Erreur lors de la sauvegarde', 'error');
            }
        });
    }
}

// ============================================
// CRUD OPERATIONS
// ============================================

async function viewNote(id) {
    try {
        const response = await fetch(`/chef-de-departement/informations/show/${id}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();

        if (data.success) {
            const note = data.notification;
            document.getElementById('viewTitle').textContent = note.titre;
            document.getElementById('viewDate').textContent = note.date;
            document.getElementById('viewContent').textContent = note.message || 'Aucune description disponible.';
            showModal('viewModal');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors du chargement', 'error');
    }
}

async function editNote(id) {
    try {
        const response = await fetch(`/chef-de-departement/informations/show/${id}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();

        if (data.success) {
            const note = data.notification;
            currentNoteId = id;

            document.getElementById('noteTitle').value = note.titre;
            document.getElementById('noteDescription').value = note.message || '';

            if (note.has_file) {
                selectNoteType('file');
                document.getElementById('fileName').textContent = note.file_name;
                document.getElementById('filePreview').classList.remove('hidden');
            } else {
                selectNoteType('note');
            }

            showModal('publishModal');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors du chargement', 'error');
    }
}

function deleteNote(id) {
    currentNoteId = id;
    const note = notesData.find(n => n.id === id);
    document.getElementById('deleteNoteTitle').textContent = note ? note.titre : 'cette note';
    showModal('deleteModal');
}

async function confirmDelete() {
    if (!currentNoteId) return;

    try {
        const response = await fetch(`/chef-de-departement/informations/delete/${currentNoteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            closeModal('deleteModal');
            showNotification(data.message, 'success');
            await loadNotes();
        } else {
            showNotification(data.message || 'Erreur lors de la suppression', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la suppression', 'error');
    }
}

function downloadNote(id) {
    window.location.href = `/chef-de-departement/informations/download/${id}`;
}

// ============================================
// NOTIFICATIONS
// ============================================

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColors = {
        'success': 'bg-green-500',
        'error': 'bg-red-500',
        'info': 'bg-blue-500'
    };
    const icons = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'info': 'fa-info-circle'
    };

    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 ${bgColors[type]} text-white`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${icons[type]} mr-2"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);
    setTimeout(() => notification.classList.remove('translate-x-full'), 10);
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => document.body.removeChild(notification), 300);
    }, 3000);
}

// ============================================
// INITIALISATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('Module Notes d\'information chargé');

    // Charger les notes au démarrage
    loadNotes();

    // Initialiser le gestionnaire de formulaire
    initFormHandler();

    // Event listener pour le bouton de confirmation de suppression
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.onclick = confirmDelete;
    }
});
