<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graxel Tech - Informations</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   
    <!-- Vos fichiers existants -->
    <script src="assets/javascript/config.js"></script>
    <script src="assets/javascript/animate.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
     <link rel="stylesheet" href="assets/css/demandes.css">
    <!-- Nouveaux scripts pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.2.0"></script>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
</head>
<body class="h-full bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 font-poppins transition-all duration-500">
    <!-- Background Pattern -->
    <div class="fixed inset-0 bg-pattern opacity-5 pointer-events-none"></div>

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
                    <div class="w-25 h-25 rounded-2xl flex items-center justify-center shadow-lg">
                        <img src="assets/images/logo.png" alt="Logo Graxel Tech" class="w-full h-full object-contain rounded-2xl" />
                    </div>
                </div>

             
                <!-- Navigation -->
                <nav class="space-y-3 flex-1">
                      <a href="tableau de bord-manager.html" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                   <i class="fas fa-chart-pie w-5 h-5 text-lg"></i>
                        <span></span>Tableau de bord
                    </a>
                    <a href="demandes-equipe.html" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-users w-5 h-5 text-lg"></i>
                        <span>Demandes d'équipe</span>
                    </a>
                    <a href="calendrier-manager.html" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                    <i class="fas fa-umbrella-beach w-5 h-5 text-lg"></i>
                        <span>Congés</span>
                    </a>
                     <a href="#" class="nav-item flex items-center space-x-4 px-4 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover-lift transition-all duration-300 click-scale shadow-lg">
                        <i class="fas fa-circle-info"></i>
                        <span class="font-medium">Informations</span>
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
                        <div class="flex-2">
                            <p class="font-semibold text-gray-900 dark:text-white">Jean Martin </p>
                        </div>
                         <a href="#"  id="logoutBtn"  class="flex items-center space-x-3 text-red-600 hover:text-red-700 dark:text-red-400 text-sm hover-lift transition-all duration-200 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                            <i class="fas fa-sign-out-alt w-4 h-4"></i>
                            <span></span>
                        </a>
                    </div>
                </div>
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
                            <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Informations</h1>
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
      
<!-- Section "Notes d'information" -->
<div class="grid grid-cols-1 gap-0 w-full mb-6">
    <div class="w-full">
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-none shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden w-full">
            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-purple-500/5 to-blue-500/5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Notes d'information</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Gestion des documents et communications</p>
                    </div>
                    <button onclick="showPublishModal()" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 text-white rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-plus mr-2"></i>Publier une note
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Titre</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="notesTableBody" class="bg-white dark:bg-gray-800/50 divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Note 1 -->
                        <tr class="note-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors" data-id="1">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-pink-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-file-pdf text-white text-sm"></i>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Politique de congés 2025</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">15/09/2025</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">PDF</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="downloadNote(1)" class="text-blue-500 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors" title="Télécharger">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button onclick="viewNote(1)" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" title="Visualiser">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editNote(1)" class="text-orange-500 hover:text-orange-700 p-2 rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/30 transition-colors" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteNote(1)" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        
                        <!-- Note 2 -->
                        <tr class="note-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors" data-id="2">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-teal-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-file-excel text-white text-sm"></i>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Planning été 2025</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">10/09/2025</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">XLSX</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="downloadNote(2)" class="text-blue-500 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors" title="Télécharger">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button onclick="viewNote(2)" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" title="Visualiser">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editNote(2)" class="text-orange-500 hover:text-orange-700 p-2 rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/30 transition-colors" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteNote(2)" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Note 3 -->
                        <tr class="note-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors" data-id="3">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-sticky-note text-white text-sm"></i>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Réunion équipe - Septembre</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">08/09/2025</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">NOTE</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="viewNote(3)" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" title="Visualiser">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editNote(3)" class="text-orange-500 hover:text-orange-700 p-2 rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/30 transition-colors" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteNote(3)" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Note 4 -->
                        <tr class="note-row hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors" data-id="4">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-file-image text-white text-sm"></i>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">Organigramme 2025</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">05/09/2025</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">PNG</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="downloadNote(4)" class="text-blue-500 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors" title="Télécharger">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button onclick="viewNote(4)" class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" title="Visualiser">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editNote(4)" class="text-orange-500 hover:text-orange-700 p-2 rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/30 transition-colors" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteNote(4)" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Supprimer">
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

