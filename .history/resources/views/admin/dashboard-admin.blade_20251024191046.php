<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graxel Tech - Tableau de bord admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/javascript/animate.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
       <link rel="stylesheet" href="assets/css/dashboard.css">
    <!-- Nouveaux scripts pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1"></script>
  
</head>
<body class="h-full bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 font-poppins transition-all duration-500">
    <!-- Background Pattern -->
    <div class="fixed inset-0 bg-pattern opacity-5 pointer-events-none"></div>

    <!-- Token de notification -->
    <div id="notification-token" class="notification-token glass-effect text-white px-6 py-3 rounded-2xl shadow-2xl">
        <div class="flex items-center space-x-3">
            <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
            <span id="token-text" class="text-sm font-medium">Bienvenue, Chef de Département</span>
            <i id="token-icon" class="fas fa-check text-sm"></i>
        </div>
    </div>

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden sidebar-overlay"></div>

    <div class="flex h-full">
        <!-- Sidebar -->
        <div id="sidebar" class="fixed md:relative w-80 md:w-72 bg-white dark:bg-gray-800 shadow-2xl border-r border-gray-200 dark:border-gray-700 z-50 md:z-auto sidebar-mobile animate-slide-right transition-all duration-300">
            <div class="p-6 h-full flex flex-col">
                <!-- Close button (Mobile) -->
                <button id="close-sidebar" class="absolute top-4 right-4 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden">
                    <i class="fas fa-times text-gray-600 dark:text-gray-400"></i>
                </button>

                <!-- Logo -->
                <div class="flex items-center space-x-4 mb-10">
                    <div class="w-30 h-30 rounded-2xl flex items-center justify-center shadow-lg">
                        <img src="assets/images/logo.png" alt="Logo Graxel Tech" class="w-full h-full object-contain rounded-2xl" />
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="space-y-3 flex-1">
                    <a href="#" class="nav-item flex items-center space-x-4 px-4 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover-lift transition-all duration-300 click-scale shadow-lg">
                        <i class="fas fa-chart-pie w-5 h-5 text-lg"></i>
                        <span class="font-medium">Tableau de bord</span>
                    </a>
                    <a href="administration.html" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                 <i class="fas fa-clipboard-list w-5 h-5 text-lg"></i>
                        <span>Administration</span>
                    </a>
                    <a href="calendrier-admin.html" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                             <i class="fas fa-umbrella-beach w-5 h-5 text-lg"></i>
                        <span>Congés</span>
                    </a>
                    <a href="profile.html" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-user w-5 h-5 text-lg"></i>
                        <span>Mon profil</span>
                    </a>
                    
                     <!-- User Profile -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-lg animate-float">
                            JM
                        </div>
                        <div class="flex-2">
                            <p class="font-semibold text-gray-900 dark:text-white">Hula DJYEMBI </p>
                        </div>
                        
                         <div class="space-y-2">
                        <a href="#"  id="logoutBtn"  class="flex items-center space-x-3 text-red-600 hover:text-red-700 dark:text-red-400 text-sm hover-lift transition-all duration-200 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                            <i class="fas fa-sign-out-alt w-4 h-4"></i>
                            <span></span>
                        </a>
                    </div>
                    </div>
                   
                </div>  
                </nav>

             
            </div>
        </div>


