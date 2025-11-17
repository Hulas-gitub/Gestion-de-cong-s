<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graxel Tech - Gestion des demande de congés</title>
            <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <script src="{{ asset('assets/javascript/config.js') }}"></script>
    <script src="{{ asset('assets/javascript/animate.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/calendrier-manager.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css')}}">   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.2.0"></script>
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
                    <div class="w-30 h-30 rounded-2xl flex items-center justify-center shadow-lg">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo Graxel Tech" class="w-full h-full object-contain rounded-2xl" />
                    </div>
                </div>

            <!-- Navigation -->
<nav class="space-y-3 flex-1">
    <a href="{{ url('chef-de-departement/tableau-de-bord-manager') }}" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
        <i class="fas fa-chart-pie w-5 h-5 text-lg"></i>
        <span>Tableau de bord</span>
    </a>
    <a href="{{ url('chef-de-departement/demandes-equipe') }}" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
        <i class="fas fa-users w-5 h-5 text-lg"></i>
        <span>Equipe</span>
    </a>
    <a href="#" class="nav-item flex items-center space-x-4 px-4 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover-lift transition-all duration-300 click-scale shadow-lg">
        <i class="fas fa-umbrella-beach w-5 h-5 text-lg"></i>
        <span class="font-medium">Congés</span>
    </a>
    <a href="{{ url('chef-de-departement/informations') }}" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
        <i class="fas fa-circle-info w-5 h-5 text-lg"></i>
        <span>Informations</span>
    </a>
    <a href="{{ url('chef-de-departement/profile') }}" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
        <i class="fas fa-user w-5 h-5 text-lg"></i>
        <span>Mon profile</span>
    </a>
</nav>

          <!-- User Profile -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
    <div class="flex items-center space-x-4 mb-4">
        @auth
            @php
                // Récupérer les informations de l'utilisateur
                $prenom = Auth::user()->prenom ?? '';
                $nom = Auth::user()->nom ?? '';
                $initiales = strtoupper(substr($nom, 0, 1) . substr($prenom, 0, 1));
                $nomComplet = trim($nom. ' ' . $prenom);
                $role = Auth::user()->role->nom_role ?? 'Utilisateur';

                // Vérifier si une photo existe
                $photoUrl = Auth::user()->photo_url;
                $hasPhoto = $photoUrl && Storage::disk('public')->exists($photoUrl);

                // Couleurs aléatoires pour les initiales
                $colors = [
                    'from-purple-400 to-pink-400',
                    'from-blue-400 to-indigo-400',
                    'from-green-400 to-teal-400',
                    'from-orange-400 to-red-400',
                    'from-yellow-400 to-orange-400',
                    'from-pink-400 to-rose-400',
                ];
                $colorIndex = strlen($nomComplet) % count($colors);
                $gradient = $colors[$colorIndex];
            @endphp

            @if($hasPhoto)
                <!-- Photo de profil -->
                <img
                    src="{{ asset('storage/' . $photoUrl) }}"
                    alt="Photo de profil"
                    class="w-12 h-12 rounded-full object-cover animate-float ring-2 ring-white dark:ring-gray-700 shadow-lg"
                >
            @else
                <!-- Initiales si pas de photo -->
                <div class="w-12 h-12 bg-gradient-to-r {{ $gradient }} rounded-full flex items-center justify-center text-white font-bold text-lg animate-float shadow-lg">
                    {{ $initiales }}
                </div>
            @endif

            <div class="flex-1">
                <p class="font-semibold text-gray-900 dark:text-white">{{ $nomComplet }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($role) }}</p>
            </div>
        @else
            <div class="w-12 h-12 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center text-white font-bold text-lg animate-float">
                ?
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900 dark:text-white">Utilisateur</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Non connecté</p>
            </div>
        @endauth

        <a href="#" id="logoutBtn" class="flex items-center space-x-3 text-red-600 hover:text-red-700 dark:text-red-400 text-sm hover-lift transition-all duration-200 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
            <i class="fas fa-sign-out-alt w-4 h-4"></i>
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
    <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
        Gestion des demandes congés
    </h1>
    <p class="text-gray-600 dark:text-gray-400 mt-1">
        <i class="fas fa-users mr-2"></i>
        <span id="current-date"></span>
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

            <div class="p-4 md:p-8 space-y-8">
                   <!-- Section pour le filtre par employé (à ajouter avant les boutons de filtre) -->
