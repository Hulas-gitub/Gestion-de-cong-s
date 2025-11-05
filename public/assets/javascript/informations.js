
// Données simulées des soldes de congés
const soldesData = {
    1: { // Jean Martin - Congés payés
        total: 25,
        utilise: 7,
        restant: 18,
        type: 'Congés payés',
        couleur: 'from-blue-500 to-blue-600'
    },
    2: { // Sophie Dubois - Congé maladie
        total: 90,
        utilise: 3,
        restant: 87,
        type: 'Congés maladie',
        couleur: 'from-red-400 to-orange-500'
    },
    3: { // Marie Dupont - Congé maternité
        total: 112,
        utilise: 0,
        restant: 106,
        type: 'Congé maternité',
        couleur: 'from-pink-500 to-purple-500'
    },
    4: { // Pierre Leroux - RTT
        total: 10,
        utilise: 6,
        restant: 4,
        type: 'RTT',
        couleur: 'from-yellow-400 to-orange-500'
    }
};

// Fonction pour mettre à jour les barres de progression
function updateSoldeBar(employeeId) {
    const data = soldesData[employeeId];
    if (!data) return;
    
    const percentage = Math.round((data.restant / data.total) * 100);
    const bar = document.getElementById(`solde-bar-${employeeId}`);
    const text = document.getElementById(`solde-text-${employeeId}`);
    
    if (bar && text) {
        bar.style.width = percentage + '%';
        bar.className = `bg-gradient-to-r ${data.couleur} h-2 rounded-full transition-all duration-300`;
        text.textContent = `${data.restant}/${data.total} jours`;
    }
}

// Fonction pour animer les barres de progression au chargement
function animateSoldeBars() {
    Object.keys(soldesData).forEach(employeeId => {
        setTimeout(() => {
            updateSoldeBar(employeeId);
        }, parseInt(employeeId) * 200); // Animation décalée
    });
}

// Initialiser les barres au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    animateSoldeBars();
});

// Fonction pour simuler la mise à jour des soldes
function simulateUpdateSolde(employeeId, newUtilise) {
    if (soldesData[employeeId]) {
        soldesData[employeeId].utilise = newUtilise;
        soldesData[employeeId].restant = soldesData[employeeId].total - newUtilise;
        updateSoldeBar(employeeId);
        
        // Mettre à jour le texte des jours utilisés/restants
        const utilisElem = document.querySelector(`[data-id="${employeeId}"] .mt-1:last-child span:first-child`);
        const restantElem = document.querySelector(`[data-id="${employeeId}"] .mt-1:last-child span:last-child`);
        
        if (utilisElem && restantElem) {
            utilisElem.textContent = `Utilisé: ${newUtilise} jours`;
            restantElem.textContent = `Restant: ${soldesData[employeeId].restant} jours`;
        }
    }
}


let currentNoteType = 'note';
let currentNoteId = 0;
let notes = [
    {
        id: 1,
        title: "Politique de congés 2025",
        date: "15/09/2025",
        type: "PDF",
        hasFile: true,
        fileName: "politique_conges_2025.pdf",
        description: "Nouvelle politique de gestion des congés pour l'année 2025. Merci de prendre connaissance des nouvelles règles.",
        icon: "fas fa-file-pdf",
        bgColor: "from-red-500 to-pink-500",
        badgeColor: "bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300"
    },
    {
        id: 2,
        title: "Planning été 2025",
        date: "10/09/2025",
        type: "XLSX",
        hasFile: true,
        fileName: "planning_ete_2025.xlsx",
        description: "Planning détaillé pour la période estivale avec les remplacements prévus.",
        icon: "fas fa-file-excel",
        bgColor: "from-green-500 to-teal-500",
        badgeColor: "bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300"
    },
    {
        id: 3,
        title: "Réunion équipe - Septembre",
        date: "08/09/2025",
        type: "NOTE",
        hasFile: false,
        description: "Ordre du jour de la réunion d'équipe du mois de septembre :\n\n1. Bilan des activités\n2. Nouveaux projets\n3. Planning octobre\n4. Questions diverses",
        icon: "fas fa-sticky-note",
        bgColor: "from-blue-500 to-indigo-500",
        badgeColor: "bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300"
    },
    {
        id: 4,
        title: "Organigramme 2025",
        date: "05/09/2025",
        type: "PNG",
        hasFile: true,
        fileName: "organigramme_2025.png",
        description: "Nouvel organigramme de l'entreprise suite aux récents changements organisationnels.",
        icon: "fas fa-file-image",
        bgColor: "from-purple-500 to-pink-500",
        badgeColor: "bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300"
    }
];

