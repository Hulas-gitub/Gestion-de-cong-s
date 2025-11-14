<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graxel Tech - Démandes de congés Employer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="{{ asset('assets/javascript/config.js') }}"></script>
    <script src="{{ asset('assets/javascript/animate.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">

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
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo Graxel Tech"
                            class="w-full h-full object-contain rounded-2xl" />
                    </div>
                    <div>
                    </div>

                </div>
                <!-- Navigation -->
                <nav class="space-y-3 flex-1">
                    <a href=" {{ url('employes/tableau-de-bord-employers') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-chart-pie w-5 h-5 text-lg"></i>
                        <span>Tableau de bord</span>
                    </a>
                    <a href="#"
                        class="nav-item flex items-center space-x-4 px-4 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover-lift transition-all duration-300 click-scale shadow-lg">
                        <i class="fas fa-clipboard-list w-5 h-5 text-lg"></i>
                        <span class="font-medium">Mes demandes</span>
                    </a>
                    <a href="{{ url('employes/calendrier-employers') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-calendar-alt w-5 h-5 text-lg"></i>
                        <span>Calendrier</span>
                    </a>
                    <a href="{{ url('employes/profile') }}"
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
                                Demande de congés</h1>
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
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        Demande approuvée</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Il y a 1j
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="notification-item">
                                                <div class="notification-badge bg-yellow-500 animate-pulse"></div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        Rappel: Réunion demain</p>
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


                <!-- Main Content -->
                <div class="p-4 md:p-8 space-y-8">
                    <!-- Header avec bouton Nouvelle demande -->
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Historique de mes demandes</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Gérez vos demandes de congés</p>
                        </div>
                         <button id="btn-nouvelle-demande" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 text-white rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-plus mr-2"></i>Nouvelle demande
                    </button>
                    </div>


