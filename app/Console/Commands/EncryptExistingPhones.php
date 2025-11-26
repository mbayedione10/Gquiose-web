<?php

namespace App\Console\Commands;

use App\Models\Utilisateur;
use App\Models\Code;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class EncryptExistingPhones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:encrypt-phones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt existing phone numbers in database for GDPR compliance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting phone encryption process...');
        $this->newLine();

        // Encrypt utilisateurs phone numbers
        $users = Utilisateur::whereNotNull('phone')->get();
        $usersCount = 0;
        $usersAlreadyEncrypted = 0;

        $this->info("Found {$users->count()} users with phone numbers");

        foreach ($users as $user) {
            // Vérifier si déjà chiffré
            try {
                $decrypted = Crypt::decryptString($user->phone);
                // Déjà chiffré, skip
                $usersAlreadyEncrypted++;
                continue;
            } catch (\Exception $e) {
                // Pas chiffré, on chiffre
                $originalPhone = $user->phone;
                $user->phone = Crypt::encryptString($user->phone);
                $user->save();
                $usersCount++;
                $this->line("✓ Encrypted phone for user ID {$user->id}: {$originalPhone} -> [encrypted]");
            }
        }

        $this->newLine();
        $this->info("Users processed:");
        $this->line("  - Already encrypted: {$usersAlreadyEncrypted}");
        $this->line("  - Newly encrypted: {$usersCount}");

        // Encrypt codes phone numbers
        $this->newLine();
        $this->info('Processing codes table...');

        $codes = Code::whereNotNull('phone')->get();
        $codesCount = 0;
        $codesAlreadyEncrypted = 0;

        $this->info("Found {$codes->count()} codes with phone numbers");

        foreach ($codes as $code) {
            // Vérifier si déjà chiffré
            try {
                $decrypted = Crypt::decryptString($code->phone);
                // Déjà chiffré, skip
                $codesAlreadyEncrypted++;
                continue;
            } catch (\Exception $e) {
                // Pas chiffré, on chiffre
                $code->phone = Crypt::encryptString($code->phone);
                $code->save();
                $codesCount++;
            }
        }

        $this->newLine();
        $this->info("Codes processed:");
        $this->line("  - Already encrypted: {$codesAlreadyEncrypted}");
        $this->line("  - Newly encrypted: {$codesCount}");

        $this->newLine();
        $this->info('✓ Phone encryption completed successfully!');
        $this->info("Total encrypted: {$usersCount} users, {$codesCount} codes");

        return 0;
    }
}
