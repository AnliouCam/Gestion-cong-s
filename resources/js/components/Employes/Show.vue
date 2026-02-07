<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <router-link to="/employes" class="mr-4 text-gray-400 hover:text-gray-600">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </router-link>
            <div v-if="employe">
              <h1 class="text-3xl font-bold text-gray-900">
                {{ employe.prenom }} {{ employe.nom }}
              </h1>
              <p class="mt-1 text-sm text-gray-500">{{ employe.poste }}</p>
            </div>
          </div>
          <router-link v-if="employe" :to="`/employes/${employe.id}/edit`" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            Modifier
          </router-link>
        </div>
      </div>
    </div>

    <!-- Contenu -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Chargement -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      </div>

      <!-- Erreur -->
      <div v-else-if="!employe" class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
        <p class="text-sm text-red-700">Employé non trouvé</p>
      </div>

      <!-- Fiche employé -->
      <div v-else class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="text-sm font-medium text-gray-500">Nom complet</h3>
              <p class="mt-1 text-lg text-gray-900">{{ employe.prenom }} {{ employe.nom }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500">Statut</h3>
              <span :class="{'bg-green-100 text-green-800': employe.statut === 'actif', 'bg-yellow-100 text-yellow-800': employe.statut === 'suspendu', 'bg-red-100 text-red-800': employe.statut === 'sorti'}" class="mt-1 inline-flex px-2 text-xs font-semibold rounded-full">
                {{ employe.statut === 'actif' ? 'Actif' : employe.statut === 'suspendu' ? 'Suspendu' : 'Sorti' }}
              </span>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500">Poste</h3>
              <p class="mt-1 text-lg text-gray-900">{{ employe.poste }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500">Équipe</h3>
              <p class="mt-1 text-lg text-gray-900">{{ employe.equipe?.nom || 'Non assigné' }}</p>
            </div>
            <div v-if="employe.email">
              <h3 class="text-sm font-medium text-gray-500">Email</h3>
              <p class="mt-1 text-lg text-gray-900">{{ employe.email }}</p>
            </div>
            <div v-if="employe.telephone">
              <h3 class="text-sm font-medium text-gray-500">Téléphone</h3>
              <p class="mt-1 text-lg text-gray-900">{{ employe.telephone }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500">Salaire mensuel</h3>
              <p class="mt-1 text-lg text-gray-900">{{ formatSalaire(employe.salaire_mensuel) }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500">Date d'embauche</h3>
              <p class="mt-1 text-lg text-gray-900">{{ formatDate(employe.date_embauche) }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-500">Solde congés annuel</h3>
              <p class="mt-1 text-lg text-gray-900">{{ employe.solde_conges_annuel }} jours</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import employeService from '../../services/employeService';

const route = useRoute();
const employe = ref(null);
const loading = ref(true);

const loadEmploye = async () => {
  try {
    const response = await employeService.getById(route.params.id);
    employe.value = response.data.data;
  } catch (error) {
    console.error('Erreur:', error);
  } finally {
    loading.value = false;
  }
};

const formatSalaire = (salaire) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', minimumFractionDigits: 0 }).format(salaire);
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' });
};

onMounted(() => {
  loadEmploye();
});
</script>
