
        // ==================== BASE DE DONNÉES UTILISATEURS ====================
        const users = [
            // Administrateurs
            {
                email: "grellionelmubaghu@gmail.com",
                password: "Billynho1721@",
                role: "admin",
                name: "Administrateur Principal"
            },
            {
                email: "hulassanders@gmail.com",
                password: "Hulas1998",
                role: "admin",
                name: "Admin Système"
            },

            // Managers (Chefs de département)
            {
                email: "grellionelmubaghu+1@gmail.com",
                password: "Billynho1721@",
                role: "manager",
                name: "Manager RH"
            },
            {
                email: "hulassanderss@gmail.com",
                password: "Hulas1998",
                role: "manager",
                name: "Manager IT"
            },
            {
                email: "chef.commercial@graxel.com",
                password: "manager123",
                role: "manager",
                name: "Chef Commercial"
            },

            // Employés
            {
                email: "grellionelmubaghu+2@gmail.com",
                password: "Billynho1721@",
                role: "employee",
                name: "Jean Dupont"
            },
            {
                email: "hulassandersss@gmail.com",
                password: "Hulas1998",
                role: "employee",
                name: "Marie Martin"
            },
            {
                email: "employe3@graxel.com",
                password: "employe123",
                role: "employee",
                name: "Pierre Dubois"
            }
        ];

        // ==================== FONCTION MINI POPUP TOAST ====================
        function showMiniToast(message, type = 'success') {
            // Supprimer les anciens toasts
            const existingToasts = document.querySelectorAll('.custom-toast');
            existingToasts.forEach(toast => toast.remove());

            // Créer le nouveau toast
            const toast = document.createElement('div');
            toast.className = `custom-toast ${type}`;

            // Définir l'icône et le titre selon le type
            let icon, title;
            if (type === 'success') {
                icon = 'fa-check-circle';
                title = 'Succès';
            } else {
                icon = 'fa-exclamation-circle';
                title = 'Erreur';
            }

            toast.innerHTML = `
                <div class="custom-toast-icon">
                    <i class="fas ${icon}"></i>
                </div>
                <div class="custom-toast-content">
                    <div class="custom-toast-title">${title}</div>
                    <div class="custom-toast-message">${message}</div>
                </div>
                <button class="custom-toast-close">
                    <i class="fas fa-times"></i>
                </button>
            `;

            document.body.appendChild(toast);

            // Bouton de fermeture
            const closeBtn = toast.querySelector('.custom-toast-close');
            closeBtn.addEventListener('click', () => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 500);
            });

            // Afficher le toast avec animation
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);

            // Retirer automatiquement après 4 secondes
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }

        // ==================== GESTION DU LOADER ====================
        window.addEventListener('load', function() {
            const loader = document.getElementById('loader');
            const mainContent = document.getElementById('mainContent');

            let progress = 0;
            const progressBar = document.querySelector('.loader-progress-bar');
            const interval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);

                    setTimeout(() => {
                        loader.classList.add('hide');
                        setTimeout(() => {
                            loader.style.display = 'none';
                            mainContent.classList.add('show');
                        }, 500);
                    }, 500);
                }
                progressBar.style.width = `${progress}%`;
            }, 200);
        });

        // ==================== GESTION DU THÈME ====================
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;
        let currentTheme = localStorage.getItem('theme') || 'dark';

        if (currentTheme === 'light') {
            body.setAttribute('data-theme', 'light');
            themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        }

        themeToggle.addEventListener('click', () => {
            if (currentTheme === 'light') {
                body.removeAttribute('data-theme');
                themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
                currentTheme = 'dark';
            } else {
                body.setAttribute('data-theme', 'light');
                themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
                currentTheme = 'light';
            }
            localStorage.setItem('theme', currentTheme);
        });

        // ==================== GESTION DU MOT DE PASSE ====================
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');

        passwordToggle.addEventListener('click', () => {
            const icon = passwordToggle.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                icon.className = 'fas fa-eye';
            }
        });

        // ==================== GESTION DU POPUP MOT DE PASSE OUBLIÉ ====================
        const forgotPasswordBtn = document.getElementById('forgotPasswordBtn');
        const forgotPasswordPopup = document.getElementById('forgotPasswordPopup');
        const closePopup = document.getElementById('closePopup');
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');

        forgotPasswordBtn.addEventListener('click', () => {
            forgotPasswordPopup.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });

        closePopup.addEventListener('click', () => {
            forgotPasswordPopup.style.display = 'none';
            document.body.style.overflow = 'auto';
        });

        forgotPasswordPopup.addEventListener('click', (e) => {
            if (e.target === forgotPasswordPopup) {
                forgotPasswordPopup.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });

        // ==================== FORMULAIRE DE RÉINITIALISATION ====================
        forgotPasswordForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const resetEmail = document.getElementById('resetEmail').value;
            const btnContent = document.querySelector('#resetSubmitBtn .btn-content');
            const btnLoader = document.querySelector('#resetSubmitBtn .btn-loader');

            if (!resetEmail) {
                showMiniToast("Veuillez saisir votre adresse email", 'error');
                return;
            }

            if (!resetEmail.includes('@')) {
                showMiniToast("Veuillez utiliser une adresse email valide", 'error');
                return;
            }

            btnContent.style.opacity = '0';
            btnLoader.style.display = 'block';

            setTimeout(() => {
                showMiniToast("Un lien de réinitialisation a été envoyé à votre adresse email", 'success');

                setTimeout(() => {
                    forgotPasswordPopup.style.display = 'none';
                    document.body.style.overflow = 'auto';
                    btnContent.style.opacity = '1';
                    btnLoader.style.display = 'none';
                    document.getElementById('resetEmail').value = '';
                }, 1500);
            }, 1500);
        });

        // ==================== FONCTION D'AUTHENTIFICATION ====================
        function authenticateUser(email, password) {
            return users.find(user => user.email === email && user.password === password);
        }

         // ==================== FONCTION DE REDIRECTION PAR RÔLE ====================
    function redirectByRole(role, userName) {
        let redirectUrl = '';

        switch(role) {
            case 'admin':
                redirectUrl = '/admin/dashboard-admin';
                break;
            case 'manager':
                redirectUrl = '/chef-de-departement/tableaude bord-manager';
                break;
            case 'employee':
                redirectUrl = '/employes/tableau de bord-employers';
                break;
            default:
                redirectUrl = '/employes/tableau de bord-employers';
        }

        showMiniToast(`Bienvenue ${userName} ! Connexion réussie`, 'success');

        setTimeout(() => {
            window.location.href = redirectUrl;
        }, 1500);
    }
        // ==================== FORMULAIRE DE CONNEXION ====================
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const btnContent = document.querySelector('#submitBtn .btn-content');
            const btnLoader = document.querySelector('#submitBtn .btn-loader');

            if (!email) {
                showMiniToast("Veuillez saisir votre adresse email", 'error');
                return;
            }

            if (!password) {
                showMiniToast("Veuillez saisir votre mot de passe", 'error');
                return;
            }

            if (!email.includes('@')) {
                showMiniToast("Veuillez utiliser une adresse email valide", 'error');
                return;
            }

            if (password.length < 6) {
                showMiniToast("Le mot de passe doit contenir au moins 6 caractères", 'error');
                return;
            }

            btnContent.style.opacity = '0';
            btnLoader.style.display = 'block';

            setTimeout(() => {
                const user = authenticateUser(email, password);

                if (user) {
                    const userSession = {
                        email: user.email,
                        name: user.name,
                        role: user.role,
                        loginTime: new Date().toISOString()
                    };
                    localStorage.setItem('userSession', JSON.stringify(userSession));

                    redirectByRole(user.role, user.name);
                } else {
                    btnContent.style.opacity = '1';
                    btnLoader.style.display = 'none';
                    showMiniToast("Email ou mot de passe incorrect", 'error');
                }
            }, 1500);
        });

        // ==================== ANIMATIONS SUPPLÉMENTAIRES ====================
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.style.boxShadow = '0 0 0 4px rgba(79, 70, 229, 0.2)';
            });

            input.addEventListener('blur', () => {
                input.style.boxShadow = 'none';
            });
        });
