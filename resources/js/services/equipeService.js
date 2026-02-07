import api from './api';

/**
 * Service pour gérer les équipes
 * Communique avec l'API Laravel backend
 */
const equipeService = {
    /**
     * Récupérer la liste de toutes les équipes
     * @param {Object} filters - Filtres optionnels (statut, search)
     * @returns {Promise} Liste des équipes
     */
    getAll(filters = {}) {
        return api.get('/equipes', { params: filters });
    },

    /**
     * Récupérer les détails d'une équipe
     * @param {Number} id - ID de l'équipe
     * @returns {Promise} Détails de l'équipe
     */
    getById(id) {
        return api.get(`/equipes/${id}`);
    },

    /**
     * Créer une nouvelle équipe
     * @param {Object} data - Données de l'équipe (nom, description, statut)
     * @returns {Promise} Équipe créée
     */
    create(data) {
        return api.post('/equipes', data);
    },

    /**
     * Modifier une équipe existante
     * @param {Number} id - ID de l'équipe
     * @param {Object} data - Nouvelles données
     * @returns {Promise} Équipe modifiée
     */
    update(id, data) {
        return api.put(`/equipes/${id}`, data);
    },

    /**
     * Supprimer une équipe
     * @param {Number} id - ID de l'équipe
     * @returns {Promise} Confirmation de suppression
     */
    delete(id) {
        return api.delete(`/equipes/${id}`);
    }
};

export default equipeService;
