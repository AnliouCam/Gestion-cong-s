<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder
 * Crée un compte direction par défaut pour tester l'application
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création du compte direction pour les tests
        User::create([
            'name' => 'Direction',
            'email' => 'direction@entreprise.com',
            'password' => Hash::make('password'), // Mot de passe : "password"
            'role' => 'direction',
            'actif' => true,
        ]);

        echo "✅ Utilisateur créé avec succès !\n";
        echo "📧 Email : direction@entreprise.com\n";
        echo "🔑 Mot de passe : password\n";
    }
}
