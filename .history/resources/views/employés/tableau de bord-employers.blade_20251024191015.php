<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graxel Tech - Tableau de bord Employer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="assets/javascript/config.js"></script>
    <script src="assets/javascript/animate.js"></script>
 <link rel="stylesheet" href="assets/css/style.css">
  <link rel="icon" type="image/png" href="assets/images/logo.png">
</head>
<body class="h-full bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 font-poppins transition-all duration-500">
    <!-- Background Pattern -->
    <div class="fixed inset-0 bg-pattern opacity-5 pointer-events-none"></div>
    
    <!-- Token de notification -->
    <div id="notification-token" class="notification-token glass-effect text-white px-6 py-3 rounded-2xl shadow-2xl">
        <div class="flex items-center space-x-3">
            <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
            <span id="token-text" class="text-sm font-medium">Bienvenue</span>
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
    <div>
    </div>
    
</div>
                <!-- Navigation -->
                <nav class="space-y-3 flex-1">
                    <a href="#" class="nav-item flex items-center space-x-4 px-4 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover-lift transition-all duration-300 click-scale shadow-lg">
                        <i class="fas fa-chart-pie w-5 h-5 text-lg"></i>
                        <span class="font-medium">Tableau de bord</span>
                    </a>
                    <a href="conges-employers.html" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-clipboard-list w-5 h-5 text-lg"></i>
                        <span>Mes demandes</span>
                    </a>
                    <a href="calendrier-employers.html" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-calendar-alt w-5 h-5 text-lg"></i>
                        <span>Calendrier</span>
                    </a>
                    <a href="profile.html" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-user w-5 h-5 text-lg"></i>
                        <span>Mon profil</span>
                    </a>
                    
                </nav>

                <!-- User Profile -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-lg animate-float">
                            JM
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">Jean Martin</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <a href="#"  id="logoutBtn" class="flex items-center space-x-3 text-red-600 hover:text-red-700 dark:text-red-400 text-sm hover-lift transition-all duration-200 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                            <i class="fas fa-sign-out-alt w-4 h-4"></i>
                            <span>Déconnexion</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
