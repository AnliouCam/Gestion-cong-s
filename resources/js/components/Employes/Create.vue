<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center">
          <router-link
            to="/employes"
            class="mr-4 text-gray-400 hover:text-gray-600"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </router-link>
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Nouvel Employé</h1>
            <p class="mt-1 text-sm text-gray-500">
              Créez une nouvelle fiche employé
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
          <!-- Informations personnelles -->
          <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h3>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <!-- Nom -->
              <div>
                <label for="nom" class="block text-sm font-medium text-gray-700">
                  Nom <span class="text-red-500">*</span>
                </label>
                <input
                  id="nom"
                  v-model="form.nom"
                  type="text"
                  required
                  class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-300': errors.nom, 'border-gray-300': !errors.nom }"
                  placeholder="Ex: DIALLO"
                />
                <p v-if="errors.nom" class="mt-1 text-sm text-red-600">{{ errors.nom[0] }}</p>
              </div>

              <!-- Prénom -->
              <div>
                <label for="prenom" class="block text-sm font-medium text-gray-700">
                  Prénom <span class="text-red-500">*</span>
                </label>
                <input
                  id="prenom"
                  v-model="form.prenom"
                  type="text"
                  required
                  class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-300': errors.prenom, 'border-gray-300': !errors.prenom }"
                  placeholder="Ex: Mamadou"
                />
                <p v-if="errors.prenom" class="mt-1 text-sm text-red-600">{{ errors.prenom[0] }}</p>
              </div>

              <!-- Email -->
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                  Email
                </label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="exemple@email.com"
                />
                <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</p>
              </div>

              <!-- Téléphone -->
              <div>
                <label for="telephone" class="block text-sm font-medium text-gray-700">
                  Téléphone
                </label>
                <input
                  id="telephone"
                  v-model="form.telephone"
                  type="tel"
                  class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="Ex: +224 XXX XXX XXX"
                />
                <p v-if="errors.telephone" class="mt-1 text-sm text-red-600">{{ errors.telephone[0] }}</p>
              </div>
            </div>
          </div>

          <!-- Informations professionnelles -->
          <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations professionnelles</h3>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <!-- Poste -->
              <div>
                <label for="poste" class="block text-sm font-medium text-gray-700">
                  Poste <span class="text-red-500">*</span>
                </label>
                <input
                  id="poste"
                  v-model="form.poste"
                  type="text"
                  required
                  class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-300': errors.poste, 'border-gray-300': !errors.poste }"
                  placeholder="Ex: Maçon, Commercial, Assistant"
                />
                <p v-if="errors.poste" class="mt-1 text-sm text-red-600">{{ errors.poste[0] }}</p>
              </div>

              <!-- Équipe -->
              <div>
                <label for="equipe_id" class="block text-sm font-medium text-gray-700">
                  Équipe <span class="text-red-500">*</span>
                </label>
                <select
                  id="equipe_id"
                  v-model="form.equipe_id"
                  required
                  class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-300': errors.equipe_id, 'border-gray-300': !errors.equipe_id }"
                >
                  <option value="">Sélectionner une équipe</option>
                  <option v-for="equipe in equipes" :key="equipe.id" :value="equipe.id">
                    {{ equipe.nom }}
                  </option>
                </select>
                <p v-if="errors.equipe_id" class="mt-1 text-sm text-red-600">{{ errors.equipe_id[0] }}</p>
              </div>

              <!-- Salaire mensuel -->
              <div>
                <label for="salaire_mensuel" class="block text-sm font-medium text-gray-700">
                  Salaire mensuel (FCFA) <span class="text-red-500">*</span>
                </label>
                <input
                  id="salaire_mensuel"
                  v-model.number="form.salaire_mensuel"
                  type="number"
                  min="0"
                  step="1000"
                  required
                  class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-300': errors.salaire_mensuel, 'border-gray-300': !errors.salaire_mensuel }"
                  placeholder="Ex: 500000"
                />
                <p v-if="errors.salaire_mensuel" class="mt-1 text-sm text-red-600">{{ errors.salaire_mensuel[0] }}</p>
              </div>

              <!-- Date d'embauche -->
              <div>
                <label for="date_embauche" class="block text-sm font-medium text-gray-700">
                  Date d'embauche <span class="text-red-500">*</span>
                </label>
                <input
                  id="date_embauche"
                  v-model="form.date_embauche"
                  type="date"
                  required
                  class="mt-1 block w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-300': errors.date_embauche, 'border-gray-300': !errors.date_embauche }"
                />
                <p v-if="errors.date_embauche" class="mt-1 text-sm text-red-600">{{ errors.date_embauche[0] }}</p>
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
                  <option value="actif">Actif</option>
                  <option value="suspendu">Suspendu</option>
                  <option value="sorti">Sorti</option>
                </select>
              </div>

              <!-- Solde congés annuel -->
              <div>
                <label for="solde_conges_annuel" class="block text-sm font-medium text-gray-700">
                  Solde congés annuel (jours)
                </label>
                <input
                  id="solde_conges_annuel"
                  v-model.number="form.solde_conges_annuel"
                  type="number"
                  min="0"
                  max="365"
                  class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="30"
                />
                <p class="mt-1 text-sm text-gray-500">Par défaut : 30 jours</p>
              </div>
            </div>
          </div>

          <!-- Boutons d'action -->
          <div class="flex items-center justify-end space-x-4 pt-6">
            <router-link
              to="/employes"
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
              <span v-else>Créer l'employé</span>
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
import employeService from '../../services/employeService';
import equipeService from '../../services/equipeService';

const router = useRouter();

// État des équipes
const equipes = ref([]);

// État du formulaire
const form = ref({
  nom: '',
  prenom: '',
  email: '',
  telephone: '',
  poste: '',
  salaire_mensuel: '',
  date_embauche: new Date().toISOString().split('T')[0], // Date du jour par défaut
  statut: 'actif',
  solde_conges_annuel: 30,
  equipe_id: ''
});

const loading = ref(false);
const errors = ref({});
const errorMessage = ref('');

// Charger les équipes
const loadEquipes = async () => {
  try {
    const response = await equipeService.getAll();
    equipes.value = response.data.data.filter(e => e.statut === 'active');
  } catch (error) {
    console.error('Erreur lors du chargement des équipes:', error);
    errorMessage.value = 'Impossible de charger les équipes';
  }
};

// Soumettre le formulaire
const handleSubmit = async () => {
  loading.value = true;
  errors.value = {};
  errorMessage.value = '';

  try {
    await employeService.create(form.value);

    // Rediriger vers la liste avec un message de succès
    router.push({
      name: 'employes',
      state: { success: `L'employé "${form.value.prenom} ${form.value.nom}" a été créé avec succès` }
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

// Charger les équipes au montage
onMounted(() => {
  loadEquipes();
});
</script>
