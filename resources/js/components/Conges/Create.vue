<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center">
          <router-link to="/conges" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </router-link>
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Nouvelle Demande de Congé</h1>
            <p class="mt-1 text-sm text-gray-500">Enregistrer une demande de congé pour un employé</p>
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
          <!-- Informations employé -->
          <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations employé</h3>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <!-- Sélection employé -->
              <div class="sm:col-span-2">
                <label for="employe_id" class="block text-sm font-medium text-gray-700">
                  Employé <span class="text-red-500">*</span>
                </label>
                <select
                  id="employe_id"
                  v-model="form.employe_id"
                  required
                  class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-300': errors.employe_id, 'border-gray-300': !errors.employe_id }"
                  @change="onEmployeChange"
                >
                  <option value="">Sélectionner un employé</option>
                  <option v-for="employe in employes" :key="employe.id" :value="employe.id">
                    {{ employe.prenom }} {{ employe.nom }} - {{ employe.equipe?.nom || 'Sans équipe' }}
                  </option>
                </select>
                <p v-if="errors.employe_id" class="mt-1 text-sm text-red-600">{{ errors.employe_id[0] }}</p>
              </div>

              <!-- Solde de congés (affiché si employé sélectionné) -->
              <div v-if="employeSelectionne" class="sm:col-span-2 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                  <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                  </svg>
                  <div>
                    <p class="text-sm font-medium text-blue-800">
                      Solde de congés annuel : <span class="font-bold">{{ employeSelectionne.solde_conges_annuel }} jours</span>
                    </p>
                    <p class="text-xs text-blue-600 mt-1">
                      Après cette demande : {{ soldeApresConge }} jours
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Informations congé -->
          <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Détails du congé</h3>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <!-- Type de congé -->
              <div class="sm:col-span-2">
                <label for="type" class="block text-sm font-medium text-gray-700">
                  Type de congé <span class="text-red-500">*</span>
                </label>
                <select
                  id="type"
                  v-model="form.type"
                  required
                  class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-300': errors.type, 'border-gray-300': !errors.type }"
                >
                  <option value="">Sélectionner un type</option>
                  <option value="annuel">Congé annuel (déduit du solde)</option>
                  <option value="maladie">Congé maladie (ne déduit pas du solde)</option>
                  <option value="exceptionnel">Congé exceptionnel (ne déduit pas du solde)</option>
                </select>
                <p v-if="errors.type" class="mt-1 text-sm text-red-600">{{ errors.type[0] }}</p>
              </div>

              <!-- Date début -->
              <div>
                <label for="date_debut" class="block text-sm font-medium text-gray-700">
                  Date de début <span class="text-red-500">*</span>
                </label>
                <input
                  id="date_debut"
                  v-model="form.date_debut"
                  type="date"
                  required
                  class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-300': errors.date_debut, 'border-gray-300': !errors.date_debut }"
                  @change="calculateNbJours"
                />
                <p v-if="errors.date_debut" class="mt-1 text-sm text-red-600">{{ errors.date_debut[0] }}</p>
              </div>

              <!-- Date fin -->
              <div>
                <label for="date_fin" class="block text-sm font-medium text-gray-700">
                  Date de fin <span class="text-red-500">*</span>
                </label>
                <input
                  id="date_fin"
                  v-model="form.date_fin"
                  type="date"
                  required
                  :min="form.date_debut"
                  class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-300': errors.date_fin, 'border-gray-300': !errors.date_fin }"
                  @change="calculateNbJours"
                />
                <p v-if="errors.date_fin" class="mt-1 text-sm text-red-600">{{ errors.date_fin[0] }}</p>
              </div>

              <!-- Nombre de jours (calculé automatiquement) -->
              <div v-if="nbJoursCalcule > 0" class="sm:col-span-2 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-700">
                  Durée : <span class="text-lg font-bold text-indigo-600">{{ nbJoursCalcule }} jour(s)</span>
                </p>
              </div>

              <!-- Avertissement si solde insuffisant -->
              <div v-if="soldeInsuffisant" class="sm:col-span-2 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                  <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                  <p class="text-sm text-red-700">
                    <strong>Attention :</strong> Le solde de congés est insuffisant pour cette demande.
                  </p>
                </div>
              </div>

              <!-- Commentaire -->
              <div class="sm:col-span-2">
                <label for="commentaire" class="block text-sm font-medium text-gray-700">
                  Commentaire (optionnel)
                </label>
                <textarea
                  id="commentaire"
                  v-model="form.commentaire"
                  rows="3"
                  class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="Raison ou détails supplémentaires..."
                ></textarea>
                <p class="mt-1 text-sm text-gray-500">Max 500 caractères</p>
              </div>
            </div>
          </div>

          <!-- Boutons d'action -->
          <div class="flex items-center justify-end space-x-4 pt-6">
            <router-link
              to="/conges"
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
                Enregistrement en cours...
              </span>
              <span v-else>Enregistrer la demande</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import congeService from '../../services/congeService';
