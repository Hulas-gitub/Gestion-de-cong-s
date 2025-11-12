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

    /**
     * Configuration de PHPMailer
     */
    private function configureMailer()
    {
        try {
            // Configuration serveur SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host = env('MAIL_HOST', 'smtp.gmail.com');
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = env('MAIL_USERNAME');
            $this->mailer->Password = env('MAIL_PASSWORD');
            $this->mailer->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
            $this->mailer->Port = env('MAIL_PORT', 587);

            // Configuration expéditeur
            $this->mailer->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME', 'Graxel Congés'));

            // Configuration encodage
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';

        } catch (Exception $e) {
            Log::error('Erreur configuration PHPMailer: ' . $e->getMessage());
        }
    }

    /**
     * Email de réinitialisation de mot de passe
     */
    public function envoyerResetPassword($destinataire, $nom, $prenom, $token)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinataire, "$prenom $nom");

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Réinitialisation de votre mot de passe - Graxel Congés';

            // Créer le lien de réinitialisation
            $lienReset = url("/reset-password/{$token}?email=" . urlencode($destinataire));

            $this->mailer->Body = $this->templateResetPassword($nom, $prenom, $lienReset);

            $result = $this->mailer->send();

            if ($result) {
                Log::info('Email de réinitialisation envoyé avec succès', [
                    'destinataire' => $destinataire
                ]);
            }

            return $result;

        } catch (Exception $e) {
            Log::error('Erreur envoi email réinitialisation: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Email d'activation de compte
     */
    public function envoyerActivationCompte($destinataire, $nom, $prenom, $tokenActivation)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinataire, "$prenom $nom");

            // Copie à l'admin
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

    /**
     * Email de nouvelle demande de congé (pour chef et admin)
     */
    public function envoyerNouvelleDemande($demande, $employe, $chef)
    {
        try {
            $this->mailer->clearAddresses();

            // Destinataire principal : Chef de département
            if ($chef && $chef->email) {
                $this->mailer->addAddress($chef->email, "{$chef->prenom} {$chef->nom}");
            }

            // Copie à l'admin
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

    /**
     * Email de demande approuvée (pour employé et admin)
     */
    public function envoyerDemandeApprouvee($demande, $employe, $validateur)
    {
        try {
            $this->mailer->clearAddresses();

            // Destinataire : Employé
            $this->mailer->addAddress($employe->email, "{$employe->prenom} {$employe->nom}");

            // Copie à l'admin
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

    /**
     * Email de demande refusée (pour employé et admin)
     */
    public function envoyerDemandeRefusee($demande, $employe, $validateur)
    {
        try {
            $this->mailer->clearAddresses();

            // Destinataire : Employé
            $this->mailer->addAddress($employe->email, "{$employe->prenom} {$employe->nom}");

            // Copie à l'admin
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

    /**
     * Récupérer l'email de l'admin
     */
    private function getAdminEmail()
    {
        $admin = \App\Models\User::whereHas('role', function($query) {
            $query->where('nom_role', 'Admin');
        })->first();

        return $admin ? $admin->email : null;
    }

    // ========== TEMPLATES HTML ==========

    private function templateResetPassword($nom, $prenom, $lienReset)
    {
        $logoUrl = asset('assets/image/logo.png');

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #4F7EFF 0%, #9B4FFF 100%); color: white; padding: 40px 20px; text-align: center; }
                .header img { max-width: 180px; height: auto; margin-bottom: 20px; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .header p { margin: 10px 0 0; opacity: 0.9; font-size: 14px; }
                .content { padding: 40px 30px; }
                .content h2 { color: #333; font-size: 22px; margin: 0 0 20px; }
                .content p { color: #666; margin: 15px 0; line-height: 1.8; }
                .info-box { background: #f8f9fa; border-left: 4px solid #4F7EFF; padding: 20px; margin: 25px 0; border-radius: 5px; }
                .info-box strong { color: #333; display: block; margin-bottom: 8px; }
                .button-container { text-align: center; margin: 35px 0; }
                .button { display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #4F7EFF 0%, #9B4FFF 100%); color: white !important; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; transition: transform 0.2s; box-shadow: 0 4px 15px rgba(79, 126, 255, 0.4); }
                .button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(79, 126, 255, 0.6); }
                .link-text { word-break: break-all; color: #4F7EFF; background: #f8f9fa; padding: 12px; border-radius: 5px; font-size: 13px; margin: 20px 0; }
                .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 25px 0; border-radius: 5px; color: #856404; }
                .warning strong { display: block; margin-bottom: 5px; }
                .footer { background: linear-gradient(135deg, #4F7EFF 0%, #9B4FFF 100%); padding: 30px 25px; text-align: center; color: white; }
                .footer img { max-width: 150px; height: auto; margin-bottom: 15px; }
                .footer p { margin: 5px 0; opacity: 0.9; font-size: 13px; }
                .footer a { color: white; text-decoration: none; opacity: 0.9; }
                .icon { font-size: 48px; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='{$logoUrl}' alt='Graxel Technologies'>
                    <div class='icon'>🔐</div>
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
                    <img src='{$logoUrl}' alt='Graxel Technologies'>
                    <p><strong>Graxel Technologies</strong></p>
                    <p>Système de Gestion des Congés</p>
                    <p style='margin-top: 15px;'>
                        <a href='mailto:support@graxel.com'>support@graxel.com</a>
                    </p>
                    <p style='margin-top: 10px; opacity: 0.8; font-size: 12px;'>
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
        $logoUrl = asset('assets/image/logo.png');

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #4F7EFF 0%, #9B4FFF 100%); color: white; padding: 40px 20px; text-align: center; }
                .header img { max-width: 180px; height: auto; margin-bottom: 20px; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .header p { margin: 10px 0 0; opacity: 0.9; font-size: 14px; }
                .content { padding: 40px 30px; }
                .content h2 { color: #333; font-size: 22px; margin: 0 0 20px; }
                .content p { color: #666; margin: 15px 0; line-height: 1.8; }
                .button-container { text-align: center; margin: 35px 0; }
                .button { display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #4F7EFF 0%, #9B4FFF 100%); color: white !important; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(79, 126, 255, 0.4); }
                .link-text { word-break: break-all; color: #4F7EFF; background: #f8f9fa; padding: 12px; border-radius: 5px; font-size: 13px; margin: 20px 0; }
                .info-box { background: #f8f9fa; border-left: 4px solid #4F7EFF; padding: 20px; margin: 25px 0; border-radius: 5px; }
                .footer { background: linear-gradient(135deg, #4F7EFF 0%, #9B4FFF 100%); padding: 30px 25px; text-align: center; color: white; }
                .footer img { max-width: 150px; height: auto; margin-bottom: 15px; }
                .footer p { margin: 5px 0; opacity: 0.9; font-size: 13px; }
                .icon { font-size: 48px; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='{$logoUrl}' alt='Graxel Technologies'>
                    <div class='icon'>🎉</div>
                    <h1>Bienvenue chez Graxel !</h1>
                    <p>Activation de votre compte</p>
                </div>
                <div class='content'>
                    <h2>Bonjour {$prenom} {$nom},</h2>
                    <p>Votre compte a été créé avec succès ! Pour l'activer et définir votre mot de passe, cliquez sur le bouton ci-dessous :</p>

                    <div class='button-container'>
                        <a href='{$lienActivation}' class='button'>Activer mon compte</a>
                    </div>

                    <p>Ou copiez ce lien dans votre navigateur :</p>
                    <div class='link-text'>{$lienActivation}</div>

                    <div class='info-box'>
                        <strong>⏰ Important :</strong> Ce lien est valable pendant <strong>48 heures</strong>.
                    </div>
                </div>
                <div class='footer'>
                    <img src='{$logoUrl}' alt='Graxel Technologies'>
                    <p><strong>Graxel Technologies</strong></p>
                    <p>Système de Gestion des Congés</p>
                    <p style='margin-top: 10px; opacity: 0.8; font-size: 12px;'>
                        © 2025 Graxel Technologies. Tous droits réservés.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function templateNouvelleDemande($demande, $employe)
    {
        $logoUrl = asset('assets/image/logo.png');
        $dateDebut = date('d/m/Y', strtotime($demande->date_debut));
        $dateFin = date('d/m/Y', strtotime($demande->date_fin));

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #4F7EFF 0%, #9B4FFF 100%); color: white; padding: 40px 20px; text-align: center; }
                .header img { max-width: 180px; height: auto; margin-bottom: 20px; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .content { padding: 40px 30px; }
                .content h2 { color: #333; font-size: 22px; margin: 0 0 20px; }
                .content p { color: #666; margin: 15px 0; line-height: 1.8; }
                .info-box { background: #f8f9fa; border-left: 4px solid #4F7EFF; padding: 20px; margin: 25px 0; border-radius: 5px; }
                .info-box p { margin: 10px 0; color: #333; }
                .button-container { text-align: center; margin: 35px 0; }
                .button { display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #4F7EFF 0%, #9B4FFF 100%); color: white !important; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(79, 126, 255, 0.4); }
                .footer { background: linear-gradient(135deg, #4F7EFF 0%, #9B4FFF 100%); padding: 30px 25px; text-align: center; color: white; }
                .footer img { max-width: 150px; height: auto; margin-bottom: 15px; }
                .footer p { margin: 5px 0; opacity: 0.9; font-size: 13px; }
                .icon { font-size: 48px; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='{$logoUrl}' alt='Graxel Technologies'>
                    <div class='icon'>📋</div>
                    <h1>Nouvelle demande de congé</h1>
                </div>
                <div class='content'>
                    <h2>Bonjour,</h2>
                    <p><strong>{$employe->prenom} {$employe->nom}</strong> a soumis une nouvelle demande de congé :</p>

                    <div class='info-box'>
                        <p><strong>📅 Période demandée :</strong> Du {$dateDebut} au {$dateFin}</p>
                        <p><strong>⏱️ Durée :</strong> {$demande->nb_jours} jour(s)</p>
                        <p><strong>💬 Motif du refus :</strong> {$demande->commentaire_refus}</p>
                    </div>

                    <p>Pour plus d'informations, veuillez contacter votre responsable.</p>
                </div>
                <div class='footer'>
                    <img src='{$logoUrl}' alt='Graxel Technologies'>
                    <p><strong>Graxel Technologies</strong></p>
                    <p style='margin-top: 10px; opacity: 0.8; font-size: 12px;'>
                        © 2025 Graxel Technologies. Tous droits réservés.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}>📅 Période :</strong> Du {$dateDebut} au {$dateFin}</p>
                        <p><strong>⏱️ Durée :</strong> {$demande->nb_jours} jour(s)</p>
                        <p><strong>📝 Motif :</strong> {$demande->motif}</p>
                    </div>

                    <div class='button-container'>
                        <a href='" . url('/demandes-conges') . "' class='button'>Voir la demande</a>
                    </div>
                </div>
                <div class='footer'>
                    <img src='{$logoUrl}' alt='Graxel Technologies'>
                    <p><strong>Graxel Technologies</strong></p>
                    <p style='margin-top: 10px; opacity: 0.8; font-size: 12px;'>
                        © 2025 Graxel Technologies. Tous droits réservés.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function templateDemandeApprouvee($demande, $employe, $validateur)
    {
        $logoUrl = asset('assets/image/logo.png');
        $dateDebut = date('d/m/Y', strtotime($demande->date_debut));
        $dateFin = date('d/m/Y', strtotime($demande->date_fin));

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 40px 20px; text-align: center; }
                .header img { max-width: 180px; height: auto; margin-bottom: 20px; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .content { padding: 40px 30px; }
                .content h2 { color: #333; font-size: 22px; margin: 0 0 20px; }
                .content p { color: #666; margin: 15px 0; line-height: 1.8; }
                .info-box { background: #f0fdf4; border-left: 4px solid #10b981; padding: 20px; margin: 25px 0; border-radius: 5px; }
                .info-box p { margin: 10px 0; color: #333; }
                .footer { background: linear-gradient(135deg, #4F7EFF 0%, #9B4FFF 100%); padding: 30px 25px; text-align: center; color: white; }
                .footer img { max-width: 150px; height: auto; margin-bottom: 15px; }
                .footer p { margin: 5px 0; opacity: 0.9; font-size: 13px; }
                .icon { font-size: 48px; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='{$logoUrl}' alt='Graxel Technologies'>
                    <div class='icon'>✅</div>
                    <h1>Demande approuvée !</h1>
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
                    <img src='{$logoUrl}' alt='Graxel Technologies'>
                    <p><strong>Graxel Technologies</strong></p>
                    <p style='margin-top: 10px; opacity: 0.8; font-size: 12px;'>
                        © 2025 Graxel Technologies. Tous droits réservés.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function templateDemandeRefusee($demande, $employe, $validateur)
    {
        $logoUrl = asset('assets/image/logo.png');
        $dateDebut = date('d/m/Y', strtotime($demande->date_debut));
        $dateFin = date('d/m/Y', strtotime($demande->date_fin));

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 40px 20px; text-align: center; }
                .header img { max-width: 180px; height: auto; margin-bottom: 20px; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .content { padding: 40px 30px; }
                .content h2 { color: #333; font-size: 22px; margin: 0 0 20px; }
                .content p { color: #666; margin: 15px 0; line-height: 1.8; }
                .info-box { background: #fef2f2; border-left: 4px solid #ef4444; padding: 20px; margin: 25px 0; border-radius: 5px; }
                .info-box p { margin: 10px 0; color: #333; }
                .footer { background: linear-gradient(135deg, #4F7EFF 0%, #9B4FFF 100%); padding: 30px 25px; text-align: center; color: white; }
                .footer img { max-width: 150px; height: auto; margin-bottom: 15px; }
                .footer p { margin: 5px 0; opacity: 0.9; font-size: 13px; }
                .icon { font-size: 48px; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='{$logoUrl}' alt='Graxel Technologies'>
                    <div class='icon'>❌</div>
                    <h1>Demande refusée</h1>
                </div>
                <div class='content'>
                    <h2>Bonjour {$employe->prenom},</h2>
                    <p>Nous vous informons que votre demande de congé a été <strong>refusée</strong> par {$validateur->prenom} {$validateur->nom}.</p>

                    <div class='info-box'>
                        <p><strong
