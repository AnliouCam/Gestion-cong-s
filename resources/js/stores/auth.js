/**
 * Auth Store (Pinia)
 * Gère l'état global de l'authentification
 *
 * Responsabilités :
 * - Stocker le token Sanctum
 * - Stocker les informations de l'utilisateur connecté
 * - Fournir les méthodes login() et logout()
 * - Vérifier si l'utilisateur est connecté
 */

import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '../services/api';

export const useAuthStore = defineStore('auth', () => {
    /**
     * STATE (données réactives)
     */

    // Token Sanctum (stocké aussi dans localStorage)
    const token = ref(localStorage.getItem('token') || null);

    // Informations utilisateur connecté
    const user = ref(JSON.parse(localStorage.getItem('user')) || null);

    /**
     * GETTERS (propriétés calculées)
     */

    // Vérifie si l'utilisateur est authentifié
    const isAuthenticated = computed(() => !!token.value);

    /**
     * ACTIONS (méthodes)
     */

    /**
     * LOGIN
     * Authentifie l'utilisateur et stocke le token
     *
     * @param {Object} credentials - { email, password }
     * @returns {Promise}
     */
    async function login(credentials) {
        try {
            // Appel API login
            const response = await api.post('/login', credentials);

            // Extraction des données
            const { token: authToken, user: userData } = response.data;

            // Stockage du token et utilisateur
            token.value = authToken;
            user.value = userData;

            // Persistance dans localStorage (pour garder la session)
            localStorage.setItem('token', authToken);
            localStorage.setItem('user', JSON.stringify(userData));

            // Configuration du token dans axios pour les prochaines requêtes
            api.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;

            return response.data;
        } catch (error) {
            // Propagation de l'erreur pour affichage dans le composant
            throw error;
        }
    }

    /**
     * LOGOUT
     * Déconnecte l'utilisateur et supprime le token
     *
     * @returns {Promise}
     */
    async function logout() {
        try {
            // Appel API logout (supprime le token côté serveur)
            await api.post('/logout');
        } catch (error) {
            console.error('Erreur lors du logout:', error);
        } finally {
            // Nettoyage local (même si l'API échoue)
            token.value = null;
            user.value = null;
            localStorage.removeItem('token');
            localStorage.removeItem('user');

            // Suppression du header Authorization
            delete api.defaults.headers.common['Authorization'];
        }
    }

    /**
     * INIT
     * Initialise le token dans axios si présent au chargement
     */
    function initAuth() {
        if (token.value) {
            api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
        }
    }

    // Initialisation au chargement du store
    initAuth();

    /**
     * EXPORT
     */
    return {
        // State
        token,
        user,

        // Getters
        isAuthenticated,

        // Actions
        login,
        logout,
    };
});
