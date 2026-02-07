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
            <h1 class="text-3xl font-bold text-gray-900">Nouvelle Équipe</h1>
            <p class="mt-1 text-sm text-gray-500">
              Créez une nouvelle équipe ou service
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Formulaire -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
            <p class="mt-1 text-sm text-gray-500">
              Par défaut, l'équipe est créée avec le statut "Active"
            </p>
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
                Création en cours...
              </span>
              <span v-else>Créer l'équipe</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import equipeService from '../../services/equipeService';

const router = useRouter();

// État du formulaire
const form = ref({
  nom: '',
  description: '',
  statut: 'active'
});

const loading = ref(false);
const errors = ref({});
const errorMessage = ref('');

// Soumettre le formulaire
const handleSubmit = async () => {
  loading.value = true;
  errors.value = {};
  errorMessage.value = '';

  try {
    await equipeService.create(form.value);

    // Rediriger vers la liste avec un message de succès
    router.push({
      name: 'equipes',
      state: { success: `L'équipe "${form.value.nom}" a été créée avec succès` }
    });
  } catch (error) {
    console.error('Erreur lors de la création:', error);

    // Gérer les erreurs de validation
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {};
      errorMessage.value = 'Veuillez corriger les erreurs dans le formulaire';
    } else {
      errorMessage.value = error.response?.data?.message || 'Une erreur est survenue lors de la création';
    }
  } finally {
    loading.value = false;
  }
};
</script>
