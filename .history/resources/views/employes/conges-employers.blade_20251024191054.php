<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graxel Tech - Démandes de congés Employer</title>
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

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden sidebar-overlay"></div>
    

    <!-- Pop-up Nouvelle demande - EXACTEMENT VOTRE CODE HTML ORIGINAL -->
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
                        <option value="conges-payes">Congés paternité</option>
                        <option value="conges-payes">Congés maternité</option>
                        <option value="conge-maladie">Congé maladie</option>
                        <option value="conge-formation">Autre</option>
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
                
                <!-- Document justificatif avec bouton pour ouvrir le popup -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document justificatif (optionnel)</label>
                    <button id="open-document-upload" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2">
                        <i class="fas fa-file-upload text-gray-600 dark:text-gray-400"></i>
                        <span class="text-gray-700 dark:text-gray-300">Téléverser un document</span>
                    </button>
                    <!-- Affichage du document sélectionné -->
                    <div id="selected-document-info" class="mt-2 hidden">
                        <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center">
                                    <i class="fas fa-file-alt text-green-600 dark:text-green-400 text-sm"></i>
                                </div>
                                <div>
                                    <p id="document-name" class="text-sm font-medium text-green-800 dark:text-green-200"></p>
                                    <p id="document-size" class="text-xs text-green-600 dark:text-green-400"></p>
                                </div>
                            </div>
                            <button id="remove-document" class="text-red-500 hover:text-red-700 dark:hover:text-red-300 transition-colors">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
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

   
<!-- Document Upload Popup (adapté du popup photo de profil) -->
<div id="document-upload-popup" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden items-center justify-center z-50 transition-all duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full m-4 transform transition-all duration-300 scale-95 opacity-0" id="document-popup-content">
        <!-- Popup Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Téléverser un document justificatif</h3>
            <button type="button" id="close-document-popup-btn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <!-- Popup Body -->
        <div class="p-6">
            <!-- Preview Zone -->
            <div class="mb-6">
                <div class="w-24 h-24 mx-auto bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg" id="document-preview">
                    <i class="fas fa-file-alt text-3xl"></i>
                </div>
            </div>
            <!-- Upload Zone -->
            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center mb-4 hover:border-blue-500 dark:hover:border-blue-400 transition-colors cursor-pointer" id="document-upload-zone">
                <div class="space-y-3">
                    <div class="w-12 h-12 mx-auto bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-cloud-upload-alt text-blue-500 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Cliquez pour téléverser</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">ou glissez-déposez votre document</p>
                    </div>
                    <div class="text-xs text-gray-400 dark:text-gray-500">
                        PDF, DOC, DOCX, JPG, PNG, GIF, TXT (Max 10MB)
                    </div>
                </div>
            </div>
            <!-- File Input -->
            <input type="file" id="document-file-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.txt,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png,image/gif,text/plain" class="hidden">
            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button type="button" id="document-cancel-btn" class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">
                    Annuler
                </button>
                <button type="button" id="document-confirm-btn" class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-medium" disabled>
                    Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

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
    <div class="w-30 h-50 rounded-2xl flex items-center justify-center shadow-lg">
        <img src="assets/images/logo.png" alt="Logo Graxel Tech" class="w-full h-full object-contain rounded-2xl" />
    </div>
    <div>
    </div>
    
</div>
               <!-- Navigation -->
                <nav class="space-y-3 flex-1">
                    <a href="tableau de bord-employers.html" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                             <i class="fas fa-chart-pie w-5 h-5 text-lg"></i>
                        <span>Tableau de bord</span>
                    </a>
                     <a href="#" class="nav-item flex items-center space-x-4 px-4 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover-lift transition-all duration-300 click-scale shadow-lg">
                    <i class="fas fa-clipboard-list w-5 h-5 text-lg"></i>
                        <span class="font-medium">Mes demandes</span>
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
                        <a href="#" id="logoutBtn" class="flex items-center space-x-3 text-red-600 hover:text-red-700 dark:text-red-400 text-sm hover-lift transition-all duration-200 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                            <i class="fas fa-sign-out-alt w-4 h-4"></i>
                            <span>Déconnexion</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto custom-scrollbar">
            <!-- Header -->
            <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg shadow-lg border-b border-gray-200/50 dark:border-gray-700/50 px-4 md:px-8 py-6 animate-fade-in sticky top-0 z-30">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <button id="toggle-sidebar" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden">
                            <i class="fas fa-bars text-gray-600 dark:text-gray-400"></i>
                        </button>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Demande de congés</h1>
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
    <button id="notifications-btn" class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors relative">
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
         
                <!-- Main Content -->
            <div class="p-4 md:p-8 space-y-8">
              
  
    <!-- Main Content -->
            <div class="p-4 md:p-8 space-y-8">
                <!-- Header avec bouton Nouvelle demande -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Historique de mes demandes</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Gérez vos demandes de congés</p>
                    </div>
                    <button id="btn-nouvelle-demande" class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-xl transition-all duration-300 hover-lift click-scale shadow-lg">
                        <i class="fas fa-plus mr-2"></i>
                        Nouvelle demande
                    </button>
                </div>