function showPublishModal() {
    // Réinitialiser le formulaire
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
        const fileName = document.getElementById('fileName');
        const fileIcon = document.getElementById('fileIcon');
        const filePreview = document.getElementById('filePreview');
        
        fileName.textContent = file.name;
        
        // Icône selon le type de fichier
        const extension = file.name.split('.').pop().toLowerCase();
        switch (extension) {
            case 'pdf':
                fileIcon.className = 'fas fa-file-pdf text-red-500 mr-3';
                break;
            case 'xlsx':
            case 'xls':
                fileIcon.className = 'fas fa-file-excel text-green-500 mr-3';
                break;
            case 'png':
            case 'jpg':
            case 'jpeg':
                fileIcon.className = 'fas fa-file-image text-purple-500 mr-3';
                break;
            case 'doc':
            case 'docx':
                fileIcon.className = 'fas fa-file-word text-blue-500 mr-3';
                break;
            default:
                fileIcon.className = 'fas fa-file text-gray-500 mr-3';
        }
        
        filePreview.classList.remove('hidden');
    }
}

function removeFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').classList.add('hidden');
}

// Gestion du formulaire
document.getElementById('noteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const title = document.getElementById('noteTitle').value;
    const description = document.getElementById('noteDescription').value;
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    if (!title.trim()) {
        alert('Le titre est obligatoire');
        return;
    }
    
    // Créer la nouvelle note
    const newNote = {
        id: ++currentNoteId + notes.length,
        title: title.trim(),
        date: new Date().toLocaleDateString('fr-FR'),
        description: description.trim() || '',
        hasFile: currentNoteType === 'file' && file,
        fileName: file ? file.name : null
    };
    
    if (currentNoteType === 'file' && file) {
        // Déterminer le type et l'icône selon l'extension
        const extension = file.name.split('.').pop().toLowerCase();
        switch (extension) {
            case 'pdf':
                newNote.type = 'PDF';
                newNote.icon = 'fas fa-file-pdf';
                newNote.bgColor = 'from-red-500 to-pink-500';
                newNote.badgeColor = 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300';
                break;
            case 'xlsx':
            case 'xls':
                newNote.type = 'XLSX';
                newNote.icon = 'fas fa-file-excel';
                newNote.bgColor = 'from-green-500 to-teal-500';
                newNote.badgeColor = 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300';
                break;
            case 'png':
            case 'jpg':
            case 'jpeg':
                newNote.type = extension.toUpperCase();
                newNote.icon = 'fas fa-file-image';
                newNote.bgColor = 'from-purple-500 to-pink-500';
                newNote.badgeColor = 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300';
                break;
            case 'doc':
            case 'docx':
                newNote.type = 'DOC';
                newNote.icon = 'fas fa-file-word';
                newNote.bgColor = 'from-blue-500 to-indigo-500';
                newNote.badgeColor = 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300';
                break;
            default:
                newNote.type = 'FILE';
                newNote.icon = 'fas fa-file';
                newNote.bgColor = 'from-gray-500 to-slate-500';
                newNote.badgeColor = 'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300';
        }
    } else {
        newNote.type = 'NOTE';
        newNote.icon = 'fas fa-sticky-note';
        newNote.bgColor = 'from-blue-500 to-indigo-500';
        newNote.badgeColor = 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300';
    }
    
    // Ajouter la note au début du tableau
    notes.unshift(newNote);
    
    // Mettre à jour l'affichage
    renderNotes();
    
    // Fermer la modal
    closeModal('publishModal');
    
    // Afficher un message de succès
    showNotification('Note publiée avec succès !', 'success');
});

