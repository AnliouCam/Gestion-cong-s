<template>
  <Layout>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="md:flex md:items-center md:justify-between">
          <div class="flex-1 min-w-0">
            <h1 class="text-3xl font-bold text-gray-900">Gestion des Employés</h1>
            <p class="mt-1 text-sm text-gray-500">
              Gérez les fiches employés et leurs informations
            </p>
          </div>
          <div class="mt-4 flex md:mt-0 md:ml-4">
            <router-link
              to="/employes/create"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
            >
              <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Nouvel Employé
            </router-link>
          </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <!-- Recherche -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
            <input
              v-model="search"
              type="text"
              placeholder="Nom, prénom, poste..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @input="filterEmployes"
            />
          </div>

          <!-- Filtre par équipe -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Équipe</label>
            <select
              v-model="filterEquipe"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @change="filterEmployes"
            >
              <option value="">Toutes les équipes</option>
              <option v-for="equipe in equipes" :key="equipe.id" :value="equipe.id">
                {{ equipe.nom }}
              </option>
            </select>
          </div>

          <!-- Filtre par statut -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
            <select
              v-model="filterStatut"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @change="filterEmployes"
            >
              <option value="">Tous les statuts</option>
              <option value="actif">Actif</option>
              <option value="suspendu">Suspendu</option>
              <option value="sorti">Sorti</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Contenu principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Message de succès -->
      <div v-if="successMessage" class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <div class="flex">
          <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <p class="ml-3 text-sm text-green-700">{{ successMessage }}</p>
        </div>
      </div>

      <!-- Message d'erreur -->
      <div v-if="errorMessage" class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
        <div class="flex">
          <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
          <p class="ml-3 text-sm text-red-700">{{ errorMessage }}</p>
        </div>
      </div>

      <!-- État de chargement -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      </div>

      <!-- Liste vide -->
      <div v-else-if="employes.length === 0" class="text-center py-12 bg-white rounded-lg shadow">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun employé</h3>
        <p class="mt-1 text-sm text-gray-500">Commencez par créer votre premier employé.</p>
        <div class="mt-6">
          <router-link
            to="/employes/create"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Créer un employé
          </router-link>
        </div>
      </div>

      <!-- Tableau des employés -->
      <div v-else class="bg-white shadow overflow-x-auto sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Employé
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Poste
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Équipe
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Statut
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Salaire
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="employe in employes" :key="employe.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                      <span class="text-indigo-700 font-medium text-sm">
                        {{ getInitiales(employe.nom, employe.prenom) }}
                      </span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ employe.prenom }} {{ employe.nom }}
                    </div>
                    <div class="text-sm text-gray-500" v-if="employe.email">
                      {{ employe.email }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ employe.poste }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ employe.equipe?.nom || 'Non assigné' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="{
                    'bg-green-100 text-green-800': employe.statut === 'actif',
                    'bg-yellow-100 text-yellow-800': employe.statut === 'suspendu',
                    'bg-red-100 text-red-800': employe.statut === 'sorti'
                  }"
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                >
                  {{ employe.statut === 'actif' ? 'Actif' : employe.statut === 'suspendu' ? 'Suspendu' : 'Sorti' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatSalaire(employe.salaire_mensuel) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <router-link
                  :to="`/employes/${employe.id}`"
                  class="text-indigo-600 hover:text-indigo-900 mr-4"
                >
                  Voir
                </router-link>
                <router-link
                  :to="`/employes/${employe.id}/edit`"
                  class="text-indigo-600 hover:text-indigo-900 mr-4"
                >
                  Modifier
                </router-link>
                <button
                  @click="confirmDelete(employe)"
                  class="text-red-600 hover:text-red-900"
                >
                  Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Layout from '../Layout.vue';
import employeService from '../../services/employeService';
import equipeService from '../../services/equipeService';

const router = useRouter();

// État
const employes = ref([]);
const equipes = ref([]);
const loading = ref(false);
const search = ref('');
const filterEquipe = ref('');
const filterStatut = ref('');
const successMessage = ref('');
const errorMessage = ref('');

// Charger les équipes pour le filtre
const loadEquipes = async () => {
  try {
    const response = await equipeService.getAll();
    equipes.value = response.data.data;
  } catch (error) {
    console.error('Erreur lors du chargement des équipes:', error);
  }
};

// Charger les employés
const loadEmployes = async () => {
  loading.value = true;
  errorMessage.value = '';

  try {
    const filters = {
      search: search.value,
      equipe_id: filterEquipe.value,
      statut: filterStatut.value
    };

    const response = await employeService.getAll(filters);
    employes.value = response.data.data;
  } catch (error) {
    console.error('Erreur lors du chargement des employés:', error);
    errorMessage.value = 'Impossible de charger les employés';
  } finally {
    loading.value = false;
  }
};

// Filtrer les employés (appelé lors de la saisie dans les filtres)
const filterEmployes = () => {
  loadEmployes();
};

// Obtenir les initiales d'un employé
const getInitiales = (nom, prenom) => {
  return (prenom.charAt(0) + nom.charAt(0)).toUpperCase();
};

// Formater le salaire
const formatSalaire = (salaire) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XOF',
    minimumFractionDigits: 0
  }).format(salaire);
};

// Confirmer et supprimer un employé
const confirmDelete = async (employe) => {
  if (confirm(`Êtes-vous sûr de vouloir supprimer ${employe.prenom} ${employe.nom} ?\n\nCette action ne peut pas être annulée.`)) {
    try {
      await employeService.delete(employe.id);
      successMessage.value = `L'employé "${employe.prenom} ${employe.nom}" a été supprimé avec succès`;
      loadEmployes();

      // Masquer le message après 3 secondes
      setTimeout(() => {
        successMessage.value = '';
      }, 3000);
    } catch (error) {
      console.error('Erreur lors de la suppression:', error);
      errorMessage.value = error.response?.data?.message || 'Impossible de supprimer l\'employé';
    }
  }
};

// Charger les données au montage
onMounted(() => {
  loadEquipes();
  loadEmployes();
});
</script>