<!-- Container principal avec bon espacement -->
<div class="p-4 md:p-6">

    <!-- Filtre par employé - Collapsible sur mobile -->
    <div id="employeeFilterContainer" class="mb-4 relative" style="z-index: 10;">
        <!-- Contenu généré dynamiquement -->
    </div>

    <!-- Boutons de filtre avec scroll horizontal -->
    <div class="mb-4 sm:mb-6 relative" style="z-index: 5;">
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide -mx-2 px-2 sm:mx-0 sm:px-0">
            <button class="filter-button active bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg px-3 sm:px-6 py-2 sm:py-3 font-medium rounded-xl transition-all duration-300 hover-lift click-scale whitespace-nowrap flex-shrink-0 text-sm sm:text-base" data-filter="all">
                <i class="fas fa-list mr-1 sm:mr-2"></i>
                <span>Tous</span>
            </button>

            <button class="filter-button bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 sm:px-6 py-2 sm:py-3 font-medium rounded-xl transition-all duration-300 hover-lift click-scale whitespace-nowrap flex-shrink-0 text-sm sm:text-base" data-filter="pending">
                <i class="fas fa-hourglass-half mr-1 sm:mr-2"></i>
                <span>En attente</span>
            </button>

            <button class="filter-button bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 sm:px-6 py-2 sm:py-3 font-medium rounded-xl transition-all duration-300 hover-lift click-scale whitespace-nowrap flex-shrink-0 text-sm sm:text-base" data-filter="approved">
                <i class="fas fa-check-circle mr-1 sm:mr-2"></i>
                <span class="hidden sm:inline">Employés en congé</span>
                <span class="sm:hidden">En congé</span>
            </button>

            <button class="filter-button bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 sm:px-6 py-2 sm:py-3 font-medium rounded-xl transition-all duration-300 hover-lift click-scale whitespace-nowrap flex-shrink-0 text-sm sm:text-base" data-filter="rejected">
                <i class="fas fa-times-circle mr-1 sm:mr-2"></i>
                <span>Refusées</span>
            </button>
        </div>
    </div>

    <!-- Contenu dynamique -->
    <div id="dynamicContent" class="relative" style="z-index: 1;">
        <!-- Le contenu change selon le filtre sélectionné -->
    </div>
</div>


        </div>

        <br><br>
             <!-- Footer -->
            <footer class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-t border-gray-200/50 dark:border-gray-700/50 p-6 mt-8">
                <div class="max-w-7xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div class="col-span-1 md:col-span-2">
                            <div class="flex items-center space-x-4 mb-4">

                            </div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                © 2025 Graxel Technologies. Tous droits réservés. <br> Une solution dédiée aux chefs de département pour une gestion optimale des congés et des ressources humaines.
                            </p>
                        </div>

                    </div>

                </div>
            </footer>
    </div>

