<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modèle Paie
 * Représente un enregistrement de paiement mensuel pour un employé
 *
 * Calcul du montant :
 * montant_total = salaire_base + primes - retenues
 *
 * Objectif : traçabilité complète des paiements pour éviter les conflits
 *
 * SoftDeletes : conserve l'historique des paies
 */
class Paie extends Model
{
    use HasFactory, SoftDeletes;

    // Champs modifiables en masse
    protected $fillable = [
        'employe_id',
        'mois',                      // Format: YYYY-MM (ex: 2025-01)
        'salaire_base',              // Salaire mensuel de base
        'primes',                    // Primes exceptionnelles ce mois
        'retenues',                  // Retenues sur salaire ce mois
        'montant_total',             // Montant final payé (calculé)
        'date_paiement',             // Quand le paiement a été effectué
        'mode_paiement',             // cash ou virement
        'statut',                    // paye ou non_paye
        'reference_paiement',        // N° virement, reçu, etc. (preuve)
        'commentaire',               // Note additionnelle
        'enregistre_par_user_id',    // Qui a enregistré le paiement (direction)
    ];

    // Conversion automatique des types
    protected $casts = [
        'salaire_base' => 'decimal:2',
        'primes' => 'decimal:2',
        'retenues' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    /**
     * RELATIONS
     */

    // Une paie appartient à un employé
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    // Une paie est enregistrée par un utilisateur (direction)
    public function enregistrePar()
    {
        return $this->belongsTo(User::class, 'enregistre_par_user_id');
    }
}
