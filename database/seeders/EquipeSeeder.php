<?php

namespace Database\Seeders;

use App\Models\Equipe;
use Illuminate\Database\Seeder;

class EquipeSeeder extends Seeder
{
    /**
     * Seeder pour créer des équipes de démonstration
     */
    public function run(): void
    {
        // Supprimer les équipes existantes (seulement pour le développement)
        Equipe::truncate();

        // Créer des équipes de test
        $equipes = [
            [
                'nom' => 'Équipe Terrain Nord',
                'description' => 'Équipe en charge des chantiers dans la zone nord de la ville',
                'statut' => 'active',
            ],
            [
                'nom' => 'Équipe Terrain Sud',
                'description' => 'Équipe en charge des chantiers dans la zone sud',
                'statut' => 'active',
            ],
            [
                'nom' => 'Service Bureau',
                'description' => 'Équipe administrative et gestion des dossiers',
                'statut' => 'active',
            ],
            [
                'nom' => 'Service Commercial',
                'description' => 'Équipe commerciale et prospection clients',
                'statut' => 'active',
            ],
            [
                'nom' => 'Équipe Maintenance',
                'description' => 'Équipe de maintenance et support technique',
                'statut' => 'inactive',
            ],
        ];

        foreach ($equipes as $equipeData) {
            Equipe::create($equipeData);
        }

        $this->command->info('✅ 5 équipes de test créées avec succès !');
    }
}