<!-- Modal de publication de note -->
<div id="publishModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="backdrop flex items-center justify-center min-h-screen px-4 py-6 bg-black bg-opacity-50 transition-opacity duration-300">
        <div class="modal bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full transform transition-all duration-300 scale-95 opacity-0">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Publier une note</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Choisissez le type de note à créer</p>
            </div>
            
            <!-- Sélection du type -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex space-x-4">
                    <button id="noteTypeBtn" onclick="selectNoteType('note')" class="flex-1 p-4 border-2 border-blue-500 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-center transition-all">
                        <i class="fas fa-sticky-note text-blue-500 text-2xl mb-2"></i>
                        <div class="font-semibold text-blue-700 dark:text-blue-300">Note écrite</div>
                        <div class="text-sm text-blue-600 dark:text-blue-400">Information textuelle</div>
                    </button>
                    <button id="fileTypeBtn" onclick="selectNoteType('file')" class="flex-1 p-4 border-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/30 rounded-lg text-center transition-all">
                        <i class="fas fa-file-upload text-gray-500 text-2xl mb-2"></i>
                        <div class="font-semibold text-gray-700 dark:text-gray-300">Avec fichier</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Document joint</div>
                    </button>
                </div>
            </div>
            
            <!-- Formulaire -->
            <div class="p-6">
                <form id="noteForm">
                    <!-- Titre -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Titre *</label>
                        <input type="text" id="noteTitle" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Entrez le titre de la note" required>
                    </div>
                    
                    <!-- Zone de téléversement (masquée par défaut) -->
                    <div id="fileUploadSection" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Téléverser un document</label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors cursor-pointer" onclick="document.getElementById('fileInput').click()">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 dark:text-gray-500 mb-2"></i>
                            <p class="text-gray-600 dark:text-gray-400">Cliquez pour sélectionner un fichier</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">PDF, XLSX, PNG, JPG (Max 10MB)</p>
                            <input type="file" id="fileInput" class="hidden" accept=".pdf,.xlsx,.xls,.png,.jpg,.jpeg,.doc,.docx" onchange="handleFileSelect(event)">
                        </div>
                        <div id="filePreview" class="mt-3 hidden">
                            <div class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                <i id="fileIcon" class="fas fa-file text-blue-500 mr-3"></i>
                                <span id="fileName" class="text-sm text-blue-700 dark:text-blue-300 flex-1"></span>
                                <button type="button" onclick="removeFile()" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea id="noteDescription" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Entrez la description de la note"></textarea>
                    </div>
                    
                    <!-- Boutons -->
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeModal('publishModal')" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 text-white rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Publier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de visualisation -->
<div id="viewModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="backdrop flex items-center justify-center min-h-screen px-4 py-6 bg-black bg-opacity-50 transition-opacity duration-300">
        <div class="modal bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full transform transition-all duration-300 scale-95 opacity-0">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 id="viewTitle" class="text-xl font-semibold text-gray-900 dark:text-white">Titre de la note</h3>
                <p id="viewDate" class="text-sm text-gray-500 dark:text-gray-400 mt-1">Date de publication</p>
            </div>
            <div class="p-6">
                <div id="viewContent" class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap"></div>
            </div>
            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeModal('viewModal')" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="backdrop flex items-center justify-center min-h-screen px-4 py-6 bg-black bg-opacity-50 transition-opacity duration-300">
        <div class="modal bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-trash text-red-500"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Supprimer la note</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">Êtes-vous sûr de vouloir supprimer cette note ? Cette action est irréversible.</p>
                <div class="text-center mb-6">
                    <p id="deleteNoteTitle" class="font-medium text-gray-900 dark:text-white"></p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="closeModal('deleteModal')" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Annuler
                    </button>
                    <button id="confirmDeleteBtn" class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-trash mr-2"></i>Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

 
    <script src="assets/javascript/config.js"></script>
    <script src="assets/javascript/informations.js"></script>
    <script src="assets/javascript/logout.js"></script>
 
