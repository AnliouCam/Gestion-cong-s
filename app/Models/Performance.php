<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modèle Performance
 * Représente une évaluation mensuelle de performance d'un employé
 *
 * Workflow :
 * - En fin de mois, la direction évalue chaque employé
 * - Note de 1 à 5 (1 = mauvais, 5 = excellent)
 * - Commentaire pour justifier la note
 *
 * Objectif : base pour décisions (primes, sanctions, promotions)
 *
 * SoftDeletes : conserve l'historique des évaluations
 */
class Performance extends Model
{
    use HasFactory, SoftDeletes;

    // Champs modifiables en masse
    protected $fillable = [
        'employe_id',
        'mois',                  // Format: YYYY-MM (ex: 2025-01)
        'note',                  // Note de 1 à 5
        'commentaire',           // Justification de la note
        'evalue_par_user_id',    // Qui a évalué (direction)
    ];

    // Conversion automatique des types
    protected $casts = [
        'note' => 'integer',
    ];

    /**
     * RELATIONS
     */

    // Une performance appartient à un employé
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    // Une performance est créée par un utilisateur (direction)
    public function evaluePar()
    {
        return $this->belongsTo(User::class, 'evalue_par_user_id');
    }
}
