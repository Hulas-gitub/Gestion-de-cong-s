// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function() {

    // Variables globales
    let selectedFile = null;
    let soldeCongesInitial = 22;

    // ===== POPUP NOUVELLE DEMANDE =====
    const popupNouvelleDemande = document.getElementById('popup-nouvelle-demande');
    const closePopupBtn = document.getElementById('close-popup');
    const annulerDemandeBtn = document.getElementById('annuler-demande');
    const soumettreDemandeBtn = document.getElementById('soumettre-demande');

    // Fonction pour ouvrir le popup de nouvelle demande (à appeler depuis votre bouton principal)
    window.ouvrirPopupNouvelleDemande = function() {
        popupNouvelleDemande.classList.remove('hidden');
    }

    // Fermer le popup
    function fermerPopupNouvelleDemande() {
        popupNouvelleDemande.classList.add('hidden');
        // Réinitialiser le formulaire
        document.getElementById('type-conge').value = '';
        document.getElementById('date-debut').value = '';
        document.getElementById('date-fin').value = '';
        document.getElementById('motif').value = '';
        selectedFile = null;
        document.getElementById('selected-document-info').classList.add('hidden');
        calculerJours();
    }

    closePopupBtn.addEventListener('click', fermerPopupNouvelleDemande);
    annulerDemandeBtn.addEventListener('click', fermerPopupNouvelleDemande);

    // Soumettre la demande
    soumettreDemandeBtn.addEventListener('click', function() {
        const typeConge = document.getElementById('type-conge').value;
        const dateDebut = document.getElementById('date-debut').value;
        const dateFin = document.getElementById('date-fin').value;
        const motif = document.getElementById('motif').value;

        if (!dateDebut || !dateFin) {
            alert('Veuillez renseigner les dates de début et de fin');
            return;
        }

        // Ici vous pouvez traiter la demande
        console.log({
            typeConge,
            dateDebut,
            dateFin,
            motif,
            document: selectedFile
        });

        alert('Demande soumise avec succès !');
        fermerPopupNouvelleDemande();
    });

    // Calculer le nombre de jours
    function calculerJours() {
        const dateDebut = document.getElementById('date-debut').value;
        const dateFin = document.getElementById('date-fin').value;

        if (dateDebut && dateFin) {
            const debut = new Date(dateDebut);
            const fin = new Date(dateFin);
            const diffTime = Math.abs(fin - debut);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            document.getElementById('nb-jours').textContent = diffDays;
            document.getElementById('solde-apres').textContent = soldeCongesInitial - diffDays;
        } else {
            document.getElementById('nb-jours').textContent = '0';
            document.getElementById('solde-apres').textContent = soldeCongesInitial;
        }
    }

    document.getElementById('date-debut').addEventListener('change', calculerJours);
    document.getElementById('date-fin').addEventListener('change', calculerJours);

    // ===== POPUP DOCUMENT =====
    const documentPopup = document.getElementById('document-popup');
    const openDocumentUploadBtn = document.getElementById('open-document-upload');
    const closeDocumentPopupBtn = document.getElementById('close-document-popup-btn');
    const documentCancelBtn = document.getElementById('document-cancel-btn');
    const documentConfirmBtn = document.getElementById('document-confirm-btn');
    const documentFileInput = document.getElementById('document-file-input');
    const documentUploadZone = document.getElementById('document-upload-zone');
    const documentPreview = document.getElementById('document-preview');

    // Ouvrir le popup de document
    openDocumentUploadBtn.addEventListener('click', function() {
        documentPopup.classList.remove('hidden');
        documentPopup.classList.add('flex');
        setTimeout(() => {
            document.getElementById('document-popup-content').style.opacity = '1';
            document.getElementById('document-popup-content').style.transform = 'scale(1)';
        }, 10);
    });

    // Fermer le popup de document
    function fermerPopupDocument() {
        document.getElementById('document-popup-content').style.opacity = '0';
        document.getElementById('document-popup-content').style.transform = 'scale(0.95)';
        setTimeout(() => {
            documentPopup.classList.add('hidden');
            documentPopup.classList.remove('flex');
        }, 300);
    }

    closeDocumentPopupBtn.addEventListener('click', fermerPopupDocument);
    documentCancelBtn.addEventListener('click', fermerPopupDocument);

    // Cliquer sur la zone pour ouvrir le sélecteur de fichier
    documentUploadZone.addEventListener('click', function() {
        documentFileInput.click();
    });

    // Drag and drop
    documentUploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        documentUploadZone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/10');
    });

    documentUploadZone.addEventListener('dragleave', function() {
        documentUploadZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/10');
    });

    documentUploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        documentUploadZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/10');

        if (e.dataTransfer.files.length > 0) {
            handleFileSelect(e.dataTransfer.files[0]);
        }
    });

    // Sélection de fichier
    documentFileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });

    // Gérer la sélection de fichier
    function handleFileSelect(file) {
        // Vérifier la taille (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            alert('Le fichier est trop volumineux (max 10MB)');
            return;
        }

        selectedFile = file;

        // Mettre à jour la prévisualisation
        const fileExt = file.name.split('.').pop().toLowerCase();
        let icon = 'fa-file-alt';

        if (['pdf'].includes(fileExt)) icon = 'fa-file-pdf';
        else if (['doc', 'docx'].includes(fileExt)) icon = 'fa-file-word';
        else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) icon = 'fa-file-image';
        else if (['txt'].includes(fileExt)) icon = 'fa-file-text';

        documentPreview.innerHTML = `<i class="fas ${icon} text-3xl"></i>`;

        // Activer le bouton confirmer
        documentConfirmBtn.disabled = false;
    }

    // Confirmer le document
    documentConfirmBtn.addEventListener('click', function() {
        if (selectedFile) {
            // Afficher les informations du document
            document.getElementById('document-name').textContent = selectedFile.name;
            document.getElementById('document-size').textContent = formatFileSize(selectedFile.size);
            document.getElementById('selected-document-info').classList.remove('hidden');

            fermerPopupDocument();
        }
    });

    // Supprimer le document
    document.getElementById('remove-document').addEventListener('click', function() {
        selectedFile = null;
        document.getElementById('selected-document-info').classList.add('hidden');
        documentFileInput.value = '';
        documentPreview.innerHTML = '<i class="fas fa-file-alt text-3xl"></i>';
        documentConfirmBtn.disabled = true;
    });

    // Formater la taille du fichier
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Fermer les popups en cliquant à l'extérieur
    popupNouvelleDemande.addEventListener('click', function(e) {
        if (e.target === popupNouvelleDemande) {
            fermerPopupNouvelleDemande();
        }
    });

    documentPopup.addEventListener('click', function(e) {
        if (e.target === documentPopup) {
            fermerPopupDocument();
        }
    });

}); // Fin du DOMContentLoaded