<!-- Modal de confirmation -->
<div id="confirmModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="backdrop flex items-center justify-center min-h-screen px-4 py-6 bg-black bg-opacity-50 transition-opacity duration-300">
        <div class="modal bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div id="confirmIcon" class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-500"></i>
                    </div>
                </div>
                <h3 id="confirmTitle" class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Approuver la demande</h3>
                <p id="confirmMessage" class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">Êtes-vous sûr de vouloir approuver cette demande de congés ?</p>
                <div class="text-center mb-6">
                    <p id="confirmDetails" class="font-medium text-gray-900 dark:text-white">Jean Martin - Congés payés</p>
                    <p id="confirmDates" class="text-sm text-gray-500 dark:text-gray-400">15/09/2025 - 20/09/2025 (5 jours)</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="closeModal('confirmModal')" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Annuler
                    </button>
                    <button id="confirmActionBtn" onclick="executeAction()" class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Approuver
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation pour actions groupées -->
<div id="bulkConfirmModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="backdrop flex items-center justify-center min-h-screen px-4 py-6 bg-black bg-opacity-50 transition-opacity duration-300">
        <div class="modal bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div id="bulkConfirmIcon" class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-500"></i>
                    </div>
                </div>
                <h3 id="bulkConfirmTitle" class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Approuver les demandes</h3>
                <p id="bulkConfirmMessage" class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">Êtes-vous sûr de vouloir approuver toutes les demandes sélectionnées ?</p>
                <div class="text-center mb-6">
                    <p id="bulkConfirmCount" class="font-medium text-gray-900 dark:text-white">3 demandes sélectionnées</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="closeModal('bulkConfirmModal')" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Annuler
                    </button>
                    <button id="bulkConfirmActionBtn" onclick="executeBulkAction()" class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Approuver
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast de notification -->
<div id="toast" class="fixed top-4 right-4 bg-white dark:bg-gray-800 border-l-4 border-green-500 rounded-lg shadow-lg p-4 transform translate-x-full transition-transform duration-300 z-50 min-w-80">
    <div class="flex items-start space-x-3">
        <div id="toastIcon" class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check text-green-500"></i>
        </div>
        <div class="flex-1">
            <h4 id="toastTitle" class="font-semibold text-gray-900 dark:text-white text-sm">Action réussie</h4>
            <p id="toastMessage" class="text-sm text-gray-500 dark:text-gray-400">L'action a été effectuée avec succès</p>
        </div>
        <button onclick="document.getElementById('toast').style.transform = 'translateX(100%)'" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

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
                                <li><a href="tableau de bord-manager.html" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Tableau de bord</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Demandes d'équipe</a></li>
                                <li><a href="calendrier-manager.html" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Calendrier équipe</a></li>
                                <li><a href="profil.html" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Mon profile</a></li>
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

    <!-- Toast notification -->
    <div id="toast" class="fixed top-4 right-4 z-50 transform translate-x-full transition-transform duration-300">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-4 border-l-4 max-w-sm">
            <div class="flex items-center space-x-3">
                <div id="toastIcon" class="w-8 h-8 rounded-full flex items-center justify-center">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <p id="toastTitle" class="font-semibold text-gray-900 dark:text-white">Action réussie</p>
                    <p id="toastMessage" class="text-sm text-gray-600 dark:text-gray-400">L'action a été effectuée avec succès.</p>
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

  <!-- Modal de détails de la demande -->
    <div id="detailsModal" class="fixed inset-0 z-50 hidden">
        <div class="backdrop absolute inset-0 bg-black bg-opacity-50" onclick="closeModal('detailsModal')"></div>
        <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full max-h-screen overflow-hidden">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Détails de la demande</h3>
                    <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" onclick="closeModal('detailsModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Content -->
                <div class="p-6 space-y-6 max-h-96 overflow-y-auto">
                    <div class="flex items-center space-x-4">
                        <div id="detailsAvatar" class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user text-white text-xl"></i>
                        </div>
                        <div>
                            <h4 id="detailsName" class="text-lg font-semibold text-gray-900 dark:text-white">Jean Martin</h4>
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
                        <p id="detailsReason" class="text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">Congés pour vacances en famille. Voyage prévu depuis plusieurs mois.</p>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Justificatif (PDF)</label>
                        <div class="pdf-viewer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border">
                            <div class="flex items-center space-x-3 mb-3">
                                <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                <span id="pdfName" class="text-gray-900 dark:text-white font-medium">justificatif_conges.pdf</span>
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
                    <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors" onclick="closeModal('detailsModal')">
                        Fermer
                    </button>
                    <button class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors" onclick="closeModal('detailsModal'); showConfirmModal('reject', currentRequestId)">
                        <i class="fas fa-times mr-2"></i>Refuser
                    </button>
                    <button class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors" onclick="closeModal('detailsModal'); showConfirmModal('approve', currentRequestId)">
                        <i class="fas fa-check mr-2"></i>Approuver
                    </button>
                </div>
            </div>
        </div>
    </div>
 
</body>
</html>