<!-- Modal de visualisation des congés approuvés/refusés -->
<div id="leaveDetailsModal" class="fixed inset-0 z-50 hidden">
    <div class="backdrop absolute inset-0 bg-black bg-opacity-50" onclick="closeModal('leaveDetailsModal')"></div>
    <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-900 dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full overflow-hidden">
            <!-- Header avec bouton fermer -->
            <div class="flex items-center justify-between p-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Détails du congé</h3>
                <button class="text-gray-400 hover:text-white transition-colors" onclick="closeModal('leaveDetailsModal')">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-4">
                <!-- Avatar et nom -->
                <div class="flex items-center space-x-4 mb-6">
                    <div id="leaveDetailsAvatar" class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-white text-xl"></i>
                    </div>
                    <div>
                        <h4 id="leaveDetailsName" class="text-xl font-bold text-white">Marc Petit</h4>
                        <p id="leaveDetailsType" class="text-sm text-gray-400">Maternité</p>
                    </div>
                </div>

                <!-- Informations -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">Date de début:</span>
                        <span id="leaveDetailsStartDate" class="text-white font-semibold">05/10/2025</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">Date de fin:</span>
                        <span id="leaveDetailsEndDate" class="text-white font-semibold">12/10/2025</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">Durée:</span>
                        <span id="leaveDetailsDuration" class="text-white font-semibold">7 jour(s)</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">Solde restant:</span>
                        <span id="leaveDetailsBalance" class="text-white font-semibold">6 jours</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">Type de congé:</span>
                        <span id="leaveDetailsTypeBadge" class="px-3 py-1 text-xs font-semibold rounded-full bg-pink-500 text-white">
                            Maternité
                        </span>
                    </div>
                </div>

                <!-- Motif -->
                <div class="space-y-2 pt-4 border-t border-gray-700">
                    <label class="text-gray-400 text-sm">Motif:</label>
                    <p id="leaveDetailsReason" class="text-white text-sm bg-gray-800 p-3 rounded-lg">
                        Congé maternité pour la naissance de mon second enfant.
                    </p>
                </div>

                <!-- Document justificatif -->
                <div id="leaveDetailsDocumentSection" class="pt-4 border-t border-gray-700">
                    <!-- Contenu dynamique -->
                </div>

                <!-- Statut -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-700">
                    <span class="text-gray-400 text-sm">Statut:</span>
                    <span id="leaveDetailsStatusBadge" class="px-4 py-2 text-sm font-semibold rounded-lg bg-green-500 text-white">
                        <i class="fas fa-check-circle mr-1"></i>Approuvé
                    </span>
                </div>

                <!-- Boutons d'action (masqués si approuvé/refusé) -->
                <div id="leaveDetailsActions" class="flex items-center space-x-3 pt-4 border-t border-gray-700">
                    <button class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors text-sm font-medium" onclick="closeModal('leaveDetailsModal'); showConfirmModal('reject', currentRequestId)">
                        <i class="fas fa-times mr-1"></i>Refuser
                    </button>
                    <button class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors text-sm font-medium" onclick="closeModal('leaveDetailsModal'); showConfirmModal('approve', currentRequestId)">
                        <i class="fas fa-check mr-1"></i>Approuver
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation simple -->
<div id="confirmModal" class="fixed inset-0 z-50 hidden">
    <div class="backdrop absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6">
            <div class="flex flex-col items-center text-center space-y-4">
                <div id="confirmIcon" class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-500"></i>
                </div>
                <h3 id="confirmTitle" class="text-xl font-semibold text-gray-900 dark:text-white">Approuver la demande</h3>
                <p id="confirmMessage" class="text-gray-600 dark:text-gray-400">Êtes-vous sûr de vouloir approuver cette demande de congés ?</p>
                <div class="w-full space-y-2 text-left bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p id="confirmDetails" class="text-sm font-medium text-gray-900 dark:text-white">Jean Martin - Congés payés</p>
                    <p id="confirmDates" class="text-xs text-gray-600 dark:text-gray-400">15/09/2025 - 20/09/2025 (5 jours)</p>
                </div>
                <div class="flex items-center space-x-3 w-full">
                    <button class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm font-medium" onclick="closeModal('confirmModal')">
                        Annuler
                    </button>
                    <button id="confirmActionBtn" class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors text-sm font-medium" onclick="executeAction()">
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
            </div>

            <!-- Actions (masqués si approuvé/refusé) -->
            <div id="detailsModalActions" class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700">
                <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm font-medium" onclick="closeModal('detailsModal')">
                    Fermer
                </button>
                <button class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors text-sm font-medium" onclick="closeModal('detailsModal'); showConfirmModal('reject', currentRequestId)">
                    <i class="fas fa-times mr-1"></i>Refuser
                </button>
                <button class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors text-sm font-medium" onclick="closeModal('detailsModal'); showConfirmModal('approve', currentRequestId)">
                    <i class="fas fa-check mr-1"></i>Approuver
                </button>
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
                        @auth
                            @php
                                // Récupérer les informations de l'utilisateur
                                $prenom = Auth::user()->prenom ?? '';
                                $nom = Auth::user()->nom ?? '';
                                $initiales = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
                                $nomComplet = trim($prenom . ' ' . $nom);
                                $role = Auth::user()->role->nom_role ?? 'Utilisateur';

                                // Vérifier si une photo existe
                                $photoUrl = Auth::user()->photo_url;
                                $hasPhoto = $photoUrl && Storage::disk('public')->exists($photoUrl);

                                // Couleurs aléatoires pour les initiales
                                $colors = [
                                    'from-purple-400 to-pink-400',
                                    'from-blue-400 to-indigo-400',
                                    'from-green-400 to-teal-400',
                                    'from-orange-400 to-red-400',
                                    'from-yellow-400 to-orange-400',
                                    'from-pink-400 to-rose-400',
                                ];
                                $colorIndex = strlen($nomComplet) % count($colors);
                                $gradient = $colors[$colorIndex];
                            @endphp

                            @if($hasPhoto)
                                <!-- Photo de profil -->
                                <img
                                    src="{{ asset('storage/' . $photoUrl) }}"
                                    alt="Photo de profil"
                                    class="w-10 h-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-600 shadow-md"
                                >
                            @else
                                <!-- Initiales si pas de photo -->
                                <div class="w-10 h-10 bg-gradient-to-r {{ $gradient }} rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md">
                                    {{ $initiales }}
                                </div>
                            @endif

                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $nomComplet }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($role) }}</p>
                            </div>
                        @else
                            <div class="w-10 h-10 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                ?
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Utilisateur</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Non connecté</p>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700">
                <button type="button"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                    onclick="closeLogoutModal()">
                    <i class="fas fa-times mr-2"></i>
                    Annuler
                </button>
                <button type="button"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                    onclick="executeLogout()">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Se déconnecter
                </button>
            </div>
        </div>
    </div>
</div>        <!-- Formulaire de déconnexion caché -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>


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

<script src="{{ asset('assets/javascript/demandes.js') }}"></script>
    <script src="{{ asset('assets/javascript/dashboard-manager.js') }}"></script>
<script src="{{ asset('assets/javascript/logout.js') }}"></script>
<script src="{{ asset('assets/javascript/config.js') }}"></script>

</body>
</html>
