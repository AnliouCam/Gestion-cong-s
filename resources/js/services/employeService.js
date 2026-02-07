import api from './api';

/**
 * Service pour gérer les employés
 * Communique avec l'API Laravel backend
 */
const employeService = {
    /**
     * Récupérer la liste de tous les employés
     * @param {Object} filters - Filtres optionnels (equipe_id, statut, search)
     * @returns {Promise} Liste des employés
     */
    getAll(filters = {}) {
        return api.get('/employes', { params: filters });
    },

    /**
     * Récupérer les détails d'un employé
     * @param {Number} id - ID de l'employé
     * @returns {Promise} Détails de l'employé
     */
    getById(id) {
        return api.get(`/employes/${id}`);
    },

    /**
     * Créer un nouvel employé
     * @param {Object} data - Données de l'employé
     * @returns {Promise} Employé créé
     */
    create(data) {
        return api.post('/employes', data);
    },

    /**
     * Modifier un employé existant
     * @param {Number} id - ID de l'employé
     * @param {Object} data - Nouvelles données
     * @returns {Promise} Employé modifié
     */
    update(id, data) {
        return api.put(`/employes/${id}`, data);
    },

    /**
     * Supprimer un employé
     * @param {Number} id - ID de l'employé
     * @returns {Promise} Confirmation de suppression
     */
    delete(id) {
        return api.delete(`/employes/${id}`);
    },

    /**
     * Récupérer l'historique complet d'un employé
     * (congés, paies, performances)
     * @param {Number} id - ID de l'employé
     * @returns {Promise} Historique complet
     */
    getHistorique(id) {
        return api.get(`/employes/${id}/historique`);
    }
};

export default employeService;
