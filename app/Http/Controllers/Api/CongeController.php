<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conge;
use App\Models\Employe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * CongeController
 * Gère les demandes de congés (annuel, maladie, exceptionnel)
 *
 * Fonctionnalités :
 * - Enregistrer une demande de congé
 * - Lister les demandes avec filtres (statut, employé, type)
 * - Valider ou refuser une demande
 * - Historique complet
 */
class CongeController extends Controller
{
    /**
     * Liste des congés avec filtres
     *
     * Filtres disponibles :
     * - statut : en_attente, valide, refuse
     * - employe_id : filtrer par employé
     * - type : annuel, maladie, exceptionnel
     * - search : recherche par nom d'employé
     */
    public function index(Request $request): JsonResponse
    {
        $query = Conge::with(['employe.equipe', 'validePar']);

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par employé
        if ($request->filled('employe_id')) {
            $query->where('employe_id', $request->employe_id);
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Recherche par nom d'employé
        if ($request->filled('search')) {
            $query->whereHas('employe', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('prenom', 'like', '%' . $request->search . '%');
            });
        }

        $conges = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $conges
        ]);
    }

    /**
     * Créer une demande de congé
     *
     * Calcule automatiquement le nombre de jours
     * Statut initial : en_attente
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'type' => 'required|in:annuel,maladie,exceptionnel',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'commentaire' => 'nullable|string|max:500'
        ]);

        // Calcul automatique du nombre de jours
        $dateDebut = Carbon::parse($validated['date_debut']);
        $dateFin = Carbon::parse($validated['date_fin']);
        $nbJours = $dateDebut->diffInDays($dateFin) + 1;

        // Vérification du solde pour les congés annuels
        if ($validated['type'] === 'annuel') {
            $employe = Employe::find($validated['employe_id']);

            if ($employe->solde_conges_annuel < $nbJours) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solde de congés insuffisant',
                    'errors' => [
                        'solde' => [
                            "L'employé dispose de {$employe->solde_conges_annuel} jours de congés, mais la demande est de {$nbJours} jours"
                        ]
                    ]
                ], 422);
            }
        }

        // Création de la demande
        $conge = Conge::create([
            'employe_id' => $validated['employe_id'],
            'type' => $validated['type'],
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'],
            'nb_jours' => $nbJours,
            'statut' => 'en_attente',
            'commentaire' => $validated['commentaire'] ?? null
        ]);

        $conge->load(['employe.equipe']);

        return response()->json([
            'success' => true,
            'message' => 'Demande de congé créée avec succès',
            'data' => $conge
        ], 201);
    }

    /**
     * Afficher le détail d'un congé
     */
    public function show($id): JsonResponse
    {
        $conge = Conge::with(['employe.equipe', 'validePar'])->find($id);

        if (!$conge) {
            return response()->json([
                'success' => false,
                'message' => 'Congé non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $conge
        ]);
    }

    /**
     * Valider une demande de congé
     *
     * Si type = annuel : déduit du solde de l'employé
     * Si type = maladie ou exceptionnel : pas de déduction
     */
    public function valider(Request $request, $id): JsonResponse
    {
        $conge = Conge::with('employe')->find($id);

        if (!$conge) {
            return response()->json([
                'success' => false,
                'message' => 'Congé non trouvé'
            ], 404);
        }

        // Vérifier que le congé est en attente
        if ($conge->statut !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Ce congé a déjà été traité'
            ], 400);
        }

        // Pour les congés annuels : vérifier et déduire du solde
        if ($conge->type === 'annuel') {
            $employe = $conge->employe;

            if ($employe->solde_conges_annuel < $conge->nb_jours) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solde de congés insuffisant',
                    'errors' => [
                        'solde' => [
                            "L'employé dispose de {$employe->solde_conges_annuel} jours, mais le congé demandé est de {$conge->nb_jours} jours"
                        ]
                    ]
                ], 422);
            }

            // Déduction du solde
            $employe->solde_conges_annuel -= $conge->nb_jours;
            $employe->save();
        }

        // Mise à jour du statut
        $conge->statut = 'valide';
        $conge->valide_par_user_id = auth()->id();
        $conge->date_validation = now();
        $conge->save();

        $conge->load(['employe.equipe', 'validePar']);

        return response()->json([
            'success' => true,
            'message' => 'Congé validé avec succès',
            'data' => $conge
        ]);
    }

    /**
     * Refuser une demande de congé
     *
     * Aucune déduction du solde
     */
    public function refuser(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'commentaire' => 'nullable|string|max:500'
        ]);

        $conge = Conge::with('employe')->find($id);

        if (!$conge) {
            return response()->json([
                'success' => false,
                'message' => 'Congé non trouvé'
            ], 404);
        }

        // Vérifier que le congé est en attente
        if ($conge->statut !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Ce congé a déjà été traité'
            ], 400);
        }

        // Mise à jour du statut
        $conge->statut = 'refuse';
        $conge->valide_par_user_id = auth()->id();
        $conge->date_validation = now();

        // Ajouter le commentaire de refus
        if (isset($validated['commentaire'])) {
            $conge->commentaire = ($conge->commentaire ? $conge->commentaire . ' | ' : '') . 'Refus : ' . $validated['commentaire'];
        }

        $conge->save();

        $conge->load(['employe.equipe', 'validePar']);

        return response()->json([
            'success' => true,
            'message' => 'Congé refusé',
            'data' => $conge
        ]);
    }
}