<!-- Main Content -->
<div class="flex-1 overflow-auto custom-scrollbar">
    <!-- Header (à fleur) -->
    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg shadow-lg border-b border-gray-200/50 dark:border-gray-700/50 px-4 md:px-0 py-6 animate-fade-in sticky top-0 z-30 w-full">
        <div class="max-w-full mx-0 px-4 md:px-6 flex justify-between items-center w-full">
             <div class="flex items-center space-x-4">
                        <button id="toggle-sidebar" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden">
                            <i class="fas fa-bars text-gray-600 dark:text-gray-400"></i>
                        </button>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">tableau de bord admin</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                <i class="fas fa-users mr-2"></i>
                                <span id="current-date"></span>
                                <span>| Département Finance</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                           <!-- Bouton notifications CORRIGÉ -->
                <div class="notifications-container">
                    <button id="notifications-btn" class="p-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors relative">
                        <i class="fas fa-bell text-lg"></i>
                        <span class="notification-count">5</span>
                    </button>
                    
                    <!-- Dropdown notifications CORRIGÉ -->
                    <div id="notifications-dropdown" class="notification-dropdown">
                        <div class="dropdown-header">
                            <h3>Notifications</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">5 nouvelles notifications</p>
                        </div>
                        
                                    <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                        <div class="p-2 space-y-1">
                                            <!-- Notification Nouvelle Demande -->
                                            <div class="notification-item p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg cursor-pointer transition-colors border-l-4 border-yellow-500">
                                                <div class="flex-shrink-0">
                                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mt-1 animate-pulse"></div>
                                                </div>
                                                <div class="flex-1 min-w-0 pl-3">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">Nouvelle demande de Jean Martin</p>
                                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Congé annuel du 15/09 au 20/09</p>
                                                    <div class="flex items-center justify-between mt-2">
                                                        <span class="text-xs text-gray-400">Il y a 1h</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Notification Demande Approuvée -->
                                            <div class="notification-item p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg cursor-pointer transition-colors border-l-4 border-green-500">
                                                <div class="flex-shrink-0">
                                                    <div class="w-3 h-3 bg-green-500 rounded-full mt-1 animate-pulse"></div>
                                                </div>
                                                <div class="flex-1 min-w-0 pl-3">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">Demande approuvée</p>
                                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Congé maladie de Marie Dupont validé</p>
                                                    <div class="flex items-center justify-between mt-2">
                                                        <span class="text-xs text-gray-400">Il y a 2h</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Notification Chevauchement -->
                                            <div class="notification-item p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg cursor-pointer transition-colors border-l-4 border-red-500">
                                                <div class="flex-shrink-0">
                                                    <div class="w-3 h-3 bg-red-500 rounded-full mt-1 animate-pulse"></div>
                                                </div>
                                                <div class="flex-1 min-w-0 pl-3">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">⚠️ 3 employés absents le 18/09</p>
                                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Jean, Marie, Pierre</p>
                                                    <div class="flex items-center justify-between mt-2">
                                                        <span class="text-xs text-gray-400">Il y a 3h</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-2 border-t border-gray-200 dark:border-gray-700 text-center">
                                        <button class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                            Voir toutes les notifications
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Bouton de thème (conservé pour la cohérence visuelle, mais la logique est dans config.js) -->
                        <button id="theme-toggle" class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 click-scale hover-lift">
                            <i class="fas fa-moon dark:hidden text-gray-600 text-lg"></i>
                            <i class="fas fa-sun hidden dark:block text-yellow-400 text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
    <!-- Dashboard Content (à fleur) -->
    <div class="p-0 md:p-0 space-y-6 w-full">
  
