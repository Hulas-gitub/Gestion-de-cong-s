// ========== GESTION DES TYPES DE CONGÉ ==========

// Données
let congesData = [];
let nextId = 1;

// Variable globale pour stocker l'instance Alpine.js
let alpineModal = null;

// ========== COMPOSANT ALPINE.JS POUR LE MODAL ==========
function congeModal() {
    return {
        isOpen: false,
        editingId: null,
        modalTitle: 'Ajouter un type de congé',
        formData: {
            id: null,
            nom: '',
            couleur: '#10b981'
        },

        init() {
            // Stocker l'instance dans la variable globale
            alpineModal = this;
        },

        // Ouvrir le modal en mode ajout
        openModal() {
            this.editingId = null;
            this.modalTitle = 'Ajouter un type de congé';
            this.formData = {
                id: null,
                nom: '',
                couleur: '#10b981'
            };
            this.isOpen = true;

            // Focus sur le champ nom après ouverture
            this.$nextTick(() => {
                document.getElementById('inputNomType')?.focus();
            });
        },

        // Ouvrir le modal en mode édition
        openEditModal(conge) {
            this.editingId = conge.id_type;
            this.modalTitle = 'Modifier le type de congé';
            this.formData = {
                id: conge.id_type,
                nom: conge.nom_type,
                couleur: conge.couleur_calendrier
            };
            this.isOpen = true;

            this.$nextTick(() => {
                document.getElementById('inputNomType')?.focus();
            });
        },

        // Fermer le modal
        closeModal() {
            this.isOpen = false;
            this.editingId = null;
        },

        // Mise à jour de l'aperçu (appelé automatiquement par x-model)
        updatePreview() {
            // Rien à faire, Alpine.js gère automatiquement avec x-model et :style
        },

        // Sauvegarder le congé
        async saveConge() {
            const nom = this.formData.nom.trim();
            const couleur = this.formData.couleur;

            if (!nom) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Attention',
                        text: 'Veuillez remplir le nom du type de congé',
                        confirmButtonColor: '#3b82f6'
                    });
                } else {
                    alert('Veuillez remplir le nom du type de congé');
                }
                return;
            }

            try {
                let response;

                if (this.editingId) {
                    // Modification
                    response = await fetch(`/admin/api/types-conges/${this.editingId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            nom_type: nom,
                            couleur_calendrier: couleur
                        })
                    });
                } else {
                    // Ajout
                    response = await fetch('/admin/api/types-conges', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            nom_type: nom,
                            couleur_calendrier: couleur
                        })
                    });
                }

                const data = await response.json();

                if (data.success) {
                    // Fermer le modal
                    this.closeModal();

                    // Afficher le toast
                    showToast(
                        this.editingId ? 'Modification réussie' : 'Ajout réussi',
                        data.message
                    );

                    // Rafraîchir le tableau
                    await loadTypesConges();
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: data.message,
                            confirmButtonColor: '#3b82f6'
                        });
                    } else {
                        alert(data.message);
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Une erreur est survenue lors de la sauvegarde',
                        confirmButtonColor: '#3b82f6'
                    });
                } else {
                    alert('Une erreur est survenue');
                }
            }
        }
    }
}

// ========== INITIALISATION ==========
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Page chargée');
    loadTypesConges();
});

// ========== CHARGER LES TYPES DE CONGÉS ==========
async function loadTypesConges() {
    try {
        const response = await fetch('/admin/api/types-conges', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            congesData = data.data;
            renderTable();
        } else {
            console.error('Erreur lors du chargement:', data.message);
        }
    } catch (error) {
        console.error('Erreur:', error);
    }
}

// ========== AFFICHAGE TABLEAU ==========
function renderTable() {
    const tbody = document.getElementById('tableBodyConges');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (congesData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p>Aucun type de congé trouvé</p>
                </td>
            </tr>
        `;
        return;
    }

    congesData.forEach(conge => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors';
        tr.innerHTML = `
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: ${conge.couleur_calendrier}20;">
                        <i class="fas fa-calendar-day" style="color: ${conge.couleur_calendrier};"></i>
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white">${escapeHtml(conge.nom_type)}</span>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg border-2 border-gray-300 dark:border-gray-600" style="background-color: ${conge.couleur_calendrier};"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400 font-mono">${conge.couleur_calendrier}</span>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                    <button
                        type="button"
                        onclick="editConge(${conge.id_type})"
                        class="p-2 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors"
                        title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button
                        type="button"
                        onclick="deleteConge(${conge.id_type})"
                        class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                        title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// ========== ÉDITION ==========
function editConge(id) {
    const conge = congesData.find(c => c.id_type === id);
    if (!conge) return;

    // Utiliser la variable globale alpineModal
    if (alpineModal) {
        alpineModal.openEditModal(conge);
    } else {
        console.error('Alpine.js modal non initialisé');
    }
}

// ========== SUPPRESSION ==========
async function deleteConge(id) {
    const conge = congesData.find(c => c.id_type === id);
    if (!conge) return;

    if (typeof Swal !== 'undefined') {
        const result = await Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: `Voulez-vous supprimer "${conge.nom_type}" ?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/admin/api/types-conges/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Suppression réussie', data.message);
                    await loadTypesConges();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: data.message,
                        confirmButtonColor: '#3b82f6'
                    });
                }
            } catch (error) {
                console.error('Erreur:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue lors de la suppression',
                    confirmButtonColor: '#3b82f6'
                });
            }
        }
    } else {
        if (confirm(`Voulez-vous supprimer "${conge.nom_type}" ?`)) {
            try {
                const response = await fetch(`/admin/api/types-conges/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Suppression réussie', data.message);
                    await loadTypesConges();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            }
        }
    }
}

// ========== TOAST ==========
function showToast(title, message) {
    const toast = document.getElementById('toastSuccess');
    const toastTitle = document.getElementById('toastSuccessTitle');
    const toastMessage = document.getElementById('toastSuccessMessage');

    if (!toast || !toastTitle || !toastMessage) return;

    toastTitle.textContent = title;
    toastMessage.textContent = message;

    toast.classList.remove('translate-x-full');
    toast.classList.add('translate-x-0');

    setTimeout(() => {
        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-full');
    }, 3000);
}

// ========== UTILITAIRES ==========
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}
