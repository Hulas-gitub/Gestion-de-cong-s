<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graxel Tech - Demandes de congé admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Vos fichiers existants -->
    <script src="{{asset('assets/javascript/config.js')}}"></script>
    <script src="{{asset('assets/javascript/animate.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets/css/demandes.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <link rel="icon" type="image/png" href="{{asset('assets/images/logo.png')}}">
 <!-- Scripts -->
<script>
    // Injecter les données Laravel dans le JavaScript
    window.laravelData = {
        roles: @json($roles ?? []),
        departements: @json($allDepartements ?? []),
        users: @json($users ?? [])
    };
</script>
<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <div class="w-25 h-25 rounded-2xl flex items-center justify-center shadow-lg">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo Graxel Tech"
                            class="w-full h-full object-contain rounded-2xl" />
                    </div>
                </div>
                <!-- Navigation -->
                <nav class="space-y-3 flex-1">
                    <a href="{{ url('admin/dashboard-admin') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-chart-pie w-5 h-5 text-lg"></i>
                        <span>Tableau de bord</span>
                    </a>
                    <a href="#"
                        class="nav-item flex items-center space-x-4 px-4 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover-lift transition-all duration-300 click-scale shadow-lg">
                        <i class="fas fa-clipboard-list w-5 h-5 text-lg"></i>
                        <span class="font-medium">Administration</span>
                    </a>
                    <a href="{{ url('admin/calendrier-admin') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-umbrella-beach w-5 h-5 text-lg"></i>
                        <span>Congés</span>
                    </a>
                    <a href="{{ url('admin/profile') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-user w-5 h-5 text-lg"></i>
                        <span>Mon profile</span>
                    </a>

                </nav>

                <!-- User Profile -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                    <div class="flex items-center space-x-4 mb-4">
                        @auth
                        @php
                        // Récupérer les initiales de l'utilisateur
                        $prenom = Auth::user()->prenom ?? '';
                        $nom = Auth::user()->nom ?? '';
                        $initiales = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));

                        // Nom complet
                        $nomComplet = trim($prenom . ' ' . $nom);

                        // Rôle de l'utilisateur
                        $role = Auth::user()->role->nom_role ?? 'Utilisateur';

                        // Couleurs aléatoires basées sur le nom (pour cohérence)
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

                        <div
                            class="w-12 h-12 bg-gradient-to-r {{ $gradient }} rounded-full flex items-center justify-center text-white font-bold text-lg animate-float">
                            {{ $initiales }}
                        </div>
                        <div class="flex-2">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $nomComplet }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($role) }}</p>
                        </div>
                        @else
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center text-white font-bold text-lg animate-float">
                            ?
                        </div>
                        <div class="flex-2">
                            <p class="font-semibold text-gray-900 dark:text-white">Utilisateur</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Non connecté</p>
                        </div>
                        @endauth

                        <a href="#" id="logoutBtn"
                            class="flex items-center space-x-3 text-red-600 hover:text-red-700 dark:text-red-400 text-sm hover-lift transition-all duration-200 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
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
            <div
                class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg shadow-lg border-b border-gray-200/50 dark:border-gray-700/50 px-4 md:px-0 py-6 animate-fade-in sticky top-0 z-30 w-full">
                <div class="max-w-full mx-0 px-4 md:px-6 flex justify-between items-center w-full">
                    <div class="flex items-center space-x-4">
                        <button id="toggle-sidebar"
                            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden">
                            <i class="fas fa-bars text-gray-600 dark:text-gray-400"></i>
                        </button>
                        <div>
                            <h1
                                class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                Administration </h1>
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
                                <button id="notifications-btn"
                                    class="p-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors relative">
                                    <i class="fas fa-bell text-lg"></i>
                                    <span class="notification-count">5</span>
                                </button>

                                <!-- Dropdown notifications CORRIGÉ -->
                                <div id="notifications-dropdown" class="notification-dropdown">
                                    <div class="dropdown-header">
                                        <h3>Notifications</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">5 nouvelles
                                            notifications</p>
                                    </div>

                                    <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                        <div class="p-2 space-y-1">
                                            <!-- Notification Nouvelle Demande -->
                                            <div
                                                class="notification-item p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg cursor-pointer transition-colors border-l-4 border-yellow-500">
                                                <div class="flex-shrink-0">
                                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mt-1 animate-pulse">
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0 pl-3">
                                                    <p
                                                        class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                        Nouvelle demande de Jean Martin</p>
                                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Congé
                                                        annuel du 15/09 au 20/09</p>
                                                    <div class="flex items-center justify-between mt-2">
                                                        <span class="text-xs text-gray-400">Il y a 1h</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Notification Demande Approuvée -->
                                            <div
                                                class="notification-item p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg cursor-pointer transition-colors border-l-4 border-green-500">
                                                <div class="flex-shrink-0">
                                                    <div class="w-3 h-3 bg-green-500 rounded-full mt-1 animate-pulse">
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0 pl-3">
                                                    <p
                                                        class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                        Demande approuvée</p>
                                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Congé
                                                        maladie de Marie Dupont validé</p>
                                                    <div class="flex items-center justify-between mt-2">
                                                        <span class="text-xs text-gray-400">Il y a 2h</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Notification Chevauchement -->
                                            <div
                                                class="notification-item p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg cursor-pointer transition-colors border-l-4 border-red-500">
                                                <div class="flex-shrink-0">
                                                    <div class="w-3 h-3 bg-red-500 rounded-full mt-1 animate-pulse">
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0 pl-3">
                                                    <p
                                                        class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                        ⚠️ 3 employés absents le 18/09</p>
                                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Jean,
                                                        Marie, Pierre</p>
                                                    <div class="flex items-center justify-between mt-2">
                                                        <span class="text-xs text-gray-400">Il y a 3h</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-2 border-t border-gray-200 dark:border-gray-700 text-center">
                                        <button
                                            class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                            Voir toutes les notifications
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Bouton de thème (conservé pour la cohérence visuelle, mais la logique est dans config.js) -->
                        <button id="theme-toggle"
                            class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 click-scale hover-lift">
                            <i class="fas fa-moon dark:hidden text-gray-600 text-lg"></i>
                            <i class="fas fa-sun hidden dark:block text-yellow-400 text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