function renderNotes() {
    const tbody = document.getElementById('notesTableBody');
    tbody.innerHTML = '';
    
    notes.forEach(note => {
        const row = document.createElement('tr');
        row.className = 'note-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors note-added';
        row.setAttribute('data-id', note.id);
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-r ${note.bgColor} rounded-lg flex items-center justify-center mr-3">
                        <i class="${note.icon} text-white text-sm"></i>
                    </div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white">${note.title}</div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${note.date}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${note.badgeColor}">${note.type}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                ${note.hasFile ? `
                    <button onclick="downloadNote(${note.id})" class="text-blue-500 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors" title="Télécharger">
                        <i class="fas fa-download"></i>
                    </button>
                ` : ''}
                <button onclick="viewNote(${note.id})" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" title="Visualiser">
                    <i class="fas fa-eye"></i>
                </button>
                <button onclick="editNote(${note.id})" class="text-orange-500 hover:text-orange-700 p-2 rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/30 transition-colors" title="Modifier">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteNote(${note.id})" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Supprimer">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
        
        // Supprimer la classe d'animation après l'animation
        setTimeout(() => {
            row.classList.remove('note-added');
        }, 500);
    });
}

function downloadNote(id) {
    const note = notes.find(n => n.id === id);
    if (note && note.hasFile) {
        // Simulation du téléchargement
        showNotification(`Téléchargement de "${note.fileName}" en cours...`, 'info');
    }
}

function viewNote(id) {
    const note = notes.find(n => n.id === id);
    if (note) {
        document.getElementById('viewTitle').textContent = note.title;
        document.getElementById('viewDate').textContent = note.date;
        document.getElementById('viewContent').textContent = note.description || 'Aucune description disponible.';
        showModal('viewModal');
    }
}

function editNote(id) {
    const note = notes.find(n => n.id === id);
    if (note) {
        // Remplir le formulaire avec les données existantes
        document.getElementById('noteTitle').value = note.title;
        document.getElementById('noteDescription').value = note.description || '';
        
        // Ajuster le type
        if (note.hasFile) {
            selectNoteType('file');
            if (note.fileName) {
                document.getElementById('fileName').textContent = note.fileName;
                document.getElementById('filePreview').classList.remove('hidden');
            }
        } else {
            selectNoteType('note');
        }
        
        // Modifier le comportement du formulaire pour la mise à jour
        const form = document.getElementById('noteForm');
        form.onsubmit = function(e) {
            e.preventDefault();
            updateNote(id);
        };
        
        showModal('publishModal');
    }
}

function updateNote(id) {
    const note = notes.find(n => n.id === id);
    if (note) {
        note.title = document.getElementById('noteTitle').value.trim();
        note.description = document.getElementById('noteDescription').value.trim();
        
        renderNotes();
        closeModal('publishModal');
        showNotification('Note mise à jour avec succès !', 'success');
        
        // Remettre le comportement normal du formulaire
        document.getElementById('noteForm').onsubmit = function(e) {
            e.preventDefault();
            // Code de création normal...
        };
    }
}

function deleteNote(id) {
    const note = notes.find(n => n.id === id);
    if (note) {
        document.getElementById('deleteNoteTitle').textContent = note.title;
        document.getElementById('confirmDeleteBtn').onclick = function() {
            confirmDelete(id);
        };
        showModal('deleteModal');
    }
}

function confirmDelete(id) {
    const index = notes.findIndex(n => n.id === id);
    if (index !== -1) {
        const note = notes[index];
        notes.splice(index, 1);
        renderNotes();
        closeModal('deleteModal');
        showNotification(`Note "${note.title}" supprimée avec succès !`, 'success');
    }
}

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
    }, 300);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 10);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Initialiser l'affichage au chargement
document.addEventListener('DOMContentLoaded', function() {
    currentNoteId = Math.max(...notes.map(n => n.id));
    renderNotes();
});

let currentAction = '';
let currentRequestId = 0;
let bulkAction = '';
let selectedRequests = new Set();

