<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Réinitialisation du mot de passe - Graxel Technologies</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/index.css') }}">
</head>
<body>
    <!-- Loader -->
    <div class="loader-container" id="loader">
        <div class="loader-content">
            <div class="loader-logo-container">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Graxel Logo" class="loader-logo">
            </div>
            <div class="loader-spinner"></div>
            <div class="loader-text">Veuillez patienter un instant</div>
            <div class="loader-progress-container">
                <div class="loader-progress">
                    <div class="loader-progress-bar"></div>
                </div>
            </div>
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
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Graxel Logo" class="loader-logo">
                    </div>
                    <h1 class="company-name">Graxel Technologies</h1>
                    <p class="company-slogan">Réinitialisez votre mot de passe</p>
                </div>

                <form class="login-form" id="resetPasswordForm">
                    <input type="hidden" id="resetToken" value="{{ $token }}">

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
                                placeholder="exemple@gmail.com"
                                required
                            >
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>
                            Nouveau mot de passe
                        </label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="password"
                                class="form-input"
                                placeholder="Minimum 6 caractères"
                                required
                                minlength="6"
                            >
                            <i class="fas fa-key input-icon"></i>
                            <button type="button" class="password-toggle" id="passwordToggle" aria-label="Afficher/masquer">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-lock"></i>
                            Confirmer le mot de passe
                        </label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="password_confirmation"
                                class="form-input"
                                placeholder="Confirmez votre mot de passe"
                                required
                                minlength="6"
                            >
                            <i class="fas fa-key input-icon"></i>
                            <button type="button" class="password-toggle" id="passwordConfirmToggle" aria-label="Afficher/masquer">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn">
                        <div class="btn-content">
                            <span>Réinitialiser le mot de passe</span>
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="btn-loader">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </button>

                    <div class="forgot-password" style="text-align: center; margin-top: 1rem;">
                        <a href="{{ route('index') }}" style="color: var(--primary-color); text-decoration: none;">
                            <i class="fas fa-arrow-left"></i>
                            Retour à la connexion
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/javascript/reset-password.js') }}"></script>
</body>
</html>
