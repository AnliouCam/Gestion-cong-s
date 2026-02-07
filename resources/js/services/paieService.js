/**
 * Service API pour la gestion des paies
 * Gère les requêtes HTTP vers l'API Laravel pour les paiements mensuels
 */

import api from './api';

const paieService = {
    /**
     * Récupérer la liste des paiements avec filtres
     * @param {Object} filters - Filtres optionnels (mois, employe_id, statut, search)
     * @returns {Promise}
     */
    getAll(filters = {}) {
        return api.get('/paies', { params: filters });
    },

    /**
     * Récupérer un paiement par ID
     * @param {number} id - ID du paiement
     * @returns {Promise}
     */
    getById(id) {
        return api.get(`/paies/${id}`);
    },

    /**
     * Enregistrer un nouveau paiement
     * @param {Object} data - Données du paiement (employe_id, mois, salaire_base, primes, retenues, mode_paiement, date_paiement, reference_paiement, commentaire)
     * @returns {Promise}
     */
    create(data) {
        return api.post('/paies', data);
    },

    /**
     * Récupérer l'historique des paiements d'un employé
     * @param {number} employeId - ID de l'employé
     * @returns {Promise}
     */
    getHistoriqueEmploye(employeId) {
        return api.get(`/paies/employe/${employeId}/historique`);
    },

    /**
     * Récupérer la liste des employés non payés
     * @param {string} mois - Mois au format YYYY-MM (optionnel, par défaut mois en cours)
     * @returns {Promise}
     */
    getNonPayes(mois = null) {
        const params = mois ? { mois } : {};
        return api.get('/paies/non-payes/liste', { params });
    },

    /**
     * Supprimer un paiement
     * @param {number} id - ID du paiement
     * @returns {Promise}
     */
    delete(id) {
        return api.delete(`/paies/${id}`);
    }
};

export default paieService;
