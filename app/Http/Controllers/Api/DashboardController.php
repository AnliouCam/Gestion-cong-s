<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employe;
use App\Models\Equipe;
use App\Models\Conge;
use App\Models\Paie;
use App\Models\Performance;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

/**
 * DashboardController
 * Fournit toutes les statistiques pour le tableau de bord de la direction
 *
 * Fonctionnalités :
 * - Statistiques employés (actifs, suspendus, sortis)
 * - Statistiques équipes (actives, plus grande équipe)
 * - Statistiques congés (en attente, en cours, validés ce mois)
 * - Statistiques paies (payés/non payés ce mois, montant total)
 * - Statistiques performances (évaluations ce mois, moyenne)
 * - Alertes et actions requises
 */
class DashboardController extends Controller
{
    /**
     * Récupérer toutes les statistiques du dashboard
     *
     * Retourne un objet JSON avec :
     * - employes : statistiques sur les employés
     * - equipes : statistiques sur les équipes
     * - conges : statistiques sur les congés
     * - paies : statistiques sur les paies du mois en cours
     * - performances : statistiques sur les évaluations du mois en cours
     * - alertes : liste des alertes et actions requises
     */
    public function index(): JsonResponse
    {
        // Date du jour et mois en cours
        $aujourdhui = Carbon::now();
        $moisEnCours = $aujourdhui->format('Y-m');

        // Statistiques Employés
        $statsEmployes = $this->getStatsEmployes();

        // Statistiques Équipes
        $statsEquipes = $this->getStatsEquipes();

        // Statistiques Congés
        $statsConges = $this->getStatsConges($aujourdhui, $moisEnCours);

        // Statistiques Paies
        $statsPaies = $this->getStatsPaies($moisEnCours);

        // Statistiques Performances
        $statsPerformances = $this->getStatsPerformances($moisEnCours);

        // Génération des alertes
        $alertes = $this->getAlertes($statsConges, $statsPaies, $statsEmployes);

        return response()->json([
            'success' => true,
            'data' => [
                'employes' => $statsEmployes,
                'equipes' => $statsEquipes,
                'conges' => $statsConges,
                'paies' => $statsPaies,
                'performances' => $statsPerformances,
                'alertes' => $alertes,
                'mois_en_cours' => $moisEnCours,
                'date_aujourdhui' => $aujourdhui->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Statistiques Employés
     *
     * Retourne :
     * - total_actifs : nombre d'employés avec statut = 'actif'
     * - total_suspendus : nombre d'employés avec statut = 'suspendu'
     * - total_sortis : nombre d'employés avec statut = 'sorti'
     * - total : nombre total d'employés (tous statuts)
     */
    private function getStatsEmployes(): array
    {
        return [
            'total_actifs' => Employe::where('statut', 'actif')->count(),
            'total_suspendus' => Employe::where('statut', 'suspendu')->count(),
            'total_sortis' => Employe::where('statut', 'sorti')->count(),
            'total' => Employe::count()
        ];
    }

    /**
     * Statistiques Équipes
     *
     * Retourne :
     * - total_actives : nombre d'équipes avec statut = 'active'
     * - total : nombre total d'équipes
     * - equipe_plus_grande : l'équipe avec le plus d'employés actifs
     */
    private function getStatsEquipes(): array
    {
        $totalActives = Equipe::where('statut', 'active')->count();
        $total = Equipe::count();

        // Trouver l'équipe avec le plus d'employés actifs
        $equipePlusGrande = Equipe::where('statut', 'active')
            ->withCount(['employes' => function ($query) {
                $query->where('statut', 'actif');
            }])
            ->orderBy('employes_count', 'desc')
            ->first();

        return [
            'total_actives' => $totalActives,
            'total' => $total,
            'equipe_plus_grande' => $equipePlusGrande ? [
                'id' => $equipePlusGrande->id,
                'nom' => $equipePlusGrande->nom,
                'nb_employes' => $equipePlusGrande->employes_count
            ] : null
        ];
    }

    /**
     * Statistiques Congés
     *
     * Retourne :
     * - en_attente : nombre de demandes avec statut = 'en_attente'
     * - en_cours_aujourdhui : nombre de congés validés dont la période inclut aujourd'hui
     * - valides_ce_mois : nombre de congés validés ce mois
     * - refuses_ce_mois : nombre de congés refusés ce mois
     */
    private function getStatsConges(Carbon $aujourdhui, string $moisEnCours): array
    {
        // Demandes en attente (nécessitent validation)
        $enAttente = Conge::where('statut', 'en_attente')->count();

        // Congés en cours aujourd'hui (employés absents)
        // Un congé est "en cours" si : date_debut <= aujourd'hui <= date_fin ET statut = 'valide'
        $enCoursAujourdhui = Conge::where('statut', 'valide')
            ->whereDate('date_debut', '<=', $aujourdhui)
            ->whereDate('date_fin', '>=', $aujourdhui)
            ->count();

        // Congés validés ce mois (demandes traitées en validé ce mois)
        $validesCeMois = Conge::where('statut', 'valide')
            ->whereYear('date_validation', $aujourdhui->year)
            ->whereMonth('date_validation', $aujourdhui->month)
            ->count();

        // Congés refusés ce mois
        $refusesCeMois = Conge::where('statut', 'refuse')
            ->whereYear('date_validation', $aujourdhui->year)
            ->whereMonth('date_validation', $aujourdhui->month)
            ->count();

        return [
            'en_attente' => $enAttente,
            'en_cours_aujourdhui' => $enCoursAujourdhui,
            'valides_ce_mois' => $validesCeMois,
            'refuses_ce_mois' => $refusesCeMois
        ];
    }

    /**
     * Statistiques Paies
     *
     * Retourne :
     * - total_employes_actifs : nombre d'employés actifs
     * - payes_ce_mois : nombre d'employés actifs payés ce mois
     * - non_payes_ce_mois : nombre d'employés actifs non payés ce mois
     * - montant_total_ce_mois : somme des montants payés ce mois
     * - pourcentage_payes : pourcentage d'employés payés
     */
    private function getStatsPaies(string $moisEnCours): array
    {
        // Nombre d'employés actifs
        $totalEmployesActifs = Employe::where('statut', 'actif')->count();

        // IDs des employés payés ce mois (statut = 'paye' pour ce mois)
        $employesPayesIds = Paie::where('mois', $moisEnCours)
            ->where('statut', 'paye')
            ->pluck('employe_id')
            ->toArray();

        $payesCeMois = count($employesPayesIds);
        $nonPayesCeMois = $totalEmployesActifs - $payesCeMois;

        // Montant total payé ce mois
        $montantTotalCeMois = Paie::where('mois', $moisEnCours)
            ->where('statut', 'paye')
            ->sum('montant_total');

        // Pourcentage d'employés payés
        $pourcentagePayes = $totalEmployesActifs > 0
            ? round(($payesCeMois / $totalEmployesActifs) * 100, 1)
            : 0;

        return [
            'total_employes_actifs' => $totalEmployesActifs,
            'payes_ce_mois' => $payesCeMois,
            'non_payes_ce_mois' => $nonPayesCeMois,
            'montant_total_ce_mois' => (float) $montantTotalCeMois,
            'pourcentage_payes' => $pourcentagePayes
        ];
    }

    /**
     * Statistiques Performances
     *
     * Retourne :
     * - total_evaluations_ce_mois : nombre d'évaluations enregistrées ce mois
     * - moyenne_ce_mois : moyenne des notes ce mois (sur 5)
     * - employes_evalues_ce_mois : nombre d'employés évalués ce mois
     * - employes_non_evalues : nombre d'employés actifs non évalués ce mois
     */
    private function getStatsPerformances(string $moisEnCours): array
    {
        // Total d'évaluations ce mois
        $totalEvaluationsCeMois = Performance::where('mois', $moisEnCours)->count();

        // Moyenne des notes ce mois
        $moyenneCeMois = Performance::where('mois', $moisEnCours)->avg('note');
        $moyenneCeMois = $moyenneCeMois ? round($moyenneCeMois, 1) : 0;

        // Nombre d'employés évalués ce mois
        $employesEvaluesIds = Performance::where('mois', $moisEnCours)
            ->pluck('employe_id')
            ->unique()
            ->toArray();
        $employesEvaluesCeMois = count($employesEvaluesIds);

        // Nombre d'employés actifs non évalués
        $totalEmployesActifs = Employe::where('statut', 'actif')->count();
        $employesNonEvalues = $totalEmployesActifs - $employesEvaluesCeMois;

        return [
            'total_evaluations_ce_mois' => $totalEvaluationsCeMois,
            'moyenne_ce_mois' => (float) $moyenneCeMois,
            'employes_evalues_ce_mois' => $employesEvaluesCeMois,
            'employes_non_evalues' => $employesNonEvalues,
            'total_employes_actifs' => $totalEmployesActifs
        ];
    }

    /**
     * Génération des alertes et actions requises
     *
     * Priorité des alertes :
     * 1. Demandes de congés en attente (action requise)
     * 2. Employés non payés ce mois (action requise)
     * 3. Employés en congé aujourd'hui (info)
     * 4. Employés suspendus (info)
     *
     * Chaque alerte contient :
     * - type : 'danger' (rouge) ou 'warning' (jaune) ou 'info' (bleu)
     * - message : texte descriptif
     * - action : texte du bouton d'action
     * - link : lien vers la page concernée
     */
    private function getAlertes(array $statsConges, array $statsPaies, array $statsEmployes): array
    {
        $alertes = [];

        // Alerte 1 : Demandes de congés en attente
        if ($statsConges['en_attente'] > 0) {
            $alertes[] = [
                'type' => 'danger',
                'icon' => 'calendar',
                'message' => $statsConges['en_attente'] . ' demande(s) de congés en attente de validation',
                'action' => 'Valider les demandes',
                'link' => '/conges?statut=en_attente'
            ];
        }

        // Alerte 2 : Employés non payés ce mois
        if ($statsPaies['non_payes_ce_mois'] > 0) {
            $alertes[] = [
                'type' => 'danger',
                'icon' => 'money',
                'message' => $statsPaies['non_payes_ce_mois'] . ' employé(s) non payé(s) ce mois',
                'action' => 'Enregistrer les paiements',
                'link' => '/paies/create'
            ];
        }

        // Alerte 3 : Employés en congé aujourd'hui
        if ($statsConges['en_cours_aujourdhui'] > 0) {
            $alertes[] = [
                'type' => 'warning',
                'icon' => 'calendar',
                'message' => $statsConges['en_cours_aujourdhui'] . ' employé(s) en congé aujourd\'hui',
                'action' => 'Voir les employés absents',
                'link' => '/conges'
            ];
        }

        // Alerte 4 : Employés suspendus
        if ($statsEmployes['total_suspendus'] > 0) {
            $alertes[] = [
                'type' => 'info',
                'icon' => 'users',
                'message' => $statsEmployes['total_suspendus'] . ' employé(s) suspendu(s)',
                'action' => 'Voir les employés',
                'link' => '/employes?statut=suspendu'
            ];
        }

        return $alertes;
    }
}
