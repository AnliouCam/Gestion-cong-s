/**
 * Service API pour le Dashboard
 * Gère la récupération des statistiques du tableau de bord
 */

import api from './api';

const dashboardService = {
    /**
     * Récupérer toutes les statistiques du dashboard
     *
     * Retourne :
     * - employes : stats employés (actifs, suspendus, sortis)
     * - equipes : stats équipes (actives, plus grande équipe)
     * - conges : stats congés (en attente, en cours, validés ce mois)
     * - paies : stats paies (payés/non payés ce mois, montant total)
     * - alertes : liste des alertes et actions requises
     *
     * @returns {Promise}
     */
    getStats() {
        return api.get('/dashboard');
    }
};

export default dashboardService;