<!-- Solde Disponible (Grande carte responsive) -->
<div class="container my-4">
    <div class="rounded-xl shadow-lg p-4 sm:p-6 md:p-8 bg-gradient-to-r from-blue-500 to-purple-600 text-white">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 gap-md-4">
            <!-- Contenu principal -->
            <div class="flex-grow-1 w-100">
                <p class="text-white-50 small mb-2">SOLDE DISPONIBLE</p>
                <p class="display-6 fw-bold mb-2" id="available-balance">
                    {{ $soldeDisponible}} jour{{ $soldeDisponible> 1? 's': ''}}
                </p>
                <p class="text-white-75 small">
                    <i class="fas fa-info-circle me-2"></i>
                    <span>Vous pouvez effectuer une demande dans la limite de ce solde</span>
                </p>
                        <!-- Icône décorative -->
        <div class="flex-shrink-0 self-end md:self-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-umbrella-beach text-white text-2xl sm:text-3xl md:text-4xl"></i>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

   <!-- Liste des demandes (dynamique) -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden">
                <!-- Les demandes seront chargées ici dynamiquement via AJAX -->
            </div>

 <!-- Pop-up Nouvelle demande -->
    <div id="popup-nouvelle-demande"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 animate-slide-up">
            <form id="form-nouvelle-demande" class="w-full">
                <input type="hidden" id="demande-id" name="demande_id">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white" id="popup-title">Nouvelle demande de congé</h3>
                    <button id="close-popup" type="button"
                            class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-times text-gray-600 dark:text-gray-400"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="type-conge" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type de congé</label>
                        <select id="type-conge" name="type_conge_id"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white">
                            <option value="">Sélectionnez un type</option>
                            @foreach($typesConges as $type)
                                <option value="{{ $type->id_type }}">{{ $type->nom_type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="date-debut" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date de début</label>
                        <input type="date" id="date-debut" name="date_debut"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="date-fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date de fin</label>
                        <input type="date" id="date-fin" name="date_fin"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="motif" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motif (optionnel)</label>
                        <textarea rows="3" id="motif" name="motif" placeholder="Décrivez brièvement le motif de votre demande..."
                                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white resize-none"></textarea>
                    </div>
                    <!-- Document justificatif avec bouton pour ouvrir le popup -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document justificatif (optionnel)</label>
                        <button id="open-document-upload" type="button"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2">
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
                                <button id="remove-document" type="button" class="text-red-500 hover:text-red-700 dark:hover:text-red-300 transition-colors">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl">
                        <div class="flex items-center space-x-2 text-blue-600 dark:text-blue-400">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <p class="text-sm font-medium">Nombre de jours : <span id="nb-jours">0</span> jours</p>
                                <p class="text-sm">Solde après demande : <span id="solde-apres">{{ $soldeDisponible ?? 0 }}</span> jours</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex space-x-4">
                    <button id="annuler-demande" type="button"
                            class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">
                        Annuler
                    </button>
                    <button id="soumettre-demande" type="submit"
                            class="flex-1 px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors font-medium">
                        <span id="btn-text">Soumettre</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pop-up Retour Anticipé -->
    <div id="popup-retour-anticipe"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 animate-slide-up">
            <form id="form-retour-anticipe" class="w-full">
                <input type="hidden" id="retour-demande-id" name="demande_id">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Retour anticipé</h3>
                    <button id="close-popup-retour" type="button"
                            class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-times text-gray-600 dark:text-gray-400"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="retour-date-debut" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date de début</label>
                        <input type="date" id="retour-date-debut" readonly
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div>
                        <label for="retour-ancienne-date-fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ancienne date de fin</label>
                        <input type="date" id="retour-ancienne-date-fin" readonly
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div>
                        <label for="retour-nouvelle-date-fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nouvelle date de fin</label>
                        <input type="date" id="retour-nouvelle-date-fin" name="nouvelle_date_fin" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex space-x-4">
                    <button id="annuler-retour" type="button"
                            class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors font-medium">
                        Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pop-up Téléchargement de Document -->
    <div id="document-popup"
         class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden items-center justify-center z-50 transition-all duration-300">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full m-4 transform transition-all duration-300 opacity-0 scale-95"
             id="document-popup-content">
            <!-- Popup Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Téléverser un document justificatif</h3>
                <button type="button" id="close-document-popup-btn"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <!-- Popup Body -->
            <div class="p-6">
                <!-- Preview Zone -->
                <div class="mb-6">
                    <div class="w-24 h-24 mx-auto bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg"
                         id="document-preview">
                        <i class="fas fa-file-alt text-3xl"></i>
                    </div>
                </div>
                <!-- Upload Zone -->
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center mb-4 hover:border-blue-500 dark:hover:border-blue-400 transition-colors cursor-pointer"
                     id="document-upload-zone">
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
                <input type="file" id="document-file-input"
                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.txt,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png,image/gif,text/plain"
                       class="hidden">
                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button type="button" id="document-cancel-btn"
                            class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">
                        Annuler
                    </button>
                    <button type="button" id="document-confirm-btn"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>
                    <!-- Footer -->
                    <footer
                        class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-t border-gray-200/50 dark:border-gray-700/50 p-6 mt-8">
                        <div class="max-w-7xl mx-auto">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                                <div class="col-span-1 md:col-span-2">
                                    <div class="flex items-center space-x-4 mb-4">

                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                        © 2025 Graxel Technologies. Tous droits réservés. <br> Une solution dédiée aux
                                        chefs de département pour une gestion optimale des congés et des ressources
                                        humaines.
                                    </p>
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
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Êtes-vous sûr de vouloir vous déconnecter ?
                        </p>
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
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $nomComplet }}
                                        </p>
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

        <script src="{{ asset('assets/javascript/logout.js') }}"></script>
        <script src="{{ asset('assets/javascript/config.js') }}"></script>
        <script src="{{ asset('assets/javascript/conges-employes.js') }}"></script>
</body>

</html>