<!-- Liste des demandes -->
<div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden">
    <!-- Vacances d'été - Approuvé -->
    <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-umbrella-beach text-white"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs font-semibold rounded-full">Congés payés</span>
                        <span class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">Approuvé</span>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white">Vacances d'été</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400 mt-2">
                        <div class="space-y-1">
                            <div><i class="fas fa-play mr-2 text-green-500"></i><span class="font-medium">Début:</span> 15 Mars 2025</div>
                            <div><i class="fas fa-stop mr-2 text-red-500"></i><span class="font-medium">Fin:</span> 20 Mars 2025</div>
                            <div><i class="fas fa-clock mr-2 text-blue-500"></i><span class="font-medium">Durée:</span> 5 jours</div>
                        </div>
                        <div class="space-y-1">
                            <div><i class="fas fa-calendar-plus mr-2 text-purple-500"></i><span class="font-medium">Créé le:</span> 28 Janvier 2025</div>
                            <div><i class="fas fa-user-check mr-2 text-green-500"></i><span class="font-medium">Validé par:</span> M. Martin</div>
                            <div><i class="fas fa-comment mr-2 text-orange-500"></i><span class="font-medium">Motif:</span> Vacances familiales</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button class="p-2 text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="p-2 text-gray-400 hover:text-green-500 dark:hover:text-green-400 transition-colors">
                    <i class="fas fa-download"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Long weekend - En attente -->
    <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-teal-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-week text-white"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 text-xs font-semibold rounded-full">maternité</span>
                        <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 text-xs font-semibold rounded-full">En attente</span>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white">Répos</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400 mt-2">
                        <div class="space-y-1">
                            <div><i class="fas fa-play mr-2 text-green-500"></i><span class="font-medium">Début:</span> 25 Avril 2025</div>
                            <div><i class="fas fa-stop mr-2 text-red-500"></i><span class="font-medium">Fin:</span> 27 Avril 2025</div>
                            <div><i class="fas fa-clock mr-2 text-blue-500"></i><span class="font-medium">Durée:</span> 3 jours</div>
                        </div>
                        <div class="space-y-1">
                            <div><i class="fas fa-calendar-plus mr-2 text-purple-500"></i><span class="font-medium">Créé le:</span> 15 Mars 2025</div>
                            <div><i class="fas fa-hourglass-half mr-2 text-orange-500"></i><span class="font-medium">Statut:</span> En cours de validation</div>
                            <div><i class="fas fa-comment mr-2 text-orange-500"></i><span class="font-medium">Motif:</span> Pont du 1er Mai</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button class="p-2 text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="p-2 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
                <button class="p-2 text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Congé maladie - Refusé -->
    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-pink-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-notes-medical text-white"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 text-xs font-semibold rounded-full">Congé maladie</span>
                        <span class="px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">Refusé</span>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white">Congé maladie</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400 mt-2">
                        <div class="space-y-1">
                            <div><i class="fas fa-play mr-2 text-green-500"></i><span class="font-medium">Début:</span> 10 Février 2025</div>
                            <div><i class="fas fa-stop mr-2 text-red-500"></i><span class="font-medium">Fin:</span> 12 Février 2025</div>
                            <div><i class="fas fa-clock mr-2 text-blue-500"></i><span class="font-medium">Durée:</span> 3 jours</div>
                        </div>
                        <div class="space-y-1">
                            <div><i class="fas fa-calendar-plus mr-2 text-purple-500"></i><span class="font-medium">Créé le:</span> 09 Février 2025</div>
                            <div><i class="fas fa-times-circle mr-2 text-red-500"></i><span class="font-medium">Raison refus:</span> Justificatif manquant</div>
                            <div><i class="fas fa-comment mr-2 text-orange-500"></i><span class="font-medium">Motif:</span> Grippe saisonnière</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button class="p-2 text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                    <i class="fas fa-redo"></i>
                </button>
                <button class="p-2 text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                    <i class="fas fa-eye"></i>
                </button>
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
                                <li><a href="tableau de bord-employers.html" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Tableau de bord</a></li>
                                <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Mes demandes</a></li>
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
<script src="assets/javascript/calandrier-employers.js"></script>
</body>
</html>