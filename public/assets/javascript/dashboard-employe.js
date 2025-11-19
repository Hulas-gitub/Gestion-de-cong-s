/**
 * Dashboard Employé - Gestion dynamique
 * Récupération et affichage des données du dashboard
 */

class DashboardEmploye {
    constructor() {
        this.currentFilter = 'tous';
        this.demandes = [];
        this.notifications = [];
        this.statistiques = null;

        this.init();
    }

    async init() {
        console.log('🚀 Initialisation du Dashboard Employé...');
        await this.chargerStatistiques();
        await this.chargerDemandes();
        await this.chargerNotifications();
        this.initEventListeners();
    }

    /**
     * Charger les statistiques du dashboard
     */
    async chargerStatistiques() {
        try {
            console.log('📊 Chargement des statistiques...');
            // CORRECTION: Utiliser la bonne route
            const response = await fetch('/employes/api/tableau-de-bord/statistiques', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('Statistiques reçues:', data);

            if (data.success) {
                this.statistiques = data.statistiques;
                this.afficherStatistiques();
            } else {
                console.error('Erreur API:', data.message);
                this.afficherErreur('Impossible de charger les statistiques');
            }
        } catch (error) {
            console.error('❌ Erreur lors du chargement des statistiques:', error);
            this.afficherErreur('Erreur de connexion au serveur');
        }
    }

    /**
     * Charger l'historique des demandes
     */
    async chargerDemandes(filter = 'tous') {
        const container = document.getElementById('demandes-container');
        if (!container) {
            console.error('❌ Container demandes-container non trouvé');
            return;
        }

        // Afficher le loader
        container.innerHTML = `
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                <p class="mt-4 text-gray-500 dark:text-gray-400">Chargement des demandes...</p>
            </div>
        `;

        try {
            console.log('📋 Chargement des demandes, filtre:', filter);
            // CORRECTION: Utiliser la bonne route
            const response = await fetch(`/employes/api/tableau-de-bord/historique-demandes?filter=${filter}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('Demandes reçues:', data);

            if (data.success) {
                this.demandes = data.demandes;
                this.afficherDemandes();
            } else {
                console.error('Erreur API:', data.message);
                container.innerHTML = `
                    <div class="text-center py-12">
                        <p class="text-red-500">${data.message}</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('❌ Erreur lors du chargement des demandes:', error);
            container.innerHTML = `
                <div class="text-center py-12">
                    <p class="text-red-500">Erreur: ${error.message}</p>
                    <button onclick="location.reload()" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">
                        Réessayer
                    </button>
                </div>
            `;
        }
    }

    /**
     * Charger les notifications
     */
    async chargerNotifications(limit = 10) {
        const container = document.getElementById('notifications-container');
        if (!container) {
            console.error('❌ Container notifications-container non trouvé');
            return;
        }

        container.innerHTML = `
            <div class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500"></div>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Chargement...</p>
            </div>
        `;

        try {
            console.log('🔔 Chargement des notifications...');
            // CORRECTION: Utiliser la bonne route
            const response = await fetch(`/employes/api/tableau-de-bord/notifications?limit=${limit}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('Notifications reçues:', data);

            if (data.success) {
                this.notifications = data.notifications;
                this.afficherNotifications();
            } else {
                console.error('Erreur API:', data.message);
                container.innerHTML = `<p class="text-center text-sm text-gray-500">${data.message}</p>`;
            }
        } catch (error) {
            console.error('❌ Erreur lors du chargement des notifications:', error);
            container.innerHTML = `
                <p class="text-center text-sm text-red-500">Erreur: ${error.message}</p>
            `;
        }
    }

    /**
     * Afficher les statistiques
     */
    afficherStatistiques() {
        if (!this.statistiques) {
            console.warn('⚠️ Aucune statistique à afficher');
            return;
        }

        console.log('✅ Affichage des statistiques');
        const stats = this.statistiques;

        // Équipe du département
        const equipeElement = document.querySelector('[data-stat="equipe"]');
        if (equipeElement) {
            equipeElement.textContent = stats.equipe_departement.total;
        }

        // Demandes refusées
        const refusesElement = document.querySelector('[data-stat="refuses"]');
        const refusesVariationElement = document.querySelector('[data-stat="refuses-variation"]');
        if (refusesElement) {
            refusesElement.textContent = stats.demandes_refusees.mois_courant;
        }
        if (refusesVariationElement) {
            const variation = stats.demandes_refusees.pourcentage;
            const tendance = stats.demandes_refusees.tendance;
            refusesVariationElement.textContent = `${variation > 0 ? '+' : ''}${variation}% vs mois dernier`;
            refusesVariationElement.className = this.getVariationClass(tendance, true);
        }

        // Demandes approuvées
        const approuvesElement = document.querySelector('[data-stat="approuves"]');
        const approuvesVariationElement = document.querySelector('[data-stat="approuves-variation"]');
        if (approuvesElement) {
            approuvesElement.textContent = stats.demandes_approuvees.mois_courant;
        }
        if (approuvesVariationElement) {
            const variation = stats.demandes_approuvees.pourcentage;
            const tendance = stats.demandes_approuvees.tendance;
            approuvesVariationElement.textContent = `${variation > 0 ? '+' : ''}${variation}% vs mois dernier`;
            approuvesVariationElement.className = this.getVariationClass(tendance);
        }

        // En attente
        const enAttenteElement = document.querySelector('[data-stat="en-attente"]');
        const enAttenteDelaiElement = document.querySelector('[data-stat="en-attente-delai"]');
        if (enAttenteElement) {
            enAttenteElement.textContent = stats.en_attente.total;
        }
        if (enAttenteDelaiElement) {
            enAttenteDelaiElement.textContent = 'À traiter';
        }
    }

    /**
     * Obtenir la classe CSS pour la variation
     */
    getVariationClass(tendance, inverse = false) {
        const baseClass = 'text-xs dark:text-gray-400';

        if (tendance === 'stable') {
            return `${baseClass} text-gray-500`;
        }

        if (inverse) {
            return tendance === 'hausse'
                ? `${baseClass} text-red-500`
                : `${baseClass} text-green-500`;
        } else {
            return tendance === 'hausse'
                ? `${baseClass} text-green-500`
                : `${baseClass} text-red-500`;
        }
    }

    /**
     * Afficher les demandes
     */
    afficherDemandes() {
        const container = document.getElementById('demandes-container');
        if (!container) {
            console.error('❌ Container non trouvé');
            return;
        }

        if (this.demandes.length === 0) {
            console.log('ℹ️ Aucune demande à afficher');
            container.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Aucune demande trouvée</p>
                </div>
            `;
            return;
        }

        console.log(`✅ Affichage de ${this.demandes.length} demande(s)`);
        container.innerHTML = this.demandes.map(demande => this.getDemandeHTML(demande)).join('');
        this.attachDemandeEvents();
    }

    /**
     * Générer le HTML d'une demande
     */
    getDemandeHTML(demande) {
        const statusConfig = this.getStatusConfig(demande.statut);
        const iconConfig = this.getIconConfig(demande.type_conge);

        return `
            <div class="demand-item flex items-center justify-between p-4 ${statusConfig.bgClass} rounded-lg hover:${statusConfig.hoverClass} transition-all duration-300 cursor-pointer hover-lift w-full border border-gray-200 dark:border-gray-700" data-demande-id="${demande.id}">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 ${iconConfig.gradient} rounded-xl flex items-center justify-center">
                        <i class="${iconConfig.icon} text-white"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <h4 class="font-semibold text-gray-900 dark:text-white">${demande.type_conge}</h4>
                            <span class="text-${statusConfig.color}-500">${statusConfig.emoji}</span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">${this.formatDate(demande.date_debut)} - ${this.formatDate(demande.date_fin)} (${demande.nb_jours} jour${demande.nb_jours > 1 ? 's' : ''})</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Créé ${demande.submitted_time}</p>
                        ${demande.commentaire_refus ? `<p class="text-xs text-red-500 dark:text-red-400 font-medium mt-1">Motif: ${demande.commentaire_refus}</p>` : ''}
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-4 py-2 ${statusConfig.badgeClass} text-xs font-semibold rounded-full">${demande.statut_label}</span>
                    ${demande.has_attestation ? `
                        <button class="btn-download-attestation text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors" data-id="${demande.id}" title="Télécharger l'attestation">
                            <i class="fas fa-download"></i>
                        </button>
                    ` : ''}
                    ${demande.has_document ? `
                        <button class="btn-download-document text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors" data-id="${demande.id}" title="Télécharger le document justificatif">
                            <i class="fas fa-file-download"></i>
                        </button>
                    ` : ''}
                    <button class="btn-view-demande text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors" data-id="${demande.id}">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        `;
    }

    /**
     * Configuration des statuts
     */
    getStatusConfig(statut) {
        const configs = {
            pending: {
                color: 'yellow',
                emoji: '🟡',
                bgClass: 'bg-gray-50 dark:bg-gray-700/50',
                hoverClass: 'bg-gray-100 dark:hover:bg-gray-700',
                badgeClass: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300'
            },
            approved: {
                color: 'green',
                emoji: '🟢',
                bgClass: 'bg-green-50 dark:bg-green-900/20',
                hoverClass: 'bg-green-100 dark:hover:bg-green-900/30',
                badgeClass: 'bg-green-500 text-white'
            },
            rejected: {
                color: 'red',
                emoji: '🔴',
                bgClass: 'bg-red-50 dark:bg-red-900/20',
                hoverClass: 'bg-red-100 dark:hover:bg-red-900/30',
                badgeClass: 'bg-red-500 text-white'
            }
        };
        return configs[statut] || configs.pending;
    }

    /**
     * Configuration des icônes par type de congé
     */
    getIconConfig(typeConge) {
        const lowerType = typeConge.toLowerCase();

        if (lowerType.includes('payé') || lowerType.includes('vacances')) {
            return { icon: 'fas fa-umbrella-beach', gradient: 'bg-gradient-to-r from-blue-500 to-purple-500' };
        } else if (lowerType.includes('maladie')) {
            return { icon: 'fas fa-notes-medical', gradient: 'bg-gradient-to-r from-green-500 to-emerald-500' };
        } else if (lowerType.includes('maternité') || lowerType.includes('paternité')) {
            return { icon: 'fas fa-baby', gradient: 'bg-gradient-to-r from-pink-500 to-purple-500' };
        } else if (lowerType.includes('rtt')) {
            return { icon: 'fas fa-business-time', gradient: 'bg-gradient-to-r from-red-500 to-pink-500' };
        } else {
            return { icon: 'fas fa-calendar-alt', gradient: 'bg-gradient-to-r from-blue-500 to-cyan-500' };
        }
    }

    /**
     * Afficher les notifications
     */
    afficherNotifications() {
        const container = document.getElementById('notifications-container');
        const badge = document.getElementById('notifications-badge');

        if (!container) {
            console.error('❌ Container notifications non trouvé');
            return;
        }

        const nonLues = this.notifications.filter(n => !n.lu).length;
        console.log(`✅ Affichage de ${this.notifications.length} notification(s), ${nonLues} non lue(s)`);

        if (badge) {
            badge.textContent = nonLues;
            badge.style.display = nonLues > 0 ? 'inline-block' : 'none';
        }

        if (this.notifications.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-bell-slash text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucune notification</p>
                </div>
            `;
            return;
        }

        container.innerHTML = this.notifications.map(notif => this.getNotificationHTML(notif)).join('');
        this.attachNotificationEvents();
    }

    /**
     * Générer le HTML d'une notification
     */
    getNotificationHTML(notif) {
        const config = this.getNotificationConfig(notif.type);
        const bgClass = notif.lu ? 'bg-gray-50 dark:bg-gray-700/30' : 'bg-blue-50 dark:bg-blue-900/20';

        return `
            <div class="notification-item flex items-start space-x-3 p-3 ${bgClass} hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg cursor-pointer transition-colors border-l-4 border-${config.color}-500" data-notification-id="${notif.id}">
                <div class="flex-shrink-0">
                    ${notif.lu ?
                        `<div class="w-3 h-3 bg-gray-400 rounded-full mt-2"></div>` :
                        `<div class="w-3 h-3 bg-${config.color}-500 rounded-full mt-2 animate-pulse"></div>`
                    }
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${notif.titre} ${config.emoji}</p>
                        <div class="flex items-center space-x-1">
                            ${notif.has_document ? `
                                <button class="btn-download-notification text-blue-500 hover:text-blue-700 text-xs" data-id="${notif.id}" title="Télécharger le document">
                                    <i class="fas fa-download"></i>
                                </button>
                            ` : ''}
                            <button class="btn-dismiss-notification text-gray-400 hover:text-gray-600 text-xs" data-id="${notif.id}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">${notif.message}</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-xs text-gray-400">${notif.time_ago}</span>
                        <span class="text-xs bg-${config.color}-100 dark:bg-${config.color}-900/30 text-${config.color}-700 dark:text-${config.color}-300 px-2 py-1 rounded-full">${config.label}</span>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Configuration des notifications
     */
    getNotificationConfig(type) {
        const configs = {
            'info': { color: 'blue', emoji: '📌', label: 'Info' },
            'success': { color: 'green', emoji: '🟢', label: 'Succès' },
            'warning': { color: 'yellow', emoji: '⚠️', label: 'Attention' },
            'error': { color: 'red', emoji: '🔴', label: 'Erreur' },
            'conge_approuve': { color: 'green', emoji: '🟢', label: 'Congé' },
            'conge_refuse': { color: 'red', emoji: '🔴', label: 'Congé' },
            'conge_en_attente': { color: 'yellow', emoji: '🟡', label: 'Congé' },
            'note_service': { color: 'blue', emoji: '📑', label: 'Admin' },
            'annonce_rh': { color: 'purple', emoji: '🎉', label: 'RH' },
            'rappel': { color: 'orange', emoji: '📢', label: 'Admin' }
        };
        return configs[type] || configs['info'];
    }

    /**
     * Attacher les événements sur les demandes
     */
    attachDemandeEvents() {
        document.querySelectorAll('.btn-view-demande').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const demandeId = btn.dataset.id;
                this.voirDetailsDemande(demandeId);
            });
        });

        document.querySelectorAll('.btn-download-attestation').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const demandeId = btn.dataset.id;
                this.telechargerAttestation(demandeId);
            });
        });

        document.querySelectorAll('.btn-download-document').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const demandeId = btn.dataset.id;
                this.telechargerDocument(demandeId);
            });
        });
    }

    /**
     * Attacher les événements sur les notifications
     */
    attachNotificationEvents() {
        document.querySelectorAll('.btn-download-notification').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const notifId = btn.dataset.id;
                this.telechargerDocumentNotification(notifId);
            });
        });

        document.querySelectorAll('.btn-dismiss-notification').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.stopPropagation();
                const notifId = btn.dataset.id;
                await this.marquerNotificationLue(notifId);

                // Retirer la notification de l'affichage
                const notifElement = btn.closest('.notification-item');
                if (notifElement) {
                    notifElement.style.opacity = '0';
                    setTimeout(() => notifElement.remove(), 300);
                }

                // Mettre à jour le badge
                const badge = document.getElementById('notifications-badge');
                if (badge) {
                    const count = Math.max(0, parseInt(badge.textContent) - 1);
                    badge.textContent = count;
                    badge.style.display = count > 0 ? 'inline-block' : 'none';
                }
            });
        });
    }

    /**
     * Initialiser les événements globaux
     */
    initEventListeners() {
        console.log('🎯 Initialisation des événements');

        // Filtres des demandes
        const filterButtons = document.querySelectorAll('[data-filter]');
        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                // Réinitialiser tous les boutons
                filterButtons.forEach(b => {
                    b.classList.remove('bg-blue-100', 'dark:bg-blue-900/30', 'text-blue-600', 'dark:text-blue-300');
                    b.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300');
                });

                // Activer le bouton cliqué
                btn.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300');
                btn.classList.add('bg-blue-100', 'dark:bg-blue-900/30', 'text-blue-600', 'dark:text-blue-300');

                const filter = btn.dataset.filter;
                this.currentFilter = filter;
                console.log('🔍 Filtre changé:', filter);
                this.chargerDemandes(filter);
            });
        });

        // Bouton "Voir toutes les notifications"
        const btnVoirToutesNotifs = document.getElementById('btn-voir-toutes-notifications');
        if (btnVoirToutesNotifs) {
            btnVoirToutesNotifs.addEventListener('click', () => {
                this.chargerNotifications(null); // null = toutes les notifications
            });
        }
    }

    /**
     * Voir les détails d'une demande
     */
    async voirDetailsDemande(demandeId) {
        window.location.href = `/employes/conges-employers?demande=${demandeId}`;
    }

    /**
     * Télécharger une attestation
     * CORRECTION: Utiliser la bonne route
     */
    async telechargerAttestation(demandeId) {
        window.open(`/employes/api/tableau-de-bord/documents/attestation/${demandeId}/telecharger`, '_blank');
    }

    /**
     * Télécharger un document justificatif
     * CORRECTION: Utiliser la bonne route
     */
    async telechargerDocument(demandeId) {
        window.open(`/employes/api/tableau-de-bord/documents/justificatif/${demandeId}/telecharger`, '_blank');
    }

    /**
     * Télécharger un document de notification
     * CORRECTION: Utiliser la bonne route
     */
    async telechargerDocumentNotification(notifId) {
        window.open(`/employes/api/tableau-de-bord/documents/notification/${notifId}/telecharger`, '_blank');
    }

    /**
     * Marquer une notification comme lue
     * CORRECTION: Utiliser la bonne route
     */
    async marquerNotificationLue(notifId) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const response = await fetch(`/employes/api/tableau-de-bord/notifications/${notifId}/lire`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.content : ''
                }
            });

            if (!response.ok) {
                throw new Error('Erreur lors du marquage de la notification');
            }

            console.log('✅ Notification marquée comme lue');
        } catch (error) {
            console.error('❌ Erreur lors du marquage de la notification:', error);
        }
    }

    /**
     * Formater une date
     */
    formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
        return date.toLocaleDateString('fr-FR', options);
    }

    /**
     * Afficher une erreur
     */
    afficherErreur(message) {
        console.error('❌', message);
        // Vous pouvez ajouter une notification toast ici
    }
}

// Initialiser le dashboard au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    console.log('📄 DOM chargé, initialisation du dashboard...');
    new DashboardEmploye();
});
