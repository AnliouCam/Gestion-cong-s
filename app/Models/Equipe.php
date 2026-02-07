<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modèle Equipe
 * Représente une équipe/service de l'entreprise
 *
 * SoftDeletes : permet de garder l'historique des équipes supprimées
 */
class Equipe extends Model
{
    use HasFactory, SoftDeletes;

    // Champs modifiables en masse
    protected $fillable = [
        'nom',
        'description',
        'chef_equipe_id', // ID de l'employé qui dirige l'équipe (optionnel)
        'statut',         // active, inactive, dissoute
    ];

    protected $casts = [
        'statut' => 'string',
    ];

    /**
     * RELATIONS
     */

    // Une équipe a plusieurs employés
    public function employes()
    {
        return $this->hasMany(Employe::class);
    }

    // Une équipe peut avoir un chef d'équipe (qui est un employé)
    public function chefEquipe()
    {
        return $this->belongsTo(Employe::class, 'chef_equipe_id');
    }
}
