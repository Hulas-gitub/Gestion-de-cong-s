<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graxel Technologies - Connexion</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./Gestion de congés Graxel Technologie/assets/css/index.css">
    <link rel="icon" type="image/png" href="./Gestion de congés Graxel Technologie/assets/images/logo.png">
<link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <!-- Loader amélioré et centré -->
    <div class="loader-container" id="loader">
        <div class="loader-content">
            <div class="loader-logo-container">
                <img src="./Gestion de congés Graxel Technologie/assets/images/logo.png" alt="Graxel Logo" class="loader-logo">
            </div>
            <div class="loader-spinner"></div>
            <div class="loader-text">Chargement en cours</div>
            <div class="loader-progress-container">
                <div class="loader-progress">
                    <div class="loader-progress-bar"></div>
                </div>
            </div>
            <div style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.75rem;">
                Préparation de votre espace...
            </div>
        </div>
    </div>

    <!-- Popup Mot de passe oublié -->
    <div class="popup-overlay" id="forgotPasswordPopup">
        <div class="popup-container">
            <button class="popup-close" id="closePopup" aria-label="Fermer">
                <i class="fas fa-times"></i>
            </button>
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 1rem;">
                    <i class="fas fa-key"></i>
                </div>
                <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">Mot de passe oublié</h2>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">
                    Saisissez votre adresse email pour recevoir un lien de réinitialisation
                </p>
            </div>
            <form id="forgotPasswordForm">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i>
                        Adresse email
                    </label>
                    <div class="input-wrapper">
                        <input
                            type="email"
                            id="resetEmail"
                            class="form-input"
                            placeholder="votre.email@graxel.com"
                            required
                        >
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>
                <button type="submit" class="submit-btn" id="resetSubmitBtn">
                    <div class="btn-content">
                        <span>Envoyer le lien</span>
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="btn-loader">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-container" id="mainContent">
        <div class="login-card">
            <div class="card-decoration"></div>
            <div class="card-content">
                <button class="theme-toggle" id="themeToggle" aria-label="Changer de thème">
                    <i class="fas fa-moon"></i>
                </button>

                <div class="logo-section">
                    <div class="logo">
                        <img src="./Gestion de congés Graxel Technologie/assets/images/logo.png" alt="Logo Graxel Technologie" style="width: 150%; height: 150%; object-fit: contain;">
                    </div>
                    <h1 class="company-name">Graxel Technologies</h1>
                    <p class="company-slogan">Gestion des Congés - Connectez-vous à votre espace</p>
                </div>

                <form class="login-form" id="loginForm">
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i>
                            Adresse email
                        </label>
                        <div class="input-wrapper">
                            <input
                                type="email"
                                id="email"
                                class="form-input"
                                placeholder="votre.email@graxel.com"
                                required
                            >
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>
                            Mot de passe
                        </label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="password"
                                class="form-input"
                                placeholder="Votre mot de passe"
                                required
                            >
                            <i class="fas fa-key input-icon"></i>
                            <button type="button" class="password-toggle" id="passwordToggle" aria-label="Afficher/masquer le mot de passe">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="forgot-password">
                        <button type="button" id="forgotPasswordBtn" class="forgot-password-link">
                            <i class="fas fa-question-circle"></i>
                            Mot de passe oublié ?
                        </button>
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn">
                        <div class="btn-content">
                            <span>Se connecter</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="btn-loader">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="./Gestion de congés Graxel Technologie/assets/javascript/index.js"></script>
</body>
</html>