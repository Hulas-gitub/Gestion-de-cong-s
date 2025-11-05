<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graxel Tech - Calendrier Employer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="assets/javascript/animate.js"></script>
    <link rel="stylesheet" href="assets/css/calendrier.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <style>
        .holiday-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 8px;
            height: 8px;
            background: #EF4444;
            border-radius: 50%;
            animation: pulse 2s infinite;
            z-index: 10;
        }

        .calendar-day.holiday {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%) !important;
            border: 2px solid #ef4444 !important;
            position: relative;
            min-height: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px 4px;
        }

        .dark .calendar-day.holiday {
            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%) !important;
            border-color: #dc2626 !important;
            color: #fecaca !important;
        }

        .progress-bar-container {
            height: 8px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            transition: width 0.5s ease;
            border-radius: 9999px;
        }

        .holiday-tooltip {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            margin-bottom: 8px;
            z-index: 100;
            display: none;
        }

        .holiday-tooltip:after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }

        @media (max-width: 767px) {
            .calendar-day.holiday .holiday-label {
                display: none !important;
            }
        }

        .sick-leave {
            background: linear-gradient(135deg, #fecaca 0%, #f87171 100%) !important;
            border: 2px solid #ef4444 !important;
            position: relative;
        }

        .dark .sick-leave {
            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%) !important;
            border-color: #dc2626 !important;
            color: #fecaca !important;
        }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 font-poppins transition-all duration-500">
    <div class="fixed inset-0 bg-pattern opacity-5 pointer-events-none"></div>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden sidebar-overlay"></div>
    
    <div id="popup-nouvelle-demande" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 animate-slide-up">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Nouvelle demande de congé</h3>
                <button id="close-popup" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-times text-gray-600 dark:text-gray-400"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type de congé</label>
                    <select id="type-conge" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white">
                        <option value="">Congés payés</option>
                        <option value="conges-payes">Congés payés</option>
                        <option value="rtt">RTT</option>
                        <option value="conge-maladie">Congé maladie</option>
                        <option value="conge-formation">Congé formation</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date de début</label>
                    <input type="date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date de fin</label>
                    <input type="date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motif (optionnel)</label>
                    <textarea rows="3" placeholder="Décrivez brièvement le motif de votre demande..." class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white resize-none"></textarea>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl">
                    <div class="flex items-center space-x-2 text-blue-600 dark:text-blue-400">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <p class="text-sm font-medium">Nombre de jours : 0 jours</p>
                            <p class="text-sm">Solde après demande : 22 jours</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex space-x-4">
                <button id="annuler-demande" class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">
                    Annuler
                </button>
                <button id="soumettre-demande" class="flex-1 px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors font-medium">
                    Soumettre
                </button>
            </div>
        </div>
    </div>
    
    <div class="flex h-full">
        <div id="sidebar" class="fixed md:relative w-80 md:w-72 bg-white dark:bg-gray-800 shadow-2xl border-r border-gray-200 dark:border-gray-700 z-50 md:z-auto sidebar-mobile animate-slide-right transition-all duration-300">
            <div class="p-6 h-full flex flex-col">
                <button id="close-sidebar" class="absolute top-4 right-4 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden">
                    <i class="fas fa-times text-gray-600 dark:text-gray-400"></i>
                </button>
                <div class="flex items-center space-x-4 mb-10">
                    <div class="w-30 h-50 rounded-2xl flex items-center justify-center shadow-lg">
                        <img src="assets/images/logo.png" alt="Logo Graxel Tech" class="w-full h-full object-contain rounded-2xl" />
                    </div>
                </div>
                <nav class="space-y-3 flex-1">
                    <a href="tableau de bord-employers.html" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-chart-pie w-5 h-5 text-lg"></i>
                        <span class="font-medium">Tableau de bord</span>
                    </a>
                    <a href="conges-employers.html" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-calendar-alt w-5 h-5 text-lg"></i>
                        <span>Mes demandes</span>
                    </a>
                    <a href="#" class="nav-item flex items-center space-x-4 px-4 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover-lift transition-all duration-300 click-scale shadow-lg">
                        <i class="fas fa-file-alt w-5 h-5 text-lg"></i>
                        <span class="font-medium">Calendrier</span>
                    </a>
                    <a href="profile.html" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-user w-5 h-5 text-lg"></i>
                        <span>Mon profil</span>
                    </a>
                    
                </nav>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-lg animate-float">
                            JM
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">Jean Martin</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Vous faites partie du departement Finance</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <a href="#" id="logoutBtn" class="flex items-center space-x-3 text-red-600 hover:text-red-700 dark:text-red-400 text-sm hover-lift transition-all duration-200 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                            <i class="fas fa-sign-out-alt w-4 h-4"></i>
                            <span>Déconnexion</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex-1 overflow-auto custom-scrollbar">
            <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg shadow-lg border-b border-gray-200/50 dark:border-gray-700/50 px-4 md:px-8 py-6 animate-fade-in sticky top-0 z-30">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <button id="toggle-sidebar" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden">
                            <i class="fas fa-bars text-gray-600 dark:text-gray-400"></i>
                        </button>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Calendrier des congés</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                <i class="fas fa-calendar-day mr-2"></i>
                                <span id="current-date"></span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <div class="notifications-container">
                                <button id="notifications-btn" class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors relative">
                                    <i class="fas fa-bell text-lg"></i>
                                    <span class="notification-count animate-pulse">3</span>
                                </button>

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
                        </div>
                        <button id="theme-toggle" class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 click-scale hover-lift">
                            <i class="fas fa-moon dark:hidden text-gray-600 text-lg"></i>
                            <i class="fas fa-sun hidden dark:block text-yellow-400 text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="p-4 md:p-8 space-y-8">
              
                <!-- NOUVELLE SECTION: Solde de Congés Cumulés -->
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl shadow-xl border border-blue-200/50 dark:border-gray-600/50 p-8 animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-calculator text-white text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Solde de Congés</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Calcul automatique de vos droits</p>
                            </div>
                        </div>
                        <div class="hidden md:block">
                            <div class="px-4 py-2 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                                <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">Mis à jour</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Mois Travaillés -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-calendar-check text-green-600 dark:text-green-400"></i>
                                </div>
                                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Mois Travaillés</h3>
                            </div>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white mb-2" id="months-worked">12 mois</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Période complète de travail</p>
                        </div>

                        <!-- Congés Cumulés -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-plus-circle text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Congés Cumulés</h3>
                            </div>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2" id="accumulated-leave">24 jours</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">2 jours × <span id="months-multiplier">12</span> mois</p>
                        </div>

                        <!-- Congés Consommés -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-minus-circle text-orange-600 dark:text-orange-400"></i>
                                </div>
                                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Congés Consommés</h3>
                            </div>
                            <p class="text-3xl font-bold text-orange-600 dark:text-orange-400 mb-2" id="consumed-leave">5 jours</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Congés déductibles uniquement</p>
                        </div>
                    </div>

                 <!-- Solde Disponible (Grande carte en bas) -->
