
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1)',
                        'slide-up': 'slideUp 0.8s cubic-bezier(0.4, 0, 0.2, 1)',
                        'slide-right': 'slideRight 0.6s cubic-bezier(0.4, 0, 0.2, 1)',
                        'bounce-subtle': 'bounceSubtle 1.2s ease-out',
                        'pulse-ring': 'pulseRing 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'shimmer': 'shimmer 2s linear infinite',
                    },
                    backgroundImage: {
                        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                        'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
                    }
                }
            }
        }
   // Configuration universelle du token CSRF
    (function() {
        const token = document.querySelector('meta[name="csrf-token"]');

        if (token) {
            const csrfToken = token.getAttribute('content');

            // Pour Axios (si utilisé)
            if (typeof axios !== 'undefined') {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
            }

            // Pour jQuery (si utilisé)
            if (typeof $ !== 'undefined') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
            }

            // Pour Fetch API (moderne)
            window.csrfToken = csrfToken;

            // Intercepteur fetch global
            const originalFetch = window.fetch;
            window.fetch = function(url, options = {}) {
                if (!options.headers) {
                    options.headers = {};
                }

                // Ajouter le token pour les requêtes POST, PUT, DELETE, PATCH
                const method = (options.method || 'GET').toUpperCase();
                if (['POST', 'PUT', 'DELETE', 'PATCH'].includes(method)) {
                    options.headers['X-CSRF-TOKEN'] = csrfToken;
                }

                return originalFetch(url, options);
            };
        } else {
            console.warn('⚠️ Meta tag CSRF manquant ! Ajoutez <meta name="csrf-token" content="{{ csrf_token() }}">');
        }
    })();
