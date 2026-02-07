<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modèle Employe
 * Représente un employé de l'entreprise
 *
 * SoftDeletes : conserve l'historique même après suppression
 * Important : permet de garder la trace des congés, paies et performances d'anciens employés
 */
class Employe extends Model
{
    use HasFactory, SoftDeletes;

    // Champs modifiables en masse
    protected $fillable = [
        'nom',
        'prenom',
        'email',                  // Optionnel : pour contact futur
        'telephone',              // Optionnel : contact rapide
        'poste',
        'salaire_mensuel',        // Salaire de base mensuel
        'date_embauche',
        'date_sortie',            // Rempli quand statut = "sorti"
        'statut',                 // actif, suspendu, sorti
        'solde_conges_annuel',    // Nombre de jours de congés par an (défaut: 30)
        'equipe_id',              // Équipe à laquelle l'employé appartient
    ];

    // Conversion automatique des types
    protected $casts = [
        'salaire_mensuel' => 'decimal:2',
        'date_embauche' => 'date',
        'date_sortie' => 'date',
        'solde_conges_annuel' => 'integer',
    ];

    /**
     * RELATIONS
     */

    // Un employé appartient à une équipe
    public function equipe()
    {
        return $this->belongsTo(Equipe::class);
    }

    // Un employé a plusieurs demandes de congés
    public function conges()
    {
        return $this->hasMany(Conge::class);
    }

    // Un employé a plusieurs enregistrements de paie
    public function paies()
    {
        return $this->hasMany(Paie::class);
    }

    // Un employé a plusieurs évaluations de performance
    public function performances()
    {
        return $this->hasMany(Performance::class);
    }
}
