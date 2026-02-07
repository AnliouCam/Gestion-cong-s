<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center">
          <router-link to="/paies" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </router-link>
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Enregistrer un Paiement</h1>
            <p class="mt-1 text-sm text-gray-500">Enregistrer le paiement mensuel d'un employé</p>
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

              <!-- Mois de paiement -->
              <div class="sm:col-span-2">
                <label for="mois" class="block text-sm font-medium text-gray-700">
                  Mois du paiement <span class="text-red-500">*</span>
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

          <!-- Détails du paiement -->
          <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Détails du paiement</h3>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <!-- Salaire de base -->
              <div class="sm:col-span-2">
                <label for="salaire_base" class="block text-sm font-medium text-gray-700">
                  Salaire de base <span class="text-red-500">*</span>
                </label>
                <div class="mt-1 relative rounded-lg shadow-sm">
                  <input
                    id="salaire_base"
                    v-model.number="form.salaire_base"
                    type="number"
                    step="0.01"
                    min="0"
                    required
                    class="block w-full px-4 py-2 pr-20 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    :class="{ 'border-red-300': errors.salaire_base, 'border-gray-300': !errors.salaire_base }"
                    @input="calculateMontantTotal"
                  />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">FCFA</span>
                  </div>
                </div>
                <p v-if="errors.salaire_base" class="mt-1 text-sm text-red-600">{{ errors.salaire_base[0] }}</p>
                <p v-if="employeSelectionne" class="mt-1 text-xs text-gray-500">
                  Salaire mensuel de l'employé : {{ formatMontant(employeSelectionne.salaire_mensuel) }}
                </p>
              </div>

              <!-- Primes -->
              <div>
                <label for="primes" class="block text-sm font-medium text-gray-700">
                  Primes (optionnel)
                </label>
                <div class="mt-1 relative rounded-lg shadow-sm">
                  <input
                    id="primes"
                    v-model.number="form.primes"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0"
                    class="block w-full px-4 py-2 pr-20 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    @input="calculateMontantTotal"
                  />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">FCFA</span>
                  </div>
                </div>
              </div>

              <!-- Retenues -->
              <div>
                <label for="retenues" class="block text-sm font-medium text-gray-700">
                  Retenues (optionnel)
                </label>
                <div class="mt-1 relative rounded-lg shadow-sm">
                  <input
                    id="retenues"
                    v-model.number="form.retenues"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0"
                    class="block w-full px-4 py-2 pr-20 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    @input="calculateMontantTotal"
                  />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">FCFA</span>
                  </div>
                </div>
              </div>

              <!-- Montant total calculé -->
              <div class="sm:col-span-2 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                <p class="text-sm font-medium text-indigo-700 mb-2">Calcul du montant total</p>
                <div class="text-sm text-indigo-600 space-y-1">
                  <div class="flex justify-between">
                    <span>Salaire de base :</span>
                    <span class="font-semibold">{{ formatMontant(form.salaire_base || 0) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>+ Primes :</span>
                    <span class="font-semibold">{{ formatMontant(form.primes || 0) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>- Retenues :</span>
                    <span class="font-semibold">{{ formatMontant(form.retenues || 0) }}</span>
                  </div>
                  <div class="border-t border-indigo-300 mt-2 pt-2 flex justify-between text-base font-bold text-indigo-900">
                    <span>MONTANT TOTAL :</span>
                    <span>{{ formatMontant(montantTotal) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Informations de paiement -->
          <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de paiement</h3>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <!-- Mode de paiement -->
              <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                  Mode de paiement <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-4">
                  <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-indigo-600 ring-2 ring-indigo-600 bg-indigo-50': form.mode_paiement === 'cash', 'border-gray-300': form.mode_paiement !== 'cash' }">
                    <input
                      type="radio"
                      v-model="form.mode_paiement"
                      value="cash"
                      class="sr-only"
                    />
                    <div class="flex-1">
                      <span class="block text-sm font-medium text-gray-900">Cash</span>
                      <span class="block text-sm text-gray-500">Paiement en espèces</span>
                    </div>
                  </label>
                  <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{ 'border-indigo-600 ring-2 ring-indigo-600 bg-indigo-50': form.mode_paiement === 'virement', 'border-gray-300': form.mode_paiement !== 'virement' }">
                    <input
                      type="radio"
                      v-model="form.mode_paiement"
                      value="virement"
                      class="sr-only"
                    />
                    <div class="flex-1">
                      <span class="block text-sm font-medium text-gray-900">Virement</span>
                      <span class="block text-sm text-gray-500">Virement bancaire</span>
                    </div>
                  </label>
                </div>
                <p v-if="errors.mode_paiement" class="mt-2 text-sm text-red-600">{{ errors.mode_paiement[0] }}</p>
              </div>

              <!-- Date de paiement -->
              <div>
                <label for="date_paiement" class="block text-sm font-medium text-gray-700">
                  Date de paiement
                </label>
                <input
                  id="date_paiement"
                  v-model="form.date_paiement"
                  type="date"
                  class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                />
                <p class="mt-1 text-xs text-gray-500">
                  Si renseignée, le statut sera "Payé". Sinon "Non payé".
                </p>
              </div>

              <!-- Référence de paiement -->
              <div>
                <label for="reference_paiement" class="block text-sm font-medium text-gray-700">
                  Référence (optionnel)
                </label>
                <input
                  id="reference_paiement"
                  v-model="form.reference_paiement"
                  type="text"
                  placeholder="N° virement, reçu..."
                  maxlength="255"
                  class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                />
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
                  maxlength="500"
                  placeholder="Notes, remarques..."
                  class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Boutons d'action -->
          <div class="flex justify-end space-x-4">
            <router-link
              to="/paies"
              class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              Annuler
            </router-link>
            <button
              type="submit"
              :disabled="submitting"
              class="px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="!submitting">Enregistrer le paiement</span>
              <span v-else>Enregistrement...</span>
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
import paieService from '../../services/paieService';
import employeService from '../../services/employeService';

const router = useRouter();

// État
const employes = ref([]);
const employeSelectionne = ref(null);
const errors = ref({});
const errorMessage = ref('');
const submitting = ref(false);

// Formulaire
const form = ref({
  employe_id: '',
  mois: new Date().toISOString().slice(0, 7), // Mois en cours par défaut (YYYY-MM)
  salaire_base: 0,
  primes: 0,
  retenues: 0,
  mode_paiement: 'cash',
  date_paiement: '',
  reference_paiement: '',
  commentaire: ''
});

// Calcul du montant total en temps réel
const montantTotal = computed(() => {
  const base = form.value.salaire_base || 0;
  const primes = form.value.primes || 0;
  const retenues = form.value.retenues || 0;
  return base + primes - retenues;
});

// Charger la liste des employés
const loadEmployes = async () => {
  try {
    const response = await employeService.getAll();
    employes.value = response.data.data;
  } catch (error) {
    console.error('Erreur lors du chargement des employés:', error);
    errorMessage.value = 'Erreur lors du chargement des employés';
  }
};

// Quand un employé est sélectionné
const onEmployeChange = () => {
  const employeId = form.value.employe_id;
  if (employeId) {
    employeSelectionne.value = employes.value.find(e => e.id == employeId);
    // Pré-remplir le salaire de base avec le salaire mensuel de l'employé
    if (employeSelectionne.value) {
      form.value.salaire_base = employeSelectionne.value.salaire_mensuel || 0;
    }
  } else {
    employeSelectionne.value = null;
    form.value.salaire_base = 0;
  }
  calculateMontantTotal();
};

// Recalculer le montant total
const calculateMontantTotal = () => {
  // Le montant total est calculé automatiquement via computed property
  // Cette fonction existe pour être appelée lors des changements de champs
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

// Soumettre le formulaire
const handleSubmit = async () => {
  errors.value = {};
  errorMessage.value = '';
  submitting.value = true;

  try {
    // Préparer les données (enlever les valeurs vides pour date_paiement et reference_paiement)
    const data = {
      employe_id: form.value.employe_id,
      mois: form.value.mois,
      salaire_base: form.value.salaire_base,
      primes: form.value.primes || 0,
      retenues: form.value.retenues || 0,
      mode_paiement: form.value.mode_paiement,
      date_paiement: form.value.date_paiement || null,
      reference_paiement: form.value.reference_paiement || null,
      commentaire: form.value.commentaire || null
    };

    await paieService.create(data);

    // Redirection avec message de succès
    router.push({
      path: '/paies',
      query: { success: 'Paiement enregistré avec succès' }
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
