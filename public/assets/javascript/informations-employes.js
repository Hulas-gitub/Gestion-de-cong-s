
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