<!-- Dashboard Content -->
<div class="p-0 md:p-0 space-y-6 w-full">
    <!-- Section principale avec filtres -->
    <div id="mainSection" class="grid grid-cols-1 gap-0 w-full mb-6">
        <div class="w-full">
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-none shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden w-full">
                <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-blue-500/5 to-purple-500/5">
                    <div class="container mx-auto px-4 py-8">
                        <div class="mb-8">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Gestion des utilisateurs</h1>
                            <p class="text-gray-600 dark:text-gray-400">Gérer les employés, chefs de département et départements</p>
                        </div>

                        <!-- Navigation par onglets -->
                        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-4 animate-slide-up">
                            <div class="flex flex-wrap gap-2">
                                <button class="tab-button active" data-tab="employes">Employés</button>
                                <button class="tab-button" data-tab="chefs">Chefs de Département</button>
                                <button class="tab-button" data-tab="departements">Départements</button>
                            </div>
                        </div>

                        <!-- Contenu des onglets -->
                        <div class="tab-content mt-6">
                            <!-- Onglet Employés -->
                            <div id="employes-tab" class="tab-pane active">
                                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden">
                                    <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-blue-500/5 to-purple-500/5">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Gestion des Employés</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Liste de tous les employés de l'entreprise</p>
                                            </div>
                                            <button id="add-employe-btn" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors">
                                                <i class="fas fa-plus mr-2"></i>Ajouter un employé
                                            </button>
                                        </div>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Matricule</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Département</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Poste</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                <!-- Le contenu sera chargé dynamiquement via JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Conteneur de pagination (sera rempli par JS) -->
                                    <div class="pagination-container"></div>
                                </div>
                            </div>

                            <!-- Onglet Chefs de Département -->
                            <div id="chefs-tab" class="tab-pane hidden">
                                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden">
                                    <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-blue-500/5 to-purple-500/5">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Gestion des Chefs de Département</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Liste de tous les chefs de département</p>
                                            </div>
                                            <button id="add-chef-btn" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors">
                                                <i class="fas fa-plus mr-2"></i>Ajouter un chef
                                            </button>
                                        </div>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Matricule</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Département</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date de nomination</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                <!-- Le contenu sera chargé dynamiquement via JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Conteneur de pagination (sera rempli par JS) -->
                                    <div class="pagination-container"></div>
                                </div>
                            </div>

                            <!-- Onglet Départements -->
                            <div id="departements-tab" class="tab-pane hidden">
                                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden">
                                    <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-blue-500/5 to-purple-500/5">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Gestion des Départements</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Liste de tous les départements de l'entreprise</p>
                                            </div>
                                            <button id="add-departement-btn" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors">
                                                <i class="fas fa-plus mr-2"></i>Ajouter un département
                                            </button>
                                        </div>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Chef de département</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre d'employés</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                <!-- Le contenu sera chargé dynamiquement via JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Conteneur de pagination (sera rempli par JS) -->
                                    <div class="pagination-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-t border-gray-200/50 dark:border-gray-700/50 p-6 mt-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-4 mb-4">
                        <!-- Logo si nécessaire -->
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        © 2025 Graxel Technologies. Tous droits réservés. <br>
                        Une solution dédiée aux chefs de département pour une gestion optimale des congés et des ressources humaines.
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div>

<!-- ========================================
     MODALS - TOUS LES MODALS SONT ICI
     ======================================== -->