// Données simulées des demandes
const requests = {
    1: {
        name: "Jean Martin",
        type: "Congés payés",
        startDate: "15/09/2025",
        endDate: "20/09/2025",
        duration: "5 jours",
        reason: "Congés pour vacances en famille. Voyage prévu depuis plusieurs mois.",
        pdfName: "justificatif_conges.pdf",
        avatar: "from-blue-500 to-purple-500",
        submitted: "il y a 2h"
    },
    2: {
        name: "Sophie Dubois",
        type: "Congé maladie",
        startDate: "16/09/2025",
        endDate: "18/09/2025",
        duration: "3 jours",
        reason: "Arrêt maladie suite à une grippe. Certificat médical fourni.",
        pdfName: "certificat_medical.pdf",
        avatar: "from-green-500 to-teal-500",
        submitted: "il y a 1h"
    },
    3: {
        name: "Marie Dupont",
        type: "Congé maternité",
        startDate: "01/10/2025",
        endDate: "15/01/2026",
        duration: "106 jours",
        reason: "Congé maternité pour la naissance de mon second enfant.",
        pdfName: "certificat_grossesse.pdf",
        avatar: "from-pink-500 to-rose-500",
        submitted: "il y a 3h"
    },
    4: {
        name: "Pierre Leroux",
        type: "RTT",
        startDate: "22/09/2025",
        endDate: "22/09/2025",
        duration: "1 jour",
        reason: "Récupération d'heures supplémentaires effectuées.",
        pdfName: "justificatif_rtt.pdf",
        avatar: "from-indigo-500 to-purple-500",
        submitted: "il y a 4h"
    },
    5: {
        name: "Antoine Moreau",
        type: "Congé sans solde",
        startDate: "05/10/2025",
        endDate: "05/11/2025",
        duration: "31 jours",
        reason: "Projet personnel nécessitant une absence prolongée.",
        pdfName: "demande_conge_sans_solde.pdf",
        avatar: "from-orange-500 to-red-500",
        submitted: "il y a 6h"
    },
    6: {
        name: "Lucie Bernard",
        type: "Formation",
        startDate: "25/09/2025",
        endDate: "27/09/2025",
        duration: "3 jours",
        reason: "Formation professionnelle en management d'équipe.",
        pdfName: "programme_formation.pdf",
        avatar: "from-cyan-500 to-blue-500",
        submitted: "il y a 1 jour"
    }
};

function showDetailsModal(requestId) {
    currentRequestId = requestId;
    const request = requests[requestId];
    
    // Remplir les détails
    document.getElementById('detailsName').textContent = request.name;
    document.getElementById('detailsType').textContent = request.type;
    document.getElementById('detailsStartDate').textContent = request.startDate;
    document.getElementById('detailsEndDate').textContent = request.endDate;
    document.getElementById('detailsDuration').textContent = request.duration;
    document.getElementById('detailsReason').textContent = request.reason;
    document.getElementById('pdfName').textContent = request.pdfName;
    document.getElementById('detailsAvatar').className = `w-16 h-16 bg-gradient-to-r ${request.avatar} rounded-xl flex items-center justify-center`;
    
    showModal('detailsModal');
}

function showConfirmModal(action, requestId) {
    currentAction = action;
    currentRequestId = requestId;
    const request = requests[requestId];
    
    const isApprove = action === 'approve';
    const icon = document.getElementById('confirmIcon');
    const title = document.getElementById('confirmTitle');
    const message = document.getElementById('confirmMessage');
    const actionBtn = document.getElementById('confirmActionBtn');
    
    // Configuration selon l'action
    if (isApprove) {
        icon.className = 'w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center';
        icon.innerHTML = '<i class="fas fa-check text-green-500"></i>';
        title.textContent = 'Approuver la demande';
        message.textContent = 'Êtes-vous sûr de vouloir approuver cette demande de congés ?';
        actionBtn.className = 'flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors';
        actionBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Approuver';
    } else {
        icon.className = 'w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center';
        icon.innerHTML = '<i class="fas fa-times text-red-500"></i>';
        title.textContent = 'Refuser la demande';
        message.textContent = 'Êtes-vous sûr de vouloir refuser cette demande de congés ?';
        actionBtn.className = 'flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors';
        actionBtn.innerHTML = '<i class="fas fa-times mr-2"></i>Refuser';
    }
    
    document.getElementById('confirmDetails').textContent = `${request.name} - ${request.type}`;
    document.getElementById('confirmDates').textContent = `${request.startDate} - ${request.endDate} (${request.duration})`;
    
    showModal('confirmModal');
}

