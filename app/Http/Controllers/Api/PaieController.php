<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paie;
use App\Models\Employe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * PaieController
 * Gère les enregistrements de paiements mensuels des employés
 *
 * Fonctionnalités :
 * - Enregistrer un nouveau paiement (avec calcul montant_total automatique)
 * - Lister les paiements avec filtres (mois, employé, statut)
 * - Consulter un paiement spécifique
 * - Historique d'un employé
 * - Liste des employés non payés
 *
 * Règles métier :
 * - montant_total = salaire_base + primes - retenues
 * - Un employé = maximum 1 paiement par mois (pas de doublon)
 * - Pas de modification après création (traçabilité)
 * - Si date_paiement renseignée → statut = "paye"
 */
class PaieController extends Controller
{
    /**
     * Liste des paiements avec filtres
     *
     * Filtres disponibles :
     * - mois : YYYY-MM (ex: 2025-01)
     * - employe_id : filtrer par employé
     * - statut : paye, non_paye
     * - search : recherche par nom d'employé
     */
    public function index(Request $request): JsonResponse
    {
        $query = Paie::with(['employe.equipe', 'enregistrePar']);

        // Filtre par mois
        if ($request->filled('mois')) {
            $query->where('mois', $request->mois);
        }

        // Filtre par employé
        if ($request->filled('employe_id')) {
            $query->where('employe_id', $request->employe_id);
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Recherche par nom d'employé
        if ($request->filled('search')) {
            $query->whereHas('employe', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('prenom', 'like', '%' . $request->search . '%');
            });
        }

        $paies = $query->orderBy('mois', 'desc')
                       ->orderBy('created_at', 'desc')
                       ->get();

        return response()->json([
            'success' => true,
            'data' => $paies
        ]);
    }

    /**
     * Enregistrer un nouveau paiement
     *
     * Calcule automatiquement le montant_total
     * Vérifie qu'il n'existe pas déjà un paiement pour cet employé ce mois
     * Détermine le statut selon la présence de date_paiement
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'mois' => 'required|date_format:Y-m',
            'salaire_base' => 'required|numeric|min:0',
            'primes' => 'nullable|numeric|min:0',
            'retenues' => 'nullable|numeric|min:0',
            'mode_paiement' => 'required|in:cash,virement',
            'date_paiement' => 'nullable|date',
            'reference_paiement' => 'nullable|string|max:255',
            'commentaire' => 'nullable|string|max:500'
        ]);

        // Vérifier qu'il n'existe pas déjà un paiement pour cet employé ce mois
        $paiementExistant = Paie::where('employe_id', $validated['employe_id'])
                                ->where('mois', $validated['mois'])
                                ->first();

        if ($paiementExistant) {
            return response()->json([
                'success' => false,
                'message' => 'Un paiement existe déjà pour cet employé pour ce mois',
                'errors' => [
                    'mois' => ['Un paiement a déjà été enregistré pour cet employé pour le mois de ' . $validated['mois']]
                ]
            ], 422);
        }

        // Valeurs par défaut pour primes et retenues
        $primes = $validated['primes'] ?? 0;
        $retenues = $validated['retenues'] ?? 0;

        // Calcul du montant total : salaire_base + primes - retenues
        $montantTotal = $validated['salaire_base'] + $primes - $retenues;

        // Détermination du statut selon la présence de date_paiement
        $statut = isset($validated['date_paiement']) ? 'paye' : 'non_paye';

        // Création du paiement
        $paie = Paie::create([
            'employe_id' => $validated['employe_id'],
            'mois' => $validated['mois'],
            'salaire_base' => $validated['salaire_base'],
            'primes' => $primes,
            'retenues' => $retenues,
            'montant_total' => $montantTotal,
            'mode_paiement' => $validated['mode_paiement'],
            'date_paiement' => $validated['date_paiement'] ?? null,
            'statut' => $statut,
            'reference_paiement' => $validated['reference_paiement'] ?? null,
            'commentaire' => $validated['commentaire'] ?? null,
            'enregistre_par_user_id' => $request->user()->id,
        ]);

        // Charger les relations pour le retour
        $paie->load(['employe.equipe', 'enregistrePar']);

        return response()->json([
            'success' => true,
            'message' => 'Paiement enregistré avec succès',
            'data' => $paie
        ], 201);
    }

    /**
     * Afficher un paiement spécifique
     */
    public function show($id): JsonResponse
    {
        $paie = Paie::with(['employe.equipe', 'enregistrePar'])->find($id);

        if (!$paie) {
            return response()->json([
                'success' => false,
                'message' => 'Paiement non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $paie
        ]);
    }

    /**
     * Historique des paiements d'un employé
     *
     * Retourne tous les paiements d'un employé, triés par mois décroissant
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

        // Récupérer tous les paiements de cet employé
        $paies = Paie::where('employe_id', $employeId)
                     ->with(['enregistrePar'])
                     ->orderBy('mois', 'desc')
                     ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'employe' => $employe,
                'paies' => $paies
            ]
        ]);
    }

    /**
     * Liste des employés non payés
     *
     * Retourne les employés actifs qui n'ont pas été payés pour un mois donné
     * Si aucun mois n'est spécifié, utilise le mois en cours
     */
    public function nonPayes(Request $request): JsonResponse
    {
        // Mois à vérifier (mois en cours par défaut)
        $mois = $request->input('mois', Carbon::now()->format('Y-m'));

        // Récupérer tous les employés actifs
        $employesActifs = Employe::where('statut', 'actif')
                                 ->with('equipe')
                                 ->get();

        // Récupérer les IDs des employés déjà payés ce mois
        $employesPayesIds = Paie::where('mois', $mois)
                                ->where('statut', 'paye')
                                ->pluck('employe_id')
                                ->toArray();

        // Filtrer les employés non payés
        $employesNonPayes = $employesActifs->filter(function ($employe) use ($employesPayesIds) {
            return !in_array($employe->id, $employesPayesIds);
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'mois' => $mois,
                'total_employes_actifs' => $employesActifs->count(),
                'total_employes_payes' => count($employesPayesIds),
                'total_employes_non_payes' => $employesNonPayes->count(),
                'employes_non_payes' => $employesNonPayes
            ]
        ]);
    }

    /**
     * Supprimer un paiement
     *
     * Soft delete pour conserver l'historique
     * Utile en cas d'erreur de saisie
     */
    public function destroy($id): JsonResponse
    {
        $paie = Paie::find($id);

        if (!$paie) {
            return response()->json([
                'success' => false,
                'message' => 'Paiement non trouvé'
            ], 404);
        }

        $paie->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paiement supprimé avec succès'
        ]);
    }
}
