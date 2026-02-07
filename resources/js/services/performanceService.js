/**
 * Service API pour la gestion des performances
 * Gère les requêtes HTTP vers l'API Laravel pour les évaluations des employés
 */

import api from './api';

const performanceService = {
    /**
     * Récupérer la liste des évaluations avec filtres
     * @param {Object} filters - Filtres optionnels (mois, employe_id, equipe_id, note, search)
     * @returns {Promise}
     */
    getAll(filters = {}) {
        return api.get('/performances', { params: filters });
    },

    /**
     * Récupérer une évaluation par ID
     * @param {number} id - ID de l'évaluation
     * @returns {Promise}
     */
    getById(id) {
        return api.get(`/performances/${id}`);
    },

    /**
     * Créer une nouvelle évaluation
     * @param {Object} data - Données de l'évaluation (employe_id, mois, note, commentaire)
     * @returns {Promise}
     */
    create(data) {
        return api.post('/performances', data);
    },

    /**
     * Modifier une évaluation existante
     * @param {number} id - ID de l'évaluation
     * @param {Object} data - Nouvelles données (note, commentaire)
     * @returns {Promise}
     */
    update(id, data) {
        return api.put(`/performances/${id}`, data);
    },

    /**
     * Supprimer une évaluation
     * @param {number} id - ID de l'évaluation
     * @returns {Promise}
     */
    delete(id) {
        return api.delete(`/performances/${id}`);
    },

    /**
     * Récupérer l'historique des évaluations d'un employé
     * @param {number} employeId - ID de l'employé
     * @returns {Promise}
     */
    getHistoriqueEmploye(employeId) {
        return api.get(`/performances/employe/${employeId}/historique`);
    },

    /**
     * Récupérer la moyenne d'une équipe
     * @param {number} equipeId - ID de l'équipe
     * @param {string} mois - Mois au format YYYY-MM (optionnel)
     * @returns {Promise}
     */
    getMoyenneEquipe(equipeId, mois = null) {
        const params = mois ? { mois } : {};
        return api.get(`/performances/equipe/${equipeId}/moyenne`, { params });
    },

    /**
     * Récupérer la moyenne générale du mois
     * @param {string} mois - Mois au format YYYY-MM (optionnel, par défaut mois en cours)
     * @returns {Promise}
     */
    getMoyenneMois(mois = null) {
        const params = mois ? { mois } : {};
        return api.get('/performances/mois/moyenne', { params });
    },

    /**
     * Récupérer les moyennes par équipe pour un mois
     * @param {string} mois - Mois au format YYYY-MM (optionnel, par défaut mois en cours)
     * @returns {Promise}
     */
    getMoyennesParEquipe(mois = null) {
        const params = mois ? { mois } : {};
        return api.get('/performances/mois/moyennes-par-equipe', { params });
    }
};

export default performanceService;
