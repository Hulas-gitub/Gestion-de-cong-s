<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MailService;

class TestMail extends Command
{
    protected $signature = 'test:mail {email}';
    protected $description = 'Tester l\'envoi d\'email';

    public function handle()
    {
        $email = $this->argument('email');

        $mailService = new MailService();
        $result = $mailService->envoyerActivationCompte(
            $email,
            'Test',
            'Utilisateur',
            'token123'
        );

        if ($result) {
            $this->info('✅ Email envoyé avec succès à ' . $email);
        } else {
            $this->error('❌ Erreur lors de l\'envoi');
        }
    }
}
