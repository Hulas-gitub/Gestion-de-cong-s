<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>État de Connexion - GRAXFL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            transition: background 0.5s ease;
        }
        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        .slide-down {
            animation: slideDown 0.5s ease-out;
        }
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .rotate-balance {
            animation: rotateBalance 3s ease-in-out infinite;
            transform-origin: center;
        }
        @keyframes rotateBalance {
            0%, 100% { transform: rotate(-5deg); }
            50% { transform: rotate(5deg); }
        }
        .float-icon {
            animation: floatIcon 3s ease-in-out infinite;
        }
        @keyframes floatIcon {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .glow-online {
            filter: drop-shadow(0 0 20px rgba(74, 144, 226, 0.8)) drop-shadow(0 0 40px rgba(155, 81, 224, 0.6));
            animation: glowPulse 2s ease-in-out infinite;
        }
        @keyframes glowPulse {
            0%, 100% { filter: drop-shadow(0 0 20px rgba(74, 144, 226, 0.8)) drop-shadow(0 0 40px rgba(155, 81, 224, 0.6)); }
            50% { filter: drop-shadow(0 0 30px rgba(74, 144, 226, 1)) drop-shadow(0 0 60px rgba(155, 81, 224, 0.8)); }
        }
        .glow-offline {
            filter: drop-shadow(0 0 10px rgba(107, 114, 128, 0.5));
        }
        .scale-pulse {
            animation: scalePulse 2s ease-in-out infinite;
        }
        @keyframes scalePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center transition-all duration-500">
    <div class="text-center p-8">
        <!-- Logo GRAXFL -->
        <div class="mb-8 flex justify-center">
            <div class="relative">
                <svg width="120" height="120" viewBox="0 0 120 120" class="drop-shadow-2xl">
                    <!-- Cercle principal -->
                    <circle cx="60" cy="60" r="55" id="mainCircle" class="transition-all duration-500"/>
                    <!-- Balance/Justice icon -->
                    <g id="icon" class="transition-all duration-500">
                        <circle cx="45" cy="55" r="4" fill="white" opacity="0.9"/>
                        <circle cx="75" cy="55" r="4" fill="white" opacity="0.9"/>
                        <line x1="45" y1="55" x2="75" y2="55" stroke="white" stroke-width="2"/>
                        <line x1="60" y1="45" x2="60" y2="55" stroke="white" stroke-width="2"/>
                        <path d="M 55 70 Q 60 75 65 70" stroke="white" stroke-width="2" fill="none"/>
                    </g>
                </svg>
                <!-- Indicateur de connexion pulsant -->
                <div id="statusDot" class="absolute -top-2 -right-2 w-6 h-6 rounded-full border-4 border-white transition-all duration-500"></div>
            </div>
        </div>

        <!-- Message de statut -->
        <div id="statusMessage" class="slide-down">
            <h1 id="statusTitle" class="text-4xl font-bold mb-4 transition-all duration-500"></h1>
            <p id="statusText" class="text-xl opacity-90 mb-6 transition-all duration-500"></p>

            <!-- Barre de progression (pour reconnexion) -->
            <div id="progressBar" class="hidden w-64 mx-auto bg-white bg-opacity-20 rounded-full h-2 overflow-hidden">
                <div id="progress" class="h-full bg-white transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>

        <!-- Info technique -->
        <div id="techInfo" class="mt-8 text-sm opacity-70">
            <p id="lastCheck"></p>
        </div>
    </div>

    <script>
        const body = document.body;
        const mainCircle = document.getElementById('mainCircle');
        const statusDot = document.getElementById('statusDot');
        const statusTitle = document.getElementById('statusTitle');
        const statusText = document.getElementById('statusText');
        const lastCheck = document.getElementById('lastCheck');
        const progressBar = document.getElementById('progressBar');
        const progress = document.getElementById('progress');

        let isOnline = navigator.onLine;
        let checkInterval;
        let reconnectAttempts = 0;

        function updateStatus(online) {
            const now = new Date().toLocaleTimeString('fr-FR');

            if (online) {
                // Couleurs du logo : bleu (#4A90E2) à violet (#9B51E0)
                body.className = 'min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600';
                mainCircle.setAttribute('fill', 'url(#gradient)');
                statusDot.className = 'absolute -top-2 -right-2 w-6 h-6 rounded-full border-4 border-white bg-green-400 pulse';
                statusTitle.className = 'text-4xl font-bold mb-4 text-white';
                statusTitle.textContent = 'Connecté';
                statusText.className = 'text-xl text-white opacity-90 mb-6';
                statusText.textContent = 'Votre connexion Internet est active';
                lastCheck.textContent = `Dernière vérification : ${now}`;
                progressBar.classList.add('hidden');
                reconnectAttempts = 0;
            } else {
                // Couleurs désaturées pour offline
                body.className = 'min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-600 to-gray-800';
                mainCircle.setAttribute('fill', '#6B7280');
                statusDot.className = 'absolute -top-2 -right-2 w-6 h-6 rounded-full border-4 border-white bg-red-500 pulse';
                statusTitle.className = 'text-4xl font-bold mb-4 text-white';
                statusTitle.textContent = 'Déconnecté';
                statusText.className = 'text-xl text-white opacity-90 mb-6';
                statusText.textContent = 'Aucune connexion Internet détectée';
                lastCheck.textContent = `Connexion perdue à ${now}`;

                // Afficher la tentative de reconnexion
                progressBar.classList.remove('hidden');
                simulateReconnect();
            }
        }

        function simulateReconnect() {
            reconnectAttempts++;
            let width = 0;
            const interval = setInterval(() => {
                if (width >= 100) {
                    clearInterval(interval);
                    progress.style.width = '0%';
                    if (!navigator.onLine) {
                        setTimeout(simulateReconnect, 1000);
                    }
                } else {
                    width += 2;
                    progress.style.width = width + '%';
                }
            }, 100);
        }

        // Créer le gradient SVG
        const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        const gradient = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
        gradient.setAttribute('id', 'gradient');
        gradient.setAttribute('x1', '0%');
        gradient.setAttribute('y1', '0%');
        gradient.setAttribute('x2', '100%');
        gradient.setAttribute('y2', '100%');

        const stop1 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
        stop1.setAttribute('offset', '0%');
        stop1.setAttribute('style', 'stop-color:#4A90E2;stop-opacity:1');

        const stop2 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
        stop2.setAttribute('offset', '100%');
        stop2.setAttribute('style', 'stop-color:#9B51E0;stop-opacity:1');

        gradient.appendChild(stop1);
        gradient.appendChild(stop2);
        defs.appendChild(gradient);
        document.querySelector('svg').insertBefore(defs, document.querySelector('svg').firstChild);

        // Événements de connexion
        window.addEventListener('online', () => {
            isOnline = true;
            updateStatus(true);
        });

        window.addEventListener('offline', () => {
            isOnline = false;
            updateStatus(false);
        });

        // Vérification périodique
        setInterval(() => {
            const currentStatus = navigator.onLine;
            if (currentStatus !== isOnline) {
                isOnline = currentStatus;
                updateStatus(isOnline);
            }
        }, 3000);

        // Initialisation
        updateStatus(isOnline);
    </script>
</body>
</html>