function updateSelection() {
    const checkboxes = document.querySelectorAll('.request-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    const selectAllBtn = document.getElementById('selectAllBtn');
    
    selectedRequests.clear();
    let checkedCount = 0;
    
    checkboxes.forEach((checkbox, index) => {
        if (checkbox.checked) {
            selectedRequests.add(index + 1);
            checkedCount++;
        }
    });
    
    if (checkedCount > 0) {
        bulkActions.classList.remove('hidden');
        selectedCount.classList.remove('hidden');
        selectedCount.textContent = `${checkedCount} sélectionnée${checkedCount > 1 ? 's' : ''}`;
        selectAllBtn.innerHTML = '<i class="fas fa-square mr-2"></i>Désélectionner tout';
    } else {
        bulkActions.classList.add('hidden');
        selectedCount.classList.add('hidden');
        selectAllBtn.innerHTML = '<i class="fas fa-check-square mr-2"></i>Tout sélectionner';
    }
}

function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('.request-checkbox');
    const areAllSelected = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !areAllSelected;
    });
    
    updateSelection();
}

function bulkApprove() {
    if (selectedRequests.size === 0) return;
    
    bulkAction = 'approve';
    showBulkConfirmModal('approve', selectedRequests.size);
}

function bulkReject() {
    if (selectedRequests.size === 0) return;
    
    bulkAction = 'reject';
    showBulkConfirmModal('reject', selectedRequests.size);
}

function showBulkConfirmModal(action, count) {
    const isApprove = action === 'approve';
    const icon = document.getElementById('bulkConfirmIcon');
    const title = document.getElementById('bulkConfirmTitle');
    const message = document.getElementById('bulkConfirmMessage');
    const actionBtn = document.getElementById('bulkConfirmActionBtn');
    const countDisplay = document.getElementById('bulkConfirmCount');
    
    if (isApprove) {
        icon.className = 'w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center';
        icon.innerHTML = '<i class="fas fa-check text-green-500"></i>';
        title.textContent = 'Approuver les demandes';
        message.textContent = 'Êtes-vous sûr de vouloir approuver toutes les demandes sélectionnées ?';
        actionBtn.className = 'flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors';
        actionBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Approuver';
    } else {
        icon.className = 'w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center';
        icon.innerHTML = '<i class="fas fa-times text-red-500"></i>';
        title.textContent = 'Refuser les demandes';
        message.textContent = 'Êtes-vous sûr de vouloir refuser toutes les demandes sélectionnées ?';
        actionBtn.className = 'flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors';
        actionBtn.innerHTML = '<i class="fas fa-times mr-2"></i>Refuser';
    }
    
    countDisplay.textContent = `${count} demande${count > 1 ? 's' : ''} sélectionnée${count > 1 ? 's' : ''}`;
    
    showModal('bulkConfirmModal');
}

function executeAction() {
    const request = requests[currentRequestId];
    const isApprove = currentAction === 'approve';
    
    // Fermer le modal de confirmation
    closeModal('confirmModal');
    
    // Simuler un délai de traitement
    setTimeout(() => {
        // Mettre à jour l'interface
        updateRequestStatus(currentRequestId, isApprove);
        
        // Afficher la notification
        showToast(
            isApprove ? 'Demande approuvée' : 'Demande refusée',
            `La demande de ${request.name} a été ${isApprove ? 'approuvée' : 'refusée'} avec succès.`,
            isApprove ? 'success' : 'error'
        );
    }, 500);
}

