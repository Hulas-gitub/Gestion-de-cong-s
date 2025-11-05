
// Données pour le graphique d'évolution des congés
const congesCtx = document.getElementById('congesChart').getContext('2d');
const congesChart = new Chart(congesCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
        datasets: [{
            label: 'Congés approuvés',
            data: [45, 52, 48, 65, 85, 120, 125, 95, 60, 55, 50, 42],
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: 'rgb(59, 130, 246)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#374151',
                    font: {
                        size: 12
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(156, 163, 175, 0.1)'
                },
                ticks: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#6B7280'
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#6B7280'
                }
            }
        }
    }
});

// Données pour le graphique en donut
const departmentCtx = document.getElementById('departmentChart').getContext('2d');
const departmentChart = new Chart(departmentCtx, {
    type: 'doughnut',
    data: {
        labels: ['IT', 'Finance', 'RH', 'Marketing', 'Ventes'],
        datasets: [{
            data: [35, 20, 15, 18, 12],
            backgroundColor: [
                'rgb(59, 130, 246)',   // Bleu
                'rgb(168, 85, 247)',   // Violet
                'rgb(236, 72, 153)',   // Rose
                'rgb(34, 197, 94)',    // Vert
                'rgb(249, 115, 22)'    // Orange
            ],
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#374151',
                    padding: 15,
                    font: {
                        size: 12
                    },
                    usePointStyle: true,
                    pointStyle: 'rect'
                }
            }
        },
        cutout: '70%'
    }
});


// Graphique Types de congés (Horizontal Bar)
const typesCongesCtx = document.getElementById('typesCongesChart').getContext('2d');
const typesCongesChart = new Chart(typesCongesCtx, {
    type: 'bar',
    data: {
        labels: ['Congé annuel', 'Maladie', 'Maternité', 'Sans solde', 'Formation'],
        datasets: [{
            label: 'Nombre de demandes',
            data: [155, 42, 8, 25, 32],
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',   // Bleu
                'rgba(239, 68, 68, 0.8)',    // Rouge
                'rgba(168, 85, 247, 0.8)',   // Violet
                'rgba(234, 179, 8, 0.8)',    // Jaune
                'rgba(34, 197, 94, 0.8)'     // Vert
            ],
            borderRadius: 8,
            borderWidth: 0
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(156, 163, 175, 0.1)'
                },
                ticks: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#6B7280'
                }
            },
            y: {
                grid: {
                    display: false
                },
                ticks: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#374151',
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});

// Graphique Taux d'absentéisme (Bar verticales)
const absenteismeCtx = document.getElementById('absenteismeChart').getContext('2d');
const absenteismeChart = new Chart(absenteismeCtx, {
    type: 'bar',
    data: {
        labels: ['IT', 'Finance', 'RH', 'Marketing', 'Ventes'],
        datasets: [{
            label: "Taux d'absence (%)",
            data: [5.2, 4.8, 3.5, 8.3, 6.1],
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',    // Vert (faible)
                'rgba(34, 197, 94, 0.8)',    // Vert (faible)
                'rgba(34, 197, 94, 0.8)',    // Vert (faible)
                'rgba(234, 179, 8, 0.8)',    // Jaune (élevé)
                'rgba(34, 197, 94, 0.8)'     // Vert (moyen)
            ],
            borderRadius: 8,
            borderWidth: 0
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
                max: 10,
                grid: {
                    color: 'rgba(156, 163, 175, 0.1)'
                },
                ticks: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#6B7280',
                    callback: function(value) {
                        return value + '%';
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') || '#374151',
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});

// Gestion du filtre de période
document.getElementById('periodeFilter').addEventListener('change', function(e) {
    console.log('Période sélectionnée:', e.target.value);
    // Ici vous pouvez ajouter la logique pour filtrer les données du tableau
});

