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
            $this->mailer->Subject = '🎉 Bienvenue chez Graxel - Activez votre compte';

            $lienActivation = url("/activation-compte/{$tokenActivation}");

            $this->mailer->Body = $this->templateActivationCompte($nom, $prenom, $lienActivation);

            $result = $this->mailer->send();

            if ($result) {
                Log::info('Email d\'activation envoyé avec succès', [
                    'destinataire' => $destinataire,
                    'nom' => $nom,
                    'prenom' => $prenom
                ]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Erreur envoi email activation: ' . $e->getMessage(), [
                'destinataire' => $destinataire
            ]);
            return false;
        }
    }

    private function templateActivationCompte($nom, $prenom, $lienActivation)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 20px; text-align: center; }
                .header img { max-width: 150px; margin-bottom: 15px; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .header p { margin: 10px 0 0; opacity: 0.9; font-size: 14px; }
                .content { padding: 40px 30px; }
                .content h2 { color: #333; font-size: 24px; margin: 0 0 20px; }
                .content p { color: #666; margin: 15px 0; line-height: 1.8; font-size: 15px; }
                .welcome-box { background: #f0f4ff; border-left: 4px solid #667eea; padding: 20px; margin: 25px 0; border-radius: 5px; }
                .welcome-box p { margin: 5px 0; color: #333; }
                .button-container { text-align: center; margin: 35px 0; }
                .button { display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white !important; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; transition: transform 0.2s; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); }
                .button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6); }
                .link-section { background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 25px 0; }
                .link-section p { margin: 5px 0; font-size: 13px; color: #666; }
                .link-text { word-break: break-all; color: #667eea; background: white; padding: 12px; border-radius: 5px; font-size: 13px; margin: 10px 0; border: 1px solid #e9ecef; }
                .info-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 25px 0; border-radius: 5px; }
                .info-box strong { color: #856404; display: block; margin-bottom: 10px; font-size: 16px; }
                .info-box ul { margin: 10px 0; padding-left: 20px; color: #856404; }
                .info-box li { margin: 8px 0; }
                .features { background: #f8f9fa; padding: 25px; margin: 25px 0; border-radius: 5px; }
                .features h3 { color: #333; font-size: 18px; margin: 0 0 15px; }
                .features ul { margin: 0; padding-left: 20px; }
                .features li { margin: 10px 0; color: #666; }
                .footer { background: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef; }
                .footer p { margin: 5px 0; color: #6c757d; font-size: 13px; }
                .footer strong { color: #333; font-size: 15px; }
                .footer a { color: #667eea; text-decoration: none; }
                .icon { font-size: 64px; margin-bottom: 15px; }
                .divider { height: 1px; background: #e9ecef; margin: 30px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='icon'>🎉</div>
                    <h1>Bienvenue dans l'équipe !</h1>
                    <p>Système de Gestion des Congés</p>
                </div>

                <div class='content'>
                    <h2>Bonjour {$prenom} {$nom},</h2>

                    <p>Félicitations ! Votre compte a été créé avec succès sur la plateforme <strong>Graxel Congés</strong>.</p>

                    <div class='welcome-box'>
                        <p><strong>👤 Votre profil :</strong></p>
                        <p>Nom complet : <strong>{$prenom} {$nom}</strong></p>
                        <p>Vous faites maintenant partie de notre système de gestion des congés.</p>
                    </div>

                    <p>Pour commencer à utiliser votre compte, vous devez d'abord <strong>l'activer</strong> et <strong>définir votre mot de passe</strong>.</p>

                    <div class='button-container'>
                        <a href='{$lienActivation}' class='button'>🔓 Activer mon compte</a>
                    </div>

                    <div class='link-section'>
                        <p><strong>Le bouton ne fonctionne pas ?</strong></p>
                        <p>Copiez et collez ce lien dans votre navigateur :</p>
                        <div class='link-text'>{$lienActivation}</div>
                    </div>

                    <div class='info-box'>
                        <strong>⏰ Important - Délai d'activation</strong>
                        <ul>
                            <li>Ce lien est valable pendant <strong>48 heures</strong></li>
                            <li>Après ce délai, vous devrez contacter l'administrateur</li>
                            <li>Lors de l'activation, choisissez un mot de passe sécurisé</li>
                        </ul>
                    </div>

                    <div class='features'>
                        <h3>📋 Ce que vous pourrez faire :</h3>
                        <ul>
                            <li>✅ Soumettre des demandes de congés</li>
                            <li>📅 Consulter le calendrier des absences</li>
                            <li>📊 Suivre votre solde de congés</li>
                            <li>🔔 Recevoir des notifications en temps réel</li>
                            <li>👤 Gérer votre profil</li>
                        </ul>
                    </div>

                    <div class='divider'></div>

                    <p style='margin-top: 30px; font-size: 14px; color: #666;'>
                        <strong>Besoin d'aide ?</strong><br>
                        Si vous rencontrez des difficultés lors de l'activation, n'hésitez pas à contacter le support technique ou l'administrateur système.
                    </p>
                </div>

                <div class='footer'>
                    <p><strong>Graxel Technologies</strong></p>
                    <p>Système de Gestion des Congés</p>
                    <p style='margin-top: 15px;'>
                        <a href='mailto:" . env('MAIL_FROM_ADDRESS') . "'>📧 " . env('MAIL_FROM_ADDRESS') . "</a>
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
