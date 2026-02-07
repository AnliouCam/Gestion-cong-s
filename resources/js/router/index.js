/**
 * Vue Router
 * Gère la navigation entre les pages de l'application
 *
 * Routes :
 * - /login : Page de connexion (publique)
 * - /dashboard : Tableau de bord (protégée)
 */

import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

// Import des composants (pages)
import Login from '../components/Login.vue';
import Dashboard from '../components/Dashboard.vue';

// Pages Équipes
import EquipesIndex from '../components/Equipes/Index.vue';
import EquipesCreate from '../components/Equipes/Create.vue';
import EquipesEdit from '../components/Equipes/Edit.vue';

// Pages Employés
import EmployesIndex from '../components/Employes/Index.vue';
import EmployesCreate from '../components/Employes/Create.vue';
import EmployesEdit from '../components/Employes/Edit.vue';
import EmployesShow from '../components/Employes/Show.vue';

// Pages Congés
import CongesIndex from '../components/Conges/Index.vue';
import CongesCreate from '../components/Conges/Create.vue';
import CongesShow from '../components/Conges/Show.vue';

// Pages Paies
import PaiesIndex from '../components/Paies/Index.vue';
import PaiesCreate from '../components/Paies/Create.vue';

// Pages Performances
import PerformancesIndex from '../components/Performances/Index.vue';
import PerformancesCreate from '../components/Performances/Create.vue';

// Définition des routes
const routes = [
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: { requiresAuth: false }, // Accessible sans être connecté
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }, // Nécessite d'être connecté
    },
    // Routes pour la gestion des équipes
    {
        path: '/equipes',
        name: 'equipes',
        component: EquipesIndex,
        meta: { requiresAuth: true },
    },
    {
        path: '/equipes/create',
        name: 'equipes-create',
        component: EquipesCreate,
        meta: { requiresAuth: true },
    },
    {
        path: '/equipes/:id/edit',
        name: 'equipes-edit',
        component: EquipesEdit,
        meta: { requiresAuth: true },
    },
    // Routes pour la gestion des employés
    {
        path: '/employes',
        name: 'employes',
        component: EmployesIndex,
        meta: { requiresAuth: true },
    },
    {
        path: '/employes/create',
        name: 'employes-create',
        component: EmployesCreate,
        meta: { requiresAuth: true },
    },
    {
        path: '/employes/:id',
        name: 'employes-show',
        component: EmployesShow,
        meta: { requiresAuth: true },
    },
    {
        path: '/employes/:id/edit',
        name: 'employes-edit',
        component: EmployesEdit,
        meta: { requiresAuth: true },
    },
    // Routes pour la gestion des congés
    {
        path: '/conges',
        name: 'conges',
        component: CongesIndex,
        meta: { requiresAuth: true },
    },
    {
        path: '/conges/create',
        name: 'conges-create',
        component: CongesCreate,
        meta: { requiresAuth: true },
    },
    {
        path: '/conges/:id',
        name: 'conges-show',
        component: CongesShow,
        meta: { requiresAuth: true },
    },
    // Routes pour la gestion des paies
    {
        path: '/paies',
        name: 'paies',
        component: PaiesIndex,
        meta: { requiresAuth: true },
    },
    {
        path: '/paies/create',
        name: 'paies-create',
        component: PaiesCreate,
        meta: { requiresAuth: true },
    },
    // Routes pour la gestion des performances
    {
        path: '/performances',
        name: 'performances',
        component: PerformancesIndex,
        meta: { requiresAuth: true },
    },
    {
        path: '/performances/create',
        name: 'performances-create',
        component: PerformancesCreate,
        meta: { requiresAuth: true },
    },
    {
        path: '/',
        redirect: '/dashboard', // Redirection racine → dashboard
    },
];

// Création du router
const router = createRouter({
    history: createWebHistory(),
    routes,
});

/**
 * Navigation Guard (Garde de sécurité)
 * S'exécute AVANT chaque changement de page
 *
 * Logique :
 * - Si la page nécessite auth ET utilisateur non connecté → redirection /login
 * - Si utilisateur connecté va sur /login → redirection /dashboard
 */
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    const isAuthenticated = authStore.isAuthenticated;

    // Si la route nécessite l'authentification
    if (to.meta.requiresAuth && !isAuthenticated) {
        // Redirection vers login
        next({ name: 'Login' });
    }
    // Si l'utilisateur connecté essaie d'aller sur login
    else if (to.name === 'Login' && isAuthenticated) {
        // Redirection vers dashboard
        next({ name: 'Dashboard' });
    }
    // Sinon, laisser passer
    else {
        next();
    }
});

export default router;
