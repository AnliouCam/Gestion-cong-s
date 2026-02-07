<!--
  Dashboard.vue
  Page principale après connexion avec statistiques réelles

  Fonctionnalités :
  - Affichage statistiques temps réel (employés, équipes, congés, paies, performances)
  - Navigation avec menu latéral (responsive mobile avec hamburger)
  - Bouton de déconnexion
  - Alertes et actions requises
  - Raccourcis rapides
  - Sidebar responsive : cachée sur mobile, visible sur desktop
-->

<template>
  <div class="flex h-screen bg-gray-100">
    <!-- BACKDROP (overlay sombre sur mobile quand sidebar ouverte) -->
    <div
      v-if="sidebarOpen"
      @click="closeSidebar"
      class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
    ></div>

    <!-- SIDEBAR (Menu latéral) -->
    <aside
      :class="[
        'fixed md:static inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
      ]"
    >
      <!-- Header Sidebar -->
      <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-xl font-bold text-gray-900">Gest' Congés</h1>
            <p class="text-sm text-gray-600 mt-1">Gestion Employés</p>
          </div>
          <!-- Bouton fermer (visible uniquement sur mobile) -->
          <button
            @click="closeSidebar"
            class="md:hidden text-gray-500 hover:text-gray-700"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="p-4">
        <ul class="space-y-2">
          <!-- Dashboard -->
          <li>
            <router-link to="/dashboard" class="flex items-center px-4 py-3 text-gray-700 bg-blue-50 rounded-lg font-medium">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
              </svg>
              Tableau de bord
            </router-link>
          </li>

          <!-- Équipes -->
          <li>
            <router-link to="/equipes" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              Équipes
            </router-link>
          </li>

          <!-- Employés -->
          <li>
            <router-link to="/employes" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
              Employés
            </router-link>
          </li>

          <!-- Congés -->
          <li>
            <router-link to="/conges" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              Congés
            </router-link>
          </li>

          <!-- Paies -->
          <li>
            <router-link to="/paies" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Paies
            </router-link>
          </li>

          <!-- Performances -->
          <li>
            <router-link to="/performances" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
              Performances
            </router-link>
          </li>
        </ul>
      </nav>

      <!-- Déconnexion (en bas du sidebar) -->
      <div class="absolute bottom-0 w-64 p-4 border-t border-gray-200 bg-white">
        <button
          @click="handleLogout"
          class="flex items-center w-full px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition font-medium"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          Déconnexion
        </button>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 overflow-y-auto">
      <!-- Header -->
      <header class="bg-white shadow-sm">
        <div class="px-4 md:px-8 py-4 md:py-6 flex items-center">
          <!-- Bouton hamburger (visible uniquement sur mobile) -->
          <button
            @click="toggleSidebar"
            class="mr-4 md:hidden text-gray-600 hover:text-gray-900 focus:outline-none"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>

          <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-900">Tableau de bord</h2>
            <p class="text-sm md:text-base text-gray-600 mt-1">Bienvenue, {{ authStore.user?.name }}</p>
          </div>
        </div>
      </header>

      <!-- Content -->
      <div class="p-4 md:p-8">
        <!-- Chargement -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>

        <div v-else>
          <!-- Statistiques principales (5 cartes) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 md:gap-6 mb-6">
            <!-- Card 1 : Employés -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600">Employés actifs</p>
                  <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.employes?.total_actifs || 0 }}</p>
                  <div class="flex items-center mt-2 space-x-2 text-xs">
                    <span class="text-gray-500">Suspendus: {{ stats.employes?.total_suspendus || 0 }}</span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-500">Sortis: {{ stats.employes?.total_sortis || 0 }}</span>
                  </div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Card 2 : Équipes -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600">Équipes actives</p>
                  <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.equipes?.total_actives || 0 }}</p>
                  <p v-if="stats.equipes?.equipe_plus_grande" class="text-xs text-gray-500 mt-2">
                    Plus grande : {{ stats.equipes.equipe_plus_grande.nom }} ({{ stats.equipes.equipe_plus_grande.nb_employes }})
                  </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Card 3 : Congés -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600">Congés</p>
                  <p class="text-3xl font-bold text-gray-900 mt-2">
                    <span v-if="stats.conges?.en_attente > 0" class="text-red-600">{{ stats.conges.en_attente }}</span>
                    <span v-else class="text-green-600">0</span>
                  </p>
                  <div class="flex items-center mt-2 space-x-2 text-xs">
                    <span class="text-red-600 font-semibold">En attente: {{ stats.conges?.en_attente || 0 }}</span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-500">En cours: {{ stats.conges?.en_cours_aujourdhui || 0 }}</span>
                  </div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Card 4 : Paies -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600">Paies ce mois</p>
                  <p class="text-3xl font-bold text-gray-900 mt-2">
                    <span v-if="stats.paies?.non_payes_ce_mois > 0" class="text-red-600">{{ stats.paies.payes_ce_mois }}</span>
                    <span v-else class="text-green-600">{{ stats.paies?.payes_ce_mois || 0 }}</span>
                    <span class="text-lg text-gray-500"> / {{ stats.paies?.total_employes_actifs || 0 }}</span>
                  </p>
                  <p class="text-xs text-gray-500 mt-2">
                    Total : {{ formatMontant(stats.paies?.montant_total_ce_mois || 0) }}
                  </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Card 5 : Performances -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600">Performances</p>
                  <div class="flex items-center mt-2">
                    <p class="text-3xl font-bold text-gray-900">
                      <span v-if="stats.performances?.moyenne_ce_mois > 0">{{ stats.performances.moyenne_ce_mois }}</span>
                      <span v-else class="text-gray-400">-</span>
                    </p>
                    <span v-if="stats.performances?.moyenne_ce_mois > 0" class="text-yellow-400 text-2xl ml-2">★</span>
                  </div>
                  <div class="flex items-center mt-2 space-x-2 text-xs">
                    <span class="text-gray-500">Évaluations: {{ stats.performances?.total_evaluations_ce_mois || 0 }}</span>
                  </div>
                  <p v-if="stats.performances?.employes_non_evalues > 0" class="text-xs text-red-600 mt-1">
                    {{ stats.performances.employes_non_evalues }} non évalué(s)
                  </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Alertes et actions requises -->
          <div v-if="stats.alertes && stats.alertes.length > 0" class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center mb-4">
              <svg class="w-6 h-6 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              <h3 class="text-lg font-semibold text-gray-900">Alertes & Actions requises</h3>
            </div>
            <div class="space-y-3">
              <div
                v-for="(alerte, index) in stats.alertes"
                :key="index"
                :class="[
                  'flex items-center justify-between p-4 rounded-lg border-l-4',
                  alerte.type === 'danger' ? 'bg-red-50 border-red-500' : alerte.type === 'warning' ? 'bg-yellow-50 border-yellow-500' : 'bg-blue-50 border-blue-500'
                ]"
              >
                <div class="flex items-center flex-1">
                  <div>
                    <p :class="[
                      'font-medium',
                      alerte.type === 'danger' ? 'text-red-800' : alerte.type === 'warning' ? 'text-yellow-800' : 'text-blue-800'
                    ]">
                      {{ alerte.message }}
                    </p>
                  </div>
                </div>
                <router-link
                  :to="alerte.link"
                  :class="[
                    'px-4 py-2 rounded-lg text-sm font-medium transition ml-4',
                    alerte.type === 'danger' ? 'bg-red-600 text-white hover:bg-red-700' : alerte.type === 'warning' ? 'bg-yellow-600 text-white hover:bg-yellow-700' : 'bg-blue-600 text-white hover:bg-blue-700'
                  ]"
                >
                  {{ alerte.action }}
                </router-link>
              </div>
            </div>
          </div>

          <!-- Message si aucune alerte -->
          <div v-else class="bg-green-50 border-l-4 border-green-500 rounded-lg p-6 mb-6">
            <div class="flex items-center">
              <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <p class="font-medium text-green-800">Aucune action requise pour le moment. Tout va bien ! ✅</p>
            </div>
          </div>

          <!-- Raccourcis rapides -->
          <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Raccourcis rapides</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <router-link to="/employes/create" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:shadow-md transition">
                <svg class="w-8 h-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span class="text-sm font-medium text-gray-900">Nouvel employé</span>
              </router-link>

              <router-link to="/paies/create" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:shadow-md transition">
                <svg class="w-8 h-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium text-gray-900">Enregistrer paiement</span>
              </router-link>

              <router-link to="/conges/create" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:shadow-md transition">
                <svg class="w-8 h-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-medium text-gray-900">Demande de congé</span>
              </router-link>

              <router-link to="/equipes/create" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:shadow-md transition">
                <svg class="w-8 h-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-sm font-medium text-gray-900">Nouvelle équipe</span>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import dashboardService from '../services/dashboardService';

const router = useRouter();
const authStore = useAuthStore();

// État
const sidebarOpen = ref(false);
const loading = ref(true);
const stats = ref({
  employes: {},
  equipes: {},
  conges: {},
  paies: {},
  performances: {},
  alertes: []
});

// Charger les statistiques
const loadStats = async () => {
  loading.value = true;
  try {
    const response = await dashboardService.getStats();
    stats.value = response.data.data;
  } catch (error) {
    console.error('Erreur lors du chargement des statistiques:', error);
  } finally {
    loading.value = false;
  }
};

// Toggle sidebar
function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value;
}

function closeSidebar() {
  sidebarOpen.value = false;
}

// Formater le montant en FCFA
const formatMontant = (montant) => {
  if (!montant && montant !== 0) return '0 FCFA';
  return new Intl.NumberFormat('fr-FR', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(montant) + ' FCFA';
};

// Déconnexion
async function handleLogout() {
  if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
    await authStore.logout();
    router.push({ name: 'Login' });
  }
}

// Au montage
onMounted(() => {
  loadStats();
});
</script>