<div class="p-0 md:p-0 space-y-6 w-full">
    <!-- Stats Cards (à fleur) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 w-full animate-slide-up" style="animation-delay: 0.2s;">
        <!-- Employés actifs -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover-lift click-scale relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <span class="text-xs bg-white/20 px-3 py-1 rounded-full">Actifs</span>
                </div>
                <div class="text-4xl font-bold mb-2">248</div>
                <div class="text-sm opacity-90">Employés actifs</div>
            </div>
        </div>

        <!-- Départements -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover-lift click-scale relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-building text-2xl"></i>
                    </div>
                    <span class="text-xs bg-white/20 px-3 py-1 rounded-full">Total</span>
                </div>
                <div class="text-4xl font-bold mb-2">12</div>
                <div class="text-sm opacity-90">Départements</div>
            </div>
        </div>

        <!-- Managers -->
        <div class="bg-gradient-to-br from-pink-500 to-pink-600 text-white p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover-lift click-scale relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-tie text-2xl"></i>
                    </div>
                    <span class="text-xs bg-white/20 px-3 py-1 rounded-full">Managers</span>
                </div>
                <div class="text-4xl font-bold mb-2">12</div>
                <div class="text-sm opacity-90">Chefs de département</div>
            </div>
        </div>

        <!-- Taux d'occupation -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover-lift click-scale relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <span class="text-xs bg-white/20 px-3 py-1 rounded-full">Présence</span>
                </div>
                <div class="text-4xl font-bold mb-2">94%</div>
                <div class="text-sm opacity-90">Taux d'occupation</div>
            </div>
        </div>

        <!-- Demandes en attente -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover-lift click-scale relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <span class="text-xs bg-white/20 px-3 py-1 rounded-full">Urgent</span>
                </div>
                <div class="text-4xl font-bold mb-2">12</div>
                <div class="text-sm opacity-90">Demandes en attente</div>
            </div>
        </div>
    </div>

    <!-- Graphiques Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full animate-slide-up" style="animation-delay: 0.3s;">
        <!-- Évolution des congés -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-blue-500/5 to-purple-500/5">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Évolution des congés</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">12 derniers mois</p>
            </div>
            <div class="p-6">
                <canvas id="congesChart" class="w-full" height="300"></canvas>
            </div>
        </div>

        <!-- Répartition par département -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-purple-500/5 to-pink-500/5">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Répartition par département</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Congés ce mois-ci</p>
            </div>
            <div class="p-6 flex items-center justify-center">
                <canvas id="departmentChart" class="w-full" height="300"></canvas>
            </div>
        </div>
    </div>

    
</div>

<script>
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
</script>
     <!-- Graphiques Statistiques -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full animate-slide-up" style="animation-delay: 0.5s;">
    <!-- Types de congés -->
    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
        <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-blue-500/5 to-green-500/5">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Types de congés</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Les plus demandés</p>
        </div>
        <div class="p-6">
            <canvas id="typesCongesChart" class="w-full" height="300"></canvas>
        </div>
    </div>

    <!-- Taux d'absentéisme -->
    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
        <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-yellow-500/5 to-green-500/5">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Taux d'absentéisme</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Par département</p>
        </div>
        <div class="p-6">
            <canvas id="absenteismeChart" class="w-full" height="300"></canvas>
        </div>
    </div>
</div>

