  
        // Initialisation de Notyf
        const notyf = new Notyf({
            duration: 4000,
            position: { x: 'right', y: 'top' },
            types: [
                {
                    type: 'success',
                    background: '#10B981',
                    icon: { className: 'fas fa-check-circle', tagName: 'i', color: 'white' }
                },
                {
                    type: 'error',
                    background: '#EF4444',
                    icon: { className: 'fas fa-exclamation-circle', tagName: 'i', color: 'white' }
                },
                {
                    type: 'warning',
                    background: '#F59E0B',
                    icon: { className: 'fas fa-exclamation-triangle', tagName: 'i', color: 'white' }
                }
            ]
        });
        // Gestion du loader
        window.addEventListener('load', function() {
            const loader = document.getElementById('loader');
            const mainContent = document.getElementById('mainContent');

            setTimeout(() => {
                loader.classList.add('hide');
                setTimeout(() => {
                    loader.style.display = 'none';
                    mainContent.classList.add('show');
                    animateFormElements();
                }, 500);
            }, 2500);
        });
        // Animation des éléments du formulaire
        function animateFormElements() {
            const elements = document.querySelectorAll('.form-group, .security-requirements, .submit-btn');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';

                setTimeout(() => {
                    el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 150);
            });
        }
        // Gestion du thème
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;
        let currentTheme = 'dark';

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
        });
        // Bouton retour
        document.getElementById('backBtn').addEventListener('click', () => {
            window.location.href = '../index.html';
        });
        // Gestion des boutons de visibilité des mots de passe
        const passwordToggles = document.querySelectorAll('.password-toggle');
        passwordToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const input = toggle.parentElement.querySelector('input');
                const icon = toggle.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'fas fa-eye-slash';
                } else {
                    input.type = 'password';
                    icon.className = 'fas fa-eye';
                }
            });
        });
        // Éléments pour la validation du mot de passe
        const newPasswordInput = document.getElementById('newPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        const passwordMatch = document.getElementById('passwordMatch');
        // Critères de sécurité
        const requirements = {
            length: document.getElementById('lengthReq'),
            uppercase: document.getElementById('uppercaseReq'),
            lowercase: document.getElementById('lowercaseReq'),
            number: document.getElementById('numberReq'),
            special: document.getElementById('specialReq')
        };
        // Fonction de validation de la force du mot de passe avec niveaux détaillés
        function checkPasswordStrength(password) {
            let score = 0;
            const checks = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[^A-Za-z0-9]/.test(password)
            };
            // Bonus pour longueur supplémentaire
            let lengthBonus = 0;
            if (password.length >= 12) lengthBonus = 1;
            if (password.length >= 16) lengthBonus = 2;
            // Mise à jour des critères visuels
            Object.keys(checks).forEach(key => {
                const req = requirements[key];
                if (req) {
                    const icon = req.querySelector('.requirement-icon');

                    if (checks[key]) {
                        req.classList.add('met');
                        icon.className = 'fas fa-check-circle requirement-icon';
                        score++;
                    } else {
                        req.classList.remove('met');
                        icon.className = 'fas fa-circle requirement-icon';
                    }
                }
            });
            // Calcul du score total avec bonus
            const totalScore = score + lengthBonus;
            let level = 'none';
            let percentage = 0;
            let icon = 'fas fa-info-circle';
            let message = 'Saisissez un mot de passe';
            // Détermination du niveau de solidité
            if (password.length === 0) {
                level = 'none';
                percentage = 0;
                icon = 'fas fa-info-circle';
                message = 'Saisissez un mot de passe';
            } else if (totalScore <= 1) {
                level = 'very-weak';
                percentage = 15;
                icon = 'fas fa-exclamation-triangle';
                message = 'Très faible - Ajoutez plus d\'éléments';
            } else if (totalScore === 2) {
                level = 'weak';
                percentage = 30;
                icon = 'fas fa-exclamation-circle';
                message = 'Faible - Améliorez votre mot de passe';
            } else if (totalScore === 3) {
                level = 'fair';
                percentage = 50;
                icon = 'fas fa-minus-circle';
                message = 'Moyen - Ajoutez plus de caractères';
            } else if (totalScore === 4) {
                level = 'good';
                percentage = 70;
                icon = 'fas fa-check-circle';
                message = 'Bon - Presque parfait !';
            } else if (totalScore === 5) {
                level = 'strong';
                percentage = 85;
                icon = 'fas fa-shield-check';
                message = 'Fort - Excellent choix !';
            } else if (totalScore >= 6) {
                level = 'excellent';
                percentage = 100;
                icon = 'fas fa-crown';
                message = 'Excellent - Sécurité maximale !';
            }
            // Mise à jour de la barre et du texte
            strengthFill.style.width = `${percentage}%`;
            strengthFill.className = `strength-fill ${level}`;
            strengthText.className = `strength-text ${level}`;
            strengthText.innerHTML = `<i class="${icon}"></i> ${message}`;
            // Animation de la barre
            strengthFill.style.transform = 'scaleX(0)';
            setTimeout(() => {
                strengthFill.style.transform = 'scaleX(1)';
                strengthFill.style.transformOrigin = 'left';
                strengthFill.style.transition = 'transform 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            }, 50);
            return score >= 4; // Minimum "bon" pour être accepté
        }
        // Fonction de vérification de la correspondance des mots de passe
        function checkPasswordMatch() {
            const password = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            if (confirmPassword === '') {
                passwordMatch.innerHTML = '';
                passwordMatch.className = 'password-match';
                return false;
            }
            if (password === confirmPassword) {
                passwordMatch.innerHTML = '<i class="fas fa-check-circle"></i> Les mots de passe correspondent parfaitement';
                passwordMatch.className = 'password-match success';
                return true;
            } else {
                passwordMatch.innerHTML = '<i class="fas fa-times-circle"></i> Les mots de passe ne correspondent pas';
                passwordMatch.className = 'password-match error';
                return false;
            }
        }
        // Validation en temps réel
        newPasswordInput.addEventListener('input', (e) => {
            checkPasswordStrength(e.target.value);
            if (confirmPasswordInput.value) {
                checkPasswordMatch();
            }

            // Animation du champ
            e.target.style.transform = 'scale(1.02)';
            setTimeout(() => {
                e.target.style.transform = 'scale(1)';
            }, 150);
        });
        confirmPasswordInput.addEventListener('input', (e) => {
            checkPasswordMatch();

            // Animation du champ
            e.target.style.transform = 'scale(1.02)';
            setTimeout(() => {
                e.target.style.transform = 'scale(1)';
            }, 150);
        });
        // Gestion des modales
        const confirmModal = document.getElementById('confirmModal');
        const successModal = document.getElementById('successModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmBtn = document.getElementById('confirmBtn');
        const okBtn = document.getElementById('okBtn');
        function showModal(modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        function hideModal(modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
        // Événements des modales
        cancelBtn.addEventListener('click', () => {
            hideModal(confirmModal);
            notyf.open({ type: 'warning', message: 'Modification annulée' });
        });
        confirmBtn.addEventListener('click', () => {
            hideModal(confirmModal);
            processPasswordUpdate();
        });
        okBtn.addEventListener('click', () => {
            hideModal(successModal);
            redirectToLogin();
        });
        // Fermeture des modales en cliquant à l'extérieur
        [confirmModal, successModal].forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    hideModal(modal);
                }
            });
        });
        // Traitement de la mise à jour du mot de passe
        function processPasswordUpdate() {
            const submitBtn = document.getElementById('submitBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            const submitIcon = submitBtn.querySelector('.submit-icon');
            const btnLoader = submitBtn.querySelector('.btn-loader');

            // Désactiver le bouton et afficher le loader
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            submitIcon.style.display = 'none';
            btnLoader.style.display = 'inline-block';
          
            let messageIndex = 0;
            const progressInterval = setInterval(() => {
                if (messageIndex < progressMessages.length) {
                    notyf.open({
                        type: 'success',
                        message: progressMessages[messageIndex],
                        duration: 1000
                    });
                    messageIndex++;
                }
            }, 800);

            // Simulation du traitement (3.5 secondes)
            setTimeout(() => {
                clearInterval(progressInterval);

                // Réinitialiser le bouton
                submitBtn.disabled = false;
                btnText.style.display = 'inline';
                submitIcon.style.display = 'inline';
                btnLoader.style.display = 'none';

                // Afficher la modale de succès
                showModal(successModal);

                // Notification finale
                notyf.success('Mot de passe mis à jour avec succès !');

            }, 3500);
        }
        // Redirection vers la page de connexion
        function redirectToLogin() {
            // Animation de sortie
            document.querySelector('.login-container').style.transform = 'scale(0.95)';
            document.querySelector('.login-container').style.opacity = '0';

            setTimeout(() => {
                window.location.href = '../index.html';
            }, 500);
        }
        // Soumission du formulaire
        const newPasswordForm = document.getElementById('newPasswordForm');

        newPasswordForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const password = newPasswordInput.value.trim();
            const confirmPassword = confirmPasswordInput.value.trim();

            // Validation finale complète
            if (!password) {
                notyf.error('Veuillez saisir un nouveau mot de passe');
                newPasswordInput.focus();
                return;
            }

            if (password.length < 8) {
                notyf.error('Le mot de passe doit contenir au moins 8 caractères');
                newPasswordInput.focus();
                return;
            }

            if (!confirmPassword) {
                notyf.error('Veuillez confirmer votre mot de passe');
                confirmPasswordInput.focus();
                return;
            }

            if (!checkPasswordStrength(password)) {
                notyf.error('Le mot de passe ne respecte pas les critères de sécurité minimum');
                newPasswordInput.focus();
                return;
            }

            if (!checkPasswordMatch()) {
                notyf.error('Les mots de passe ne correspondent pas');
                confirmPasswordInput.focus();
                return;
            }

            // Vérifications de sécurité avancées
            const commonPasswords = [
                'password', '123456', '12345678', 'qwerty', 'abc123',
                'password123', 'admin', 'letmein', 'welcome', 'monkey'
            ];

            if (commonPasswords.includes(password.toLowerCase())) {
                notyf.error('Ce mot de passe est trop commun. Choisissez quelque chose de plus unique.');
                newPasswordInput.focus();
                return;
            }

            // Si tout est valide, afficher la modale de confirmation
            showModal(confirmModal);
        });
        // Animation au focus des champs
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.addEventListener('focus', (e) => {
                e.target.style.transform = 'translateY(-2px)';
                e.target.style.boxShadow = '0 8px 25px rgba(59, 130, 246, 0.15)';
            });

            input.addEventListener('blur', (e) => {
                e.target.style.transform = '';
                e.target.style.boxShadow = '';
            });
        });
        // Gestion des raccourcis clavier
        document.addEventListener('keydown', (e) => {
            // ESC pour fermer les modales
            if (e.key === 'Escape') {
                if (confirmModal.classList.contains('show')) {
                    hideModal(confirmModal);
                }
                if (successModal.classList.contains('show')) {
                    hideModal(successModal);
                }
            }

            // Enter pour confirmer dans les modales
            if (e.key === 'Enter') {
                if (confirmModal.classList.contains('show')) {
                    confirmBtn.click();
                }
                if (successModal.classList.contains('show')) {
                    okBtn.click();
                }
            }
        });
        // Effet de particules sur le succès (optionnel)
        function createSuccessParticles() {
            const colors = ['#10B981', '#34D399', '#6EE7B7', '#A7F3D0'];

            for (let i = 0; i < 15; i++) {
                const particle = document.createElement('div');
                particle.style.position = 'fixed';
                particle.style.width = '6px';
                particle.style.height = '6px';
                particle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                particle.style.borderRadius = '50%';
                particle.style.pointerEvents = 'none';
                particle.style.zIndex = '10001';
                particle.style.left = Math.random() * window.innerWidth + 'px';
                particle.style.top = Math.random() * window.innerHeight + 'px';
                particle.style.opacity = '1';
                particle.style.transform = 'scale(0)';
                particle.style.transition = 'all 2s cubic-bezier(0.4, 0, 0.2, 1)';

                document.body.appendChild(particle);

                setTimeout(() => {
                    particle.style.transform = 'scale(1) translateY(-100px)';
                    particle.style.opacity = '0';
                }, 50);

                setTimeout(() => {
                    particle.remove();
                }, 2050);
            }
        }
        // Déclencher les particules lors du succès
        document.addEventListener('DOMContentLoaded', () => {
            okBtn.addEventListener('click', createSuccessParticles);
        });
        // Fonction utilitaire pour la validation en temps réel
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        // Optimisation des validations avec debounce
        const debouncedPasswordCheck = debounce((password) => {
            checkPasswordStrength(password);
        }, 300);
        const debouncedMatchCheck = debounce(() => {
            checkPasswordMatch();
        }, 300);
        // Application du debounce
        newPasswordInput.addEventListener('input', (e) => {
            debouncedPasswordCheck(e.target.value);
            if (confirmPasswordInput.value) {
                debouncedMatchCheck();
            }
        });
        confirmPasswordInput.addEventListener('input', () => {
            debouncedMatchCheck();
        });

        