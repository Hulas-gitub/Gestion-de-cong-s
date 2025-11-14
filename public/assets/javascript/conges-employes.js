document.addEventListener('DOMContentLoaded', function() {
    let selectedFile = null;
    let modeEdition = false;
    let soldeCongesInitial = parseInt(document.getElementById('solde-apres')?.textContent) || 0;

    chargerDemandesConges();

    function chargerDemandesConges() {
        fetch('/employes/conges-employers/data', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                afficherDemandes(data.demandes);
                const availableBalance = document.getElementById('available-balance');
                if (availableBalance) {
                    availableBalance.textContent = `${data.soldeDisponible} jour${data.soldeDisponible > 1 ? 's' : ''}`;
                }
                soldeCongesInitial = data.soldeDisponible;
                const soldeApres = document.getElementById('solde-apres');
                if (soldeApres) {
                    soldeApres.textContent = data.soldeDisponible;
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message || 'Impossible de charger les demandes.',
                    confirmButtonColor: '#EF4444'
                });
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue lors du chargement des demandes.',
                confirmButtonColor: '#EF4444'
            });
        });
    }

    function afficherDemandes(demandes) {
        const container = document.querySelector('.bg-white\\/80.dark\\:bg-gray-800\\/80');
        if (!container) return;

        container.innerHTML = '';

        if (demandes.length === 0) {
            container.innerHTML = `
                <div class="p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Aucune demande</h3>
                    <p class="text-gray-500 dark:text-gray-400">Vous n'avez pas encore effectué de demande de congé.</p>
                </div>
            `;
            return;
        }

        demandes.forEach(demande => {
            const demandeItem = document.createElement('div');
            demandeItem.className = 'p-6 border-b border-gray-200/50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors demande-item';
            demandeItem.dataset.id = demande.id_demande;

            let typeClass = 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300';
            let typeIcon = 'fa-umbrella-beach';
            let typeLabel = demande.type_conge?.nom_type || 'Non spécifié';

            switch (demande.type_conge?.nom_type?.toLowerCase()) {
                case 'congé payé':
                case 'congés payés':
                    typeClass = 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300';
                    typeIcon = 'fa-umbrella-beach';
                    break;
                case 'congé maladie':
                    typeClass = 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300';
                    typeIcon = 'fa-notes-medical';
                    break;
                case 'maternité':
                    typeClass = 'bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-300';
                    typeIcon = 'fa-baby';
                    break;
                case 'paternité':
                    typeClass = 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-800 dark:text-cyan-300';
                    typeIcon = 'fa-baby-carriage';
                    break;
                case 'autre':
                    typeClass = 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300';
                    typeIcon = 'fa-calendar-alt';
                    break;
            }

            let statutClass = '';
            switch (demande.statut) {
                case 'Approuvé':
                    statutClass = 'bg-green-500 text-white';
                    break;
                case 'En attente':
                    statutClass = 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300';
                    break;
                case 'Refusé':
                    statutClass = 'bg-red-500 text-white';
                    break;
                case 'Annulé':
                    statutClass = 'bg-gray-500 text-white';
                    break;
                default:
                    statutClass = 'bg-gray-100 dark:bg-gray-700/30 text-gray-800 dark:text-gray-300';
            }

            demandeItem.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 flex-1">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center">
                            <i class="fas ${typeIcon} text-white"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <span class="px-3 py-1 ${typeClass} text-xs font-semibold rounded-full">${typeLabel}</span>
                                <span class="px-3 py-1 ${statutClass} text-xs font-semibold rounded-full">${demande.statut}</span>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">${demande.motif || 'Demande de congé'}</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400 mt-2">
                                <div class="space-y-1">
                                    <div><i class="fas fa-play mr-2 text-green-500"></i><span class="font-medium">Début:</span> ${new Date(demande.date_debut).toLocaleDateString('fr-FR')}</div>
                                    <div><i class="fas fa-stop mr-2 text-red-500"></i><span class="font-medium">Fin:</span> ${new Date(demande.date_fin).toLocaleDateString('fr-FR')}</div>
                                    <div><i class="fas fa-clock mr-2 text-blue-500"></i><span class="font-medium">Durée:</span> ${demande.nb_jours} jour(s)</div>
                                </div>
                                <div class="space-y-1">
                                    <div><i class="fas fa-calendar-plus mr-2 text-purple-500"></i><span class="font-medium">Créé le:</span> ${new Date(demande.created_at).toLocaleDateString('fr-FR')}</div>
                                    ${demande.validateur ? `<div><i class="fas fa-user-check mr-2 text-green-500"></i><span class="font-medium">Validé par:</span> ${demande.validateur.nom}</div>` : ''}
                                    ${demande.motif ? `<div><i class="fas fa-comment mr-2 text-orange-500"></i><span class="font-medium">Motif:</span> ${demande.motif.substring(0, 30)}...</div>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        ${demande.statut === 'En attente' ? `
                            <button class="btn-modifier-demande p-2 text-gray-400 hover:text-blue-500 transition-colors"
                                    data-id="${demande.id_demande}"
                                    data-type-conge="${demande.type_conge_id}"
                                    data-date-debut="${demande.date_debut}"
                                    data-date-fin="${demande.date_fin}"
                                    data-motif="${(demande.motif || '').replace(/"/g, '&quot;')}"
                                    title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-annuler-demande p-2 text-gray-400 hover:text-red-500 transition-colors"
                                    data-id="${demande.id_demande}"
                                    title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        ` : ''}
                        ${demande.statut === 'Refusé' ? `
                            <button class="btn-relancer-demande p-2 text-gray-400 hover:text-green-500 transition-colors"
                                    data-id="${demande.id_demande}"
                                    title="Relancer">
                                <i class="fas fa-redo"></i>
                            </button>
                        ` : ''}
                        ${demande.statut === 'Approuvé' ? `
                            <button class="btn-retour-anticipe p-2 text-gray-400 hover:text-orange-500 transition-colors"
                                    data-id="${demande.id_demande}"
                                    data-date-debut="${demande.date_debut}"
                                    data-date-fin="${demande.date_fin}"
                                    title="Retour anticipé">
                                <i class="fas fa-undo"></i>
                            </button>
                        ` : ''}
                        <button class="btn-voir-details p-2 text-gray-400 hover:text-blue-500 transition-colors"
                                data-demande='${JSON.stringify(demande).replace(/'/g, "&#39;")}'
                                title="Voir les détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${demande.document_justificatif ? `
                            <a href="/employes/conges/document/${demande.id_demande}"
                               target="_blank"
                               class="p-2 text-gray-400 hover:text-green-500 transition-colors"
                               title="Télécharger le document">
                                <i class="fas fa-download"></i>
                            </a>
                        ` : ''}
                    </div>
                </div>
            `;

            container.appendChild(demandeItem);
        });
    }

    const popupNouvelleDemande = document.getElementById('popup-nouvelle-demande');
    const closePopupBtn = document.getElementById('close-popup');
    const annulerDemandeBtn = document.getElementById('annuler-demande');
    const formNouvelleDemande = document.getElementById('form-nouvelle-demande');
    const btnText = document.getElementById('btn-text');

    window.ouvrirPopupNouvelleDemande = function() {
        modeEdition = false;
        document.getElementById('demande-id').value = '';
        formNouvelleDemande.reset();
        btnText.textContent = 'Soumettre';
        selectedFile = null;
        document.getElementById('selected-document-info').classList.add('hidden');
        document.getElementById('type-conge').disabled = false;
        calculerJours();
        popupNouvelleDemande.classList.remove('hidden');
    }

    function fermerPopupNouvelleDemande() {
        popupNouvelleDemande.classList.add('hidden');
        formNouvelleDemande.reset();
        selectedFile = null;
        modeEdition = false;
        document.getElementById('demande-id').value = '';
        document.getElementById('selected-document-info').classList.add('hidden');
        document.getElementById('type-conge').disabled = false;
        calculerJours();
    }

    if (closePopupBtn) closePopupBtn.addEventListener('click', fermerPopupNouvelleDemande);
    if (annulerDemandeBtn) annulerDemandeBtn.addEventListener('click', fermerPopupNouvelleDemande);

    function calculerJours() {
        const dateDebut = document.getElementById('date-debut')?.value;
        const dateFin = document.getElementById('date-fin')?.value;
        const nbJoursElement = document.getElementById('nb-jours');
        const soldeApresElement = document.getElementById('solde-apres');

        if (dateDebut && dateFin && nbJoursElement && soldeApresElement) {
            const debut = new Date(dateDebut);
            const fin = new Date(dateFin);
            let jours = 0;
            let current = new Date(debut);

            while (current <= fin) {
                const dayOfWeek = current.getDay();
                if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                    jours++;
                }
                current.setDate(current.getDate() + 1);
            }

            nbJoursElement.textContent = jours;
            soldeApresElement.textContent = Math.max(0, soldeCongesInitial - jours);
        } else if (nbJoursElement && soldeApresElement) {
            nbJoursElement.textContent = '0';
            soldeApresElement.textContent = soldeCongesInitial;
        }
    }

    const dateDebutInput = document.getElementById('date-debut');
    const dateFinInput = document.getElementById('date-fin');
    if (dateDebutInput) dateDebutInput.addEventListener('change', calculerJours);
    if (dateFinInput) dateFinInput.addEventListener('change', calculerJours);

    if (formNouvelleDemande) {
        formNouvelleDemande.addEventListener('submit', async function(e) {
            e.preventDefault();

            const demandeId = document.getElementById('demande-id').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const formData = new FormData();

            if (modeEdition && demandeId) {
                const dateDebutValue = document.getElementById('date-debut').value;
                const dateFinValue = document.getElementById('date-fin').value;
                const motifValue = document.getElementById('motif').value || '';

                formData.append('date_debut', dateDebutValue);
                formData.append('date_fin', dateFinValue);
                formData.append('motif', motifValue);

                if (selectedFile) {
                    formData.append('document_justificatif', selectedFile);
                }

                const url = `/employes/conges/${demandeId}/modifier`;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès !',
                            text: data.message,
                            confirmButtonColor: '#10B981',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        fermerPopupNouvelleDemande();
                        setTimeout(() => chargerDemandesConges(), 2100);
                    } else {
                        let errorMessage = data.message || 'Erreur de validation';
                        if (data.errors) {
                            errorMessage += ':\n\n';
                            Object.keys(data.errors).forEach(key => {
                                const errors = Array.isArray(data.errors[key]) ? data.errors[key] : [data.errors[key]];
                                errorMessage += `• ${errors.join('\n• ')}\n`;
                            });
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            html: errorMessage.replace(/\n/g, '<br>'),
                            confirmButtonColor: '#EF4444'
                        });
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Une erreur est survenue.',
                        confirmButtonColor: '#EF4444'
                    });
                }

            } else {
                const typeCongeValue = document.getElementById('type-conge').value;
                const dateDebutValue = document.getElementById('date-debut').value;
                const dateFinValue = document.getElementById('date-fin').value;
                const motifValue = document.getElementById('motif').value || '';

                if (!typeCongeValue) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Champ manquant',
                        text: 'Veuillez sélectionner un type de congé',
                        confirmButtonColor: '#EF4444'
                    });
                    return;
                }

                formData.append('type_conge_id', typeCongeValue);
                formData.append('date_debut', dateDebutValue);
                formData.append('date_fin', dateFinValue);
                formData.append('motif', motifValue);

                if (selectedFile) {
                    formData.append('document_justificatif', selectedFile);
                }

                const url = '/employes/conges/store';

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès !',
                            text: data.message,
                            confirmButtonColor: '#10B981',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        fermerPopupNouvelleDemande();
                        setTimeout(() => chargerDemandesConges(), 2100);
                    } else {
                        let errorMessage = data.message || 'Erreur de validation';
                        if (data.errors) {
                            errorMessage += ':\n\n';
                            Object.keys(data.errors).forEach(key => {
                                const errors = Array.isArray(data.errors[key]) ? data.errors[key] : [data.errors[key]];
                                errorMessage += `• ${errors.join(', ')}\n`;
                            });
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            html: errorMessage.replace(/\n/g, '<br>'),
                            confirmButtonColor: '#EF4444'
                        });
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Une erreur est survenue.',
                        confirmButtonColor: '#EF4444'
                    });
                }
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-modifier-demande')) {
            const btn = e.target.closest('.btn-modifier-demande');

            document.getElementById('demande-id').value = btn.dataset.id;
            document.getElementById('type-conge').value = btn.dataset.typeConge;
            document.getElementById('type-conge').disable = false;
            document.getElementById('date-debut').value = btn.dataset.dateDebut;
            document.getElementById('date-fin').value = btn.dataset.dateFin;
            document.getElementById('motif').value = btn.dataset.motif || '';

            modeEdition = true;
            btnText.textContent = 'Modifier';
            selectedFile = null;
            document.getElementById('selected-document-info').classList.add('hidden');

            calculerJours();
            popupNouvelleDemande.classList.remove('hidden');
        }
    });

    document.addEventListener('click', async function(e) {
        if (e.target.closest('.btn-relancer-demande')) {
            const btn = e.target.closest('.btn-relancer-demande');
            const demandeId = btn.dataset.id;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const result = await Swal.fire({
                title: 'Relancer la demande ?',
                text: "Voulez-vous renvoyer cette demande refusée pour une nouvelle validation ?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Oui, relancer',
                cancelButtonText: 'Annuler'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/employes/conges/${demandeId}/relancer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Relancée !',
                            text: data.message,
                            confirmButtonColor: '#10B981',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        setTimeout(() => chargerDemandesConges(), 2100);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: data.message,
                            confirmButtonColor: '#EF4444'
                        });
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Une erreur est survenue.',
                        confirmButtonColor: '#EF4444'
                    });
                }
            }
        }
    });

    document.addEventListener('click', async function(e) {
        if (e.target.closest('.btn-annuler-demande')) {
            const btn = e.target.closest('.btn-annuler-demande');
            const demandeId = btn.dataset.id;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const result = await Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Voulez-vous vraiment supprimer cette demande ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/employes/conges/${demandeId}/supprimer`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Supprimée !',
                            text: data.message,
                            confirmButtonColor: '#10B981',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        setTimeout(() => chargerDemandesConges(), 2100);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: data.message,
                            confirmButtonColor: '#EF4444'
                        });
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Une erreur est survenue.',
                        confirmButtonColor: '#EF4444'
                    });
                }
            }
        }
    });

    const popupRetourAnticipe = document.getElementById('popup-retour-anticipe');
    const closePopupRetour = document.getElementById('close-popup-retour');
    const annulerRetour = document.getElementById('annuler-retour');
    const formRetourAnticipe = document.getElementById('form-retour-anticipe');

    function fermerPopupRetour() {
        if (popupRetourAnticipe) popupRetourAnticipe.classList.add('hidden');
        if (formRetourAnticipe) formRetourAnticipe.reset();
    }

    if (closePopupRetour) closePopupRetour.addEventListener('click', fermerPopupRetour);
    if (annulerRetour) annulerRetour.addEventListener('click', fermerPopupRetour);

    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-retour-anticipe')) {
            const btn = e.target.closest('.btn-retour-anticipe');

            document.getElementById('retour-demande-id').value = btn.dataset.id;
            document.getElementById('retour-date-debut').value = btn.dataset.dateDebut;
            document.getElementById('retour-ancienne-date-fin').value = btn.dataset.dateFin;
            document.getElementById('retour-nouvelle-date-fin').min = btn.dataset.dateDebut;
            document.getElementById('retour-nouvelle-date-fin').max = btn.dataset.dateFin;

            popupRetourAnticipe.classList.remove('hidden');
        }
    });

    if (formRetourAnticipe) {
        formRetourAnticipe.addEventListener('submit', async function(e) {
            e.preventDefault();

            const demandeId = document.getElementById('retour-demande-id').value;
            const nouvelleDateFin = document.getElementById('retour-nouvelle-date-fin').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(`/employes/conges/${demandeId}/retour-anticipe`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ nouvelle_date_fin: nouvelleDateFin })
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Retour enregistré !',
                        text: data.message,
                        confirmButtonColor: '#10B981',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    fermerPopupRetour();
                    setTimeout(() => chargerDemandesConges(), 2100);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: data.message,
                        confirmButtonColor: '#EF4444'
                    });
                }
            } catch (error) {
                console.error('Erreur:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue.',
                    confirmButtonColor: '#EF4444'
                });
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-voir-details')) {
            const btn = e.target.closest('.btn-voir-details');

            try {
                const demandeData = JSON.parse(btn.dataset.demande);

                const dateDebut = new Date(demandeData.date_debut).toLocaleDateString('fr-FR', {
                    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                });
                const dateFin = new Date(demandeData.date_fin).toLocaleDateString('fr-FR', {
                    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                });
                const dateCreation = new Date(demandeData.created_at).toLocaleDateString('fr-FR', {
                    year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
                });

                let typeIcon = 'fa-umbrella-beach';
                let typeColor = '#3B82F6';
                switch (demandeData.type_conge?.nom_type?.toLowerCase()) {
                    case 'congé maladie':
                        typeIcon = 'fa-notes-medical';
                        typeColor = '#EF4444';
                        break;
                    case 'maternité':
                        typeIcon = 'fa-baby';
                        typeColor = '#EC4899';
                        break;
                    case 'paternité':
                        typeIcon = 'fa-baby-carriage';
                        typeColor = '#06B6D4';
                        break;
                    case 'autre':
                        typeIcon = 'fa-calendar-alt';
                        typeColor = '#A855F7';
                        break;
                }

                let statutBadge = '';
                switch (demandeData.statut) {
                    case 'Approuvé':
                        statutBadge = '<span class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full"><i class="fas fa-check-circle mr-1"></i>Approuvé</span>';
                        break;
                    case 'En attente':
                        statutBadge = '<span class="px-3 py-1 bg-orange-500 text-white text-xs font-semibold rounded-full"><i class="fas fa-clock mr-1"></i>En attente</span>';
                        break;
                    case 'Refusé':
                        statutBadge = '<span class="px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full"><i class="fas fa-times-circle mr-1"></i>Refusé</span>';
                        break;
                    case 'Annulé':
                        statutBadge = '<span class="px-3 py-1 bg-gray-500 text-white text-xs font-semibold rounded-full"><i class="fas fa-ban mr-1"></i>Annulé</span>';
                        break;
                    default:
                        statutBadge = '<span class="px-3 py-1 bg-gray-400 text-white text-xs font-semibold rounded-full">' + demandeData.statut + '</span>';
                }

                let detailsHTML = `
                    <div class="text-left">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, ${typeColor} 0%, ${typeColor}dd 100%);">
                                    <i class="fas ${typeIcon} text-white text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">${demandeData.type_conge?.nom_type || 'Non spécifié'}</h3>
                                    <p class="text-sm text-gray-500">Demande #${demandeData.id_demande}</p>
                                </div>
                            </div>
                            ${statutBadge}
                        </div>

                        <div class="space-y-4 mb-6">
                            <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg">
                                <div class="flex items-center space-x-2 mb-2">
                                    <i class="fas fa-calendar-day text-green-600"></i>
                                    <span class="font-semibold text-gray-700">Date de début</span>
                                </div>
                                <p class="text-gray-800 ml-6">${dateDebut}</p>
                            </div>

                            <div class="bg-gradient-to-r from-red-50 to-red-100 p-4 rounded-lg">
                                <div class="flex items-center space-x-2 mb-2">
                                    <i class="fas fa-calendar-check text-red-600"></i>
                                    <span class="font-semibold text-gray-700">Date de fin</span>
                                </div>
                                <p class="text-gray-800 ml-6">${dateFin}</p>
                            </div>

                            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-lg">
                                <div class="flex items-center space-x-2 mb-2">
                                    <i class="fas fa-hourglass-half text-blue-600"></i>
                                    <span class="font-semibold text-gray-700">Durée totale</span>
                                </div>
                                <p class="text-gray-800 ml-6 text-2xl font-bold">${demandeData.nb_jours} jour${demandeData.nb_jours > 1 ? 's' : ''}</p>
                            </div>
                        </div>

                        ${demandeData.motif ? `
                            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                <div class="flex items-center space-x-2 mb-2">
                                    <i class="fas fa-comment-alt text-purple-600"></i>
                                    <span class="font-semibold text-gray-700">Motif</span>
                                </div>
                                <p class="text-gray-700 ml-6 italic">"${demandeData.motif}"</p>
                            </div>
                        ` : ''}

                        ${demandeData.validateur ? `
                            <div class="bg-emerald-50 p-4 rounded-lg mb-4">
                                <div class="flex items-center space-x-2 mb-2">
                                    <i class="fas fa-user-check text-emerald-600"></i>
                                    <span class="font-semibold text-gray-700">Validé par</span>
                                </div>
                                <p class="text-gray-800 ml-6">${demandeData.validateur.nom} ${demandeData.validateur.prenom || ''}</p>
                            </div>
                        ` : ''}

                        ${demandeData.document_justificatif ? `
                            <div class="bg-indigo-50 p-4 rounded-lg mb-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-paperclip text-indigo-600"></i>
                                        <span class="font-semibold text-gray-700">Document justificatif</span>
                                    </div>
                                    <a href="/employes/conges/document/${demandeData.id_demande}"
                                       target="_blank"
                                       class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                        <i class="fas fa-download mr-2"></i>Télécharger
                                    </a>
                                </div>
                            </div>
                        ` : ''}

                        <div class="mt-6 pt-4 border-t border-gray-200 text-center">
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-info-circle mr-2"></i>
                                Demande créée le ${dateCreation}
                            </p>
                        </div>
                    </div>
                `;

                Swal.fire({
                    title: '<span style="color: #1F2937;">Détails de la demande</span>',
                    html: detailsHTML,
                    width: '700px',
                    showCloseButton: true,
                    showConfirmButton: true,
                    confirmButtonText: 'Fermer',
                    confirmButtonColor: '#3B82F6',
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl',
                        title: 'text-2xl font-bold',
                        htmlContainer: 'text-left'
                    }
                });

            } catch (error) {
                console.error('Erreur lors de l\'affichage des détails:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible d\'afficher les détails de la demande.',
                    confirmButtonColor: '#EF4444'
                });
            }
        }
    });

    const documentPopup = document.getElementById('document-popup');
    const openDocumentUploadBtn = document.getElementById('open-document-upload');
    const closeDocumentPopupBtn = document.getElementById('close-document-popup-btn');
    const documentCancelBtn = document.getElementById('document-cancel-btn');
    const documentConfirmBtn = document.getElementById('document-confirm-btn');
    const documentFileInput = document.getElementById('document-file-input');
    const documentUploadZone = document.getElementById('document-upload-zone');
    const documentPreview = document.getElementById('document-preview');

    if (openDocumentUploadBtn) {
        openDocumentUploadBtn.addEventListener('click', function() {
            if (documentPopup) {
                documentPopup.classList.remove('hidden');
                documentPopup.classList.add('flex');
                setTimeout(() => {
                    const popupContent = document.getElementById('document-popup-content');
                    if (popupContent) {
                        popupContent.style.opacity = '1';
                        popupContent.style.transform = 'scale(1)';
                    }
                }, 10);
            }
        });
    }

    function fermerPopupDocument() {
        const popupContent = document.getElementById('document-popup-content');
        if (popupContent) {
            popupContent.style.opacity = '0';
            popupContent.style.transform = 'scale(0.95)';
        }
        setTimeout(() => {
            if (documentPopup) {
                documentPopup.classList.add('hidden');
                documentPopup.classList.remove('flex');
            }
        }, 300);
    }

    if (closeDocumentPopupBtn) closeDocumentPopupBtn.addEventListener('click', fermerPopupDocument);
    if (documentCancelBtn) documentCancelBtn.addEventListener('click', fermerPopupDocument);

    if (documentUploadZone) {
        documentUploadZone.addEventListener('click', function() {
            if (documentFileInput) documentFileInput.click();
        });

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
    }

    if (documentFileInput) {
        documentFileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });
    }

    function handleFileSelect(file) {
        if (file.size > 10 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Fichier trop volumineux',
                text: 'Le fichier ne doit pas dépasser 10MB',
                confirmButtonColor: '#EF4444'
            });
            return;
        }

        selectedFile = file;
        const fileExt = file.name.split('.').pop().toLowerCase();
        let icon = 'fa-file-alt';

        if (['pdf'].includes(fileExt)) {
            icon = 'fa-file-pdf';
            if (documentPreview) {
                documentPreview.innerHTML = `<i class="fas ${icon} text-3xl"></i>`;
            }
        } else if (['doc', 'docx'].includes(fileExt)) {
            icon = 'fa-file-word';
            if (documentPreview) {
                documentPreview.innerHTML = `<i class="fas ${icon} text-3xl"></i>`;
            }
        } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
            icon = 'fa-file-image';

            const reader = new FileReader();
            reader.onload = function(e) {
                if (documentPreview) {
                    documentPreview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-full" alt="Aperçu">`;
                }
            };
            reader.readAsDataURL(file);
        } else {
            if (documentPreview) {
                documentPreview.innerHTML = `<i class="fas ${icon} text-3xl"></i>`;
            }
        }

        if (documentConfirmBtn) {
            documentConfirmBtn.disabled = false;
        }
    }

    if (documentConfirmBtn) {
        documentConfirmBtn.addEventListener('click', function() {
            if (selectedFile) {
                const docName = document.getElementById('document-name');
                const docSize = document.getElementById('document-size');
                const selectedDocInfo = document.getElementById('selected-document-info');

                if (docName) docName.textContent = selectedFile.name;
                if (docSize) docSize.textContent = formatFileSize(selectedFile.size);
                if (selectedDocInfo) selectedDocInfo.classList.remove('hidden');

                fermerPopupDocument();
            }
        });
    }

    const removeDocBtn = document.getElementById('remove-document');
    if (removeDocBtn) {
        removeDocBtn.addEventListener('click', function() {
            selectedFile = null;
            const selectedDocInfo = document.getElementById('selected-document-info');
            if (selectedDocInfo) selectedDocInfo.classList.add('hidden');
            if (documentFileInput) documentFileInput.value = '';
            if (documentPreview) documentPreview.innerHTML = '<i class="fas fa-file-alt text-3xl"></i>';
            if (documentConfirmBtn) documentConfirmBtn.disabled = true;
        });
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    if (popupNouvelleDemande) {
        popupNouvelleDemande.addEventListener('click', function(e) {
            if (e.target === popupNouvelleDemande) {
                fermerPopupNouvelleDemande();
            }
        });
    }

    if (documentPopup) {
        documentPopup.addEventListener('click', function(e) {
            if (e.target === documentPopup) {
                fermerPopupDocument();
            }
        });
    }

    if (popupRetourAnticipe) {
        popupRetourAnticipe.addEventListener('click', function(e) {
            if (e.target === popupRetourAnticipe) {
                fermerPopupRetour();
            }
        });
    }

    const notyf = new Notyf({
        duration: 3000,
        position: { x: 'right', y: 'top' },
        types: [
            {
                type: 'success',
                background: '#10b981',
                icon: { className: 'fas fa-check-circle', tagName: 'i', color: 'white' }
            },
            {
                type: 'error',
                background: '#ef4444',
                icon: { className: 'fas fa-times-circle', tagName: 'i', color: 'white' }
            },
            {
                type: 'info',
                background: '#3b82f6',
                icon: { className: 'fas fa-info-circle', tagName: 'i', color: 'white' }
            }
        ]
    });


    function updateDateTime() {
        const now = new Date();
        const currentDateElement = document.getElementById('current-date');
        if (currentDateElement) {
            currentDateElement.setAttribute('datetime', now.toISOString());
        }
    }

    initTheme();
    updateDateTime();
    setInterval(updateDateTime, 1000);


    const toggleSidebar = document.getElementById('toggle-sidebar');
    const closeSidebar = document.getElementById('close-sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const sidebar = document.getElementById('sidebar');

    if (toggleSidebar && sidebar && sidebarOverlay) {
        toggleSidebar.addEventListener('click', function() {
            sidebar.classList.add('translate-x-0');
            sidebarOverlay.classList.remove('hidden');
        });
    }

    if (closeSidebar && sidebar && sidebarOverlay) {
        closeSidebar.addEventListener('click', function() {
            sidebar.classList.remove('translate-x-0');
            sidebarOverlay.classList.add('hidden');
        });
    }

    if (sidebarOverlay && sidebar) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('translate-x-0');
            sidebarOverlay.classList.add('hidden');
        });
    }

    const btnNouvelleDemande = document.getElementById('btn-nouvelle-demande');
    if (btnNouvelleDemande) {
        btnNouvelleDemande.addEventListener('click', function() {
            window.ouvrirPopupNouvelleDemande();
        });
    }

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            if (sidebar) sidebar.classList.remove('translate-x-0');
            if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
        }
    });
});
