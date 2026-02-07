<template>
  <Layout>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion des Paies</h1>
            <p class="mt-1 text-sm text-gray-500">Historique des paiements mensuels</p>
          </div>
          <router-link to="/paies/create" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Enregistrer un paiement
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
              @input="loadPaies"
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
              @change="loadPaies"
            />
          </div>

          <!-- Filtre par statut -->
          <div>
            <label for="filterStatut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
            <select
              id="filterStatut"
              v-model="filterStatut"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @change="loadPaies"
            >
              <option value="">Tous les statuts</option>
              <option value="paye">Payé</option>
              <option value="non_paye">Non payé</option>
            </select>
          </div>

          <!-- Filtre par employé -->
          <div>
            <label for="filterEmploye" class="block text-sm font-medium text-gray-700 mb-1">Employé</label>
            <select
              id="filterEmploye"
              v-model="filterEmploye"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              @change="loadPaies"
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

      <!-- Liste des paiements -->
      <div v-else-if="paies.length > 0" class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mois</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salaire base</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Primes</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retenues</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant total</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="paie in paies" :key="paie.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div>
                    <div class="text-sm font-medium text-gray-900">
                      {{ paie.employe.prenom }} {{ paie.employe.nom }}
                    </div>
                    <div class="text-sm text-gray-500">{{ paie.employe.equipe?.nom || 'Sans équipe' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatMois(paie.mois) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatMontant(paie.salaire_base) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatMontant(paie.primes) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatMontant(paie.retenues) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                {{ formatMontant(paie.montant_total) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatutBadgeClass(paie.statut)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ getStatutLabel(paie.statut) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <span :class="getModeBadgeClass(paie.mode_paiement)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ getModeLabel(paie.mode_paiement) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="confirmDelete(paie)"
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

      <!-- Aucun paiement -->
      <div v-else class="bg-white shadow rounded-lg p-8 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun paiement</h3>
        <p class="mt-1 text-sm text-gray-500">Aucun paiement enregistré pour les critères sélectionnés.</p>
        <div class="mt-6">
          <router-link to="/paies/create" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            Enregistrer un paiement
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
import paieService from '../../services/paieService';
import employeService from '../../services/employeService';

const router = useRouter();
const route = useRoute();

// État
const paies = ref([]);
const employes = ref([]);
const loading = ref(false);
const successMessage = ref('');

// Filtres
const search = ref('');
const filterMois = ref('');
const filterStatut = ref('');
const filterEmploye = ref('');

// Charger les paiements
const loadPaies = async () => {
  loading.value = true;
  try {
    const filters = {};

    if (search.value) filters.search = search.value;
    if (filterMois.value) filters.mois = filterMois.value;
    if (filterStatut.value) filters.statut = filterStatut.value;
    if (filterEmploye.value) filters.employe_id = filterEmploye.value;

    const response = await paieService.getAll(filters);
    paies.value = response.data.data;
  } catch (error) {
    console.error('Erreur lors du chargement des paiements:', error);
    alert('Erreur lors du chargement des paiements');
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

// Confirmer la suppression
const confirmDelete = async (paie) => {
  const employe = `${paie.employe.prenom} ${paie.employe.nom}`;
  const mois = formatMois(paie.mois);

  if (confirm(`Êtes-vous sûr de vouloir supprimer le paiement de ${employe} pour ${mois} ?\n\nMontant: ${formatMontant(paie.montant_total)}`)) {
    try {
      await paieService.delete(paie.id);
      successMessage.value = 'Paiement supprimé avec succès';
      loadPaies();

      setTimeout(() => {
        successMessage.value = '';
      }, 3000);
    } catch (error) {
      console.error('Erreur lors de la suppression:', error);
      alert('Erreur lors de la suppression du paiement');
    }
  }
};

// Formater le montant en FCFA
const formatMontant = (montant) => {
  if (!montant && montant !== 0) return '0 FCFA';
  return new Intl.NumberFormat('fr-FR', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(montant) + ' FCFA';
};

// Formater le mois (YYYY-MM → Janvier 2025)
const formatMois = (mois) => {
  if (!mois) return '';
  const [annee, moisNum] = mois.split('-');
  const moisNoms = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
  return `${moisNoms[parseInt(moisNum) - 1]} ${annee}`;
};

// Classes CSS pour le badge de statut
const getStatutBadgeClass = (statut) => {
  switch (statut) {
    case 'paye':
      return 'bg-green-100 text-green-800';
    case 'non_paye':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};

// Label du statut
const getStatutLabel = (statut) => {
  switch (statut) {
    case 'paye':
      return 'Payé';
    case 'non_paye':
      return 'Non payé';
    default:
      return statut;
  }
};

// Classes CSS pour le badge de mode de paiement
const getModeBadgeClass = (mode) => {
  switch (mode) {
    case 'cash':
      return 'bg-blue-100 text-blue-800';
    case 'virement':
      return 'bg-purple-100 text-purple-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};

// Label du mode de paiement
const getModeLabel = (mode) => {
  switch (mode) {
    case 'cash':
      return 'Cash';
    case 'virement':
      return 'Virement';
    default:
      return mode;
  }
};

// Au montage
onMounted(() => {
  loadPaies();
  loadEmployes();

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
