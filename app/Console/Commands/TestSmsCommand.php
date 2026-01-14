<?php

namespace App\Console\Commands;

use App\Services\SMS\SMSService;
use Illuminate\Console\Command;

class TestSmsCommand extends Command
{
    protected $signature = 'sms:test {phone : Numéro de téléphone au format international (ex: +224620123456)}';

    protected $description = 'Teste l\'envoi de SMS via le provider configuré';

    public function handle(): int
    {
        $phone = $this->argument('phone');

        $this->info('Provider actif: '.config('services.sms.provider'));
        $this->info("Envoi d'un SMS de test à {$phone}...");

        try {
            $smsService = new SMSService();
            $result = $smsService->sendVerificationCode($phone, '123456');

            if ($result) {
                $this->info('SMS envoyé avec succès!');

                return Command::SUCCESS;
            }

            $this->error('Échec de l\'envoi du SMS. Vérifiez les logs pour plus de détails.');

            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('Erreur: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