import employeService from '../../services/employeService';

const router = useRouter();

// État
const employes = ref([]);
const form = ref({
  employe_id: '',
  type: '',
  date_debut: '',
  date_fin: '',
  commentaire: ''
});

const loading = ref(false);
const errors = ref({});
const errorMessage = ref('');
const nbJoursCalcule = ref(0);

// Employé sélectionné
const employeSelectionne = computed(() => {
  if (!form.value.employe_id) return null;
  return employes.value.find(e => e.id === parseInt(form.value.employe_id));
});

// Solde après congé (si type = annuel)
const soldeApresConge = computed(() => {
  if (!employeSelectionne.value || form.value.type !== 'annuel' || nbJoursCalcule.value === 0) {
    return employeSelectionne.value ? employeSelectionne.value.solde_conges_annuel : 0;
  }
  return Math.max(0, employeSelectionne.value.solde_conges_annuel - nbJoursCalcule.value);
});

// Solde insuffisant ?
const soldeInsuffisant = computed(() => {
  return form.value.type === 'annuel' &&
         employeSelectionne.value &&
         nbJoursCalcule.value > 0 &&
         employeSelectionne.value.solde_conges_annuel < nbJoursCalcule.value;
});

// Charger les employés
const loadEmployes = async () => {
  try {
    const response = await employeService.getAll({ statut: 'actif' });
    employes.value = response.data.data;
  } catch (error) {
    console.error('Erreur lors du chargement des employés:', error);
    errorMessage.value = 'Impossible de charger la liste des employés';
  }
};

// Calculer le nombre de jours
const calculateNbJours = () => {
  if (form.value.date_debut && form.value.date_fin) {
    const debut = new Date(form.value.date_debut);
    const fin = new Date(form.value.date_fin);

    if (fin >= debut) {
      const diffTime = Math.abs(fin - debut);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
      nbJoursCalcule.value = diffDays;
    } else {
      nbJoursCalcule.value = 0;
    }
  } else {
    nbJoursCalcule.value = 0;
  }
};

// Quand l'employé change
const onEmployeChange = () => {
  calculateNbJours();
};

// Soumettre le formulaire
const handleSubmit = async () => {
  loading.value = true;
  errors.value = {};
  errorMessage.value = '';

  try {
    await congeService.create(form.value);

    // Redirection avec message de succès
    router.push({
      name: 'conges',
      state: { success: 'Demande de congé enregistrée avec succès' }
    });
  } catch (error) {
    console.error('Erreur lors de la création:', error);

    // Gérer les erreurs de validation
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {};
      errorMessage.value = error.response.data.message || 'Veuillez corriger les erreurs dans le formulaire';
    } else {
      errorMessage.value = error.response?.data?.message || 'Une erreur est survenue lors de l\'enregistrement';
    }
  } finally {
    loading.value = false;
  }
};

// Charger au montage
onMounted(() => {
  loadEmployes();
});
</script>