<!-- Main Content -->
<div class="flex-1 overflow-auto custom-scrollbar w-full p-0">
    <!-- Header (à fleur) -->
    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg shadow-lg border-b border-gray-200/50 dark:border-gray-700/50 py-6 animate-fade-in sticky top-0 z-30 w-full">
        <div class="flex justify-between items-center w-full px-4 md:px-6">
            <div class="flex items-center space-x-4">
                <button id="toggle-sidebar" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden">
                    <i class="fas fa-bars text-gray-600 dark:text-gray-400"></i>
                </button>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Tableau de bord Gestion de congés</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        <i class="fas fa-calendar-day mr-2"></i>
                        <span id="current-date"></span>
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative notifications-container">
                    <button id="notifications-btn" class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors relative">
                        <i class="fas fa-bell text-lg"></i>
                        <span class="notification-count animate-pulse">3</span>
                    </button>
                    <!-- Dropdown notifications -->
                    <div id="notifications-dropdown" class="notification-dropdown">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-900 dark:text-white">Notifications</h3>
                        </div>
                        <div class="max-h-96 overflow-y-auto custom-scrollbar">
                            <div class="p-2 space-y-1">
                                <div class="notification-item">
                                    <div class="notification-badge bg-blue-500 animate-pulse"></div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Nouvelle demande de Pierre</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Il y a 2h</p>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="notification-badge bg-green-500 animate-pulse"></div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Demande approuvée</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Il y a 1j</p>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="notification-badge bg-yellow-500 animate-pulse"></div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Rappel: Réunion demain</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Il y a 3h</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button id="theme-toggle" class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 click-scale hover-lift">
                    <i class="fas fa-moon dark:hidden text-gray-600 text-lg"></i>
                    <i class="fas fa-sun hidden dark:block text-yellow-400 text-lg"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Dashboard Content (à fleur) -->
    <div class="w-full p-0 space-y-6">
        <!-- Stats Cards (à fleur) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-0 w-full">
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-none shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover-lift transition-all duration-500 animate-slide-up overflow-hidden relative w-full">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10 pointer-events-none"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Congés restants</p>
                        <p class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">25</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">jours disponibles</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center shadow-lg animate-float">
                        <i class="fas fa-calendar-check text-white text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-none shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-6 hover-lift transition-all duration-500 animate-slide-up overflow-hidden relative w-full" style="animation-delay: 0.1s;">
                <div class="absolute inset-0 bg-gradient-to-r from-orange-500/10 to-red-500/10 pointer-events-none"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Congés pris</p>
                        <p class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-2">5</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">jours cette année</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center shadow-lg animate-float" style="animation-delay: 0.5s;">
                        <i class="fas fa-clock text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Grid (à fleur) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-0 w-full">
            <!-- Mes demandes récentes -->
            <div class="lg:col-span-2 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-none shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden w-full" style="animation-delay: 0.2s;">
                <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-blue-500/5 to-purple-500/5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Mes demandes récentes</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Vos dernières demandes de congés</p>
                        </div>
                        <!-- Filtres -->
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded-full hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                Tous
                            </button>
                            <button class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                Approuvé
                            </button>
                            <button class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                En attente
                            </button>
                            <button class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                Refusé
                            </button>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Demande 1 -->
                    <div class="demand-item flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-none hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 cursor-pointer hover-lift w-full">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-umbrella-beach text-white"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Congés payés</h4>
                                    <span class="text-yellow-500">🟡</span>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">2024-01-15 - 2024-01-19 (5 jours)</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Créé le 10 Jan 2024</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-4 py-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 text-xs font-semibold rounded-full">En attente</span>
                            <button class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Demande 2 -->
                    <div class="demand-item flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-none hover:bg-green-100 dark:hover:bg-green-900/30 transition-all duration-300 cursor-pointer hover-lift w-full">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-notes-medical text-white"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Congé maladie</h4>
                                    <span class="text-green-500">🟢</span>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">2024-01-10 - 2024-01-12 (3 jours)</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Créé le 08 Jan 2024</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-4 py-2 bg-green-500 text-white text-xs font-semibold rounded-full">Validé</span>
                            <button class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Demande 3 -->
                    <div class="demand-item flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 rounded-none hover:bg-red-100 dark:hover:bg-red-900/30 transition-all duration-300 cursor-pointer hover-lift w-full">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-pink-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-business-time text-white"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">RTT</h4>
                                    <span class="text-red-500">🔴</span>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">2024-01-08 - 2024-01-08 (1 jour)</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Créé le 05 Jan 2024</p>
                                <p class="text-xs text-red-500 dark:text-red-400 font-medium">Motif: Surcharge de travail</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-4 py-2 bg-red-500 text-white text-xs font-semibold rounded-full">Refusé</span>
                            <button class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Demande 4 -->
                    <div class="demand-item flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-none hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all duration-300 cursor-pointer hover-lift w-full">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-business-time text-white"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Congé maternité</h4>
                                    <span class="text-blue-500">🟣</span>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">2024-01-08 - 2024-01-08 (1 jour)</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Créé le 05 Jan 2024</p>
                                <p class="text-xs text-blue-500 dark:text-blue-400 font-medium">En cours de traitement</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-4 py-2 bg-blue-500 text-white text-xs font-semibold rounded-full">En attente</span>
                            <button class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Sidebar Améliorée -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-none shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden w-full" style="animation-delay: 0.3s;">
                <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-purple-500/5 to-pink-500/5">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Notifications</h3>
                        <div class="flex items-center space-x-2">
                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">10</span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Dernières mises à jour</p>
                </div>
                <div class="p-4 space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                    <!-- Notification Congé Approuvé -->
                    <div class="notification-item flex items-start space-x-3 animate-fade-in p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-none cursor-pointer transition-colors border-l-4 border-green-500">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-green-500 rounded-full mt-2 animate-pulse"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">Demande approuvée 🟢</p>
                                <div class="flex items-center space-x-1">
                                    <button class="text-blue-500 hover:text-blue-700 text-xs">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <button class="text-gray-400 hover:text-gray-600 text-xs">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Votre congé du 15 au 20 Sept a été validé par votre chef de service</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-400">Il y a 1j</span>
                                <span class="text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-1 rounded-full">Congé</span>
                            </div>
                        </div>
                    </div>
                    <!-- Notification Congé Refusé -->
                    <div class="notification-item flex items-start space-x-3 animate-fade-in p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-none cursor-pointer transition-colors border-l-4 border-red-500">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-red-500 rounded-full mt-2 animate-pulse"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">Demande refusée 🔴</p>
                                <div class="flex items-center space-x-1">
                                    <button class="text-blue-500 hover:text-blue-700 text-xs">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <button class="text-gray-400 hover:text-gray-600 text-xs">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Congé du 10-12 Oct refusé. Motif: surcharge de travail</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-400">Il y a 2j</span>
                                <span class="text-xs bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 px-2 py-1 rounded-full">Congé</span>
                            </div>
                        </div>
                    </div>
                    <!-- Notification En attente -->
                    <div class="notification-item flex items-start space-x-3 animate-fade-in p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-none cursor-pointer transition-colors border-l-4 border-yellow-500">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mt-2 animate-pulse"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">En cours de traitement 🟡</p>
                                <button class="text-gray-400 hover:text-gray-600 text-xs">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Votre demande du 5-7 Nov est en cours d'examen</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-400">Il y a 3h</span>
                                <span class="text-xs bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 px-2 py-1 rounded-full">Congé</span>
                            </div>
                        </div>
                    </div>
                    <!-- Notification Administrative -->
                    <div class="notification-item flex items-start space-x-3 animate-fade-in p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-none cursor-pointer transition-colors border-l-4 border-blue-500">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mt-2 animate-pulse"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">Note de service 📑</p>
                                <div class="flex items-center space-x-1">
                                    <button class="text-blue-500 hover:text-blue-700 text-xs">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <button class="text-gray-400 hover:text-gray-600 text-xs">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Nouvelle procédure de demande de congés publiée</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-400">Il y a 5h</span>
                                <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded-full">Admin</span>
                            </div>
                        </div>
                    </div>
                    <!-- Notification RH -->
                    <div class="notification-item flex items-start space-x-3 animate-fade-in p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-none cursor-pointer transition-colors border-l-4 border-purple-500">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-purple-500 rounded-full mt-2 animate-pulse"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">Annonce RH 🎉</p>
                                <button class="text-gray-400 hover:text-gray-600 text-xs">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Formation interne disponible : Gestion du temps</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-400">Il y a 1j</span>
                                <span class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 px-2 py-1 rounded-full">RH</span>
                            </div>
                        </div>
                    </div>
                    <!-- Notification Réunion -->
                    <div class="notification-item flex items-start space-x-3 animate-fade-in p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-none cursor-pointer transition-colors border-l-4 border-orange-500">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-orange-500 rounded-full mt-2 animate-pulse"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">Rappel réunion 📢</p>
                                <button class="text-gray-400 hover:text-gray-600 text-xs">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Réunion du personnel demain 20 Sept à 10h</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-400">Il y a 6h</span>
                                <span class="text-xs bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 px-2 py-1 rounded-full">Admin</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Bouton voir toutes les notifications -->
                <div class="p-4 border-t border-gray-200/50 dark:border-gray-700/50">
                    <button class="w-full text-center text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                        Voir toutes les notifications
                    </button>
                </div>
            </div>
        </div>

        <!-- Calendrier rapide et Statistiques -->
        <div class="grid grid-cols-1 lg:grid-cols-1 gap-0 w-full">
            <!-- Statistiques -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-none shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden w-full" style="animation-delay: 0.6s;">
                <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-yellow-500/5 to-orange-500/5">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Statistiques de l'équipe</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Vue d'ensemble de votre département</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Équipe du département</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">25</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Demandes refusées</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Ce mois-ci</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">3</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">-12% vs mois dernier</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Demandes approuvées</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Ce mois-ci</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">47</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">+12% vs mois dernier</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-hourglass-half text-white"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">En attente</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">À traiter</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">8</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Délai moyen: 2j</p>
                        </div>
                    </div>
            
                </div>
            </div>
        </div>

        <footer class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-t border-gray-200/50 dark:border-gray-700/50 p-6 mt-0 w-full">
            <div class="max-w-full mx-0 px-4 md:px-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg">
                                <img src="assets/images/logo.png" alt="Logo Graxel Tech" class="w-full h-full object-contain rounded-xl" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Graxel Tech</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Gestion des congés simplifiée</p>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                            Une solution moderne et intuitive pour la gestion des demandes de congés et des ressources humaines.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Liens rapides</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Tableau de bord</a></li>
                            <li><a href="conges-employers.html" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Mes demandes</a></li>
                            <li><a href="calendrier-employers.html" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Calendrier</a></li>
                            <li><a href="profile.html" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Mon profil</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Support</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Centre d'aide</a></li>
                            <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Contact</a></li>
                            <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">FAQ</a></li>
                            <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Documentation</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-6">
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            © 2025 Graxel Tech. Tous droits réservés.
                        </p>
                        <div class="flex space-x-6 text-sm">
                            <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Politique de confidentialité</a>
                            <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Conditions d'utilisation</a>
                            <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Mentions légales</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
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

<script src="assets/javascript/logout.js"></script>
<script src="assets/javascript/config.js"></script>
</body>
</html>