<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graxel Tech - Mon Profile</title>
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
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css')}}">
    <script>
    // Injecter les données Laravel dans le JavaScript
    window.laravelData = {
        roles: @json($roles ?? []),
        departements: @json($allDepartements ?? []),
        users: @json($users ?? [])
    };
</script>
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <a href="{{ url('employes/tableau-de-bord-employers') }}" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-chart-pie w-5 h-5 text-lg"></i>
                        <span>Vue d'ensemble</span>
                    </a>
                    <a href="{{ url('employes/conges-employers') }}" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-clipboard-list w-5 h-5 text-lg"></i>
                        <span>Mes demandes</span>
                    </a>
                     <a href="{{ url('employes/calendrier-employers') }}" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                     <i class="fas fa-umbrella-beach w-5 h-5 text-lg"></i>
                        <span>Calendrier de congé</span>
                    </a>
                     <a href="#" class="nav-item flex items-center space-x-4 px-4 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover-lift transition-all duration-300 click-scale shadow-lg">
                      <i class="fas fa-user w-5 h-5 text-lg"></i>
                        <span class="font-medium">Mon profile</span>
                    </a>

                </nav>
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
                            <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Gestion du compte </h1>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                           <!-- Bouton notifications CORRIGÉ -->
                <div class="notifications-container">
                    <button id="notifications-btn" class="p-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors relative">
                        <i class="fas fa-bell text-lg"></i>
                    </button>

                    <!-- Dropdown notifications CORRIGÉ -->
                    <div id="notifications-dropdown" class="notification-dropdown">

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
                <!-- En-tête avec boutons de navigation -->
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-6 animate-slide-up">
                    <div class="flex flex-col space-y-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Mettez à jour votre profil</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Gérez vos informations personnelles</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button class="tab-button active bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg px-6 py-3 font-medium rounded-xl transition-all duration-300 hover-lift click-scale" data-tab="profile">
                                <i class="fas fa-user mr-2"></i>
                                Profile
                            </button>
                            <button class="tab-button bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-3 font-medium rounded-xl transition-all duration-300 hover-lift click-scale" data-tab="securite">
                                <i class="fas fa-shield-alt mr-2"></i>
                                Sécurité
                            </button>
                        </div>
                    </div>
                </div>

        <!-- Onglet Profile Content -->
