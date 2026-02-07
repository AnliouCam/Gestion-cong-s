<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employe;
use App\Models\Equipe;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeController extends Controller
{
    /**
     * Afficher la liste de tous les employés
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Créer la requête de base avec la relation equipe
        $query = Employe::with('equipe');

        // Filtre par équipe (si fourni)
        if ($request->filled('equipe_id')) {
            $query->where('equipe_id', $request->equipe_id);
        }

        // Filtre par statut (si fourni)
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Recherche par nom ou prénom (si fournie)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', '%' . $search . '%')
                  ->orWhere('prenom', 'like', '%' . $search . '%')
                  ->orWhere('poste', 'like', '%' . $search . '%');
            });
        }

        // Récupérer les employés triés par nom
        $employes = $query->orderBy('nom', 'asc')->orderBy('prenom', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $employes
        ]);
    }

    /**
     * Créer un nouveau employé
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validation des données
        $validated = $request->validate([
            'nom' => 'required|string|min:2|max:100',
            'prenom' => 'required|string|min:2|max:100',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'poste' => 'required|string|min:2|max:100',
            'salaire_mensuel' => 'required|numeric|min:0',
            'date_embauche' => 'required|date',
            'date_sortie' => 'nullable|date|after:date_embauche',
            'statut' => 'nullable|in:actif,suspendu,sorti',
            'solde_conges_annuel' => 'nullable|integer|min:0|max:365',
            'equipe_id' => 'required|exists:equipes,id'
        ], [
            'nom.required' => 'Le nom est obligatoire',
            'nom.min' => 'Le nom doit contenir au moins 2 caractères',
            'prenom.required' => 'Le prénom est obligatoire',
            'prenom.min' => 'Le prénom doit contenir au moins 2 caractères',
            'email.email' => 'L\'email doit être valide',
            'poste.required' => 'Le poste est obligatoire',
            'salaire_mensuel.required' => 'Le salaire mensuel est obligatoire',
            'salaire_mensuel.min' => 'Le salaire doit être positif',
            'date_embauche.required' => 'La date d\'embauche est obligatoire',
            'date_sortie.after' => 'La date de sortie doit être après la date d\'embauche',
            'equipe_id.required' => 'L\'équipe est obligatoire',
            'equipe_id.exists' => 'L\'équipe sélectionnée n\'existe pas'
        ]);

        // Créer l'employé
        $employe = Employe::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'] ?? null,
            'telephone' => $validated['telephone'] ?? null,
            'poste' => $validated['poste'],
            'salaire_mensuel' => $validated['salaire_mensuel'],
            'date_embauche' => $validated['date_embauche'],
            'date_sortie' => $validated['date_sortie'] ?? null,
            'statut' => $validated['statut'] ?? 'actif',
            'solde_conges_annuel' => $validated['solde_conges_annuel'] ?? 30,
            'equipe_id' => $validated['equipe_id']
        ]);

        // Charger la relation equipe
        $employe->load('equipe');

        return response()->json([
            'success' => true,
            'message' => 'Employé créé avec succès',
            'data' => $employe
        ], 201);
    }

    /**
     * Afficher les détails d'un employé
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        // Récupérer l'employé avec toutes ses relations
        $employe = Employe::with(['equipe', 'conges', 'paies', 'performances'])
            ->find($id);

        if (!$employe) {
            return response()->json([
                'success' => false,
                'message' => 'Employé non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $employe
        ]);
    }

    /**
     * Modifier un employé existant
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        // Trouver l'employé
        $employe = Employe::find($id);

        if (!$employe) {
            return response()->json([
                'success' => false,
                'message' => 'Employé non trouvé'
            ], 404);
        }

        // Validation des données
        $validated = $request->validate([
            'nom' => 'required|string|min:2|max:100',
            'prenom' => 'required|string|min:2|max:100',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'poste' => 'required|string|min:2|max:100',
            'salaire_mensuel' => 'required|numeric|min:0',
            'date_embauche' => 'required|date',
            'date_sortie' => 'nullable|date|after:date_embauche',
            'statut' => 'nullable|in:actif,suspendu,sorti',
            'solde_conges_annuel' => 'nullable|integer|min:0|max:365',
            'equipe_id' => 'required|exists:equipes,id'
        ], [
            'nom.required' => 'Le nom est obligatoire',
            'nom.min' => 'Le nom doit contenir au moins 2 caractères',
            'prenom.required' => 'Le prénom est obligatoire',
            'prenom.min' => 'Le prénom doit contenir au moins 2 caractères',
            'email.email' => 'L\'email doit être valide',
            'poste.required' => 'Le poste est obligatoire',
            'salaire_mensuel.required' => 'Le salaire mensuel est obligatoire',
            'salaire_mensuel.min' => 'Le salaire doit être positif',
            'date_embauche.required' => 'La date d\'embauche est obligatoire',
            'date_sortie.after' => 'La date de sortie doit être après la date d\'embauche',
            'equipe_id.required' => 'L\'équipe est obligatoire',
            'equipe_id.exists' => 'L\'équipe sélectionnée n\'existe pas'
        ]);

        // Mettre à jour l'employé
        $employe->update([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'] ?? $employe->email,
            'telephone' => $validated['telephone'] ?? $employe->telephone,
            'poste' => $validated['poste'],
            'salaire_mensuel' => $validated['salaire_mensuel'],
            'date_embauche' => $validated['date_embauche'],
            'date_sortie' => $validated['date_sortie'] ?? $employe->date_sortie,
            'statut' => $validated['statut'] ?? $employe->statut,
            'solde_conges_annuel' => $validated['solde_conges_annuel'] ?? $employe->solde_conges_annuel,
            'equipe_id' => $validated['equipe_id']
        ]);

        // Charger la relation equipe
        $employe->load('equipe');

        return response()->json([
            'success' => true,
            'message' => 'Employé modifié avec succès',
            'data' => $employe
        ]);
    }

    /**
     * Supprimer un employé (soft delete)
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        // Trouver l'employé
        $employe = Employe::find($id);

        if (!$employe) {
            return response()->json([
                'success' => false,
                'message' => 'Employé non trouvé'
            ], 404);
        }

        // Supprimer l'employé (soft delete)
        $employe->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employé supprimé avec succès'
        ]);
    }

    /**
     * Récupérer l'historique complet d'un employé
     * (congés, paies, performances)
     *
     * @param int $id
     * @return JsonResponse
     */
    public function historique(int $id): JsonResponse
    {
        // Trouver l'employé
        $employe = Employe::with([
            'conges' => function($query) {
                $query->orderBy('date_debut', 'desc');
            },
            'paies' => function($query) {
                $query->orderBy('mois', 'desc');
            },
            'performances' => function($query) {
                $query->orderBy('mois', 'desc');
            }
        ])->find($id);

        if (!$employe) {
            return response()->json([
                'success' => false,
                'message' => 'Employé non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'employe' => $employe->only(['id', 'nom', 'prenom', 'poste']),
                'conges' => $employe->conges,
                'paies' => $employe->paies,
                'performances' => $employe->performances
            ]
        ]);
    }
}
