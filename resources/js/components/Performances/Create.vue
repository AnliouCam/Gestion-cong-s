<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center">
          <router-link to="/performances" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </router-link>
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Évaluer un Employé</h1>
            <p class="mt-1 text-sm text-gray-500">Enregistrer l'évaluation mensuelle d'un employé</p>
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
                >
                  <option value="">Sélectionner un employé</option>
                  <option v-for="employe in employes" :key="employe.id" :value="employe.id">
                    {{ employe.prenom }} {{ employe.nom }} - {{ employe.equipe?.nom || 'Sans équipe' }}
                  </option>
                </select>
                <p v-if="errors.employe_id" class="mt-1 text-sm text-red-600">{{ errors.employe_id[0] }}</p>
              </div>

              <!-- Mois d'évaluation -->
              <div class="sm:col-span-2">
                <label for="mois" class="block text-sm font-medium text-gray-700">
                  Mois d'évaluation <span class="text-red-500">*</span>
                </label>
                <input
                  id="mois"
                  v-model="form.mois"
                  type="month"
                  required
                  class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-300': errors.mois, 'border-gray-300': !errors.mois }"
                />
                <p v-if="errors.mois" class="mt-1 text-sm text-red-600">{{ errors.mois[0] }}</p>
              </div>
            </div>
          </div>

          <!-- Évaluation -->
          <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Évaluation</h3>

            <!-- Système de notation par étoiles -->
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Note de performance <span class="text-red-500">*</span>
              </label>

              <!-- Étoiles cliquables -->
              <div class="flex items-center space-x-1 mb-4">
                <button
                  v-for="star in 5"
                  :key="star"
                  type="button"
                  @click="setNote(star)"
                  @mouseenter="hoverNote = star"
                  @mouseleave="hoverNote = 0"
                  class="focus:outline-none transition-transform hover:scale-110"
                >
                  <svg
                    class="w-12 h-12"
                    :class="star <= (hoverNote || form.note) ? 'text-yellow-400' : 'text-gray-300'"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                </button>
              </div>

              <!-- Label de la note -->
              <div class="flex items-center space-x-3">
                <span v-if="form.note" class="text-2xl font-bold text-gray-900">{{ form.note }}/5</span>
                <span
                  v-if="form.note"
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-semibold',
                    getNoteColorClass(form.note)
                  ]"
                >
                  {{ getNoteLabel(form.note) }}
                </span>
              </div>
              <p v-if="!form.note" class="mt-2 text-sm text-gray-500">Cliquez sur les étoiles pour attribuer une note</p>
              <p v-if="errors.note" class="mt-2 text-sm text-red-600">{{ errors.note[0] }}</p>

              <!-- Description des niveaux -->
              <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <p class="text-xs font-medium text-gray-700 mb-3">Échelle d'évaluation :</p>
                <div class="space-y-2 text-xs text-gray-600">
                  <div class="flex items-center">
                    <span class="text-yellow-400 mr-2">★★★★★</span>
                    <span><strong>5 - Excellent :</strong> Performance exceptionnelle</span>
                  </div>
                  <div class="flex items-center">
                    <span class="text-yellow-400 mr-2">★★★★☆</span>
                    <span><strong>4 - Bon :</strong> Performance au-dessus des attentes</span>
                  </div>
                  <div class="flex items-center">
                    <span class="text-yellow-400 mr-2">★★★☆☆</span>
                    <span><strong>3 - Satisfaisant :</strong> Performance conforme aux attentes</span>
                  </div>
                  <div class="flex items-center">
                    <span class="text-yellow-400 mr-2">★★☆☆☆</span>
                    <span><strong>2 - Insuffisant :</strong> Performance en dessous des attentes</span>
                  </div>
                  <div class="flex items-center">
                    <span class="text-yellow-400 mr-2">★☆☆☆☆</span>
                    <span><strong>1 - Très insuffisant :</strong> Performance très faible</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Commentaire -->
            <div class="mt-6">
              <label for="commentaire" class="block text-sm font-medium text-gray-700">
                Commentaire (optionnel mais recommandé)
              </label>
              <textarea
                id="commentaire"
                v-model="form.commentaire"
                rows="5"
                maxlength="1000"
                placeholder="Décrivez les points forts, axes d'amélioration, réalisations du mois..."
                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              ></textarea>
              <p class="mt-1 text-sm text-gray-500">{{ form.commentaire?.length || 0 }} / 1000 caractères</p>
            </div>
          </div>

          <!-- Boutons d'action -->
          <div class="flex justify-end space-x-4">
            <router-link
              to="/performances"
              class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Annuler
            </router-link>
            <button
              type="submit"
              :disabled="submitting || !form.note"
              class="px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="!submitting">Enregistrer l'évaluation</span>
              <span v-else>Enregistrement...</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import performanceService from '../../services/performanceService';
import employeService from '../../services/employeService';

const router = useRouter();

// État
const employes = ref([]);
const errors = ref({});
const errorMessage = ref('');
const submitting = ref(false);
const hoverNote = ref(0); // Pour le survol des étoiles

// Formulaire
const form = ref({
  employe_id: '',
  mois: new Date().toISOString().slice(0, 7), // Mois en cours par défaut (YYYY-MM)
  note: 0,
  commentaire: ''
});

// Charger la liste des employés
const loadEmployes = async () => {
  try {
    const response = await employeService.getAll({ statut: 'actif' });
    employes.value = response.data.data;
  } catch (error) {
    console.error('Erreur lors du chargement des employés:', error);
    errorMessage.value = 'Erreur lors du chargement des employés';
  }
};

// Définir la note
const setNote = (note) => {
  form.value.note = note;
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

// Soumettre le formulaire
const handleSubmit = async () => {
  errors.value = {};
  errorMessage.value = '';
  submitting.value = true;

  try {
    const data = {
      employe_id: form.value.employe_id,
      mois: form.value.mois,
      note: form.value.note,
      commentaire: form.value.commentaire || null
    };

    await performanceService.create(data);

    // Redirection avec message de succès
    router.push({
      path: '/performances',
      query: { success: 'Évaluation enregistrée avec succès' }
    });
  } catch (error) {
    submitting.value = false;

    if (error.response && error.response.data) {
      const data = error.response.data;

      if (data.errors) {
        errors.value = data.errors;
      }

      if (data.message) {
        errorMessage.value = data.message;
      }

      // Si une évaluation existe déjà, proposer de la modifier
      if (data.evaluation_existante) {
        if (confirm('Une évaluation existe déjà pour cet employé ce mois. Voulez-vous la consulter ?')) {
          router.push('/performances');
        }
      }
    } else {
      errorMessage.value = 'Une erreur est survenue lors de l\'enregistrement';
    }

    console.error('Erreur lors de l\'enregistrement:', error);
  }
};

// Au montage
onMounted(() => {
  loadEmployes();
});
</script>