<!-- Modal Employé -->
<div id="employe-modal" class="fixed inset-0 z-50 hidden">
    <div class="backdrop absolute inset-0 bg-black bg-opacity-50" onclick="closeModal('employe-modal')"></div>
    <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 id="employe-modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">
                    Ajouter un employé
                </h3>
            </div>
            <div class="p-6">
                <form id="employe-form">
                    <div class="space-y-4">
                        <!-- Matricule (en lecture seule) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Matricule</label>
                            <input type="text" id="employe-matricule" readonly
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white">
                        </div>

                        <!-- Nom et Prénom -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom <span class="text-red-500">*</span></label>
                                <input type="text" id="employe-nom" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prénom <span class="text-red-500">*</span></label>
                                <input type="text" id="employe-prenom" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>

                        <!-- Contact -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact (Téléphone)</label>
                            <input type="tel" id="employe-contact" placeholder="+241 XX XX XX XX"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="employe-email" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <!-- Rôle -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rôle <span class="text-red-500">*</span></label>
                            <select id="employe-role" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <!-- Rempli dynamiquement -->
                            </select>
                        </div>

                        <!-- Poste -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Poste <span class="text-red-500">*</span></label>
                            <input type="text" id="employe-poste" required placeholder="Ex: Développeur, Comptable..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <!-- Département -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Département</label>
                            <select id="employe-departement"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <!-- Rempli dynamiquement -->
                            </select>
                        </div>
                    </div>
    <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date d'ambauche <span class="text-red-500">*</span></label>
                            <input type="date" id="chef-date-nomination" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
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
                <h3 id="chef-modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">
                    Ajouter un chef de département
                </h3>
            </div>
            <div class="p-6">
                <form id="chef-form">
                    <div class="space-y-4">
                        <!-- Matricule -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Matricule</label>
                            <input type="text" id="chef-matricule" readonly
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white">
                        </div>

                        <!-- Nom et Prénom -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom <span class="text-red-500">*</span></label>
                                <input type="text" id="chef-nom" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prénom <span class="text-red-500">*</span></label>
                                <input type="text" id="chef-prenom" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>

                        <!-- Contact -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact (Téléphone)</label>
                            <input type="tel" id="chef-contact" placeholder="+241 XX XX XX XX"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="chef-email" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <!-- Rôle -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rôle <span class="text-red-500">*</span></label>
                            <select id="chef-role" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <!-- Rempli dynamiquement -->
                            </select>
                        </div>

                        <!-- Poste -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Poste <span class="text-red-500">*</span></label>
                            <input type="text" id="chef-poste" required placeholder="Ex: Directeur, Manager..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <!-- Département -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Département</label>
                            <select id="chef-departement"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <!-- Rempli dynamiquement -->
                            </select>
                        </div>

                        <!-- Date de nomination -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date d'ambauche <span class="text-red-500">*</span></label>
                            <input type="date" id="chef-date-nomination" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
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
                <h3 id="departement-modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">
                    Ajouter un département
                </h3>
            </div>
            <div class="p-6">
                <form id="departement-form">
                    <div class="space-y-4">
                        <!-- Nom du département -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nom du département <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="departement-nom" required placeholder="Ex: Ressources Humaines"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea id="departement-description" rows="3" placeholder="Description du département..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                        </div>

                        <!-- Chef de département -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Chef de département</label>
                            <select id="departement-chef"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Sélectionner un chef</option>
                                <!-- Rempli dynamiquement -->
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
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Confirmation de déconnexion
                            </h3>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Êtes-vous sûr de vouloir vous déconnecter ?</p>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center space-x-3">
                                @auth
                                @php
                                // Récupérer les initiales de l'utilisateur
                                $prenom = Auth::user()->prenom ?? '';
                                $nom = Auth::user()->nom ?? '';
                                $initiales = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));

                                // Nom complet
                                $nomComplet = trim($prenom . ' ' . $nom);

                                // Rôle de l'utilisateur
                                $role = Auth::user()->role->nom_role ?? 'Utilisateur';

                                // Couleurs aléatoires basées sur le nom (pour cohérence)
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

                                <div
                                    class="w-10 h-10 bg-gradient-to-r {{ $gradient }} rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ $initiales }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $nomComplet }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($role) }}</p>
                                </div>
                                @else
                                <div
                                    class="w-10 h-10 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
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
                    <div
                        class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700">
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
        </div>

        <!-- Toast notification de déconnexion -->
        <div id="logoutToast"
            class="fixed top-4 right-4 z-50 transform translate-x-full transition-transform duration-300">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-4 border-l-4 border-l-green-500 max-w-sm">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">Déconnexion réussie</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Vous allez être redirigé...</p>
                    </div>
                </div>
            </div>
        </div>

        <script src="{{asset('assets/javascript/administration.js')}}"></script>
        <script src="{{asset('assets/javascript/logout.js')}}"></script>
        <script src="{{asset('assets/javascript/config.js')}}"></script>
</body>

</html>
