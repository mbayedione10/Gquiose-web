<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:reset 
                            {email? : Email de l\'administrateur (défaut: admin@admin.com)}
                            {--password= : Nouveau mot de passe (défaut: admin)}
                            {--force : Forcer la réinitialisation sans confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Réinitialiser ou créer le compte administrateur principal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: 'admin@admin.com';
        $password = $this->option('password') ?: 'admin';

        $this->info('🔧 Réinitialisation du compte administrateur');
        $this->newLine();

        // Chercher l'utilisateur
        $admin = User::where('email', $email)->first();

        if (! $admin) {
            $this->warn("⚠️  Aucun utilisateur trouvé avec l'email: {$email}");

            if (! $this->option('force') && ! $this->confirm('Voulez-vous créer un nouveau compte administrateur ?', true)) {
                $this->error('Opération annulée');

                return 1;
            }

            $this->info('Création d\'un nouveau compte...');
            $admin = $this->createAdmin($email, $password);

            if (! $admin) {
                return 1;
            }

            $this->info('✅ Compte créé avec succès');
        } else {
            $this->info("👤 Compte trouvé: {$admin->name} ({$admin->email})");

            if (! $this->option('force') && ! $this->confirm('Voulez-vous réinitialiser ce compte ?', true)) {
                $this->error('Opération annulée');

                return 1;
            }

            $this->info('Réinitialisation du compte...');
            $admin = $this->resetAdmin($admin, $password);
        }

        $this->newLine();
        $this->displayAccountInfo($admin);

        return 0;
    }

    /**
     * Créer un nouveau compte admin
     */
    protected function createAdmin(string $email, string $password): ?User
    {
        // Trouver le rôle Super Admin ou Admin
        $role = Role::where('name', 'Super Admin')->first();
        if (! $role) {
            $role = Role::where('name', 'Admin')->first();
        }

        if (! $role) {
            $this->error('❌ Erreur: Aucun rôle Admin trouvé');
            $this->warn('Exécutez d\'abord: php artisan db:seed --class=RoleSeeder');

            return null;
        }

        try {
            $admin = User::create([
                'name' => 'Super Admin',
                'email' => $email,
                'phone' => '+224'.str_pad(rand(600000000, 699999999), 9, '0', STR_PAD_LEFT),
                'password' => Hash::make($password),
                'role_id' => $role->id,
                'email_verified_at' => now(),
            ]);

            return $admin;
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la création: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Réinitialiser un compte admin existant
     */
    protected function resetAdmin(User $admin, string $password): User
    {
        // Trouver le rôle Super Admin ou Admin
        $role = Role::where('name', 'Super Admin')->first();
        if (! $role) {
            $role = Role::where('name', 'Admin')->first();
        }

        if ($role && $admin->role_id != $role->id) {
            $this->warn('⚠️  Rôle incorrect détecté: '.($admin->role?->name ?? 'Aucun'));
            $admin->role_id = $role->id;
            $this->info("✅ Rôle mis à jour vers: {$role->name}");
        }

        // Réinitialiser le mot de passe
        $admin->password = Hash::make($password);
        $admin->email_verified_at = now();
        $admin->save();

        $this->info('✅ Mot de passe réinitialisé');

        return $admin->fresh();
    }

    /**
     * Afficher les informations du compte
     */
    protected function displayAccountInfo(User $admin): void
    {
        $this->components->twoColumnDetail('📧 Email', $admin->email);
        $this->components->twoColumnDetail('👤 Nom', $admin->name);
        $this->components->twoColumnDetail('🔐 Rôle', $admin->role?->name ?? 'Aucun');
        $this->components->twoColumnDetail('⭐ Super Admin', $admin->isSuperAdmin() ? 'Oui' : 'Non');
        $this->components->twoColumnDetail('🔑 Permissions', $admin->getPermissions()->count());
        $this->components->twoColumnDetail('📱 Téléphone', $admin->phone ?? 'N/A');

        $this->newLine();
        $this->info('✅ Compte prêt à être utilisé');
        $this->warn('⚠️  N\'oubliez pas de changer le mot de passe après la première connexion');
    }
}