<div id="profile-tab" class="tab-pane hidden">
    <!-- Photo Upload Popup -->
    <div id="photo-upload-popup" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden items-center justify-center z-[100] transition-all duration-300">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full m-4 transform transition-all duration-300 scale-95 opacity-0" id="popup-content">
            <!-- Popup Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Changer la photo de profil</h3>
                <button type="button" id="close-popup-btn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Popup Body -->
            <div class="p-6">
                <!-- Preview Zone -->
                <div class="mb-6">
                    @auth
                        @php
                            $prenom = Auth::user()->prenom ?? '';
                            $nom = Auth::user()->nom ?? '';
                            $initiales = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
                            $photoUrl = Auth::user()->photo_url;
                            $hasPhoto = $photoUrl && Storage::disk('public')->exists($photoUrl);

                            $colors = [
                                'from-purple-400 to-pink-400',
                                'from-blue-400 to-indigo-400',
                                'from-green-400 to-teal-400',
                                'from-orange-400 to-red-400',
                                'from-yellow-400 to-orange-400',
                                'from-pink-400 to-rose-400',
                            ];
                            $nomComplet = trim($prenom . ' ' . $nom);
                            $colorIndex = strlen($nomComplet) % count($colors);
                            $gradient = $colors[$colorIndex];
                        @endphp

                        @if($hasPhoto)
                            <div id="popup-preview" class="w-24 h-24 mx-auto rounded-full shadow-lg" style="background-image: url('{{ asset('storage/' . $photoUrl) }}'); background-size: cover; background-position: center;"></div>
                        @else
                            <div id="popup-preview" class="w-24 h-24 mx-auto bg-gradient-to-r {{ $gradient }} rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                                {{ $initiales }}
                            </div>
                        @endif
                    @endauth
                </div>

                <!-- Upload Zone -->
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center mb-4 hover:border-blue-500 dark:hover:border-blue-400 transition-colors cursor-pointer" id="upload-zone">
                    <div class="space-y-3">
                        <div class="w-12 h-12 mx-auto bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-cloud-upload-alt text-blue-500 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Cliquez pour téléverser</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">ou glissez-déposez votre image</p>
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500">
                            JPG, PNG, GIF (Max 2MB)
                        </div>
                    </div>
                </div>

                <!-- File Input -->
                <input type="file" id="popup-file-input" accept="image/jpeg,image/jpg,image/png,image/gif" class="hidden">

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button type="button" id="popup-cancel-btn" class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">
                        Annuler
                    </button>
                    <button type="button" id="popup-delete-btn" class="px-4 py-2 bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/30 transition-colors font-medium">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button type="button" id="popup-confirm-btn" class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-medium" disabled>
                        Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <form id="profile-form" class="contents">
            <!-- Photo de profil -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-6 animate-slide-up hover-lift transition-all duration-300">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Photo de profil</h3>
                <div class="flex flex-col items-center space-y-6">
                    <div class="relative cursor-pointer" id="change-photo-btn">
                        @auth
                            @if($hasPhoto)
                                <div id="avatar-display" class="w-32 h-32 rounded-full shadow-lg" style="background-image: url('{{ asset('storage/' . $photoUrl) }}'); background-size: cover; background-position: center;"></div>
                            @else
                                <div id="avatar-display" class="w-32 h-32 bg-gradient-to-r {{ $gradient }} rounded-full flex items-center justify-center text-white font-bold text-4xl shadow-lg animate-float">
                                    {{ $initiales }}
                                </div>
                            @endif
                        @endauth
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300" id="photo-overlay">
                            <i class="fas fa-camera text-white text-2xl"></i>
                        </div>
                    </div>
                    <input type="file" id="profile-photo" name="profile_photo" accept="image/*" class="hidden">
                </div>
            </div>

            <!-- Informations personnelles -->
            <div class="lg:col-span-2 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-6 animate-slide-up hover-lift transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Informations personnelles</h3>
                    <button type="button" id="edit-profile-btn" class="px-4 py-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors text-sm font-medium">
                        <i class="fas fa-edit mr-2"></i>Modifier
                    </button>
                </div>

                <div class="space-y-6">
                    <!-- Nom et Prénom -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nom</label>
                            <input type="text" id="lastname" name="nom" value="{{ Auth::user()->nom ?? '' }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white transition-all" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Prénom</label>
                            <input type="text" id="firstname" name="prenom" value="{{ Auth::user()->prenom ?? '' }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white transition-all" readonly>
                        </div>
                    </div>

                    <!-- Email et Téléphone -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email</label>
                            <input type="email" id="email" name="email" value="{{ Auth::user()->email ?? '' }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white transition-all" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Téléphone</label>
                            <input type="tel" id="phone" name="telephone" value="{{ Auth::user()->telephone ?? '' }}" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white transition-all" readonly>
                        </div>
                    </div>

                    <!-- Profession et Matricule -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Profession</label>
                            <input type="text" id="profession" name="profession" value="{{ Auth::user()->profession ?? '' }}" class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-600 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-500 dark:text-gray-400 cursor-not-allowed" readonly disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Matricule</label>
                            <input type="text" id="matricule" name="matricule" value="{{ Auth::user()->matricule ?? '' }}" class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-600 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-500 dark:text-gray-400 cursor-not-allowed" readonly disabled>
                        </div>
                    </div>

                    <!-- Département et Rôle -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Département</label>
                            <input type="text" id="department" name="departement" value="{{ Auth::user()->departement->nom_departement ?? 'Non assigné' }}" class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-600 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-500 dark:text-gray-400 cursor-not-allowed" readonly disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Rôle</label>
                            <input type="text" id="role" name="role" value="{{ Auth::user()->role->nom_role ?? 'Utilisateur' }}" class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-600 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-500 dark:text-gray-400 cursor-not-allowed" readonly disabled>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div id="form-actions" class="flex flex-col md:flex-row gap-4 pt-6 border-t border-gray-200 dark:border-gray-700 hidden">
                        <button type="button" id="cancel-edit-btn" class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 font-medium">
                            <i class="fas fa-times mr-2"></i>Annuler
                        </button>
                        <button type="submit" id="save-profile-btn" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 hover-lift font-medium">
                            <i class="fas fa-save mr-2"></i>Sauvegarder
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Onglet Sécurité Content -->
<div id="securite-tab" class="tab-pane hidden">
    <div class="max-w-2xl mx-auto">
        <form id="password-form">
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-6 animate-slide-up hover-lift transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Sécurité du compte</h3>
                    <div class="flex items-center space-x-2 text-sm">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <span class="text-green-600 dark:text-green-400 font-medium">Compte sécurisé</span>
                    </div>
                </div>

                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Modifiez votre mot de passe pour renforcer la sécurité de votre compte</p>

                <div class="space-y-6">
                    <!-- Mot de passe actuel -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-lock mr-2 text-gray-500"></i>Mot de passe actuel
                        </label>
                        <div class="relative">
                            <input type="password" id="current-password" name="current_password" class="w-full px-4 py-3 pr-12 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white transition-all" placeholder="Entrez votre mot de passe actuel" required>
                            <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors" onclick="togglePassword('current-password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Nouveau mot de passe -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-key mr-2 text-gray-500"></i>Nouveau mot de passe
                        </label>
                        <div class="relative">
                            <input type="password" id="new-password" name="new_password" class="w-full px-4 py-3 pr-12 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white transition-all" placeholder="Entrez votre nouveau mot de passe" required>
                            <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors" onclick="togglePassword('new-password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        <!-- Indicateur de force -->
                        <div class="mt-3">
                            <div class="flex items-center space-x-2">
                                <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                    <div id="password-strength-bar" class="h-full bg-red-500 transition-all duration-300" style="width: 0%"></div>
                                </div>
                                <span id="password-strength-text" class="text-xs font-medium text-gray-500 dark:text-gray-400">Faible</span>
                            </div>

                            <!-- Critères -->
                            <div class="mt-3 space-y-2">
                                <div class="flex items-center space-x-2 text-xs">
                                    <i id="length-check" class="fas fa-times text-red-500"></i>
                                    <span class="text-gray-600 dark:text-gray-400">Au moins 8 caractères</span>
                                </div>
                                <div class="flex items-center space-x-2 text-xs">
                                    <i id="uppercase-check" class="fas fa-times text-red-500"></i>
                                    <span class="text-gray-600 dark:text-gray-400">Une lettre majuscule</span>
                                </div>
                                <div class="flex items-center space-x-2 text-xs">
                                    <i id="lowercase-check" class="fas fa-times text-red-500"></i>
                                    <span class="text-gray-600 dark:text-gray-400">Une lettre minuscule</span>
                                </div>
                                <div class="flex items-center space-x-2 text-xs">
                                    <i id="number-check" class="fas fa-times text-red-500"></i>
                                    <span class="text-gray-600 dark:text-gray-400">Un chiffre</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmer mot de passe -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-check-circle mr-2 text-gray-500"></i>Confirmer le nouveau mot de passe
                        </label>
                        <div class="relative">
                            <input type="password" id="confirm-password" name="confirm_password" class="w-full px-4 py-3 pr-12 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white transition-all" placeholder="Confirmez votre nouveau mot de passe" required>
                            <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors" onclick="togglePassword('confirm-password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Conseils sécurité -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5"></i>
                            <div class="text-sm">
                                <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">Conseils de sécurité</h4>
                                <ul class="space-y-1 text-blue-800 dark:text-blue-200">
                                    <li>• Utilisez un mot de passe unique pour ce compte</li>
                                    <li>• Évitez les informations personnelles facilement devinables</li>
                                    <li>• Changez votre mot de passe régulièrement</li>
                                    <li>• Ne partagez jamais votre mot de passe</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex flex-col md:flex-row gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" id="cancel-password-btn" class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 font-medium">
                            <i class="fas fa-times mr-2"></i>Annuler
                        </button>
                        <button type="submit" id="save-password-btn" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 hover-lift font-medium">
                            <i class="fas fa-save mr-2"></i>Changer le mot de passe
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
            </div>
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
</div>
        <!-- Formulaire de déconnexion caché -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

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


<script src="{{ asset('assets/javascript/logout.js') }}"></script>
<script src="{{ asset('assets/javascript/profile.js') }}"></script>
<script src="{{ asset('assets/javascript/config.js') }}"></script>
<script src="{{ asset('assets/javascript/password.js') }}"></script>
</body>
</html>
