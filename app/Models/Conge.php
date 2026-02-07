<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modèle Conge
 * Représente une demande de congé d'un employé
 *
 * Workflow :
 * 1. La direction enregistre la demande de l'employé (statut: en_attente)
 * 2. La direction valide ou refuse (statut: valide ou refuse)
 * 3. Historique conservé pour traçabilité
 *
 * SoftDeletes : conserve l'historique des congés
 */
class Conge extends Model
{
    use HasFactory, SoftDeletes;

    // Champs modifiables en masse
    protected $fillable = [
        'employe_id',
        'type',                   // annuel, maladie, exceptionnel
        'date_debut',
        'date_fin',
        'nb_jours',               // Calculé automatiquement (nombre de jours ouvrés)
        'statut',                 // en_attente, valide, refuse
        'commentaire',            // Raison ou note
        'valide_par_user_id',     // Qui a validé/refusé (direction)
        'date_validation',        // Quand la décision a été prise
    ];

    // Conversion automatique des types
    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_validation' => 'datetime',
        'nb_jours' => 'integer',
    ];

    /**
     * RELATIONS
     */

    // Un congé appartient à un employé
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    // Un congé est validé par un utilisateur (direction)
    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par_user_id');
    }
}