function executeBulkAction() {
    const isApprove = bulkAction === 'approve';
    const count = selectedRequests.size;
    
    // Fermer le modal de confirmation
    closeModal('bulkConfirmModal');
    
    // Simuler un délai de traitement
    setTimeout(() => {
        // Mettre à jour toutes les demandes sélectionnées
        selectedRequests.forEach(requestId => {
            updateRequestStatus(requestId, isApprove);
        });
        
        // Réinitialiser la sélection
        const checkboxes = document.querySelectorAll('.request-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = false);
        updateSelection();
        
        // Afficher la notification
        showToast(
            `${count} demande${count > 1 ? 's' : ''} ${isApprove ? 'approuvée' : 'refusée'}${count > 1 ? 's' : ''}`,
            `${count} demande${count > 1 ? 's ont' : ' a'} été ${isApprove ? 'approuvée' : 'refusée'}${count > 1 ? 's' : ''} avec succès.`,
            isApprove ? 'success' : 'error'
        );
    }, 500);
}

function updateRequestStatus(requestId, approved) {
    // Trouver l'élément de demande
    const demandItem = document.querySelector(`[data-id="${requestId}"]`);
    
    // Mettre à jour le badge de statut
    const statusBadge = demandItem.querySelector('.status-badge');
    const statusText = approved ? 'Approuvée' : 'Refusée';
    const statusClass = approved ? 
        'px-4 py-2 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 text-xs font-semibold rounded-full' :
        'px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 text-xs font-semibold rounded-full';
    
    statusBadge.textContent = statusText;
    statusBadge.className = statusClass;
    
    // Masquer les boutons d'action
    const approveBtn = demandItem.querySelector('.approve-btn');
    const rejectBtn = demandItem.querySelector('.reject-btn');
    const checkbox = demandItem.querySelector('.request-checkbox');
    
    approveBtn.style.display = 'none';
    rejectBtn.style.display = 'none';
    checkbox.style.display = 'none';
    
    // Changer l'émoji de statut
    const statusEmoji = demandItem.querySelector('.text-yellow-500');
    statusEmoji.textContent = approved ? '🟢' : '🔴';
    statusEmoji.className = approved ? 'text-green-500' : 'text-red-500';
    
    // Animation et style pour les demandes traitées
    demandItem.classList.add('processed');
    demandItem.style.animation = 'fadeOut 0.3s ease-out';
    setTimeout(() => {
        demandItem.style.animation = '';
    }, 300);
}

function showModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden');
    
    // Force reflow
    modal.offsetHeight;
    
    const backdrop = modal.querySelector('.backdrop');
    const modalContent = modal.querySelector('.modal');
    
    backdrop.classList.add('show');
    modalContent.classList.add('show');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const backdrop = modal.querySelector('.backdrop');
    const modalContent = modal.querySelector('.modal');
    
    backdrop.classList.remove('show');
    modalContent.classList.remove('show');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 250);
}

function showToast(title, message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastIcon = document.getElementById('toastIcon');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    const borderElement = toast.querySelector('.border-l-4');
    
    // Configuration selon le type
    if (type === 'success') {
        toastIcon.className = 'w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0';
        toastIcon.innerHTML = '<i class="fas fa-check text-green-500"></i>';
        borderElement.style.borderLeftColor = '#10B981';
    } else {
        toastIcon.className = 'w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center flex-shrink-0';
        toastIcon.innerHTML = '<i class="fas fa-times text-red-500"></i>';
        borderElement.style.borderLeftColor = '#EF4444';
    }
    
    toastTitle.textContent = title;
    toastMessage.textContent = message;
    
    // Afficher le toast
    toast.style.transform = 'translateX(0)';
    
    // Masquer automatiquement après 4 secondes
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
    }, 4000);
}