<!-- Vue d'ensemble par département -->
<div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden w-full" style="animation-delay: 0.6s;">
    <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-indigo-500/5 to-purple-500/5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Vue d'ensemble par département</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Statistiques détaillées</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- Filtres -->
                <select id="periodeFilter" class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                    <option value="mois">Ce mois</option>
                    <option value="trimestre">Ce trimestre</option>
                    <option value="annee">Cette année</option>
                </select>
                <button class="gradient-bg text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all duration-300 hover-lift click-scale flex items-center gap-2">
                    <i class="fas fa-download"></i>
                    <span class="text-sm font-semibold">Exporter</span>
                </button>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-gray-700/50">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Département</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Chef</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Employés</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">En congé</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Demandes</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Solde moyen</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Taux absence</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200/50 dark:divide-gray-700/50">
                <!-- IT -->
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                <i class="fas fa-laptop-code text-blue-500"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">IT</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Technologie</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300">Marc Durand</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold">35</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-green-500/10 text-green-600 dark:text-green-400 font-semibold">3</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-orange-500/10 text-orange-600 dark:text-orange-400 font-semibold">2</span>
                    </td>
                    <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300 font-medium">18.5j</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-500/10 text-green-600 dark:text-green-400">5.2%</span>
                    </td>
                </tr>

                <!-- Finance -->
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center">
                                <i class="fas fa-chart-line text-purple-500"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">Finance</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Comptabilité</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300">Jean Martin</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold">28</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-green-500/10 text-green-600 dark:text-green-400 font-semibold">2</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-orange-500/10 text-orange-600 dark:text-orange-400 font-semibold">3</span>
                    </td>
                    <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300 font-medium">20.2j</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-500/10 text-green-600 dark:text-green-400">4.8%</span>
                    </td>
                </tr>

                <!-- RH -->
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-pink-500/10 flex items-center justify-center">
                                <i class="fas fa-users text-pink-500"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">RH</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Ressources Humaines</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300">Sophie Bernard</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold">18</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-green-500/10 text-green-600 dark:text-green-400 font-semibold">1</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-orange-500/10 text-orange-600 dark:text-orange-400 font-semibold">1</span>
                    </td>
                    <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300 font-medium">22.0j</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-500/10 text-green-600 dark:text-green-400">3.5%</span>
                    </td>
                </tr>

                <!-- Marketing -->
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-yellow-500/10 flex items-center justify-center">
                                <i class="fas fa-bullhorn text-yellow-500"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">Marketing</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Communication</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300">Claire Dubois</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold">22</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-green-500/10 text-green-600 dark:text-green-400 font-semibold">5</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-orange-500/10 text-orange-600 dark:text-orange-400 font-semibold">4</span>
                    </td>
                    <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300 font-medium">15.8j</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-500/10 text-yellow-600 dark:text-yellow-400">8.3%</span>
                    </td>
                </tr>

                <!-- Ventes -->
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-orange-500/10 flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-orange-500"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">Ventes</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Commercial</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300">Paul Lefèvre</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 font-semibold">30</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-green-500/10 text-green-600 dark:text-green-400 font-semibold">4</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-12 h-8 rounded-lg bg-orange-500/10 text-orange-600 dark:text-orange-400 font-semibold">2</span>
                    </td>
                    <td class="px-6 py-4 text-center text-gray-700 dark:text-gray-300 font-medium">19.2j</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-500/10 text-green-600 dark:text-green-400">6.1%</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Scripts pour les nouveaux graphiques -->
<script>
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
</script>
    </div>

            <!-- Footer -->
            <footer class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-t border-gray-200/50 dark:border-gray-700/50 p-6 mt-8">
                <div class="max-w-7xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div class="col-span-1 md:col-span-2">
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg">
                                    <img src="assets/images/logo.png" alt="Logo Graxel Tech" class="w-full h-full object-contain rounded-xl" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Graxel Tech</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Gestion des congés pour les responsables</p>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                Solution dédiée aux chefs de département pour une gestion optimale des congés et des ressources humaines.
                            </p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Liens rapides</h4>
                            <ul class="space-y-2 text-sm">
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Tableau de bord</a></li>
                                <li><a href="demandes-equipe.html" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Demandes d'équipe</a></li>
                                <li><a href="calendrier-equipe.html" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Calendrier équipe</a></li>
 <li><a href="profile.html" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Mon profil</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Support</h4>
                            <ul class="space-y-2 text-sm">
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Centre d'aide</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Contact RH</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">FAQ</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-6">
                        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                © 2025 Graxel Tech. Tous droits réservés.
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
 
    
    <!-- Modal de confirmation de déconnexion -->
    <div id="logoutConfirmModal" class="fixed inset-0 z-50 hidden">
        <div class="backdrop absolute inset-0 bg-black bg-opacity-50" onclick="closeLogoutModal()"></div>
        <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                            <i class="fas fa-sign-out-alt text-xl text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Confirmation de déconnexion</h3>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Êtes-vous sûr de vouloir vous déconnecter ?</p>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                JM
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Jean Martin</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Chef de Département Finance</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700">
                    <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors" onclick="closeLogoutModal()">
                        Annuler
                    </button>
                    <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors" onclick="executeLogout()">
                        Se déconnecter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast notification de déconnexion -->
    <div id="logoutToast" class="fixed top-4 right-4 z-50 transform translate-x-full transition-transform duration-300">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-4 border-l-4 border-l-green-500 max-w-sm">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white">Déconnexion réussie</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Vous allez être redirigé...</p>
                </div>
            </div>
        </div>
    </div>
      <script src="assets/javascript/config.js"></script>
    <script src="assets/javascript/dashboard-manager.js"></script>

</body>
</html>
