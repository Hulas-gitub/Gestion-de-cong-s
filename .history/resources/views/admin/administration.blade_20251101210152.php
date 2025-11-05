<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graxel Tech - Administration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <script src="assets/javascript/config.js"></script>
    <script src="assets/javascript/animate.js"></script>
    <link rel="stylesheet" href="assets/css/demandes.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">

</head>

<body
    class="h-full bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 font-poppins transition-all duration-500">
    <!-- Background Pattern -->
    <div class="fixed inset-0 bg-pattern opacity-5 pointer-events-none"></div>


    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden sidebar-overlay"></div>

    <div class="flex h-full">
        <!-- Sidebar -->
        <div id="sidebar"
            class="fixed md:relative w-80 md:w-72 bg-white dark:bg-gray-800 shadow-2xl border-r border-gray-200 dark:border-gray-700 z-50 md:z-auto sidebar-mobile animate-slide-right transition-all duration-300">
            <div class="p-6 h-full flex flex-col">
                <!-- Close button (Mobile) -->
                <button id="close-sidebar"
                    class="absolute top-4 right-4 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden">
                    <i class="fas fa-times text-gray-600 dark:text-gray-400"></i>
                </button>

                <!-- Logo -->
                <div class="flex items-center space-x-4 mb-10">
                    <div class="w-30 h-50 rounded-2xl flex items-center justify-center shadow-lg">
                        <img src="assets/images/logo.png" alt="Logo Graxel Tech"
                            class="w-full h-full object-contain rounded-2xl" />
                    </div>
                    <div>
                    </div>

                </div>
                <!-- Navigation -->
                <nav class="space-y-3 flex-1">
                    <a href="dashboard-admin.html"
                        class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-chart-pie w-5 h-5 text-lg"></i>
                        <span>Tableau de bord</span>
                    </a>
                    <a href="#"
                        class="nav-item flex items-center space-x-4 px-4 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover-lift transition-all duration-300 click-scale shadow-lg">
                        <i class="fas fa-clipboard-list w-5 h-5 text-lg"></i>
                        <span class="font-medium">Administration</span>
                    </a>
                    <a href="calendrier-admin.html"
                        class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                                    <i class="fas fa-umbrella-beach w-5 h-5 text-lg"></i>
                        <span>Congés</span>
                    </a>
                    <a href="profile.html"
                        class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-user w-5 h-5 text-lg"></i>
                        <span>Mon profil</span>
                    </a>
                  
                </nav>

                <!-- User Profile -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-lg animate-float">
                            JM
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">Jean Martin</p>
                     
                        </div>
                        
                         <div class="space-y-2">
                        <a href="#"  id="logoutBtn"  class="flex items-center space-x-3 text-red-600 hover:text-red-700 dark:text-red-400 text-sm hover-lift transition-all duration-200 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                            <i class="fas fa-sign-out-alt w-4 h-4"></i>>
                        </a>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto custom-scrollbar">
            <!-- Header -->
            <div
                class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg shadow-lg border-b border-gray-200/50 dark:border-gray-700/50 px-4 md:px-8 py-6 animate-fade-in sticky top-0 z-30">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <button id="toggle-sidebar"
                            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden">
                            <i class="fas fa-bars text-gray-600 dark:text-gray-400"></i>
                        </button>
                        <div>
                            <h1
                                class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                Administration</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                <i class="fas fa-calendar-day mr-2"></i>
                                <span id="current-date"></span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <!-- APRÈS - nouvelle structure -->
                            <div class="notifications-container">
                                <button id="notifications-btn"
                                    class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors relative">
                                    <i class="fas fa-bell text-lg"></i>
                                    <span class="notification-count animate-pulse">3</span>
                                </button>

                                <!-- Notifications Dropdown -->
                                <div id="notifications-dropdown" class="notification-dropdown">
                                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">Notifications</h3>
                                    </div>
                                    <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                        <div class="p-2 space-y-1">
                                            <div class="notification-item">
                                                <div class="notification-badge bg-blue-500 animate-pulse"></div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        Nouvelle demande de Pierre</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Il y a 2h
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="notification-item">
                                                <div class="notification-badge bg-green-500 animate-pulse"></div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Demande
                                                        approuvée</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Il y a 1j
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="notification-item">
                                                <div class="notification-badge bg-yellow-500 animate-pulse"></div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Rappel:
                                                        Réunion demain</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Il y a 3h
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button id="theme-toggle"
                            class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 click-scale hover-lift">
                            <i class="fas fa-moon dark:hidden text-gray-600 text-lg"></i>
                            <i class="fas fa-sun hidden dark:block text-yellow-400 text-lg"></i>
                        </button>

                     
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="p-4 md:p-8 space-y-8">

              
                <!-- Modal de visualisation -->
                <div id="viewModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
                    <div
                        class="backdrop flex items-center justify-center min-h-screen px-4 py-6 bg-black bg-opacity-50 transition-opacity duration-300">
                        <div
                            class="modal bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full transform transition-all duration-300 scale-95 opacity-0">
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <h3 id="viewTitle" class="text-xl font-semibold text-gray-900 dark:text-white">Titre de
                                    la note</h3>
                                <p id="viewDate" class="text-sm text-gray-500 dark:text-gray-400 mt-1">Date de
                                    publication</p>
                            </div>
                            <div class="p-6">
                                <div id="viewContent" class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                </div>
                            </div>
                            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                                <button onclick="closeModal('viewModal')"
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                    Fermer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal de confirmation de suppression -->
                <div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
                    <div
                        class="backdrop flex items-center justify-center min-h-screen px-4 py-6 bg-black bg-opacity-50 transition-opacity duration-300">
                        <div
                            class="modal bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0">
                            <div class="p-6">
                                <div class="flex items-center justify-center mb-4">
                                    <div
                                        class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                                        <i class="fas fa-trash text-red-500"></i>
                                    </div>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">
                                    Supprimer la note</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">Êtes-vous sûr de
                                    vouloir supprimer cette note ? Cette action est irréversible.</p>
                                <div class="text-center mb-6">
                                    <p id="deleteNoteTitle" class="font-medium text-gray-900 dark:text-white"></p>
                                </div>
                                <div class="flex space-x-3">
                                    <button onclick="closeModal('deleteModal')"
                                        class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                        Annuler
                                    </button>
                                    <button id="confirmDeleteBtn"
                                        class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                                        <i class="fas fa-trash mr-2"></i>Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Navigation par onglets -->
                <div
                    class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-4 animate-slide-up">
                    <div class="flex flex-wrap gap-2">
                        <button class="tab-button active" data-tab="employes">Employés</button>
                        <button class="tab-button" data-tab="chefs">Chefs de Département</button>
                        <button class="tab-button" data-tab="departements">Départements</button>
                    </div>
                </div>
                <!-- Contenu des onglets -->
                <div class="tab-content">
                    <!-- Onglet Employés -->
                    <div id="employes-tab" class="tab-pane active">
                        <div
                            class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden">
                            <div
                                class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-blue-500/5 to-purple-500/5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Gestion des
                                            Employés</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Liste de tous les employés
                                            de l'entreprise</p>
                                    </div>
                                    <button id="add-employe-btn"
                                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Ajouter un employé
                                    </button>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Matricule</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Nom</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Email</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Département</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Poste</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Statut</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <!-- Données fictives pour les employés -->
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                EMP001</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                Jean Martin</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                jean.martin@graxeltech.com</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                Finance</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                Analyste financier</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Actif</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button
                                                    title="Voir détails"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- Bouton Bloquer -->
                                                <button title="Bloquer"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 mr-3">
                                                    <i class="fas fa-lock"></i>
                                                </button>

                                                <!-- Bouton Supprimer -->
                                                <button title="Supprimer"
                                                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                EMP002</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                Marie Dubois</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                marie.dubois@graxeltech.com</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                RH</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                Responsable RH</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Actif</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"><button
                                                    title="Voir détails"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- Bouton Bloquer -->
                                                <button title="Bloquer"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 mr-3">
                                                    <i class="fas fa-lock"></i>
                                                </button>

                                                <!-- Bouton Supprimer -->
                                                <button title="Supprimer"
                                                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                EMP003</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                Pierre Leroy</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                pierre.leroy@graxeltech.com</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                IT</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                Développeur</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Actif</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button title="Voir détails"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- Bouton Bloquer -->
                                                <button title="Bloquer"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 mr-3">
                                                    <i class="fas fa-lock"></i>
                                                </button>

                                                <!-- Bouton Supprimer -->
                                                <button title="Supprimer"
                                                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                EMP004</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                Sophie Bernard</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                sophie.bernard@graxeltech.com</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                Marketing</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                Chef de projet</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">En
                                                    congé</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button title="Voir détails"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- Bouton Bloquer -->
                                                <button title="Bloquer"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 mr-3">
                                                    <i class="fas fa-lock"></i>
                                                </button>

                                                <!-- Bouton Supprimer -->
                                                <button title="Supprimer"
                                                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Onglet Chefs de Département -->
                <div id="chefs-tab" class="tab-pane hidden">
                    <div
                        class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden">
                        <div
                            class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-blue-500/5 to-purple-500/5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Gestion des Chefs
                                        de Département</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Liste de tous les chefs de
                                        département</p>
                                </div>
                                <button id="add-chef-btn"
                                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Ajouter un chef
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Nom</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Email</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Département</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Date de nomination</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <!-- Données fictives pour les chefs de département -->
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            CHF001</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            Jean Martin</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            jean.martin@graxeltech.com</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            Finance</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            15/03/2023</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button
                                                    title="Voir détails"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- Bouton Bloquer -->
                                                <button title="Bloquer"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 mr-3">
                                                    <i class="fas fa-lock"></i>
                                                </button>

                                                <!-- Bouton Supprimer -->
                                                <button title="Supprimer"
                                                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                                                    <i class="fas fa-trash"></i>
                                                </button></td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            CHF002</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            Marie Dubois</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            marie.dubois@graxeltech.com</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">RH
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            10/01/2024</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button
                                                    title="Voir détails"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- Bouton Bloquer -->
                                                <button title="Bloquer"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 mr-3">
                                                    <i class="fas fa-lock"></i>
                                                </button>

                                                <!-- Bouton Supprimer -->
                                                <button title="Supprimer"
                                                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                                                    <i class="fas fa-trash"></i>
                                                </button></td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            CHF003</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            Paul Durand</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            paul.durand@graxeltech.com</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">IT
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            22/06/2022</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                         <button
                                                    title="Voir détails"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- Bouton Bloquer -->
                                                <button title="Bloquer"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 mr-3">
                                                    <i class="fas fa-lock"></i>
                                                </button>

                                                <!-- Bouton Supprimer -->
                                                <button title="Supprimer"
                                                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                                                    <i class="fas fa-trash"></i>
                                                </button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Onglet Départements -->
                <div id="departements-tab" class="tab-pane hidden">
                    <div
                        class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden">
                        <div
                            class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-blue-500/5 to-purple-500/5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Gestion des
                                        Départements</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Liste de tous les départements
                                        de l'entreprise</p>
                                </div>
                                <button id="add-departement-btn"
                                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Ajouter un département
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Nom</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Description</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Chef de département</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Nombre d'employés</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <!-- Données fictives pour les départements -->
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            DEP001</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            Finance</td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Gestion
                                            financière et comptable</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            Jean Martin</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">12
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button
                                                    title="Voir détails"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </button>


                                                <!-- Bouton Supprimer -->
                                                <button title="Supprimer"
                                                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            DEP002</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            Ressources Humaines</td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Gestion du
                                            personnel et recrutement</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            Marie Dubois</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">8
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                       <button
                                                    title="Voir détails"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </button>


                                                <!-- Bouton Supprimer -->
                                                <button title="Supprimer"
                                                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                                                    <i class="fas fa-trash"></i>
                                                </button></td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            DEP003</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            Technologie de l'Information</td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Développement et
                                            maintenance des systèmes</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            Paul Durand</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">25
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                       <button
                                                    title="Voir détails"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </button>


                                                <!-- Bouton Supprimer -->
                                                <button title="Supprimer"
                                                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                                                    <i class="fas fa-trash"></i>
                                                </button> </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            DEP004</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            Marketing</td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Stratégie
                                            marketing et communication</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            Sophie Bernard</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">15
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                      <button
                                                    title="Voir détails"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </button>


                                                <!-- Bouton Supprimer -->
                                                <button title="Supprimer"
                                                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                                                    <i class="fas fa-trash"></i>
                                                </button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
     
            <footer
                class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-t border-gray-200/50 dark:border-gray-700/50 p-6 mt-0 w-full">
                <div class="max-w-full mx-0 px-4 md:px-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div class="col-span-1 md:col-span-2">
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg">
                                    <img src="assets/images/logo.png" alt="Logo Graxel Tech"
                                        class="w-full h-full object-contain rounded-xl" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Graxel Tech</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Gestion des congés simplifiée
                                    </p>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                Une solution moderne et intuitive pour la gestion des demandes de congés et des
                                ressources humaines.
                            </p>
                            <div class="flex space-x-4">
                                <a href="#"
                                    class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#"
                                    class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#"
                                    class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Liens rapides</h4>
                            <ul class="space-y-2 text-sm">
                                <li><a href="tableau de bord-employers.html"
                                        class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Tableau
                                        de bord</a></li>
                                <li><a href="#"
                                        class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Mes
                                        demandes</a></li>
                                <li><a href="calendrier-employers.html"
                                        class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Calendrier</a>
                                </li>
                                <li><a href="profile.html"
                                        class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Mon
                                        profil</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Support</h4>
                            <ul class="space-y-2 text-sm">
                                <li><a href="#"
                                        class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Centre
                                        d'aide</a></li>
                                <li><a href="#"
                                        class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Contact</a>
                                </li>
                                <li><a href="#"
                                        class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">FAQ</a>
                                </li>
                                <li><a href="#"
                                        class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Documentation</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-6">
                        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                © 2025 Graxel Tech. Tous droits réservés.
                            </p>
                            <div class="flex space-x-6 text-sm">
                                <a href="#"
                                    class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Politique
                                    de confidentialité</a>
                                <a href="#"
                                    class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Conditions
                                    d'utilisation</a>
                                <a href="#"
                                    class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Mentions
                                    légales</a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    </div>

    <script src="assets/javascript/config.js"></script>
    <script src="assets/javascript/demandes-admin.js"></script>

    <!-- Modal Employé -->
    <div id="employe-modal" class="fixed inset-0 z-50 hidden">
        <div class="backdrop absolute inset-0 bg-black bg-opacity-50" onclick="closeModal('employe-modal')"></div>
        <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 id="employe-modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">Ajouter un
                        employé</h3>
                </div>
                <div class="p-6">
                    <form id="employe-form">
                        <div class="space-y-4">
                            <!-- Matricule (en lecture seule) -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Matricule</label>
                                <input type="text" id="employe-matricule"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white"
                                    readonly>
                            </div>

                            <!-- Nom et Prénom sur la même ligne -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom</label>
                                    <input type="text" id="employe-nom"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        required>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prénom</label>
                                    <input type="text" id="employe-prenom"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        required>
                                </div>
                            </div>

                            <!-- Contact -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact
                                    (Téléphone)</label>
                                <input type="tel" id="employe-contact"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    placeholder="+241 XX XX XX XX" required>
                            </div>

                            <!-- Email -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <input type="email" id="employe-email"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    required>
                            </div>

                            <!-- Rôle -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rôle</label>
                                <select id="employe-role"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    required>
                                    <option value="Employé" selected>Employé</option>
                                    <option value="Chef de Département">Chef de Département</option>
                                    <option value="Administrateur">Administrateur</option>
                                </select>
                            </div>

                            <!-- Poste -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Poste</label>
                                <input type="text" id="employe-poste"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    required>
                            </div>

                            <!-- Département -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Département</label>
                                <select id="employe-departement"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    required>
                                    <option value="">Sélectionner un département</option>
                                    <option value="Finance">Finance</option>
                                    <option value="Ressources Humaines">Ressources Humaines</option>
                                    <option value="Technologie de l'Information">Technologie de l'Information</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Commercial">Commercial</option>
                                    <option value="Production">Production</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex space-x-3 mt-6">
                            <button type="button" onclick="closeModal('employe-modal')"
                                class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                Annuler
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Chef de Département -->
    <div id="chef-modal" class="fixed inset-0 z-50 hidden">
        <div class="backdrop absolute inset-0 bg-black bg-opacity-50" onclick="closeModal('chef-modal')"></div>
        <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 id="chef-modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">Ajouter un
                        chef de département</h3>
                </div>
                <div class="p-6">
                    <form id="chef-form">
                        <div class="space-y-4">
                            <!-- Matricule (en lecture seule) -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Matricule</label>
                                <input type="text" id="chef-matricule"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white"
                                    readonly>
                            </div>

                            <!-- Nom et Prénom sur la même ligne -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom</label>
                                    <input type="text" id="chef-nom"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        required>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prénom</label>
                                    <input type="text" id="chef-prenom"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        required>
                                </div>
                            </div>

                            <!-- Contact -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact
                                    (Téléphone)</label>
                                <input type="tel" id="chef-contact"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    placeholder="+241 XX XX XX XX" required>
                            </div>

                            <!-- Email -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <input type="email" id="chef-email"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    required>
                            </div>
                            <!-- Rôle (fixe pour Chef de Département) -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rôle</label>
                                <input type="text" id="chef-role"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white"
                                    value="Chef de Département" readonly>
                            </div>

                            <!-- Poste -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Poste</label>
                                <input type="text" id="chef-poste"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    placeholder="Ex: Directeur, Manager, Responsable..." required>
                            </div>

                            <!-- Département -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Département</label>
                                <select id="chef-departement"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    required>
                                    <option value="">Sélectionner un département</option>
                                    <option value="Finance">Finance</option>
                                    <option value="Ressources Humaines">Ressources Humaines</option>
                                    <option value="Technologie de l'Information">Technologie de l'Information</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Commercial">Commercial</option>
                                    <option value="Production">Production</option>
                                </select>
                            </div>

                            <!-- Date de nomination -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date de
                                    nomination</label>
                                <input type="date" id="chef-date-nomination"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    required>
                            </div>
                        </div>

                        <div class="flex space-x-3 mt-6">
                            <button type="button" onclick="closeModal('chef-modal')"
                                class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                Annuler
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Département -->
    <div id="departement-modal" class="fixed inset-0 z-50 hidden">
        <div class="backdrop absolute inset-0 bg-black bg-opacity-50" onclick="closeModal('departement-modal')"></div>
        <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 id="departement-modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">Ajouter
                        un département</h3>
                </div>
                <div class="p-6">
                    <form id="departement-form">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom du
                                    département</label>
                                <input type="text" id="departement-nom"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    required>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea id="departement-description" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    required></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Chef de
                                    département</label>
                                <select id="departement-chef"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    required>
                                    <option value="">Sélectionner un chef</option>
                                    <option value="Jean Martin">Jean Martin</option>
                                    <option value="Marie Dubois">Marie Dubois</option>
                                    <option value="Paul Durand">Paul Durand</option>
                                    <option value="Sophie Bernard">Sophie Bernard</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex space-x-3 mt-6">
                            <button type="button" onclick="closeModal('departement-modal')"
                                class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                Annuler
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div id="delete-confirm-modal" class="fixed inset-0 z-50 hidden">
        <div class="backdrop absolute inset-0 bg-black bg-opacity-50" onclick="closeModal('delete-confirm-modal')">
        </div>
        <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-center mb-4">
                        <div
                            class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-trash text-red-500"></i>
                        </div>
                    </div>
                    <h3 id="delete-confirm-title"
                        class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Confirmer la
                        suppression</h3>
                    <p id="delete-confirm-message" class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">
                        Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.</p>
                    <div class="flex space-x-3">
                        <button onclick="closeModal('delete-confirm-modal')"
                            class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Annuler
                        </button>
                        <button id="confirm-delete-btn"
                            class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                            <i class="fas fa-trash mr-2"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast notification -->
    <div id="toast" class="fixed top-4 right-4 z-50 transform translate-x-full transition-transform duration-300">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-4 border-l-4 max-w-sm">
            <div class="flex items-center space-x-3">
                <div id="toast-icon" class="w-8 h-8 rounded-full flex items-center justify-center">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <p id="toast-title" class="font-semibold text-gray-900 dark:text-white">Action réussie</p>
                    <p id="toast-message" class="text-sm text-gray-600 dark:text-gray-400">L'action a été effectuée avec
                        succès.</p>
                </div>
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
                        <div
                            class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
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
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-sm">
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
                    <button
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                        onclick="closeLogoutModal()">
                        Annuler
                    </button>
                    <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                        onclick="executeLogout()">
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

    <!-- Modal de confirmation -->
    <div id="confirmModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div
            class="backdrop flex items-center justify-center min-h-screen px-4 py-6 bg-black bg-opacity-50 transition-opacity duration-300">
            <div
                class="modal bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0">
                <div class="p-6">
                    <div class="flex items-center justify-center mb-4">
                        <div id="confirmIcon"
                            class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-green-500"></i>
                        </div>
                    </div>
                    <h3 id="confirmTitle" class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">
                        Approuver la demande</h3>
                    <p id="confirmMessage" class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">Êtes-vous
                        sûr de vouloir approuver cette demande de congés ?</p>
                    <div class="text-center mb-6">
                        <p id="confirmDetails" class="font-medium text-gray-900 dark:text-white">Jean Martin - Congés
                            payés</p>
                        <p id="confirmDates" class="text-sm text-gray-500 dark:text-gray-400">15/09/2025 - 20/09/2025 (5
                            jours)</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="closeModal('confirmModal')"
                            class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Annuler
                        </button>
                        <button id="confirmActionBtn" onclick="executeAction()"
                            class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                            <i class="fas fa-check mr-2"></i>Approuver
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation pour actions groupées -->
    <div id="bulkConfirmModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div
            class="backdrop flex items-center justify-center min-h-screen px-4 py-6 bg-black bg-opacity-50 transition-opacity duration-300">
            <div
                class="modal bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0">
                <div class="p-6">
                    <div class="flex items-center justify-center mb-4">
                        <div id="bulkConfirmIcon"
                            class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-green-500"></i>
                        </div>
                    </div>
                    <h3 id="bulkConfirmTitle"
                        class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Approuver les
                        demandes</h3>
                    <p id="bulkConfirmMessage" class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">
                        Êtes-vous sûr de vouloir approuver toutes les demandes sélectionnées ?</p>
                    <div class="text-center mb-6">
                        <p id="bulkConfirmCount" class="font-medium text-gray-900 dark:text-white">3 demandes
                            sélectionnées</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="closeModal('bulkConfirmModal')"
                            class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Annuler
                        </button>
                        <button id="bulkConfirmActionBtn" onclick="executeBulkAction()"
                            class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                            <i class="fas fa-check mr-2"></i>Approuver
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de détails de la demande -->
    <div id="detailsModal" class="fixed inset-0 z-50 hidden">
        <div class="backdrop absolute inset-0 bg-black bg-opacity-50" onclick="closeModal('detailsModal')"></div>
        <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full max-h-screen overflow-hidden">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Détails de la demande</h3>
                    <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        onclick="closeModal('detailsModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-6 max-h-96 overflow-y-auto">
                    <div class="flex items-center space-x-4">
                        <div id="detailsAvatar"
                            class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user text-white text-xl"></i>
                        </div>
                        <div>
                            <h4 id="detailsName" class="text-lg font-semibold text-gray-900 dark:text-white">Jean Martin
                            </h4>
                            <p id="detailsType" class="text-sm text-gray-500 dark:text-gray-400">Congés payés</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Date de début</label>
                            <p id="detailsStartDate" class="text-gray-900 dark:text-white">2025-09-15</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Date de fin</label>
                            <p id="detailsEndDate" class="text-gray-900 dark:text-white">2025-09-20</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Durée</label>
                            <p id="detailsDuration" class="text-gray-900 dark:text-white">5 jours</p>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Statut</label>
                            <p id="detailsStatus" class="text-yellow-600 dark:text-yellow-400">En attente</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Motif</label>
                        <p id="detailsReason"
                            class="text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">Congés pour
                            vacances en famille. Voyage prévu depuis plusieurs mois.</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Justificatif (PDF)</label>
                        <div class="pdf-viewer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border">
                            <div class="flex items-center space-x-3 mb-3">
                                <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                <span id="pdfName"
                                    class="text-gray-900 dark:text-white font-medium">justificatif_conges.pdf</span>
                                <button class="text-blue-500 hover:text-blue-700 text-sm" onclick="viewPDF()">
                                    <i class="fas fa-external-link-alt mr-1"></i>Ouvrir
                                </button>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                <p><strong>Contenu du document:</strong></p>
                                <p>• Réservation d'hôtel confirmée</p>
                                <p>• Billets d'avion</p>
                                <p>• Planning de congés équipe</p>
                                <p>• Accord du chef d'équipe</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700">
                    <button
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                        onclick="closeModal('detailsModal')">
                        Fermer
                    </button>
                    <button class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors"
                        onclick="closeModal('detailsModal'); showConfirmModal('reject', currentRequestId)">
                        <i class="fas fa-times mr-2"></i>Refuser
                    </button>
                    <button class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors"
                        onclick="closeModal('detailsModal'); showConfirmModal('approve', currentRequestId)">
                        <i class="fas fa-check mr-2"></i>Approuver
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/javascript/administration.js"></script>
    <script src="assets/javascript/logout.js"></script>
    <script src="assets/javascript/config.js"></script>
</body>

</html>