// Fermer les modals avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('[id$="Modal"]:not(.hidden)');
        openModals.forEach(modal => {
            closeModal(modal.id);
        });
    }
});



        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.animate-slide-up');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Configuration des graphiques
            initCharts();
        });

        function initCharts() {
            // Graphique évolution des demandes
            const ctxDemandes = document.getElementById('demandesChart').getContext('2d');
            new Chart(ctxDemandes, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep'],
                    datasets: [{
                        label: 'Demandes',
                        data: [12, 19, 15, 25, 22, 18, 28, 24, 20],
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.2)'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(156, 163, 175, 0.2)'
                            }
                        }
                    }
                }
            });

            // Graphique types de congés
            const ctxTypes = document.getElementById('typesChart').getContext('2d');
            new Chart(ctxTypes, {
                type: 'doughnut',
                data: {
                    labels: ['Congés payés', 'RTT', 'Maladie', 'Formation', 'Autres'],
                    datasets: [{
                        data: [45, 25, 15, 10, 5],
                        backgroundColor: [
                            'rgb(59, 130, 246)',
                            'rgb(34, 197, 94)',
                            'rgb(239, 68, 68)',
                            'rgb(139, 92, 246)',
                            'rgb(236, 72, 153)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Graphique taux d'approbation
            const ctxApproval = document.getElementById('approvalChart').getContext('2d');
            new Chart(ctxApproval, {
                type: 'bar',
                data: {
                    labels: ['Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep'],
                    datasets: [{
                        label: 'Taux d\'approbation',
                        data: [85, 88, 82, 90, 87, 87],
                        backgroundColor: 'rgba(139, 92, 246, 0.8)',
                        borderColor: 'rgb(139, 92, 246)',
                        borderWidth: 1,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.2)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Fonctions d'interaction
        function filterRequests(status) {
            const items = document.querySelectorAll('.demand-item');
            items.forEach(item => {
                if (status === 'all') {
                    item.style.display = 'flex';
                } else {
                    const statusElement = item.querySelector('span[class*="bg-"]');
                    const statusText = statusElement.textContent.toLowerCase();
                    if (statusText.includes(status)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });
        }

        // Animation des cartes métriques
        function animateMetricCards() {
            const cards = document.querySelectorAll('.metric-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate-bounce');
                    setTimeout(() => {
                        card.classList.remove('animate-bounce');
                    }, 1000);
                }, index * 200);
            });
        }

        // Animation des barres de progression
        function animateProgressBars() {
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                const width = bar.style.width || bar.className.match(/w-(\d+)\/(\d+)/);
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.transition = 'width 1s ease-in-out';
                    if (width) {
                        bar.style.width = width;
                    }
                }, 500);
            });
        }

        // Initialiser les animations après le chargement
        setTimeout(() => {
            animateProgressBars();
        }, 1000);
  
        let currentZoom = 100;
        let selectedFile = null;

        function showUploadModal(employeeId) {
            const modal = document.getElementById('uploadJustificatifChefModal');
            const subtitle = document.getElementById('uploadChefModalSubtitle');
            
            // Simulation: récupérer le nom de l'employé
            const employeeNames = {
                1: 'Jean Dupont',
                2: 'Marie Martin',
                3: 'Pierre Bernard'
            };
            
            subtitle.textContent = `Document justificatif pour ${employeeNames[employeeId] || 'l\'employé'}`;
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.querySelector('.backdrop').classList.add('show');
                modal.querySelector('.modal').classList.add('show');
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
                resetForm();
            }, 300);
        }

        function handleJustificatifChefSelect(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            // Validation de la taille (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                alert('Le fichier est trop volumineux. Taille maximum autorisée: 10MB');
                event.target.value = '';
                return;
            }
            
            selectedFile = file;
            
            const preview = document.getElementById('justificatifChefPreview');
            const icon = document.getElementById('justificatifChefIcon');
            const name = document.getElementById('justificatifChefName');
            const size = document.getElementById('justificatifChefSize');
            const pdfSection = document.getElementById('pdfPreviewSection');
            
            // Afficher le preview
            preview.classList.remove('hidden');
            
            // Icône selon le type de fichier
            if (file.type === 'application/pdf') {
                icon.className = 'fas fa-file-pdf text-green-500 text-2xl mr-4';
                showPdfPreview(file);
            } else if (file.type.startsWith('image/')) {
                icon.className = 'fas fa-file-image text-green-500 text-2xl mr-4';
                pdfSection.classList.add('hidden');
            }
            
            name.textContent = file.name;
            size.textContent = formatFileSize(file.size);
        }

        function removeJustificatifChef() {
            const input = document.getElementById('justificatifChefInput');
            const preview = document.getElementById('justificatifChefPreview');
            const pdfSection = document.getElementById('pdfPreviewSection');
            
            input.value = '';
            preview.classList.add('hidden');
            pdfSection.classList.add('hidden');
            selectedFile = null;
        }

        function showPdfPreview(file) {
            const pdfSection = document.getElementById('pdfPreviewSection');
            const pdfFileName = document.getElementById('pdfFileName');
            
            pdfFileName.textContent = file.name;
            pdfSection.classList.remove('hidden');
            currentZoom = 100;
            updateZoomDisplay();
        }

        function simulateZoomIn() {
            if (currentZoom < 200) {
                currentZoom += 25;
                updateZoomDisplay();
                animateZoom('in');
            }
        }

        function simulateZoomOut() {
            if (currentZoom > 50) {
                currentZoom -= 25;
                updateZoomDisplay();
                animateZoom('out');
            }
        }

        function updateZoomDisplay() {
            document.getElementById('zoomLevel').textContent = currentZoom + '%';
        }

        function animateZoom(direction) {
            const pdfViewer = document.getElementById('pdfViewer').querySelector('.inline-block');
            const scale = currentZoom / 100;
            
            pdfViewer.style.transform = `scale(${scale})`;
            pdfViewer.style.transition = 'transform 0.3s ease';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function resetForm() {
            document.getElementById('justificatifChefForm').reset();
            document.getElementById('justificatifChefPreview').classList.add('hidden');
            document.getElementById('pdfPreviewSection').classList.add('hidden');
            selectedFile = null;
            currentZoom = 100;
        }

        // Gestion de la soumission du formulaire
        document.getElementById('justificatifChefForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!selectedFile) {
                alert('Veuillez sélectionner un fichier');
                return;
            }
            
            const formData = {
                file: selectedFile,
                type: document.getElementById('justificatifType').value,
                note: document.getElementById('justificatifChefNote').value,
                notifyEmployee: document.getElementById('notifyEmployee').checked
            };
            
            // Simulation de l'upload
            simulateUpload(formData);
        });

        function simulateUpload(formData) {
            const submitBtn = document.querySelector('#justificatifChefForm button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Animation de chargement
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Envoi en cours...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                alert(`Justificatif "${formData.file.name}" envoyé avec succès à l'employé!`);
                closeModal('uploadJustificatifChefModal');
                
                // Restaurer le bouton
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        }

        // Fermer le modal en cliquant sur le backdrop
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('backdrop')) {
                closeModal('uploadJustificatifChefModal');
            }
        });

        // Fermer le modal avec la touche Échap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('uploadJustificatifChefModal');
            }
        });

    
        // Fonction pour ouvrir le modal de déconnexion
        function openLogoutModal() {
            const modal = document.getElementById('logoutConfirmModal');
            modal.classList.remove('hidden');
            // Animation d'ouverture
            setTimeout(() => {
                modal.querySelector('.modal').style.opacity = '1';
                modal.querySelector('.modal').style.transform = 'scale(1)';
            }, 10);
        }

        // Fonction pour fermer le modal de déconnexion
        function closeLogoutModal() {
            const modal = document.getElementById('logoutConfirmModal');
            modal.querySelector('.modal').style.opacity = '0';
            modal.querySelector('.modal').style.transform = 'scale(0.95)';
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        // Fonction pour afficher le toast de déconnexion
        function showLogoutToast() {
            const toast = document.getElementById('logoutToast');
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
            
            // Masquer automatiquement après 3 secondes
            setTimeout(() => {
                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-full');
            }, 3000);
        }

        // Fonction d'exécution de la déconnexion
        function executeLogout() {
            // Fermer le modal
            closeLogoutModal();
            
            // Afficher le toast
            setTimeout(() => {
                showLogoutToast();
            }, 300);
            
            // Rediriger vers la page d'accueil après 2 secondes
            setTimeout(() => {
                window.location.href = '../index.html';
            }, 2000);
        }

        // Event listener pour le bouton de déconnexion
        document.getElementById('logoutBtn').addEventListener('click', function(e) {
            e.preventDefault();
            openLogoutModal();
        });

        // Fermer le modal en appuyant sur Échap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogoutModal();
            }
        });

        // Initialisation des styles du modal
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('logoutConfirmModal').querySelector('.modal');
            modal.style.opacity = '0';
            modal.style.transform = 'scale(0.95)';
            modal.style.transition = 'all 0.2s ease-out';
        });
    