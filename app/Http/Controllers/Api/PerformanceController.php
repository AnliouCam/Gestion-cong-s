<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Performance;
use App\Models\Employe;
use App\Models\Equipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * PerformanceController
 * Gère les évaluations mensuelles des employés
 *
 * Fonctionnalités :
 * - Évaluer un employé (note 1-5 + commentaire)
 * - Lister les évaluations avec filtres (mois, employé, équipe, note)
 * - Modifier une évaluation
 * - Supprimer une évaluation
 * - Historique des évaluations d'un employé
 * - Moyenne par équipe
 * - Moyenne générale du mois
 *
 * Règles métier :
 * - Une seule évaluation par employé par mois
 * - Note de 1 à 5 obligatoire
 * - Commentaire optionnel mais recommandé
 * - Modification autorisée (contrairement aux paies)
 */
class PerformanceController extends Controller
{
    /**
     * Liste des évaluations avec filtres
     *
     * Filtres disponibles :
     * - mois : YYYY-MM (ex: 2025-01)
     * - employe_id : filtrer par employé
     * - equipe_id : filtrer par équipe
     * - note : filtrer par note (1-5)
     * - search : recherche par nom d'employé
     */
    public function index(Request $request): JsonResponse
    {
        $query = Performance::with(['employe.equipe', 'evaluePar']);

        // Filtre par mois
        if ($request->filled('mois')) {
            $query->where('mois', $request->mois);
        }

        // Filtre par employé
        if ($request->filled('employe_id')) {
            $query->where('employe_id', $request->employe_id);
        }

        // Filtre par équipe
        if ($request->filled('equipe_id')) {
            $query->whereHas('employe', function ($q) use ($request) {
                $q->where('equipe_id', $request->equipe_id);
            });
        }

        // Filtre par note
        if ($request->filled('note')) {
            $query->where('note', $request->note);
        }

        // Recherche par nom d'employé
        if ($request->filled('search')) {
            $query->whereHas('employe', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('prenom', 'like', '%' . $request->search . '%');
            });
        }

        $performances = $query->orderBy('mois', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->get();

        return response()->json([
            'success' => true,
            'data' => $performances
        ]);
    }

