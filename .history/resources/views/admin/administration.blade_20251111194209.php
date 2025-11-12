<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graxel Tech - Demandes de congé admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
<script>
    window.laravelData = {
        roles: @json($roles),
        departements: @json($allDepartements),
        chefs: @json($allChefs),
        csrfToken: '{{ csrf_token() }}',
    };
</script>
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
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo Graxel Tech" class="w-full h-full object-contain rounded-2xl" />
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

            <div class="w-12 h-12 bg-gradient-to-r {{ $gradient }} rounded-full flex items-center justify-center text-white font-bold text-lg animate-float">
                {{ $initiales }}
            </div>
            <div class="flex-2">
                <p class="font-semibold text-gray-900 dark:text-white">{{ $nomComplet }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($role) }}</p>
            </div>
        @else
            <div class="w-12 h-12 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center text-white font-bold text-lg animate-float">
                ?
            </div>
            <div class="flex-2">
                <p class="font-semibold text-gray-900 dark:text-white">Utilisateur</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Non connecté</p>
            </div>
        @endauth

        <a href="#" id="logoutBtn" class="flex items-center space-x-3 text-red-600 hover:text-red-700 dark:text-red-400 text-sm hover-lift transition-all duration-200 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
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
                            <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Administration </h1>
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
<!-- Dashboard Content (à fleur) -->
    <div class="p-0 md:p-0 space-y-6 w-full">  <!-- Section principale avec filtres -->
                <div id="mainSection" class="grid grid-cols-1 gap-0 w-full mb-6">
                    <div class="w-full">
                        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-none shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden w-full">
                            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-blue-500/5 to-purple-500/5">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Administration</h1>
            <p class="text-gray-600 dark:text-gray-400">Gérer les employés, chefs de département et départements</p>
        </div>

                                <!-- Navigation par onglets -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-4 mb-6 animate-slide-up">
            <div class="flex flex-wrap gap-2">
                <button class="tab-button active px-6 py-3 rounded-lg font-medium transition-all" data-tab="employes">
                    <i class="fas fa-users mr-2"></i>Employés
                </button>
                <button class="tab-button px-6 py-3 rounded-lg font-medium transition-all" data-tab="chefs">
                    <i class="fas fa-user-tie mr-2"></i>Chefs de Département
                </button>
                <button class="tab-button px-6 py-3 rounded-lg font-medium transition-all" data-tab="departements">
                    <i class="fas fa-building mr-2"></i>Départements
                </button>
            </div>
        </div>

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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Profession</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($employes as $employe)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $employe->matricule }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $employe->nom }} {{ $employe->prenom }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $employe->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $employe->departement->nom_departement ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $employe->profession ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $employe->actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $employe->actif ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="viewUser({{ $employe->id_user }})" title="Voir détails" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editEmploye({{ $employe->id_user }})" title="Modifier" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="toggleStatus({{ $employe->id_user }}, '{{ $employe->actif ? 'bloquer' : 'débloquer' }}')" title="{{ $employe->actif ? 'Bloquer' : 'Débloquer' }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 mr-3">
                                        <i class="fas fa-{{ $employe->actif ? 'lock' : 'unlock' }}"></i>
                                    </button>
                                    @if(!$employe->actif)
                                    <button onclick="resendActivation({{ $employe->id_user }})" title="Renvoyer email d'activation" class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 mr-3">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    @endif
                                    <button onclick="deleteUser({{ $employe->id_user }}, 'employe')" title="Supprimer" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p>Aucun employé trouvé</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Employés -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $employes->links() }}
                </div>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Téléphone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($chefs as $chef)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $chef->matricule }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $chef->nom }} {{ $chef->prenom }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $chef->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $chef->departement->nom_departement ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $chef->telephone ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="viewUser({{ $chef->id_user }})" title="Voir détails" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editChef({{ $chef->id_user }})" title="Modifier" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="toggleStatus({{ $chef->id_user }}, '{{ $chef->actif ? 'bloquer' : 'débloquer' }}')" title="{{ $chef->actif ? 'Bloquer' : 'Débloquer' }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 mr-3">
                                        <i class="fas fa-{{ $chef->actif ? 'lock' : 'unlock' }}"></i>
                                    </button>
                                    @if(!$chef->actif)
                                    <button onclick="resendActivation({{ $chef->id_user }})" title="Renvoyer email d'activation" class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 mr-3">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    @endif
                                    <button onclick="deleteUser({{ $chef->id_user }}, 'chef')" title="Supprimer" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p>Aucun chef de département trouvé</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Chefs -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $chefs->links() }}
                </div>
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
                            @forelse($departements as $dept)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $dept->id_departement }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $dept->nom_departement }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($dept->description ?? 'Aucune description', 50) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($dept->chefDepartement)
                                        {{ $dept->chefDepartement->nom }} {{ $dept->chefDepartement->prenom }}
                                    @else
                                        <span class="text-gray-400">Aucun chef</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                        {{ $dept->employes_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="viewDepartement({{ $dept->id_departement }})" title="Voir détails" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editDepartement({{ $dept->id_departement }})" title="Modifier" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteDepartement({{ $dept->id_departement }})" title="Supprimer" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p>Aucun département trouvé</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Départements -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $departements->links() }}
                </div>
            </div>
        </div>
    </div><!-- Modal Employé -->
