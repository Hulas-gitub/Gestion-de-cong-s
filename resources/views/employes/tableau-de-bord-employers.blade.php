<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graxel Tech - Vue d'ensemble </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="'{{ asset('assets/javascript/config.js') }}"></script>
    <script src="{{ asset('assets/javascript/animate.js') }}"></script>
 <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
<script>
    // Injecter les données Laravel dans le JavaScript
    window.laravelData = {
        roles: @json($roles ?? []),
        departements: @json($allDepartements ?? []),
        users: @json($users ?? [])
    };
</script>
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
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo Graxel Tech" class="w-full h-full object-contain rounded-2xl" />
    </div>
    <div>
    </div>

</div>
                <!-- Navigation -->
                <nav class="space-y-3 flex-1">
                    <a href="#" class="nav-item flex items-center space-x-4 px-4 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover-lift transition-all duration-300 click-scale shadow-lg">
                        <i class="fas fa-chart-pie w-5 h-5 text-lg"></i>
                        <span class="font-medium">Vue d'ensemble</span>
                    </a>
                    <a href=" {{ url('employes/conges-employers') }}" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-clipboard-list w-5 h-5 text-lg"></i>
                        <span>Mes demandes</span>
                    </a>
                    <a href="{{ url('employes/calendrier-employers') }}" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-calendar-alt w-5 h-5 text-lg"></i>
                        <span>Calendrier</span>
                    </a>
                    <a href="{{ url('employes/profile') }}" class="nav-item flex items-center space-x-4 px-4 py-4 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl hover-lift transition-all duration-300 click-scale">
                        <i class="fas fa-user w-5 h-5 text-lg"></i>
                        <span>Mon profile</span>
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
<div class="flex-1 overflow-auto custom-scrollbar w-full p-0">
    <!-- Header (à fleur) -->
    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg shadow-lg border-b border-gray-200/50 dark:border-gray-700/50 py-6 animate-fade-in sticky top-0 z-30 w-full">
        <div class="flex justify-between items-center w-full px-4 md:px-6">
            <div class="flex items-center space-x-4">
                <button id="toggle-sidebar" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden">
                    <i class="fas fa-bars text-gray-600 dark:text-gray-400"></i>
                </button>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Vue d'ensemble</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        <i class="fas fa-calendar-day mr-2"></i>
                        <span id="current-date"></span>

       <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Département  ressources Humaines<span id="kpi-nom-departement" class="font-bold text-orange-600"></span></p>

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
                </div>
            </div>
            <div class="p-6 space-y-4">
                @php
                    // Récupérer les demandes via le controller
                    $demandesData = app('App\Http\Controllers\DashboardEmployesController')->getHistoriqueDemandes(request());
                    $demandesResponse = json_decode($demandesData->getContent());
                    $demandes = $demandesResponse->success ? collect($demandesResponse->demandes)->take(4) : collect([]);
                @endphp

                @forelse($demandes as $demande)
                    @php
                        // Définir les couleurs selon le statut
                        $statusColors = [
                            'En attente' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/30', 'text' => 'text-yellow-800 dark:text-yellow-300', 'icon' => '🟡', 'card' => 'bg-gray-50 dark:bg-gray-700/50'],
                            'Approuvé' => ['bg' => 'bg-green-500', 'text' => 'text-white', 'icon' => '🟢', 'card' => 'bg-green-50 dark:bg-green-900/20'],
                            'Refusé' => ['bg' => 'bg-red-500', 'text' => 'text-white', 'icon' => '🔴', 'card' => 'bg-red-50 dark:bg-red-900/20']
                        ];
                        $colors = $statusColors[$demande->statut_label] ?? $statusColors['En attente'];

                        // Définir les icônes selon le type de congé
                        $typeIcons = [
                            'Congés payés' => 'fa-umbrella-beach',
                            'Congé maladie' => 'fa-notes-medical',
                            'RTT' => 'fa-business-time',
                            'Congé maternité' => 'fa-baby',
                            'Congé paternité' => 'fa-baby-carriage'
                        ];
                        $icon = $typeIcons[$demande->type_conge] ?? 'fa-calendar';

                        // Couleur du gradient selon le type
                        $gradients = [
                            'Congés payés' => 'from-blue-500 to-purple-500',
                            'Congé maladie' => 'from-green-500 to-emerald-500',
                            'RTT' => 'from-red-500 to-pink-500',
                            'Congé maternité' => 'from-blue-500 to-blue-500',
                            'Congé paternité' => 'from-indigo-500 to-purple-500'
                        ];
                        $gradient = $gradients[$demande->type_conge] ?? 'from-gray-500 to-gray-600';
                    @endphp

                    <div class="demand-item flex items-center justify-between p-4 {{ $colors['card'] }} rounded-none hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 cursor-pointer hover-lift w-full">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r {{ $gradient }} rounded-xl flex items-center justify-center">
                                <i class="fas {{ $icon }} text-white"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $demande->type_conge }}</h4>
                                    <span>{{ $colors['icon'] }}</span>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($demande->date_debut)->format('Y-m-d') }} -
                                    {{ \Carbon\Carbon::parse($demande->date_fin)->format('Y-m-d') }}
                                    ({{ $demande->nb_jours }} jour{{ $demande->nb_jours > 1 ? 's' : '' }})
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    Créé {{ $demande->submitted_time }}
                                </p>
                                @if($demande->statut_label == 'Refusé' && $demande->commentaire_refus)
                                    <p class="text-xs text-red-500 dark:text-red-400 font-medium">
                                        Motif: {{ $demande->commentaire_refus }}
                                    </p>
                                @endif
                                @if($demande->statut_label == 'En attente')
                                    <p class="text-xs text-blue-500 dark:text-blue-400 font-medium">
                                        En cours de traitement
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-4 py-2 {{ $colors['bg'] }} {{ $colors['text'] }} text-xs font-semibold rounded-full">
                                {{ $demande->statut_label }}
                            </span>
                            @if($demande->has_attestation)
                                <a href="{{ route('employes.dashboard.api.documents.attestation.telecharger', $demande->id) }}"
                                   class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
                                   title="Télécharger l'attestation">
                                    <i class="fas fa-download"></i>
                                </a>
                            @endif
                            @if($demande->has_document)
                                <a href="{{ route('employes.dashboard.api.documents.justificatif.visualiser', $demande->id) }}"
                                   class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
                                   title="Voir le document">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Aucune demande récente</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Notifications Sidebar -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-none shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden w-full" style="animation-delay: 0.3s;">
            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-purple-500/5 to-pink-500/5">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Notifications</h3>
                    @php
                        $notificationsData = app('App\Http\Controllers\DashboardEmployesController')->getNotifications(request());
                        $notificationsResponse = json_decode($notificationsData->getContent());
                        $notifications = $notificationsResponse->success ? collect($notificationsResponse->notifications)->take(6) : collect([]);
                        $totalNotifications = $notificationsResponse->success ? $notificationsResponse->total : 0;
                    @endphp
                    <div class="flex items-center space-x-2">
                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $totalNotifications }}</span>
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Dernières mises à jour</p>
            </div>
            <div class="p-4 space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                @php
                    $typeColors = [
                        'conge_approuve' => ['border' => 'border-green-500', 'dot' => 'bg-green-500', 'badge' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300', 'icon' => '🟢', 'label' => 'Congé'],
                        'conge_refuse' => ['border' => 'border-red-500', 'dot' => 'bg-red-500', 'badge' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300', 'icon' => '🔴', 'label' => 'Congé'],
                        'conge_attente' => ['border' => 'border-yellow-500', 'dot' => 'bg-yellow-500', 'badge' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300', 'icon' => '🟡', 'label' => 'Congé'],
                        'administrative' => ['border' => 'border-blue-500', 'dot' => 'bg-blue-500', 'badge' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300', 'icon' => '📑', 'label' => ''],
                        'rh' => ['border' => 'border-purple-500', 'dot' => 'bg-purple-500', 'badge' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300', 'icon' => '🎉', 'label' => 'RH'],
                        'reunion' => ['border' => 'border-orange-500', 'dot' => 'bg-orange-500', 'badge' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300', 'icon' => '📢', 'label' => '']
                    ];
                @endphp

                @forelse($notifications as $notification)
                    @php
                        $colors = $typeColors[$notification->type] ?? $typeColors['administrative'];
                    @endphp
                    <div class="notification-item flex items-start space-x-3 animate-fade-in p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-none cursor-pointer transition-colors border-l-4 {{ $colors['border'] }}">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 {{ $colors['dot'] }} rounded-full mt-2 animate-pulse"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $notification->titre }} {{ $colors['icon'] }}
                                </p>
                                <div class="flex items-center space-x-1">
                                    @if($notification->has_document)
                                        <a href="{{ route('employes.dashboard.api.documents.notification.telecharger', $notification->id) }}"
                                           class="text-blue-500 hover:text-blue-700 text-xs"
                                           title="Télécharger le document">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">{{ $notification->message }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-400">{{ $notification->time_ago }}</span>
                                <span class="text-xs {{ $colors['badge'] }} px-2 py-1 rounded-full">
                                    {{ $colors['label'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-bell-slash text-4xl mb-2"></i>
                        <p>Aucune notification</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 lg:grid-cols-1 gap-0 w-full">
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-none shadow-xl border border-gray-200/50 dark:border-gray-700/50 animate-slide-up overflow-hidden w-full" style="animation-delay: 0.6s;">
            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-yellow-500/5 to-orange-500/5">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Statistiques de l'équipe</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Vue d'ensemble de votre département</p>
            </div>
            <div class="p-6 space-y-6">
                @php
                    $statsData = app('App\Http\Controllers\DashboardEmployesController')->getStatistiques();
                    $statsResponse = json_decode($statsData->getContent());
                    $stats = $statsResponse->success ? $statsResponse->statistiques : null;
                @endphp

                @if($stats)
                    <!-- Équipe du département -->
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
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats->equipe_departement->total }}</p>
                        </div>
                    </div>

                    <!-- Demandes refusées -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-pink-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-times-circle text-white"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Demandes refusées</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Ce mois-ci</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats->demandes_refusees->mois_courant }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $stats->demandes_refusees->pourcentage > 0 ? '+' : '' }}{{ $stats->demandes_refusees->pourcentage }}% vs mois dernier
                            </p>
                        </div>
                    </div>

                    <!-- Demandes approuvées -->
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
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats->demandes_approuvees->mois_courant }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $stats->demandes_approuvees->pourcentage > 0 ? '+' : '' }}{{ $stats->demandes_approuvees->pourcentage }}% vs mois dernier
                            </p>
                        </div>
                    </div>

                    <!-- En attente -->
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
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats->en_attente->total }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Délai moyen: 2j</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-chart-bar text-4xl mb-2"></i>
                        <p>Impossible de charger les statistiques</p>
                    </div>
                @endif
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
<script src="{{ asset('assets/javascript/config.js') }}"></script>

</body>
</html>