    /**
     * Créer une nouvelle évaluation
     *
     * Vérifie qu'il n'existe pas déjà une évaluation pour cet employé ce mois
     * Note obligatoire (1-5), commentaire optionnel
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'mois' => 'required|date_format:Y-m',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000'
        ]);

        // Vérifier qu'il n'existe pas déjà une évaluation pour cet employé ce mois
        $evaluationExistante = Performance::where('employe_id', $validated['employe_id'])
                                          ->where('mois', $validated['mois'])
                                          ->first();

        if ($evaluationExistante) {
            return response()->json([
                'success' => false,
                'message' => 'Une évaluation existe déjà pour cet employé pour ce mois',
                'errors' => [
                    'mois' => ['Une évaluation a déjà été enregistrée pour cet employé pour le mois de ' . $validated['mois']]
                ],
                'evaluation_existante' => $evaluationExistante
            ], 422);
        }

        // Création de l'évaluation
        $performance = Performance::create([
            'employe_id' => $validated['employe_id'],
            'mois' => $validated['mois'],
            'note' => $validated['note'],
            'commentaire' => $validated['commentaire'] ?? null,
            'evalue_par_user_id' => $request->user()->id,
        ]);

        // Charger les relations pour le retour
        $performance->load(['employe.equipe', 'evaluePar']);

        return response()->json([
            'success' => true,
            'message' => 'Évaluation enregistrée avec succès',
            'data' => $performance
        ], 201);
    }

    /**
     * Afficher une évaluation spécifique
     */
    public function show($id): JsonResponse
    {
        $performance = Performance::with(['employe.equipe', 'evaluePar'])->find($id);

        if (!$performance) {
            return response()->json([
                'success' => false,
                'message' => 'Évaluation non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $performance
        ]);
    }

    /**
     * Modifier une évaluation existante
     *
     * Contrairement aux paies, les performances peuvent être modifiées
     * (ajustement de note ou commentaire)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $performance = Performance::find($id);

        if (!$performance) {
            return response()->json([
                'success' => false,
                'message' => 'Évaluation non trouvée'
            ], 404);
        }

        $validated = $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000'
        ]);

        // Mise à jour
        $performance->update([
            'note' => $validated['note'],
            'commentaire' => $validated['commentaire'] ?? null,
        ]);

        // Recharger les relations
        $performance->load(['employe.equipe', 'evaluePar']);

        return response()->json([
            'success' => true,
            'message' => 'Évaluation mise à jour avec succès',
            'data' => $performance
        ]);
    }

    /**
     * Supprimer une évaluation
     *
     * Soft delete pour conserver l'historique
     */
    public function destroy($id): JsonResponse
    {
        $performance = Performance::find($id);

        if (!$performance) {
            return response()->json([
                'success' => false,
                'message' => 'Évaluation non trouvée'
            ], 404);
        }

        $performance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Évaluation supprimée avec succès'
        ]);
    }

    /**
     * Historique des évaluations d'un employé
     *
     * Retourne toutes les évaluations d'un employé, triées par mois décroissant
     * Calcule également la moyenne générale de cet employé
     */
    public function historiqueEmploye($employeId): JsonResponse
    {
        // Vérifier que l'employé existe
        $employe = Employe::find($employeId);

        if (!$employe) {
            return response()->json([
                'success' => false,
                'message' => 'Employé non trouvé'
            ], 404);
        }

        // Récupérer toutes les évaluations de cet employé
        $performances = Performance::where('employe_id', $employeId)
                                   ->with(['evaluePar'])
                                   ->orderBy('mois', 'desc')
                                   ->get();

        // Calculer la moyenne générale
        $moyenneGenerale = $performances->avg('note');

        return response()->json([
            'success' => true,
            'data' => [
                'employe' => $employe,
                'performances' => $performances,
                'moyenne_generale' => $moyenneGenerale ? round($moyenneGenerale, 1) : null,
                'total_evaluations' => $performances->count()
            ]
        ]);
    }

    /**
     * Moyenne des performances d'une équipe
     *
     * Calcule la moyenne des notes de tous les employés actifs d'une équipe
     * pour un mois donné ou globalement
     */
    public function moyenneEquipe(Request $request, $equipeId): JsonResponse
    {
        // Vérifier que l'équipe existe
        $equipe = Equipe::find($equipeId);

        if (!$equipe) {
            return response()->json([
                'success' => false,
                'message' => 'Équipe non trouvée'
            ], 404);
        }

        // Requête de base : performances des employés actifs de cette équipe
        $query = Performance::whereHas('employe', function ($q) use ($equipeId) {
            $q->where('equipe_id', $equipeId)
              ->where('statut', 'actif');
        });

        // Filtre par mois si spécifié
        if ($request->filled('mois')) {
            $query->where('mois', $request->mois);
            $mois = $request->mois;
        } else {
            $mois = null;
        }

        $performances = $query->get();
        $moyenne = $performances->avg('note');

        return response()->json([
            'success' => true,
            'data' => [
                'equipe' => $equipe,
                'mois' => $mois,
                'moyenne' => $moyenne ? round($moyenne, 1) : null,
                'total_evaluations' => $performances->count()
            ]
        ]);
    }

    /**
     * Moyenne générale du mois
     *
     * Calcule la moyenne de toutes les évaluations pour un mois donné
     * Si aucun mois spécifié, utilise le mois en cours
     */
    public function moyenneMois(Request $request): JsonResponse
    {
        // Mois à analyser (mois en cours par défaut)
        $mois = $request->input('mois', Carbon::now()->format('Y-m'));

        // Récupérer toutes les évaluations de ce mois
        $performances = Performance::where('mois', $mois)->get();

        // Calculer la moyenne
        $moyenne = $performances->avg('note');

        // Répartition des notes (nombre d'évaluations par note)
        $repartition = [
            '1' => $performances->where('note', 1)->count(),
            '2' => $performances->where('note', 2)->count(),
            '3' => $performances->where('note', 3)->count(),
            '4' => $performances->where('note', 4)->count(),
            '5' => $performances->where('note', 5)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'mois' => $mois,
                'moyenne' => $moyenne ? round($moyenne, 1) : null,
                'total_evaluations' => $performances->count(),
                'repartition' => $repartition
            ]
        ]);
    }

    /**
     * Moyennes par équipe pour un mois donné
     *
     * Retourne la moyenne de chaque équipe pour faciliter la comparaison
     */
    public function moyennesParEquipe(Request $request): JsonResponse
    {
        // Mois à analyser (mois en cours par défaut)
        $mois = $request->input('mois', Carbon::now()->format('Y-m'));

        // Récupérer toutes les équipes actives
        $equipes = Equipe::where('statut', 'active')->get();

        $moyennes = [];

        foreach ($equipes as $equipe) {
            // Performances des employés actifs de cette équipe pour ce mois
            $performances = Performance::where('mois', $mois)
                                       ->whereHas('employe', function ($q) use ($equipe) {
                                           $q->where('equipe_id', $equipe->id)
                                             ->where('statut', 'actif');
                                       })
                                       ->get();

            $moyenne = $performances->avg('note');

            $moyennes[] = [
                'equipe_id' => $equipe->id,
                'equipe_nom' => $equipe->nom,
                'moyenne' => $moyenne ? round($moyenne, 1) : null,
                'total_evaluations' => $performances->count()
            ];
        }

        // Trier par moyenne décroissante
        usort($moyennes, function ($a, $b) {
            return ($b['moyenne'] ?? 0) <=> ($a['moyenne'] ?? 0);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'mois' => $mois,
                'moyennes_par_equipe' => $moyennes
            ]
        ]);
    }
}
