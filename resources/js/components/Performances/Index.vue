<template>
  <Layout>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion des Performances</h1>
            <p class="mt-1 text-sm text-gray-500">Historique des évaluations des employés</p>
          </div>
          <router-link to="/performances/create" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Évaluer un employé
          </router-link>
        </div>
      </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
          <!-- Recherche -->
          <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
            <input
              id="search"
              v-model="search"
              type="text"
              placeholder="Nom de l'employé..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @input="loadPerformances"
            />
          </div>

          <!-- Filtre par mois -->
          <div>
            <label for="filterMois" class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
            <input
              id="filterMois"
              v-model="filterMois"
              type="month"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @change="loadPerformances"
            />
          </div>

          <!-- Filtre par employé -->
          <div>
            <label for="filterEmploye" class="block text-sm font-medium text-gray-700 mb-1">Employé</label>
            <select
              id="filterEmploye"
              v-model="filterEmploye"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @change="loadPerformances"
            >
              <option value="">Tous les employés</option>
              <option v-for="employe in employes" :key="employe.id" :value="employe.id">
                {{ employe.prenom }} {{ employe.nom }}
              </option>
            </select>
          </div>

          <!-- Filtre par équipe -->
          <div>
            <label for="filterEquipe" class="block text-sm font-medium text-gray-700 mb-1">Équipe</label>
            <select
              id="filterEquipe"
              v-model="filterEquipe"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @change="loadPerformances"
            >
              <option value="">Toutes les équipes</option>
              <option v-for="equipe in equipes" :key="equipe.id" :value="equipe.id">
                {{ equipe.nom }}
              </option>
            </select>
          </div>

          <!-- Filtre par note -->
          <div>
            <label for="filterNote" class="block text-sm font-medium text-gray-700 mb-1">Note</label>
            <select
              id="filterNote"
              v-model="filterNote"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @change="loadPerformances"
            >
              <option value="">Toutes les notes</option>
              <option value="5">⭐⭐⭐⭐⭐ Excellent (5)</option>
              <option value="4">⭐⭐⭐⭐ Bon (4)</option>
              <option value="3">⭐⭐⭐ Satisfaisant (3)</option>
              <option value="2">⭐⭐ Insuffisant (2)</option>
              <option value="1">⭐ Très insuffisant (1)</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Message de succès -->
      <div v-if="successMessage" class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ successMessage }}</p>
      </div>

      <!-- Chargement -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      </div>

      <!-- Liste des évaluations -->
      <div v-else-if="performances.length > 0" class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mois</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Évalué par</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="performance in performances" :key="performance.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div>
                    <div class="text-sm font-medium text-gray-900">
                      {{ performance.employe.prenom }} {{ performance.employe.nom }}
                    </div>
                    <div class="text-sm text-gray-500">{{ performance.employe.equipe?.nom || 'Sans équipe' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatMois(performance.mois) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <span class="text-yellow-400 text-lg mr-2">{{ getStars(performance.note) }}</span>
                  <span :class="getNoteColorClass(performance.note)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ getNoteLabel(performance.note) }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4">
                <p class="text-sm text-gray-900 line-clamp-2">{{ performance.commentaire || 'Aucun commentaire' }}</p>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ performance.evalue_par?.name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="confirmDelete(performance)"
                  class="text-red-600 hover:text-red-900 ml-3"
                  title="Supprimer"
                >
                  Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Aucune évaluation -->
      <div v-else class="bg-white shadow rounded-lg p-8 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune évaluation</h3>
        <p class="mt-1 text-sm text-gray-500">Aucune évaluation enregistrée pour les critères sélectionnés.</p>
        <div class="mt-6">
          <router-link to="/performances/create" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            Évaluer un employé
          </router-link>
        </div>
      </div>
    </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import Layout from '../Layout.vue';
import performanceService from '../../services/performanceService';
import employeService from '../../services/employeService';
import equipeService from '../../services/equipeService';

const router = useRouter();
const route = useRoute();

// État
const performances = ref([]);
const employes = ref([]);
const equipes = ref([]);
const loading = ref(false);
const successMessage = ref('');

// Filtres
const search = ref('');
const filterMois = ref('');
const filterEmploye = ref('');
const filterEquipe = ref('');
const filterNote = ref('');

// Charger les évaluations
const loadPerformances = async () => {
  loading.value = true;
  try {
    const filters = {};

    if (search.value) filters.search = search.value;
    if (filterMois.value) filters.mois = filterMois.value;
    if (filterEmploye.value) filters.employe_id = filterEmploye.value;
    if (filterEquipe.value) filters.equipe_id = filterEquipe.value;
    if (filterNote.value) filters.note = filterNote.value;

    const response = await performanceService.getAll(filters);
    performances.value = response.data.data;
  } catch (error) {
    console.error('Erreur lors du chargement des évaluations:', error);
    alert('Erreur lors du chargement des évaluations');
  } finally {
    loading.value = false;
  }
};

// Charger les employés pour le filtre
const loadEmployes = async () => {
  try {
    const response = await employeService.getAll();
    employes.value = response.data.data;
  } catch (error) {
    console.error('Erreur lors du chargement des employés:', error);
  }
};

// Charger les équipes pour le filtre
const loadEquipes = async () => {
  try {
    const response = await equipeService.getAll();
    equipes.value = response.data.data;
  } catch (error) {
    console.error('Erreur lors du chargement des équipes:', error);
  }
};

// Confirmer la suppression
const confirmDelete = async (performance) => {
  const employe = `${performance.employe.prenom} ${performance.employe.nom}`;
  const mois = formatMois(performance.mois);

  if (confirm(`Êtes-vous sûr de vouloir supprimer l'évaluation de ${employe} pour ${mois} ?\n\nNote: ${performance.note}/5`)) {
    try {
      await performanceService.delete(performance.id);
      successMessage.value = 'Évaluation supprimée avec succès';
      loadPerformances();

      setTimeout(() => {
        successMessage.value = '';
      }, 3000);
    } catch (error) {
      console.error('Erreur lors de la suppression:', error);
      alert('Erreur lors de la suppression de l\'évaluation');
    }
  }
};

// Formater le mois (YYYY-MM → Janvier 2025)
const formatMois = (mois) => {
  if (!mois) return '';
  const [annee, moisNum] = mois.split('-');
  const moisNoms = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
  return `${moisNoms[parseInt(moisNum) - 1]} ${annee}`;
};

// Obtenir les étoiles visuelles
const getStars = (note) => {
  return '★'.repeat(note) + '☆'.repeat(5 - note);
};

// Label de la note
const getNoteLabel = (note) => {
  const labels = {
    1: 'Très insuffisant',
    2: 'Insuffisant',
    3: 'Satisfaisant',
    4: 'Bon',
    5: 'Excellent'
  };
  return labels[note] || '';
};

// Classe CSS pour la note
const getNoteColorClass = (note) => {
  if (note >= 4) return 'bg-green-100 text-green-800';
  if (note === 3) return 'bg-blue-100 text-blue-800';
  if (note === 2) return 'bg-orange-100 text-orange-800';
  return 'bg-red-100 text-red-800';
};

// Au montage
onMounted(() => {
  loadPerformances();
  loadEmployes();
  loadEquipes();

  // Message de succès depuis la navigation
  if (route.query.success) {
    successMessage.value = route.query.success;
    router.replace({ query: {} });

    setTimeout(() => {
      successMessage.value = '';
    }, 3000);
  }
});
</script>
