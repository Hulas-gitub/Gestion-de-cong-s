<?php
namespace App\Services;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class MailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }

    private function configureMailer()
    {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = env('MAIL_HOST', 'smtp.gmail.com');
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = env('MAIL_USERNAME');
            $this->mailer->Password = env('MAIL_PASSWORD');
            $this->mailer->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
            $this->mailer->Port = env('MAIL_PORT', 587);
            $this->mailer->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME', 'Graxel Congés'));
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';
        } catch (Exception $e) {
            Log::error('Erreur configuration PHPMailer: ' . $e->getMessage());
        }
    }

    public function envoyerResetPassword($destinataire, $nom, $prenom, $token)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinataire, "$prenom $nom");
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Réinitialisation de votre mot de passe - Graxel Congés';
            $lienReset = url("/reset-password/{$token}?email=" . urlencode($destinataire));
            $this->mailer->Body = $this->templateResetPassword($nom, $prenom, $lienReset);
            $result = $this->mailer->send();
            if ($result) {
                Log::info('Email de réinitialisation envoyé avec succès', ['destinataire' => $destinataire]);
            }
            return $result;
        } catch (Exception $e) {
            Log::error('Erreur envoi email réinitialisation: ' . $e->getMessage());
            return false;
        }
    }

    public function envoyerActivationCompte($destinataire, $nom, $prenom, $tokenActivation)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinataire, "$prenom $nom");
            $adminEmail = $this->getAdminEmail();
            if ($adminEmail) {
                $this->mailer->addBCC($adminEmail);
            }
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Activation de votre compte - Graxel Congés';
            $lienActivation = url("/activation-compte/{$tokenActivation}");
            $this->mailer->Body = $this->templateActivationCompte($nom, $prenom, $lienActivation);
            return $this->mailer->send();
        } catch (Exception $e) {
            Log::error('Erreur envoi email activation: ' . $e->getMessage());
            return false;
        }
    }

    public function envoyerNouvelleDemande($demande, $employe, $chef)
    {
        try {
            $this->mailer->clearAddresses();
            if ($chef && $chef->email) {
                $this->mailer->addAddress($chef->email, "{$chef->prenom} {$chef->nom}");
            }
            $adminEmail = $this->getAdminEmail();
            if ($adminEmail) {
                $this->mailer->addCC($adminEmail);
            }
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Nouvelle demande de congé - ' . $employe->prenom . ' ' . $employe->nom;
            $this->mailer->Body = $this->templateNouvelleDemande($demande, $employe);
            return $this->mailer->send();
        } catch (Exception $e) {
            Log::error('Erreur envoi email nouvelle demande: ' . $e->getMessage());
            return false;
        }
    }

    public function envoyerDemandeApprouvee($demande, $employe, $validateur)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($employe->email, "{$employe->prenom} {$employe->nom}");
            $adminEmail = $this->getAdminEmail();
            if ($adminEmail) {
                $this->mailer->addBCC($adminEmail);
            }
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '✅ Votre demande de congé a été approuvée';
            $this->mailer->Body = $this->templateDemandeApprouvee($demande, $employe, $validateur);
            return $this->mailer->send();
        } catch (Exception $e) {
            Log::error('Erreur envoi email demande approuvée: ' . $e->getMessage());
            return false;
        }
    }

    public function envoyerDemandeRefusee($demande, $employe, $validateur)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($employe->email, "{$employe->prenom} {$employe->nom}");
            $adminEmail = $this->getAdminEmail();
            if ($adminEmail) {
                $this->mailer->addBCC($adminEmail);
            }
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '❌ Votre demande de congé a été refusée';
            $this->mailer->Body = $this->templateDemandeRefusee($demande, $employe, $validateur);
            return $this->mailer->send();
        } catch (Exception $e) {
            Log::error('Erreur envoi email demande refusée: ' . $e->getMessage());
            return false;
        }
    }

    private function getAdminEmail()
    {
        $admin = \App\Models\User::whereHas('role', function($query) {
            $query->where('nom_role', 'Admin');
        })->first();
        return $admin ? $admin->email : null;
    }

    // TEMPLATES HTML AVEC LOGO
    private function templateResetPassword($nom, $prenom, $lienReset)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; }
                .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .header p { margin: 10px 0 0; opacity: 0.9; font-size: 14px; }
                .content { padding: 40px 30px; }
                .content h2 { color: #333; font-size: 22px; margin: 0 0 20px; }
                .content p { color: #666; margin: 15px 0; line-height: 1.8; }
                .info-box { background: #f8f9fa; border-left: 4px solid #667eea; padding: 20px; margin: 25px 0; border-radius: 5px; }
                .info-box strong { color: #333; display: block; margin-bottom: 8px; }
                .button-container { text-align: center; margin: 35px 0; }
                .button { display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white !important; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; transition: transform 0.2s; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); }
                .button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6); }
                .link-text { word-break: break-all; color: #667eea; background: #f8f9fa; padding: 12px; border-radius: 5px; font-size: 13px; margin: 20px 0; }
                .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 25px 0; border-radius: 5px; color: #856404; }
                .warning strong { display: block; margin-bottom: 5px; }
                .footer { background: #f8f9fa; padding: 25px; text-align: center; border-top: 1px solid #e9ecef; }
                .footer p { margin: 5px 0; color: #6c757d; font-size: 13px; }
                .footer a { color: #667eea; text-decoration: none; }
                .icon { font-size: 48px; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='assets/image/logo.png' alt='Graxel Technologies' style='max-width: 150px; margin-bottom: 10px;'>
                    <h1>Réinitialisation de mot de passe</h1>
                    <p>Système de Gestion des Congés</p>
                </div>
                <div class='content'>
                    <h2>Bonjour {$prenom} {$nom},</h2>
                    <p>Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte Graxel Congés.</p>
                    <p>Pour réinitialiser votre mot de passe, cliquez sur le bouton ci-dessous :</p>
                    <div class='button-container'>
                        <a href='{$lienReset}' class='button'>Réinitialiser mon mot de passe</a>
                    </div>
                    <p>Ou copiez et collez ce lien dans votre navigateur :</p>
                    <div class='link-text'>{$lienReset}</div>
                    <div class='warning'>
                        <strong>⚠️ Important :</strong>
                        Ce lien est valable pendant <strong>48 heures</strong> seulement. Après ce délai, vous devrez refaire une demande de réinitialisation.
                    </div>
                    <div class='info-box'>
                        <strong>🛡️ Mesures de sécurité :</strong>
                        Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer ce message. Votre mot de passe actuel reste inchangé et votre compte est sécurisé.
                    </div>
                    <p style='margin-top: 30px;'>Pour toute question ou assistance, n'hésitez pas à contacter le support technique.</p>
                </div>
                <div class='footer'>
                    <p><strong>Graxel Technologies</strong></p>
                    <p>Système de Gestion des Congés</p>
                    <p style='margin-top: 15px;'>
                        <a href='mailto:support@graxel.com'>support@graxel.com</a>
                    </p>
                    <p style='margin-top: 10px; color: #adb5bd; font-size: 12px;'>
                        © 2025 Graxel Technologies. Tous droits réservés.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function templateActivationCompte($nom, $prenom, $lienActivation)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
                .content { background: #f9fafb; padding: 30px; }
                .button { display: inline-block; padding: 12px 30px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='assets/image/logo.png' alt='Graxel Technologies' style='max-width: 150px; margin-bottom: 10px;'>
                    <h1>Bienvenue chez Graxel Congés</h1>
                </div>
                <div class='content'>
                    <h2>Bonjour {$prenom} {$nom},</h2>
                    <p>Votre compte a été créé avec succès ! Pour l'activer et définir votre mot de passe, cliquez sur le bouton ci-dessous :</p>
                    <p style='text-align: center;'>
                        <a href='{$lienActivation}' class='button'>Activer mon compte</a>
                    </p>
                    <p>Ou copiez ce lien dans votre navigateur :</p>
                    <p style='word-break: break-all; color: #3b82f6;'>{$lienActivation}</p>
                    <p><strong>Ce lien est valable pendant 48 heures.</strong></p>
                </div>
                <div class='footer'>
                    <p>© 2025 Graxel Technologies - Système de gestion des congés</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function templateNouvelleDemande($demande, $employe)
    {
        $dateDebut = date('d/m/Y', strtotime($demande->date_debut));
        $dateFin = date('d/m/Y', strtotime($demande->date_fin));
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #f59e0b; color: white; padding: 20px; text-align: center; }
                .content { background: #f9fafb; padding: 30px; }
                .info-box { background: white; padding: 15px; border-left: 4px solid #f59e0b; margin: 15px 0; }
                .button { display: inline-block; padding: 12px 30px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='assets/image/logo.png' alt='Graxel Technologies' style='max-width: 150px; margin-bottom: 10px;'>
                    <h1>📋 Nouvelle demande de congé</h1>
                </div>
                <div class='content'>
                    <h2>Bonjour,</h2>
                    <p><strong>{$employe->prenom} {$employe->nom}</strong> a soumis une nouvelle demande de congé :</p>
                    <div class='info-box'>
                        <p><strong>📅 Période :</strong> Du {$dateDebut} au {$dateFin}</p>
                        <p><strong>⏱️ Durée :</strong> {$demande->nb_jours} jour(s)</p>
                        <p><strong>📝 Motif :</strong> {$demande->motif}</p>
                    </div>
                    <p style='text-align: center;'>
                        <a href='" . url('/demandes-conges') . "' class='button'>Voir la demande</a>
                    </p>
                </div>
                <div class='footer'>
                    <p>© 2025 Graxel Technologies</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function templateDemandeApprouvee($demande, $employe, $validateur)
    {
        $dateDebut = date('d/m/Y', strtotime($demande->date_debut));
        $dateFin = date('d/m/Y', strtotime($demande->date_fin));
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #10b981; color: white; padding: 20px; text-align: center; }
                .content { background: #f9fafb; padding: 30px; }
                .info-box { background: white; padding: 15px; border-left: 4px solid #10b981; margin: 15px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='assets/image/logo.png' alt='Graxel Technologies' style='max-width: 150px; margin-bottom: 10px;'>
                    <h1>✅ Demande approuvée !</h1>
                </div>
                <div class='content'>
                    <h2>Bonjour {$employe->prenom},</h2>
                    <p>Bonne nouvelle ! Votre demande de congé a été <strong>approuvée</strong> par {$validateur->prenom} {$validateur->nom}.</p>
                    <div class='info-box'>
                        <p><strong>📅 Période :</strong> Du {$dateDebut} au {$dateFin}</p>
                        <p><strong>⏱️ Durée :</strong> {$demande->nb_jours} jour(s)</p>
                    </div>
                    <p>Profitez bien de vos congés ! 🌴</p>
                </div>
                <div class='footer'>
                    <p>© 2025 Graxel Technologies</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function templateDemandeRefusee($demande, $employe, $validateur)
    {
        $dateDebut = date('d/m/Y', strtotime($demande->date_debut));
        $dateFin = date('d/m/Y', strtotime($demande->date_fin));
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #ef4444; color: white; padding: 20px; text-align: center; }
                .content { background: #f9fafb; padding: 30px; }
                .info-box { background: white; padding: 15px; border-left: 4px solid #ef4444; margin: 15px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='assets/image/logo.png' alt='Graxel Technologies' style='max-width: 150px; margin-bottom: 10px;'>
                    <h1>❌ Demande refusée</h1>
                </div>
                <div class='content'>
                    <h2>Bonjour {$employe->prenom},</h2>
                    <p>Nous vous informons que votre demande de congé a été <strong>refusée</strong> par {$validateur->prenom} {$validateur->nom}.</p>
                    <div class='info-box'>
                        <p><strong>📅 Période demandée :</strong> Du {$dateDebut} au {$dateFin}</p>
                        <p><strong>⏱️ Durée :</strong> {$demande->nb_jours} jour(s)</p>
                        <p><strong>💬 Motif du refus :</strong> {$demande->commentaire_refus}</p>
                    </div>
                    <p>Pour plus d'informations, veuillez contacter votre responsable.</p>
                </div>
                <div class='footer'>
                    <p>© 2025 Graxel Technologies</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
