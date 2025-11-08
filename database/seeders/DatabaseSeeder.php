<?php

namespace Database\Seeders;

use App\Models\{
    Administration,
    Classe,
    Comptable,
    Etudiant,
    Inscription,
    Paiement,
    Personne,
    User
};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Désactiver les contraintes FK temporairement
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Vider les tables dans l'ordre inverse des dépendances
        Paiement::truncate();
        Inscription::truncate();
        Etudiant::truncate();
        Comptable::truncate();
        Administration::truncate();
        Personne::truncate();
        User::truncate();
        Classe::truncate();

        // Réactiver les contraintes
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 1. Création de l'utilisateur admin AVANT la personne
        $adminUser = User::create([
            'name' => 'Admin System',
            'email' => 'admin@ecole.edu',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // 2. Création de l'utilisateur comptable AVANT la personne
        $comptableUser = User::create([
            'name' => 'Comptable Ecole',
            'email' => 'comptable@ecole.edu',
            'password' => Hash::make('password'),
            'role' => 'comptable'
        ]);

        // 3. Création de l'utilisateur test
        $testUser = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'etudiant'
        ]);

        // 4. Création des classes avec libellés uniques
        $niveaux = ['Licence 1', 'Licence 2', 'Licence 3', 'Master 1', 'Master 2'];
        $groupes = ['A', 'B', 'C'];

        foreach ($niveaux as $niveau) {
            foreach ($groupes as $groupe) {
                Classe::create([
                    'libelle' => "$niveau Groupe $groupe",
                    'description' => "Description pour $niveau Groupe $groupe"
                ]);
            }
        }
        $classes = Classe::all();

        // 5. Création des personnes et étudiants (la factory crée automatiquement les users)
        $etudiants = Etudiant::factory()
            ->count(30)
            ->has(Personne::factory())
            ->create();

        // 6. Création de la personne admin AVEC user_id
        $adminPersonne = Personne::create([
            'user_id' => $adminUser->id, // IMPORTANT
            'email' => 'admin@ecole.edu',
            'nom' => 'Admin',
            'prenom' => 'Ecole',
            'nom_d_utilisateur' => 'admin.ecole'
        ]);
        $admin = Administration::create(['personne_id' => $adminPersonne->id]);

        // 7. Création de la personne comptable AVEC user_id
        $comptablePersonne = Personne::create([
            'user_id' => $comptableUser->id, // IMPORTANT
            'email' => 'comptable@ecole.edu',
            'nom' => 'Comptable',
            'prenom' => 'Ecole',
            'nom_d_utilisateur' => 'comptable.ecole'
        ]);
        $comptable = Comptable::create(['personne_id' => $comptablePersonne->id]);

        // 8. Création des inscriptions
        $inscriptions = Inscription::factory()
            ->count(40)
            ->sequence(fn () => [
                'etudiant_id' => $etudiants->random()->id,
                'classe_id' => $classes->random()->id,
                'administration_id' => $admin->id
            ])
            ->create();

        // 9. Création des paiements
        Paiement::factory()
            ->count(60)
            ->sequence(fn () => [
                'inscription_id' => $inscriptions->random()->id,
                'comptable_id' => $comptable->id,
                'statut' => fake()->randomElement(['valide', 'en_attente', 'rejete'])
            ])
            ->create();

        $this->command->info('✅ Base de données seedée avec succès !');
        $this->command->info('📧 Admin: admin@ecole.edu | Password: password');
        $this->command->info('📧 Comptable: comptable@ecole.edu | Password: password');
        $this->command->info('📧 Test: test@example.com | Password: password');
    }
}
