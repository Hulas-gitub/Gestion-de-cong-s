
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
                    labels: ['Congés payés', 'Maladie','Paternité', 'Maternité', 'Autres'],
                    datasets: [{
                        data: [45, 25, 15, 10, 5],
                        backgroundColor: [
                            'rgb(59, 130, 246)',
                            'rgb(239, 68, 68)',
                            'rgb(139, 92, 246)',
                            'rgb(236, 72, 153)',
                            'rgba(5, 185, 95, 1)'
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

   
        let currentAction = '';
        let currentRequestId = 0;
        
        // Données simulées des demandes
        const requests = {
            1: {
                name: "Jean Martin",
                type: "Congés payés",
                startDate: "2025-09-15",
                endDate: "2025-09-20",
                duration: "5 jours",
                reason: "Congés pour vacances en famille. Voyage prévu depuis plusieurs mois.",
                pdfName: "justificatif_conges.pdf",
                avatar: "from-blue-500 to-purple-500",
                submitted: "il y a 2h"
            },
            2: {
                name: "Sophie Dubois",
                type: "Congé maladie",
                startDate: "2025-09-16",
                endDate: "2025-09-18",
                duration: "3 jours",
                reason: "Arrêt maladie suite à une grippe. Certificat médical fourni.",
                pdfName: "certificat_medical.pdf",
                avatar: "from-green-500 to-teal-500",
                submitted: "il y a 1h"
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
                actionBtn.className = 'px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors';
                actionBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Approuver';
            } else {
                icon.className = 'w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center';
                icon.innerHTML = '<i class="fas fa-times text-red-500"></i>';
                title.textContent = 'Refuser la demande';
                message.textContent = 'Êtes-vous sûr de vouloir refuser cette demande de congés ?';
                actionBtn.className = 'px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors';
                actionBtn.innerHTML = '<i class="fas fa-times mr-2"></i>Refuser';
            }
            
            document.getElementById('confirmDetails').textContent = `${request.name} - ${request.type}`;
            document.getElementById('confirmDates').textContent = `${request.startDate} - ${request.endDate} (${request.duration})`;
            
            showModal('confirmModal');
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

        function updateRequestStatus(requestId, approved) {
            // Trouver l'élément de demande
            const demandItems = document.querySelectorAll('.demand-item');
            const demandItem = demandItems[requestId - 1];
            
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
            approveBtn.style.display = 'none';
            rejectBtn.style.display = 'none';
            
            // Changer l'émoji de statut
            const statusEmoji = demandItem.querySelector('.text-yellow-500');
            statusEmoji.textContent = approved ? '🟢' : '🔴';
            statusEmoji.className = approved ? 'text-green-500' : 'text-red-500';
            
            // Animation de fade
            demandItem.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => {
                demandItem.style.animation = '';
                demandItem.style.opacity = '0.7';
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
            
            // Configuration selon le type
            if (type === 'success') {
                toastIcon.className = 'w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center';
                toastIcon.innerHTML = '<i class="fas fa-check text-green-500"></i>';
                toast.querySelector('.border-l-4').style.borderLeftColor = '#10B981';
            } else {
                toastIcon.className = 'w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center';
                toastIcon.innerHTML = '<i class="fas fa-times text-red-500"></i>';
                toast.querySelector('.border-l-4').style.borderLeftColor = '#EF4444';
            }
            
            toastTitle.textContent = title;
            toastMessage.textContent = message;
            
            // Afficher le toast
            toast.style.transform = 'translateX(0)';
            
            // Masquer automatiquement après 3 secondes
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
            }, 3000);
        }

        function viewPDF() {
            // Simulation de l'ouverture du PDF
            showToast('PDF ouvert', 'Le justificatif a été ouvert dans un nouvel onglet.', 'success');
        }

        // Fermer les modals avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('.modal:not(.hidden)');
                openModals.forEach(modal => {
                    closeModal(modal.id);
                });
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
       