/**
 * Service API pour la gestion des congés
 * Gère les requêtes HTTP vers l'API Laravel pour les congés
 */

import api from './api';

const congeService = {
    /**
     * Récupérer la liste des congés avec filtres
     * @param {Object} filters - Filtres optionnels (statut, employe_id, type, search)
     * @returns {Promise}
     */
    getAll(filters = {}) {
        return api.get('/conges', { params: filters });
    },

    /**
     * Récupérer un congé par ID
     * @param {number} id - ID du congé
     * @returns {Promise}
     */
    getById(id) {
        return api.get(`/conges/${id}`);
    },

    /**
     * Créer une nouvelle demande de congé
     * @param {Object} data - Données du congé (employe_id, type, date_debut, date_fin, commentaire)
     * @returns {Promise}
     */
    create(data) {
        return api.post('/conges', data);
    },

    /**
     * Valider une demande de congé
     * @param {number} id - ID du congé
     * @returns {Promise}
     */
    valider(id) {
        return api.post(`/conges/${id}/valider`);
    },

    /**
     * Refuser une demande de congé
     * @param {number} id - ID du congé
     * @param {Object} data - Données optionnelles (commentaire)
     * @returns {Promise}
     */
    refuser(id, data = {}) {
        return api.post(`/conges/${id}/refuser`, data);
    }
};

export default congeService;
