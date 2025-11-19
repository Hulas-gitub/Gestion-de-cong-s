// ========================================
// VARIABLES GLOBALES POUR LES GRAPHIQUES
// ========================================
let demandesChart = null;
let typesChart = null;
let approvalChart = null;

// ========================================
// INITIALISATION AU CHARGEMENT DE LA PAGE
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    // Animation d'entrée
    const elements = document.querySelectorAll('.animate-slide-up');
    elements.forEach((el, index) => {
        setTimeout(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Charger toutes les données du dashboard
    loadDashboardData();
});

// ========================================
// CHARGEMENT DE TOUTES LES DONNÉES
// ========================================
function loadDashboardData() {
    loadKpiStats();
    loadEvolutionDemandes();
    loadTypesConges();
    loadTauxApprobation();
}

// ========================================
// I. CHARGEMENT DES KPIs
// ========================================
function loadKpiStats() {
    fetch('/chef-de-departement/api/dashboard/kpis')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const data = result.data;

                // Mise à jour des KPIs avec animation
                animateValue('kpi-demandes-attente', 0, data.demandes_en_attente, 1000);
                animateValue('kpi-total-employes', 0, data.total_employes, 1000);
                animateValue('kpi-demandes-validees', 0, data.demandes_validees_mois, 1000);
                animateValue('kpi-demandes-refusees', 0, data.demandes_refusees, 1000);

                // Mise à jour du nom du département
                document.getElementById('kpi-nom-departement').textContent = data.nom_departement;
            } else {
                console.error('Erreur KPIs:', result.message);
                showErrorToast('Erreur lors du chargement des statistiques');
            }
        })
        .catch(error => {
            console.error('Erreur réseau KPIs:', error);
            showErrorToast('Erreur de connexion');
        });
}

// ========================================
// II. CHARGEMENT ÉVOLUTION DES DEMANDES PAR EMPLOYÉ
// ========================================
function loadEvolutionDemandes() {
    fetch('/chef-de-departement/api/dashboard/evolution-demandes')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                createDemandesChart(result.data);
            } else {
                console.error('Erreur évolution:', result.message);
            }
        })
        .catch(error => {
            console.error('Erreur réseau évolution:', error);
        });
}

function createDemandesChart(employesData) {
    const ctx = document.getElementById('demandesChart').getContext('2d');

    // Destruction de l'ancien graphique s'il existe
    if (demandesChart) {
        demandesChart.destroy();
    }

    // Si aucun employé, afficher un message
    if (!employesData || employesData.length === 0) {
        ctx.font = '16px Arial';
        ctx.fillStyle = '#666';
        ctx.textAlign = 'center';
        ctx.fillText('Aucune donnée disponible', ctx.canvas.width / 2, ctx.canvas.height / 2);
        return;
    }

    // Extraire les labels (mois) du premier employé
    const labels = employesData[0].demandes_par_mois.map(d => d.mois);

    // Créer un dataset pour chaque employé
    const datasets = employesData.map((employe, index) => {
        return {
            label: employe.nom_complet,
            data: employe.demandes_par_mois.map(d => d.nombre),
            borderColor: getColorForIndex(index),
            backgroundColor: getColorForIndex(index, 0.1),
            tension: 0.4,
            fill: true,
            borderWidth: 2
        };
    });

    demandesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 10,
                        font: {
                            size: 11
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' demande(s)';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
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
}

// ========================================
// III. CHARGEMENT DES TYPES DE CONGÉS
// ========================================
function loadTypesConges() {
    fetch('/chef-de-departement/api/dashboard/types-conges')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                createTypesChart(result.data);
            } else {
                console.error('Erreur types:', result.message);
            }
        })
        .catch(error => {
            console.error('Erreur réseau types:', error);
        });
}

function createTypesChart(typesData) {
    const ctx = document.getElementById('typesChart').getContext('2d');

    // Destruction de l'ancien graphique s'il existe
    if (typesChart) {
        typesChart.destroy();
    }

    // Si aucune donnée
    if (!typesData || typesData.length === 0) {
        ctx.font = '16px Arial';
        ctx.fillStyle = '#666';
        ctx.textAlign = 'center';
        ctx.fillText('Aucune donnée disponible', ctx.canvas.width / 2, ctx.canvas.height / 2);
        return;
    }

    const labels = typesData.map(t => t.nom);
    const data = typesData.map(t => t.nombre);
    const colors = typesData.map(t => t.couleur);

    typesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

// ========================================
// IV. CHARGEMENT DU TAUX D'APPROBATION
// ========================================
function loadTauxApprobation() {
    fetch('/chef-de-departement/api/dashboard/taux-approbation')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                createApprovalChart(result.data);
            } else {
                console.error('Erreur taux:', result.message);
            }
        })
        .catch(error => {
            console.error('Erreur réseau taux:', error);
        });
}

function createApprovalChart(tauxData) {
    const ctx = document.getElementById('approvalChart').getContext('2d');

    // Destruction de l'ancien graphique s'il existe
    if (approvalChart) {
        approvalChart.destroy();
    }

    // Si aucune donnée
    if (!tauxData || tauxData.length === 0) {
        ctx.font = '16px Arial';
        ctx.fillStyle = '#666';
        ctx.textAlign = 'center';
        ctx.fillText('Aucune donnée disponible', ctx.canvas.width / 2, ctx.canvas.height / 2);
        return;
    }

    const labels = tauxData.map(t => t.mois);
    const data = tauxData.map(t => t.taux);

    approvalChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Taux d\'approbation',
                data: data,
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
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const index = context.dataIndex;
                            const item = tauxData[index];
                            return [
                                'Taux: ' + item.taux + '%',
                                'Approuvées: ' + item.approuvees,
                                'Total: ' + item.total
                            ];
                        }
                    }
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

// ========================================
// FONCTIONS UTILITAIRES
// ========================================

// Animation des chiffres
function animateValue(id, start, end, duration) {
    const element = document.getElementById(id);
    if (!element) return;

    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;

    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 16);
}

// Génération de couleurs pour les graphiques multi-datasets
function getColorForIndex(index, alpha = 1) {
    const colors = [
        `rgba(59, 130, 246, ${alpha})`,   // Bleu
        `rgba(239, 68, 68, ${alpha})`,    // Rouge
        `rgba(139, 92, 246, ${alpha})`,   // Violet
        `rgba(34, 197, 94, ${alpha})`,    // Vert
        `rgba(249, 115, 22, ${alpha})`,   // Orange
        `rgba(236, 72, 153, ${alpha})`,   // Rose
        `rgba(234, 179, 8, ${alpha})`,    // Jaune
        `rgba(20, 184, 166, ${alpha})`,   // Cyan
        `rgba(168, 85, 247, ${alpha})`,   // Violet clair
        `rgba(244, 63, 94, ${alpha})`     // Rouge rosé
    ];

    // Si l'index dépasse le nombre de couleurs, générer une couleur HSL
    if (index >= colors.length) {
        const hue = (index * 137.508) % 360;
        return `hsla(${hue}, 70%, 60%, ${alpha})`;
    }

    return colors[index];
}

// Toast d'erreur
function showErrorToast(message) {
    // Vous pouvez utiliser votre système de toast existant
    console.error(message);

    // Exemple simple d'alerte (à remplacer par votre système de toast)
    if (typeof showToast === 'function') {
        showToast('Erreur', message, 'error');
    } else {
        alert(message);
    }
}

// Rafraîchir les données toutes les 5 minutes
setInterval(() => {
    loadDashboardData();
}, 300000); // 300000ms = 5 minutes
