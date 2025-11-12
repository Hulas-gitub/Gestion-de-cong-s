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
            $query->where('nom_role', 'Administrateur');
        })->first();

        return $admin ? $admin->email : null;
    }

    // ========== TEMPLATES HTML ==========

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
