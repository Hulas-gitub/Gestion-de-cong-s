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

    /**
     * Méthode générique pour envoyer un email
     */
    public function sendEmail($destinataire, $sujet, $template, $data)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinataire);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $sujet;

            // Générer le corps de l'email selon le template
            switch ($template) {
                case 'emails.demande-approuvee':
                    $this->mailer->Body = $this->templateDemandeApprouvee($data);
                    break;
                case 'emails.demande-refusee':
                    $this->mailer->Body = $this->templateDemandeRefusee($data);
                    break;
                default:
                    throw new \Exception("Template email inconnu: $template");
            }

            $result = $this->mailer->send();

            if ($result) {
                Log::info("Email envoyé avec succès", [
                    'destinataire' => $destinataire,
                    'sujet' => $sujet
                ]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Erreur envoi email: ' . $e->getMessage(), [
                'destinataire' => $destinataire,
                'sujet' => $sujet
            ]);
            return false;
        }
    }

    /**
     * Template pour demande approuvée
     */
    private function templateDemandeApprouvee($data)
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
                .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 40px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .header p { margin: 10px 0 0; opacity: 0.9; font-size: 14px; }
                .content { padding: 40px 30px; }
                .content h2 { color: #333; font-size: 24px; margin: 0 0 20px; }
                .content p { color: #666; margin: 15px 0; line-height: 1.8; font-size: 15px; }
                .success-box { background: #d1fae5; border-left: 4px solid #10b981; padding: 20px; margin: 25px 0; border-radius: 5px; }
                .success-box p { margin: 5px 0; color: #065f46; }
                .info-item { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #e9ecef; }
                .info-item label { display: block; color: #666; font-size: 12px; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
                .info-item .value { font-size: 16px; font-weight: 600; color: #333; }
                .icon { font-size: 64px; margin-bottom: 15px; }
                .footer { background: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef; }
                .footer p { margin: 5px 0; color: #6c757d; font-size: 13px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='icon'>✅</div>
                    <h1>Demande approuvée !</h1>
                    <p>Bonne nouvelle</p>
                </div>
                <div class='content'>
                    <h2>Bonjour {$data['nom_employe']},</h2>
                    <p>Nous avons le plaisir de vous informer que votre demande de congé a été <strong>approuvée</strong> par {$data['nom_chef']}.</p>
                    <div class='success-box'>
                        <p><strong>✅ Votre demande a été validée</strong></p>
                        <p>Vous pouvez profiter de vos congés aux dates prévues.</p>
                    </div>
                    <div class='info-item'>
                        <label>📋 Type de congé</label>
                        <div class='value'>{$data['type_conge']}</div>
                    </div>
                    <div class='info-item'>
                        <label>📅 Période</label>
                        <div class='value'>Du {$data['date_debut']} au {$data['date_fin']}</div>
                    </div>
                    <div class='info-item'>
                        <label>⏱️ Durée</label>
                        <div class='value'>{$data['nb_jours']} jour(s)</div>
                    </div>
                    <p style='margin-top: 30px; font-size: 14px; color: #666;'>
                        Profitez bien de vos congés ! 🌴<br>
                        Nous vous souhaitons un excellent repos.
                    </p>
                </div>
                <div class='footer'>
                    <p><strong>Graxel Technologies</strong></p>
                    <p>Système de Gestion des Congés</p>
                    <p style='margin-top: 10px; color: #adb5bd; font-size: 12px;'>
                        © 2025 Graxel Technologies. Tous droits réservés.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Template pour demande refusée
     */
    private function templateDemandeRefusee($data)
    {
        $commentaireHtml = !empty($data['commentaire']) ? "
            <div class='info-item'>
                <label>💬 Motif du refus</label>
                <div class='value'>{$data['commentaire']}</div>
            </div>
        " : "";

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 40px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .header p { margin: 10px 0 0; opacity: 0.9; font-size: 14px; }
                .content { padding: 40px 30px; }
                .content h2 { color: #333; font-size: 24px; margin: 0 0 20px; }
                .content p { color: #666; margin: 15px 0; line-height: 1.8; font-size: 15px; }
                .alert-box { background: #fee2e2; border-left: 4px solid #ef4444; padding: 20px; margin: 25px 0; border-radius: 5px; }
                .alert-box p { margin: 5px 0; color: #991b1b; }
                .info-item { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #e9ecef; }
                .info-item label { display: block; color: #666; font-size: 12px; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
                .info-item .value { font-size: 16px; font-weight: 600; color: #333; }
                .icon { font-size: 64px; margin-bottom: 15px; }
                .footer { background: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef; }
                .footer p { margin: 5px 0; color: #6c757d; font-size: 13px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='icon'>❌</div>
                    <h1>Demande refusée</h1>
                    <p>Information importante</p>
                </div>
                <div class='content'>
                    <h2>Bonjour {$data['nom_employe']},</h2>
                    <p>Nous vous informons que votre demande de congé a été <strong>refusée</strong> par {$data['nom_chef']}.</p>
                    <div class='alert-box'>
                        <p><strong>❌ Votre demande n'a pas été validée</strong></p>
                        <p>Veuillez contacter votre responsable pour plus d'informations.</p>
                    </div>
                    <div class='info-item'>
                        <label>📋 Type de congé</label>
                        <div class='value'>{$data['type_conge']}</div>
                    </div>
                    <div class='info-item'>
                        <label>📅 Période demandée</label>
                        <div class='value'>Du {$data['date_debut']} au {$data['date_fin']}</div>
                    </div>
                    <div class='info-item'>
                        <label>⏱️ Durée</label>
                        <div class='value'>{$data['nb_jours']} jour(s)</div>
                    </div>
                    {$commentaireHtml}
                    <p style='margin-top: 30px; font-size: 14px; color: #666;'>
                        <strong>Besoin d'aide ?</strong><br>
                        N'hésitez pas à contacter votre responsable pour discuter de cette décision ou soumettre une nouvelle demande avec d'autres dates.
                    </p>
                </div>
                <div class='footer'>
                    <p><strong>Graxel Technologies</strong></p>
                    <p>Système de Gestion des Congés</p>
                    <p style='margin-top: 10px; color: #adb5bd; font-size: 12px;'>
                        © 2025 Graxel Technologies. Tous droits réservés.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Envoyer les credentials de connexion au nouvel utilisateur
     */
    public function envoyerCredentialsCompte($destinataire, $nom, $prenom, $motDePasse, $matricule)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinataire, "$prenom $nom");
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '🎉Nos félicitations - Votre compte est créé';
            $lienConnexion = url("/");
            $this->mailer->Body = $this->templateCredentialsCompte($nom, $prenom, $motDePasse, $matricule, $lienConnexion, $destinataire);
            $result = $this->mailer->send();
            if ($result) {
                Log::info('Email credentials envoyé avec succès', [
                    'destinataire' => $destinataire,
                    'nom' => $nom,
                    'prenom' => $prenom
                ]);
            }
            return $result;
        } catch (Exception $e) {
            Log::error('Erreur envoi email credentials: ' . $e->getMessage(), [
                'destinataire' => $destinataire
            ]);
            return false;
        }
    }

    /**
     * Template HTML pour l'email avec les credentials
     */
    private function templateCredentialsCompte($nom, $prenom, $motDePasse, $matricule, $lienConnexion, $email)
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
                .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
                .header p { margin: 10px 0 0; opacity: 0.9; font-size: 14px; }
                .content { padding: 40px 30px; }
                .content h2 { color: #333; font-size: 24px; margin: 0 0 20px; }
                .content p { color: #666; margin: 15px 0; line-height: 1.8; font-size: 15px; }
                .welcome-box { background: #f0f4ff; border-left: 4px solid #667eea; padding: 20px; margin: 25px 0; border-radius: 5px; }
                .welcome-box p { margin: 5px 0; color: #333; }
                .credentials-box { background: #fff3cd; border: 2px solid #ffc107; padding: 25px; margin: 25px 0; border-radius: 8px; }
                .credentials-box h3 { color: #856404; margin: 0 0 15px; font-size: 18px; }
                .credential-item { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #e9ecef; }
                .credential-item label { display: block; color: #666; font-size: 12px; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
                .credential-item .value { font-size: 18px; font-weight: 600; color: #333; font-family: 'Courier New', monospace; }
                .button-container { text-align: center; margin: 35px 0; }
                .button { display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white !important; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; transition: transform 0.2s; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); }
                .button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6); }
                .warning-box { background: #fee; border-left: 4px solid #ef4444; padding: 20px; margin: 25px 0; border-radius: 5px; }
                .warning-box strong { color: #dc2626; display: block; margin-bottom: 10px; font-size: 16px; }
                .warning-box ul { margin: 10px 0; padding-left: 20px; color: #dc2626; }
                .warning-box li { margin: 8px 0; }
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
                        <p>Matricule : <strong>{$matricule}</strong></p>
                    </div>
                    <div class='credentials-box'>
                        <h3>🔐 Vos identifiants de connexion</h3>
                        <p style='color: #856404; margin-bottom: 15px;'>Utilisez ces informations pour vous connecter à la plateforme :</p>
                        <div class='credential-item'>
                            <label>📧 Email de connexion</label>
                            <div class='value'>{$email}</div>
                        </div>
                        <div class='credential-item'>
                            <label>🔑 Mot de passe temporaire</label>
                            <div class='value'>{$motDePasse}</div>
                        </div>
                    </div>
                    <div class='warning-box'>
                        <strong>⚠️ IMPORTANT - Sécurité de votre compte</strong>
                        <ul>
                            <li><strong>Changez votre mot de passe</strong> dès votre première connexion</li>
                            <li>Ne partagez jamais vos identifiants avec qui que ce soit</li>
                            <li>Choisissez un mot de passe fort et unique</li>
                            <li>Mettez à jour vos informations personnelles si nécessaire</li>
                        </ul>
                    </div>
                    <div class='button-container'>
                        <a href='{$lienConnexion}' class='button'>🚀 Se connecter maintenant</a>
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
                        Si vous rencontrez des difficultés lors de la connexion, n'hésitez pas à contacter le support technique ou l'administrateur système.
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
                        <a href='" . url('/chef-de-departement/demandes-equipe') . "' class='button'>Voir la demande</a>
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

    private function templateDemandeApprouvee_OLD($demande, $employe, $validateur)
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

    private function templateDemandeRefusee_OLD($demande, $employe, $validateur)
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

    /**
     * Notifier le chef de département d'une nouvelle demande
     */
    public function envoyerNouvelleDemande($demande, $employe, $chef)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($chef->email, "{$chef->prenom} {$chef->nom}");
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '📋 Nouvelle demande de congé - ' . $employe->prenom . ' ' . $employe->nom;
            $this->mailer->Body = $this->templateNouvelleDemande($demande, $employe);
            $result = $this->mailer->send();
            if ($result) {
                Log::info('Email nouvelle demande envoyé', [
                    'chef' => $chef->email,
                    'employe' => $employe->email
                ]);
            }
            return $result;
        } catch (Exception $e) {
            Log::error('Erreur envoi email nouvelle demande: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notifier l'employé que sa demande est approuvée (ANCIENNE MÉTHODE - CONSERVÉE POUR COMPATIBILITÉ)
     */
    public function envoyerDemandeApprouvee($demande, $employe, $validateur)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($employe->email, "{$employe->prenom} {$employe->nom}");
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '✅ Demande de congé approuvée';
            $this->mailer->Body = $this->templateDemandeApprouvee_OLD($demande, $employe, $validateur);
            return $this->mailer->send();
        } catch (Exception $e) {
            Log::error('Erreur envoi email approbation: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notifier l'employé que sa demande est refusée (ANCIENNE MÉTHODE - CONSERVÉE POUR COMPATIBILITÉ)
     */
    public function envoyerDemandeRefusee($demande, $employe, $validateur)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($employe->email, "{$employe->prenom} {$employe->nom}");
            $this->mailer->isHTML(true);
            $this->mailer->Subject = '❌ Demande de congé refusée';
            $this->mailer->Body = $this->templateDemandeRefusee_OLD($demande, $employe, $validateur);
            return $this->mailer->send();
        } catch (Exception $e) {
            Log::error('Erreur envoi email refus: ' . $e->getMessage());
            return false;
        }
    }
/**
 * Envoyer une notification de nouvelle note d'information aux employés
 */
public function envoyerNouvelleNoteInformation($destinataire, $nom, $prenom, $nomChef, $titre, $message, $hasDocument, $lienInformations)
{
    try {
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($destinataire, "$prenom $nom");
        $this->mailer->isHTML(true);
        $this->mailer->Subject = '📢 Nouvelle note d\'information - ' . $titre;
        $this->mailer->Body = $this->templateNouvelleNoteInformation($nom, $prenom, $nomChef, $titre, $message, $hasDocument, $lienInformations);

        $result = $this->mailer->send();

        if ($result) {
            Log::info('Email note d\'information envoyé avec succès', [
                'destinataire' => $destinataire,
                'titre' => $titre
            ]);
        }

        return $result;
    } catch (Exception $e) {
        Log::error('Erreur envoi email note information: ' . $e->getMessage(), [
            'destinataire' => $destinataire
        ]);
        return false;
    }
}

/**
 * Template pour nouvelle note d'information
 */
private function templateNouvelleNoteInformation($nom, $prenom, $nomChef, $titre, $message, $hasDocument, $lienInformations)
{
    $documentBadge = $hasDocument ? "
        <div style='background: #dbeafe; padding: 10px 15px; border-radius: 5px; display: inline-block; margin-top: 10px;'>
            <span style='color: #1e40af; font-size: 14px;'>📎 Fichier joint disponible</span>
        </div>
    " : "";

    $messageContent = !empty($message) ? "
        <div class='info-item'>
            <label>💬 Message</label>
            <div class='value' style='font-weight: normal; white-space: pre-wrap;'>" . nl2br(htmlspecialchars($message)) . "</div>
        </div>
    " : "";

    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 40px 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
            .header p { margin: 10px 0 0; opacity: 0.9; font-size: 14px; }
            .content { padding: 40px 30px; }
            .content h2 { color: #333; font-size: 24px; margin: 0 0 20px; }
            .content p { color: #666; margin: 15px 0; line-height: 1.8; font-size: 15px; }
            .info-box { background: #eff6ff; border-left: 4px solid #3b82f6; padding: 20px; margin: 25px 0; border-radius: 5px; }
            .info-box p { margin: 5px 0; color: #1e40af; }
            .info-item { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #e9ecef; }
            .info-item label { display: block; color: #666; font-size: 12px; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
            .info-item .value { font-size: 16px; font-weight: 600; color: #333; }
            .icon { font-size: 64px; margin-bottom: 15px; }
            .button-container { text-align: center; margin: 35px 0; }
            .button { display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white !important; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; transition: transform 0.2s; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4); }
            .button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6); }
            .footer { background: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef; }
            .footer p { margin: 5px 0; color: #6c757d; font-size: 13px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='icon'>📢</div>
                <h1>Nouvelle note d'information</h1>
                <p>Communication interne</p>
            </div>
            <div class='content'>
                <h2>Bonjour {$prenom} {$nom},</h2>
                <p><strong>{$nomChef}</strong> a publié une nouvelle note d'information pour votre département.</p>
                <div class='info-box'>
                    <p><strong>📋 Titre de la note</strong></p>
                    <p style='font-size: 18px; margin-top: 10px;'>{$titre}</p>
                    {$documentBadge}
                </div>
                {$messageContent}
                <div class='button-container'>
                    <a href='{$lienInformations}' class='button'>📖 Consulter la note complète</a>
                </div>
                <p style='margin-top: 30px; font-size: 14px; color: #666;'>
                    Cliquez sur le bouton ci-dessus pour accéder à la plateforme et consulter tous les détails de cette note.
                </p>
            </div>
            <div class='footer'>
                <p><strong>Graxel Technologies</strong></p>
                <p>Système de Gestion des Congés</p>
                <p style='margin-top: 10px; color: #adb5bd; font-size: 12px;'>
                    © 2025 Graxel Technologies. Tous droits réservés.
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
}
}