<div class="mt-6 bg-gradient-to-r from-blue-400 to-purple-600 rounded-xl p-8 shadow-2xl">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-white/80 text-sm font-medium mb-2">SOLDE DISPONIBLE</p>
            <p class="text-5xl font-bold text-white mb-2" id="available-balance">19 jours</p>
            <p class="text-white/90 text-sm">
                <i class="fas fa-info-circle mr-2"></i>
                Vous pouvez effectuer une demande dans la limite de ce solde
            </p>
        </div>
        <div class="hidden md:block">
            <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-umbrella-beach text-white text-4xl"></i>
            </div>
        </div>
    </div>
</div>

                    <!-- Détails des congés non déductibles -->
                    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-shield-alt text-white text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Congés non déductibles du solde</h4>
                                <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <span><strong>Congé maternité:</strong> Droits naturels protégés</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        <span><strong>Congé paternité:</strong> Droits naturels protégés</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-times-circle text-red-500"></i>
                                        <span><strong>Congés annuels & convenances personnelles:</strong> Déductibles du solde</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques des Congés -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-6">
                    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-6 animate-slide-up hover-lift" style="animation-delay: 0.2s;">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-briefcase-medical text-red-600 dark:text-red-400 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Congé Maladie</h3>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="sick-leave-balance">20 jours</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Consommés</span>
                                <span class="font-semibold text-gray-900 dark:text-white" id="sick-leave-used">0 / 20 jours</span>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar" id="sick-leave-progress" style="width: 0%; background: linear-gradient(90deg, #ef4444, #f87171);"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span id="sick-leave-remaining">Restant: 20 jours</span>
                                <span id="sick-leave-percentage">0%</span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="p-4 md:p-8 space-y-8">
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-6 animate-slide-up">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex items-center space-x-4">
                            <button id="prev-month" class="calendar-navigation p-3 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-all duration-300">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <div class="text-center">
                                <h2 id="current-month" class="text-2xl font-bold text-gray-900 dark:text-white"></h2>
                                <p id="current-year" class="text-sm text-gray-500 dark:text-gray-400"></p>
                            </div>
                            <button id="next-month" class="calendar-navigation p-3 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-all duration-300">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>  
                <!-- Calendar -->
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up calendar-appear" style="animation-delay: 0.4s;">
                    <div class="p-6">
                        <!-- Days of week header -->
                        <div class="grid grid-cols-7 gap-2 mb-4">
                            <div class="text-center text-sm font-semibold text-gray-500 dark:text-gray-400 py-3">Lun</div>
                            <div class="text-center text-sm font-semibold text-gray-500 dark:text-gray-400 py-3">Mar</div>
                            <div class="text-center text-sm font-semibold text-gray-500 dark:text-gray-400 py-3">Mer</div>
                            <div class="text-center text-sm font-semibold text-gray-500 dark:text-gray-400 py-3">Jeu</div>
                            <div class="text-center text-sm font-semibold text-gray-500 dark:text-gray-400 py-3">Ven</div>
                            <div class="text-center text-sm font-semibold text-gray-500 dark:text-gray-400 py-3">Sam</div>
                            <div class="text-center text-sm font-semibold text-gray-500 dark:text-gray-400 py-3">Dim</div>
                        </div>
                        <!-- Calendar Grid -->
                        <div id="calendar-grid" class="grid grid-cols-7 gap-2">
                            <!-- Calendar days will be generated by JavaScript -->
                        </div>
                    </div>
                </div>
                
                <!-- Legend -->
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-6 animate-slide-up" style="animation-delay: 0.5s;">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Légende</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-4">
                        <div class="legend-item flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="w-4 h-4 leave-sick rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Congé maladie</span>
                        </div>
                        <div class="legend-item flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="w-4 h-4 leave-maternity rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Maternité</span>
                        </div>
                        <div class="legend-item flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="w-4 h-4 leave-paid rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Congés payés</span>
                        </div>
                        <div class="legend-item flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="w-4 h-4 leave-rtt rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">RTT</span>
                        </div>
                        <div class="legend-item flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="w-4 h-4 leave-unpaid rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Sans solde</span>
                        </div>
                        <div class="legend-item flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="w-4 h-4 leave-paternity rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Paternité</span>
                        </div>
                        <div class="legend-item flex items-center space-x-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-xl">
                            <div class="w-4 h-4 bg-red-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Jour férié</span>
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
    
    <!-- Modal de confirmation de déconnexion -->
    <div id="logoutConfirmModal" class="fixed inset-0 z-50 hidden">
        <div class="backdrop absolute inset-0 bg-black bg-opacity-50" onclick="closeLogoutModal()"></div>
        <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                            <i class="fas fa-sign-out-alt text-xl text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Confirmation de déconnexion</h3>
                    </div>
                </div>

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
      
    <script src="assets/javascript/calandrier-employers.js"></script>
    <script src="assets/javascript/logout.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="assets/javascript/config.js"></script>
</body>
</html>