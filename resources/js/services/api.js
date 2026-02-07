/**
 * Service API
 * Configure Axios pour communiquer avec le backend Laravel
 *
 * Fonctionnalités :
 * - URL de base automatique (/api)
 * - Ajout automatique du token Sanctum dans les headers
 * - Gestion des erreurs 401 (redirection login)
 * - Gestion des erreurs réseau
 */

import axios from 'axios';

// Création d'une instance Axios personnalisée
const api = axios.create({
    baseURL: '/api', // Toutes les requêtes seront préfixées par /api
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

/**
 * INTERCEPTEUR DE REQUÊTE
 * S'exécute AVANT chaque requête
 *
 * Permet d'ajouter automatiquement le token si disponible
 */
api.interceptors.request.use(
    (config) => {
        // Récupération du token depuis localStorage
        const token = localStorage.getItem('token');

        // Si token existe, on l'ajoute au header Authorization
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

/**
 * INTERCEPTEUR DE RÉPONSE
 * S'exécute APRÈS chaque réponse
 *
 * Gestion des erreurs :
 * - 401 Unauthorized → redirection vers login
 * - Autres erreurs → affichage console
 */
api.interceptors.response.use(
    (response) => {
        // Si la réponse est OK, on la retourne telle quelle
        return response;
    },
    (error) => {
        // Gestion erreur 401 (token invalide ou expiré)
        if (error.response && error.response.status === 401) {
            // Nettoyage du localStorage
            localStorage.removeItem('token');
            localStorage.removeItem('user');

            // Redirection vers login
            window.location.href = '/login';
        }

        // Propagation de l'erreur pour gestion dans les composants
        return Promise.reject(error);
    }
);

export default api;
