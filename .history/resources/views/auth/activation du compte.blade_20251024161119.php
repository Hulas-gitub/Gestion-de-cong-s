<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graxel Technologies - Activation du Compte</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <link rel="stylesheet" href="./assets/css/new password.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
</head>
<body>
    <!-- Loader -->
    <div class="loader-container" id="loader">
        <div class="loader-spinner"></div>
        <div class="loader-text">Préparation de l'activation de votre compte...</div>
        <div class="loader-progress">
            <div class="loader-progress-bar"></div>
        </div>
        <div class="loader-subtitle">Bienvenue chez Graxel Technologies...</div>
    </div>
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="login-container">
            <button class="theme-toggle" id="themeToggle" aria-label="Changer de thème">
                <i class="fas fa-moon"></i>
            </button>
         
            <div class="logo-container">
                <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #3B82F6, #6d28ee); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-user-check" style="font-size: 1.75rem; color: white;"></i>
                </div>
            </div>

            <div class="password-reset-header">
                <h1 class="title">Activation du Compte</h1>
                <p class="subtitle">Créez votre mot de passe pour activer votre compte Graxel Technologies</p>
            </div>
            <form id="activationForm">
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
                            placeholder="Créez votre mot de passe"
                            required
                        >
                        <i class="fas fa-key input-icon"></i>
                        <button type="button" class="password-toggle" id="passwordToggle" aria-label="Afficher/masquer le mot de passe">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <p class="strength-text" id="strengthText">
                            <i class="fas fa-info-circle"></i>
                            Saisissez un mot de passe
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirmPassword" class="form-label">
                        <i class="fas fa-check-double"></i>
                        Confirmer le mot de passe
                    </label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="confirmPassword"
                            class="form-input"
                            placeholder="Confirmez votre mot de passe"
                            required
                        >
                        <i class="fas fa-check-double input-icon"></i>
                        <button type="button" class="password-toggle" id="confirmPasswordToggle" aria-label="Afficher/masquer le mot de passe">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-match" id="passwordMatch"></div>
                </div>
                <!-- Critères de sécurité -->
                <div class="security-requirements">
                    <h3 class="requirements-title">
                        <i class="fas fa-shield-check"></i>
                        Critères de sécurité
                    </h3>
                    <ul class="requirements-list">
                        <li class="requirement" id="lengthReq">
                            <i class="fas fa-circle requirement-icon"></i>
                            Au moins 8 caractères
                        </li>
                        <li class="requirement" id="uppercaseReq">
                            <i class="fas fa-circle requirement-icon"></i>
                            Une lettre majuscule (A-Z)
                        </li>
                        <li class="requirement" id="lowercaseReq">
                            <i class="fas fa-circle requirement-icon"></i>
                            Une lettre minuscule (a-z)
                        </li>
                        <li class="requirement" id="numberReq">
                            <i class="fas fa-circle requirement-icon"></i>
                            Au moins un chiffre (0-9)
                        </li>
                    </ul>
                </div>
                <button type="submit" class="submit-btn" id="submitBtn">
                    <span class="btn-text">Activer mon compte</span>
                    <i class="fas fa-check-circle submit-icon"></i>
                    <div class="btn-loader" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </button>
            </form>
        </div>
    </div>
    <!-- Modal de Confirmation -->
    <div class="modal" id="confirmModal">
        <div class="modal-content">
            <div class="modal-icon warning">
                <i class="fas fa-user-check"></i>
            </div>
            <h3 class="modal-title">Confirmer l'activation</h3>
            <p class="modal-message">
                Êtes-vous sûr de vouloir activer votre compte avec ce mot de passe ?
                Vous pourrez vous connecter immédiatement après l'activation.
            </p>
            <div class="modal-actions">
                <button class="modal-btn secondary" id="cancelBtn">Annuler</button>
                <button class="modal-btn primary" id="confirmBtn">Activer</button>
            </div>
        </div>
    </div>
    <!-- Modal de Succès -->
    <div class="modal" id="successModal">
        <div class="modal-content">
            <div class="modal-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 class="modal-title">Compte activé avec succès !</h3>
            <p class="modal-message">
                Votre compte Graxel Technologies est maintenant actif.
                Vous allez être redirigé vers la page de connexion.
            </p>
            <div class="modal-actions">
                <button class="modal-btn primary" id="okBtn">Continuer</button>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="assets/javascript/atctive.js"></script>
</body>
</html>