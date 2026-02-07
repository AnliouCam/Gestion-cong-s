<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <router-link to="/conges" class="mr-4 text-gray-400 hover:text-gray-600">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </router-link>
            <div v-if="conge">
              <h1 class="text-3xl font-bold text-gray-900">Demande de Congé</h1>
              <p class="mt-1 text-sm text-gray-500">
                {{ conge.employe.prenom }} {{ conge.employe.nom }} -
                <span :class="getStatutBadgeClass(conge.statut)" class="px-2 py-1 text-xs font-semibold rounded-full ml-2">
                  {{ getStatutLabel(conge.statut) }}
                </span>
              </p>
            </div>
          </div>
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
      <div v-else-if="!conge" class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
        <p class="text-sm text-red-700">Demande de congé non trouvée</p>
      </div>

      <!-- Détail du congé -->
      <div v-else class="space-y-6">
        <!-- Informations employé -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Informations employé</h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h4 class="text-sm font-medium text-gray-500">Nom complet</h4>
                <p class="mt-1 text-lg text-gray-900">{{ conge.employe.prenom }} {{ conge.employe.nom }}</p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-500">Équipe</h4>
                <p class="mt-1 text-lg text-gray-900">{{ conge.employe.equipe?.nom || 'Sans équipe' }}</p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-500">Poste</h4>
                <p class="mt-1 text-lg text-gray-900">{{ conge.employe.poste }}</p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-500">Solde congés annuel actuel</h4>
                <p class="mt-1 text-lg font-bold text-indigo-600">{{ conge.employe.solde_conges_annuel }} jours</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Détails du congé -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Détails du congé</h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h4 class="text-sm font-medium text-gray-500">Type de congé</h4>
                <span :class="getTypeBadgeClass(conge.type)" class="mt-1 inline-flex px-3 py-1 text-sm font-semibold rounded-full">
                  {{ getTypeLabel(conge.type) }}
                </span>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-500">Statut</h4>
                <span :class="getStatutBadgeClass(conge.statut)" class="mt-1 inline-flex px-3 py-1 text-sm font-semibold rounded-full">
                  {{ getStatutLabel(conge.statut) }}
                </span>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-500">Date de début</h4>
                <p class="mt-1 text-lg text-gray-900">{{ formatDate(conge.date_debut) }}</p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-500">Date de fin</h4>
                <p class="mt-1 text-lg text-gray-900">{{ formatDate(conge.date_fin) }}</p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-500">Nombre de jours</h4>
                <p class="mt-1 text-lg font-bold text-gray-900">{{ conge.nb_jours }} jour(s)</p>
              </div>
              <div v-if="conge.commentaire">
                <h4 class="text-sm font-medium text-gray-500">Commentaire</h4>
                <p class="mt-1 text-sm text-gray-700">{{ conge.commentaire }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Informations de validation (si déjà traité) -->
        <div v-if="conge.statut !== 'en_attente' && conge.valide_par" class="bg-white shadow rounded-lg overflow-hidden">
          <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Informations de validation</h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h4 class="text-sm font-medium text-gray-500">Validé/Refusé par</h4>
                <p class="mt-1 text-lg text-gray-900">{{ conge.valide_par.name }}</p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-500">Date de validation</h4>
                <p class="mt-1 text-lg text-gray-900">{{ formatDateTime(conge.date_validation) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions (si en attente) -->
        <div v-if="conge.statut === 'en_attente'" class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
          <div class="flex space-x-4">
            <button
              @click="validerDirectement"
              :disabled="loadingAction"
              class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
            >
              <svg v-if="!loadingAction" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              {{ loadingAction ? 'Validation...' : 'Valider la demande' }}
            </button>
            <button
              @click="refuserDirectement"
              :disabled="loadingAction"
              class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
            >
              <svg v-if="!loadingAction" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              {{ loadingAction ? 'Refus...' : 'Refuser la demande' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import congeService from '../../services/congeService';

const route = useRoute();
const router = useRouter();

// État
const conge = ref(null);
const loading = ref(true);
const loadingAction = ref(false);

// Charger le congé
const loadConge = async () => {
  try {
    const response = await congeService.getById(route.params.id);
    conge.value = response.data.data;
  } catch (error) {
    console.error('Erreur:', error);
  } finally {
    loading.value = false;
  }
};

// Valider directement (sans modal)
const validerDirectement = async () => {
  if (!confirm('Confirmer la validation de ce congé ?')) return;

  loadingAction.value = true;
  console.log('Tentative de validation du congé', conge.value.id);

  try {
    const response = await congeService.valider(conge.value.id);
    console.log('Réponse validation:', response.data);

    // Rediriger avec message de succès
    router.push({
      path: '/conges',
      state: { success: 'Congé validé avec succès' }
    });
  } catch (error) {
    console.error('Erreur complète:', error);
    console.error('Réponse erreur:', error.response);
    const message = error.response?.data?.message || error.message || 'Erreur lors de la validation';
    alert('Erreur: ' + message);
  } finally {
    loadingAction.value = false;
  }
};

// Refuser directement (sans modal)
const refuserDirectement = async () => {
  const raison = prompt('Raison du refus (optionnel):');
  if (raison === null) return; // Annulé

  loadingAction.value = true;
  console.log('Tentative de refus du congé', conge.value.id);

  try {
    const response = await congeService.refuser(conge.value.id, { commentaire: raison });
    console.log('Réponse refus:', response.data);

    // Rediriger avec message de succès
    router.push({
      path: '/conges',
      state: { success: 'Congé refusé' }
    });
  } catch (error) {
    console.error('Erreur complète:', error);
    console.error('Réponse erreur:', error.response);
    const message = error.response?.data?.message || error.message || 'Erreur lors du refus';
    alert('Erreur: ' + message);
  } finally {
    loadingAction.value = false;
  }
};

// Formatage de la date
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' });
};

// Formatage date et heure
const formatDateTime = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
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
    'annuel': 'Congé annuel',
    'maladie': 'Congé maladie',
    'exceptionnel': 'Congé exceptionnel'
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
  loadConge();
});
</script>
