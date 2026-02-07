<template>
  <Layout>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion des Congés</h1>
            <p class="mt-1 text-sm text-gray-500">Liste des demandes de congés</p>
          </div>
          <router-link to="/conges/create" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouvelle demande
          </router-link>
        </div>
      </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Recherche -->
          <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
            <input
              id="search"
              v-model="search"
              type="text"
              placeholder="Nom de l'employé..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @input="loadConges"
            />
          </div>

          <!-- Filtre par statut -->
          <div>
            <label for="filterStatut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
            <select
              id="filterStatut"
              v-model="filterStatut"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @change="loadConges"
            >
              <option value="">Tous les statuts</option>
              <option value="en_attente">En attente</option>
              <option value="valide">Validé</option>
              <option value="refuse">Refusé</option>
            </select>
          </div>

          <!-- Filtre par type -->
          <div>
            <label for="filterType" class="block text-sm font-medium text-gray-700 mb-1">Type de congé</label>
            <select
              id="filterType"
              v-model="filterType"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @change="loadConges"
            >
              <option value="">Tous les types</option>
              <option value="annuel">Annuel</option>
              <option value="maladie">Maladie</option>
              <option value="exceptionnel">Exceptionnel</option>
            </select>
          </div>

          <!-- Filtre par employé -->
          <div>
            <label for="filterEmploye" class="block text-sm font-medium text-gray-700 mb-1">Employé</label>
            <select
              id="filterEmploye"
              v-model="filterEmploye"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @change="loadConges"
            >
              <option value="">Tous les employés</option>
              <option v-for="employe in employes" :key="employe.id" :value="employe.id">
                {{ employe.prenom }} {{ employe.nom }}
              </option>
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

      <!-- Liste des congés -->
      <div v-else-if="conges.length > 0" class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nb jours</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="conge in conges" :key="conge.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div>
                    <div class="text-sm font-medium text-gray-900">
                      {{ conge.employe.prenom }} {{ conge.employe.nom }}
                    </div>
                    <div class="text-sm text-gray-500">{{ conge.employe.equipe?.nom || 'Sans équipe' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getTypeBadgeClass(conge.type)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ getTypeLabel(conge.type) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatDate(conge.date_debut) }} - {{ formatDate(conge.date_fin) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ conge.nb_jours }} jour(s)
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatutBadgeClass(conge.statut)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ getStatutLabel(conge.statut) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <router-link :to="`/conges/${conge.id}`" class="text-indigo-600 hover:text-indigo-900">
                  Voir
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Aucun congé -->
      <div v-else class="bg-white shadow rounded-lg p-8 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune demande de congé</h3>
        <p class="mt-1 text-sm text-gray-500">Commencez par créer une nouvelle demande</p>
        <div class="mt-6">
          <router-link to="/conges/create" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
            Nouvelle demande
          </router-link>
        </div>
      </div>
    </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Layout from '../Layout.vue';
import congeService from '../../services/congeService';
import employeService from '../../services/employeService';

const router = useRouter();

// État
const conges = ref([]);
const employes = ref([]);
const loading = ref(true);
const search = ref('');
const filterStatut = ref('');
const filterType = ref('');
const filterEmploye = ref('');
const successMessage = ref('');

// Charger les congés avec filtres
const loadConges = async () => {
  loading.value = true;
  try {
    const filters = {
      search: search.value,
      statut: filterStatut.value,
      type: filterType.value,
      employe_id: filterEmploye.value
    };

    const response = await congeService.getAll(filters);
    conges.value = response.data.data;
  } catch (error) {
    console.error('Erreur lors du chargement des congés:', error);
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

// Formatage de la date
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

// Classes de badge pour le type
const getTypeBadgeClass = (type) => {
  const classes = {
    'annuel': 'bg-blue-100 text-blue-800',
    'maladie': 'bg-orange-100 text-orange-800',
    'exceptionnel': 'bg-purple-100 text-purple-800'
  };
  return classes[type] || 'bg-gray-100 text-gray-800';
};

// Label pour le type
const getTypeLabel = (type) => {
  const labels = {
    'annuel': 'Annuel',
    'maladie': 'Maladie',
    'exceptionnel': 'Exceptionnel'
  };
  return labels[type] || type;
};

// Classes de badge pour le statut
const getStatutBadgeClass = (statut) => {
  const classes = {
    'en_attente': 'bg-yellow-100 text-yellow-800',
    'valide': 'bg-green-100 text-green-800',
    'refuse': 'bg-red-100 text-red-800'
  };
  return classes[statut] || 'bg-gray-100 text-gray-800';
};

// Label pour le statut
const getStatutLabel = (statut) => {
  const labels = {
    'en_attente': 'En attente',
    'valide': 'Validé',
    'refuse': 'Refusé'
  };
  return labels[statut] || statut;
};

// Charger au montage
onMounted(() => {
  // Récupérer le message de succès depuis router state
  if (history.state.success) {
    successMessage.value = history.state.success;
    setTimeout(() => {
      successMessage.value = '';
    }, 5000);
  }

  loadConges();
  loadEmployes();
});
</script>