<div id="employe-modal" class="fixed inset-0 z-50 hidden">
    <div class="backdrop absolute inset-0 bg-black bg-opacity-50 transition-opacity duration-300" style="opacity: 0;" onclick="closeModal('employe-modal')"></div>
    <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4 transition-all duration-300" style="transform: scale(0.95); opacity: 0;">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 id="employe-modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">Ajouter un employé</h3>
            </div>
            <div class="p-6">
                <form id="employe-form">
                    @csrf
                    <input type="hidden" id="employe-id" name="id">
                    <input type="hidden" id="employe-role-id" name="role_id" value="{{ $roles->where('nom_role', 'Employé')->first()->id_role ?? '' }}">

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom <span class="text-red-500">*</span></label>
                                <input type="text" name="nom" id="employe-nom" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prénom <span class="text-red-500">*</span></label>
                                <input type="text" name="prenom" id="employe-prenom" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Solde congés annuel <span class="text-red-500">*</span></label>
                            <input type="number" name="solde_conges_annuel" id="employe-solde-conges" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" min="0" max="60" value="30" required>
                        </div>
                    </div>

                    <div class="flex space-x-3 mt-6">
                        <button type="button" onclick="closeModal('employe-modal')" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors">
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
    <div class="backdrop absolute inset-0 bg-black bg-opacity-50 transition-opacity duration-300" style="opacity: 0;" onclick="closeModal('chef-modal')"></div>
    <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4 transition-all duration-300" style="transform: scale(0.95); opacity: 0;">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 id="chef-modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">Ajouter un chef de département</h3>
            </div>
            <div class="p-6">
                <form id="chef-form">
                    @csrf
                    <input type="hidden" id="chef-id" name="id">
                    <input type="hidden" id="chef-role-id" name="role_id" value="{{ $roles->where('nom_role', 'LIKE', '%chef%')->first()->id_role ?? '' }}">

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom <span class="text-red-500">*</span></label>
                                <input type="text" name="nom" id="chef-nom" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prénom <span class="text-red-500">*</span></label>
                                <input type="text" name="prenom" id="chef-prenom" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="chef-email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Téléphone</label>
                            <input type="tel" name="telephone" id="chef-telephone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="+241 XX XX XX XX">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Matricule <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <input type="text" name="matricule" id="chef-matricule" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                                <button type="button" onclick="generateMatricule('chef')" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                    <i class="fas fa-magic"></i> Générer
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Profession</label>
                            <input type="text" name="profession" id="chef-profession" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Ex: Directeur, Manager, Responsable...">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date d'embauche <span class="text-red-500">*</span></label>
                            <input type="date" name="date_embauche" id="chef-date-embauche" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Département</label>
                            <select name="departement_id" id="chef-departement" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Sélectionner un département</option>
                                @foreach($allDepartements as $dept)
                                <option value="{{ $dept->id_departement }}">{{ $dept->nom_departement }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Solde congés annuel <span class="text-red-500">*</span></label>
                            <input type="number" name="solde_conges_annuel" id="chef-solde-conges" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" min="0" max="60" value="30" required>
                        </div>
                    </div>

                    <div class="flex space-x-3 mt-6">
                        <button type="button" onclick="closeModal('chef-modal')" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors">
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
    <div class="backdrop absolute inset-0 bg-black bg-opacity-50 transition-opacity duration-300" style="opacity: 0;" onclick="closeModal('departement-modal')"></div>
    <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4 transition-all duration-300" style="transform: scale(0.95); opacity: 0;">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 id="departement-modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">Ajouter un département</h3>
            </div>
            <div class="p-6">
                <form id="departement-form">
                    @csrf
                    <input type="hidden" id="departement-id" name="id">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom du département <span class="text-red-500">*</span></label>
                            <input type="text" name="nom_departement" id="departement-nom" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea name="description" id="departement-description" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Chef de département</label>
                            <select name="chef_departement_id" id="departement-chef" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Sélectionner un chef</option>
                                @foreach($allChefs as $chef)
                                <option value="{{ $chef->id_user }}">{{ $chef->nom }} {{ $chef->prenom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Couleur (calendrier)</label>
                            <input type="color" name="couleur_calendrier" id="departement-couleur" class="w-full h-10 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700" value="#3b82f6">
                        </div>
                    </div>
                    <div class="flex space-x-3 mt-6">
                        <button type="button" onclick="closeModal('departement-modal')" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors">
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
    <div class="backdrop absolute inset-0 bg-black bg-opacity-50 transition-opacity duration-300" style="opacity: 0;" onclick="closeModal('delete-confirm-modal')"></div>
    <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4 transition-all duration-300" style="transform: scale(0.95); opacity: 0;">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-trash text-red-500 text-xl"></i>
                    </div>
                </div>
                <h3 id="delete-confirm-title" class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Confirmer la suppression</h3>
                <p id="delete-confirm-message" class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.</p>
                <div class="flex space-x-3">
                    <button onclick="closeModal('delete-confirm-modal')" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Annuler
                    </button>
                    <button id="confirm-delete-btn" class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-trash mr-2"></i>Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de visualisation -->
<div id="view-modal" class="fixed inset-0 z-50 hidden">
    <div class="backdrop absolute inset-0 bg-black bg-opacity-50 transition-opacity duration-300" style="opacity: 0;" onclick="closeModal('view-modal')"></div>
    <div class="modal relative z-10 flex items-center justify-center min-h-screen p-4 transition-all duration-300" style="transform: scale(0.95); opacity: 0;">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 id="view-modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">Détails</h3>
            </div>
            <div class="p-6">
                <div id="view-modal-content" class="space-y-3"></div>
            </div>
            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeModal('view-modal')" class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>
<script>
window.adminData = {
 csrfToken: '{{ csrf_token() }}',  // ← AJOUTEZ CETTE LIGNE
    routes: {
        usersStore: '{{ route("admin.users.store") }}',
        usersUpdate: '{{ url("admin/users") }}/',
        usersShow: '{{ url("admin/users") }}/',
        usersDelete: '{{ url("admin/users") }}/',
        usersBlock: '{{ url("admin/users") }}/',
        usersUnblock: '{{ url("admin/users") }}/',
        usersResendActivation: '{{ url("admin/users") }}/',
        departementsStore: '{{ route("admin.departements.store") }}',
        departementsUpdate: '{{ url("admin/departements") }}/',
        departementsShow: '{{ url("admin/departements") }}/',
        departementsDelete: '{{ url("admin/departements") }}/',
        generateMatricule: '{{ route("admin.users.generate-matricule") }}'
    }
};
</script>


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

            <!-- Footer -->
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

                            <div class="w-10 h-10 bg-gradient-to-r {{ $gradient }} rounded-full flex items-center justify-center text-white font-bold text-sm">
                                {{ $initiales }}
                            </div>
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
                <button type="button" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors" onclick="closeLogoutModal()">
                    <i class="fas fa-times mr-2"></i>
                    Annuler
                </button>
                <button type="button" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors" onclick="executeLogout()">
                    <i class="fas fa-sign-out-alt mr-2"></i>
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

    <script src="{{asset('assets/javascript/administration.js')}}"></script>
    <script src="{{asset('assets/javascript/logout.js')}}"></script>
    <script src="{{asset('assets/javascript/config.js')}}"></script>
</body>
</html>
