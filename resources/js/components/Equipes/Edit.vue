<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center">
          <router-link
            to="/equipes"
            class="mr-4 text-gray-400 hover:text-gray-600"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </router-link>
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Modifier l'Équipe</h1>
            <p class="mt-1 text-sm text-gray-500">
              Modifiez les informations de l'équipe
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- État de chargement initial -->
    <div v-if="initialLoading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Message d'erreur si équipe non trouvée -->
    <div v-else-if="!equipe" class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
        <div class="flex">
          <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
          <p class="ml-3 text-sm text-red-700">Équipe non trouvée</p>
        </div>
      </div>
    </div>

    <!-- Formulaire -->
    <div v-else class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Message d'erreur global -->
      <div v-if="errorMessage" class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
        <div class="flex">
          <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
          <p class="ml-3 text-sm text-red-700">{{ errorMessage }}</p>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg">
        <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
          <!-- Nom de l'équipe -->
          <div>
            <label for="nom" class="block text-sm font-medium text-gray-700">
              Nom de l'équipe <span class="text-red-500">*</span>
            </label>
            <input
              id="nom"
              v-model="form.nom"
              type="text"
              required
              class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              :class="{ 'border-red-300': errors.nom, 'border-gray-300': !errors.nom }"
              placeholder="Ex: Équipe Terrain Nord"
            />
            <p v-if="errors.nom" class="mt-1 text-sm text-red-600">{{ errors.nom }}</p>
          </div>

          <!-- Description -->
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700">
              Description
            </label>
            <textarea
              id="description"
              v-model="form.description"
              rows="4"
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="Décrivez le rôle de cette équipe (optionnel)"
            ></textarea>
            <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
          </div>

          <!-- Statut -->
          <div>
            <label for="statut" class="block text-sm font-medium text-gray-700">
              Statut
            </label>
            <select
              id="statut"
              v-model="form.statut"
              class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="dissoute">Dissoute</option>
            </select>
          </div>

          <!-- Informations supplémentaires -->
          <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Informations</h3>
            <div class="text-sm text-gray-600 space-y-1">
              <p>Nombre d'employés: <strong>{{ equipe.employes_count || 0 }}</strong></p>
              <p>Créée le: <strong>{{ formatDate(equipe.created_at) }}</strong></p>
            </div>
          </div>

          <!-- Boutons d'action -->
          <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
            <router-link
              to="/equipes"
              class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Annuler
            </router-link>
            <button
              type="submit"
              :disabled="loading"
              class="px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Modification en cours...
              </span>
              <span v-else>Enregistrer les modifications</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import equipeService from '../../services/equipeService';

const router = useRouter();
const route = useRoute();

// État
const equipe = ref(null);
const initialLoading = ref(true);
const loading = ref(false);
const errors = ref({});
const errorMessage = ref('');

// État du formulaire
const form = ref({
  nom: '',
  description: '',
  statut: 'active'
});

// Charger les données de l'équipe
const loadEquipe = async () => {
  initialLoading.value = true;

  try {
    const response = await equipeService.getById(route.params.id);
    equipe.value = response.data.data;

    // Pré-remplir le formulaire
    form.value = {
      nom: equipe.value.nom,
      description: equipe.value.description || '',
      statut: equipe.value.statut
    };
  } catch (error) {
    console.error('Erreur lors du chargement:', error);
    errorMessage.value = 'Impossible de charger les données de l\'équipe';
  } finally {
    initialLoading.value = false;
  }
};

// Formater la date
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

// Soumettre le formulaire
const handleSubmit = async () => {
  loading.value = true;
  errors.value = {};
  errorMessage.value = '';

  try {
    await equipeService.update(route.params.id, form.value);

    // Rediriger vers la liste avec un message de succès
    router.push({
      name: 'equipes',
      state: { success: `L'équipe "${form.value.nom}" a été modifiée avec succès` }
    });
  } catch (error) {
    console.error('Erreur lors de la modification:', error);

    // Gérer les erreurs de validation
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {};
      errorMessage.value = 'Veuillez corriger les erreurs dans le formulaire';
    } else {
      errorMessage.value = error.response?.data?.message || 'Une erreur est survenue lors de la modification';
    }
  } finally {
    loading.value = false;
  }
};

// Charger l'équipe au montage
onMounted(() => {
  loadEquipe();
});
</script>
