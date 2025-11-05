 
        // Gestion des événements du popup
        const btnNouvelleDemande = document.getElementById('btn-nouvelle-demande');
        const popup = document.getElementById('popup-nouvelle-demande');
        const closePopup = document.getElementById('close-popup');
        const annulerDemande = document.getElementById('annuler-demande');
        const soumettredemande = document.getElementById('soumettre-demande');

        // Fonction pour ouvrir le popup
        function ouvrirPopup() {
            popup.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Empêche le scroll du body
        }

        // Fonction pour fermer le popup
        function fermerPopup() {
            popup.classList.add('hidden');
            document.body.style.overflow = 'auto'; // Restore le scroll du body
        }

        // Event listeners
        btnNouvelleDemande?.addEventListener('click', ouvrirPopup);
        closePopup?.addEventListener('click', fermerPopup);
        annulerDemande?.addEventListener('click', fermerPopup);

        // Fermer en cliquant sur le backdrop
        popup?.addEventListener('click', function(e) {
            if (e.target === popup) {
                fermerPopup();
            }
        });

        // Fermer avec la touche Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !popup.classList.contains('hidden')) {
                fermerPopup();
            }
        });

        // Gestion du téléversement de document (simulé)
        document.getElementById('open-document-upload')?.addEventListener('click', function() {
            // Simulation d'un fichier sélectionné
            const documentInfo = document.getElementById('selected-document-info');
            const documentName = document.getElementById('document-name');
            const documentSize = document.getElementById('document-size');
            
            documentName.textContent = 'certificat_medical.pdf';
            documentSize.textContent = '245 KB';
            documentInfo.classList.remove('hidden');
        });

        // Supprimer le document
        document.getElementById('remove-document')?.addEventListener('click', function() {
            document.getElementById('selected-document-info').classList.add('hidden');
        });

    

// Gestion du popup de téléversement de documents
document.addEventListener('DOMContentLoaded', function() {
    const openDocumentUpload = document.getElementById('open-document-upload');
    const documentUploadPopup = document.getElementById('document-upload-popup');
    const documentPopupContent = document.getElementById('document-popup-content');
    const closeDocumentPopupBtn = document.getElementById('close-document-popup-btn');
    const documentCancelBtn = document.getElementById('document-cancel-btn');
    const documentConfirmBtn = document.getElementById('document-confirm-btn');
    const documentUploadZone = document.getElementById('document-upload-zone');
    const documentFileInput = document.getElementById('document-file-input');
    const documentPreview = document.getElementById('document-preview');
    
    const selectedDocumentInfo = document.getElementById('selected-document-info');
    const documentName = document.getElementById('document-name');
    const documentSize = document.getElementById('document-size');
    const removeDocument = document.getElementById('remove-document');
    
    let selectedFile = null;
    
    // Ouvrir le popup
    openDocumentUpload.addEventListener('click', function() {
        documentUploadPopup.classList.remove('hidden');
        documentUploadPopup.classList.add('flex');
        setTimeout(() => {
            documentPopupContent.classList.remove('scale-95', 'opacity-0');
            documentPopupContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    });
    
    // Fermer le popup
    function closeDocumentPopup() {
        documentPopupContent.classList.remove('scale-100', 'opacity-100');
        documentPopupContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            documentUploadPopup.classList.add('hidden');
            documentUploadPopup.classList.remove('flex');
        }, 300);
        
        // Reset
        selectedFile = null;
        documentFileInput.value = '';
        documentConfirmBtn.disabled = true;
        resetPreview();
    }
    
    closeDocumentPopupBtn.addEventListener('click', closeDocumentPopup);
    documentCancelBtn.addEventListener('click', closeDocumentPopup);
    
    // Clic sur la zone de téléversement
    documentUploadZone.addEventListener('click', function() {
        documentFileInput.click();
    });
    
    // Glisser-déposer
    documentUploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        documentUploadZone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/10');
    });
    
    documentUploadZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        documentUploadZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/10');
    });
    
    documentUploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        documentUploadZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/10');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelection(files[0]);
        }
    });
    
    // Sélection de fichier
    documentFileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            handleFileSelection(e.target.files[0]);
        }
    });
    
    function handleFileSelection(file) {
        // Vérification de la taille (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            alert('Le fichier est trop volumineux. Taille maximale : 10MB');
            return;
        }
        
        // Vérification du type de fichier
        const allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'text/plain'
        ];
        
        if (!allowedTypes.includes(file.type)) {
            alert('Type de fichier non autorisé. Formats acceptés : PDF, DOC, DOCX, JPG, PNG, GIF, TXT');
            return;
        }
        
        selectedFile = file;
        updatePreview(file);
        documentConfirmBtn.disabled = false;
    }
    
    function updatePreview(file) {
        const fileType = file.type;
        let icon = 'fas fa-file-alt';
        
        if (fileType.includes('pdf')) icon = 'fas fa-file-pdf';
        else if (fileType.includes('word') || fileType.includes('document')) icon = 'fas fa-file-word';
        else if (fileType.includes('image')) icon = 'fas fa-file-image';
        else if (fileType.includes('text')) icon = 'fas fa-file-alt';
        
        documentPreview.innerHTML = `<i class="${icon} text-3xl"></i>`;
    }
    
    function resetPreview() {
        documentPreview.innerHTML = '<i class="fas fa-file-alt text-3xl"></i>';
    }
    
    // Confirmer le téléversement
    documentConfirmBtn.addEventListener('click', function() {
        if (selectedFile) {
            // Afficher les infos du document dans le popup principal
            documentName.textContent = selectedFile.name;
            documentSize.textContent = formatFileSize(selectedFile.size);
            selectedDocumentInfo.classList.remove('hidden');
            
            // Mettre à jour le texte du bouton
            openDocumentUpload.innerHTML = `
                <i class="fas fa-file-check text-green-600 dark:text-green-400"></i>
                <span class="text-green-700 dark:text-green-300">Document sélectionné</span>
            `;
            
            closeDocumentPopup();
        }
    });
    
    // Supprimer le document sélectionné
    removeDocument.addEventListener('click', function() {
        selectedFile = null;
        selectedDocumentInfo.classList.add('hidden');
        openDocumentUpload.innerHTML = `
            <i class="fas fa-file-upload text-gray-600 dark:text-gray-400"></i>
            <span class="text-gray-700 dark:text-gray-300">Téléverser un document</span>
        `;
    });
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
