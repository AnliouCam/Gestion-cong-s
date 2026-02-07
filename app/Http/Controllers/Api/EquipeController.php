<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipe;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EquipeController extends Controller
{
    /**
     * Afficher la liste de toutes les équipes
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Créer la requête de base
        $query = Equipe::query();

        // Filtre par statut (si fourni)
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Recherche par nom (si fournie)
        if ($request->filled('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        // Récupérer les équipes triées par date (plus récent en premier)
        $equipes = $query->orderBy('created_at', 'desc')->get();

        // Ajouter le compteur d'employés à chaque équipe
        $equipes->each(function($equipe) {
            $equipe->employes_count = $equipe->employes()->count();
        });

        return response()->json([
            'success' => true,
            'data' => $equipes
        ]);
    }

    /**
     * Créer une nouvelle équipe
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validation des données
        $validated = $request->validate([
            'nom' => 'required|string|min:3|max:100',
            'description' => 'nullable|string|max:500',
            'statut' => 'nullable|in:active,inactive,dissoute'
        ], [
            'nom.required' => 'Le nom de l\'équipe est obligatoire',
            'nom.min' => 'Le nom doit contenir au moins 3 caractères',
            'nom.max' => 'Le nom ne peut pas dépasser 100 caractères',
            'description.max' => 'La description ne peut pas dépasser 500 caractères',
            'statut.in' => 'Le statut doit être: active, inactive ou dissoute'
        ]);

        // Créer l'équipe
        $equipe = Equipe::create([
            'nom' => $validated['nom'],
            'description' => $validated['description'] ?? null,
            'statut' => $validated['statut'] ?? 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Équipe créée avec succès',
            'data' => $equipe
        ], 201);
    }

    /**
     * Afficher les détails d'une équipe
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        // Récupérer l'équipe avec ses employés et le chef d'équipe
        $equipe = Equipe::with(['employes', 'chefEquipe'])
            ->withCount('employes')
            ->find($id);

        if (!$equipe) {
            return response()->json([
                'success' => false,
                'message' => 'Équipe non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $equipe
        ]);
    }

    /**
     * Modifier une équipe existante
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        // Trouver l'équipe
        $equipe = Equipe::find($id);

        if (!$equipe) {
            return response()->json([
                'success' => false,
                'message' => 'Équipe non trouvée'
            ], 404);
        }

        // Validation des données
        $validated = $request->validate([
            'nom' => 'required|string|min:3|max:100',
            'description' => 'nullable|string|max:500',
            'statut' => 'nullable|in:active,inactive,dissoute'
        ], [
            'nom.required' => 'Le nom de l\'équipe est obligatoire',
            'nom.min' => 'Le nom doit contenir au moins 3 caractères',
            'nom.max' => 'Le nom ne peut pas dépasser 100 caractères',
            'description.max' => 'La description ne peut pas dépasser 500 caractères',
            'statut.in' => 'Le statut doit être: active, inactive ou dissoute'
        ]);

        // Mettre à jour l'équipe
        $equipe->update([
            'nom' => $validated['nom'],
            'description' => $validated['description'] ?? $equipe->description,
            'statut' => $validated['statut'] ?? $equipe->statut
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Équipe modifiée avec succès',
            'data' => $equipe
        ]);
    }

    /**
     * Supprimer une équipe (soft delete)
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        // Trouver l'équipe
        $equipe = Equipe::find($id);

        if (!$equipe) {
            return response()->json([
                'success' => false,
                'message' => 'Équipe non trouvée'
            ], 404);
        }

        // Vérifier s'il y a des employés dans cette équipe
        $nombreEmployes = $equipe->employes()->count();

        if ($nombreEmployes > 0) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer cette équipe car elle contient {$nombreEmployes} employé(s). Veuillez d'abord réaffecter les employés."
            ], 400);
        }

        // Supprimer l'équipe (soft delete)
        $equipe->delete();

        return response()->json([
            'success' => true,
            'message' => 'Équipe supprimée avec succès'
        ]);
    }
}
