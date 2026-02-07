<!--
  Login.vue
  Page de connexion pour la direction

  Fonctionnalités :
  - Formulaire email + password
  - Validation côté client
  - Appel API login
  - Gestion des erreurs
  - Redirection automatique vers dashboard si succès
-->

<template>
  <div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="w-full max-w-md">
      <!-- Card de login -->
      <div class="bg-white rounded-2xl shadow-xl p-8">
        <!-- Logo / Titre -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Gestion Employés</h1>
          <p class="text-gray-600 mt-2">Connexion Direction</p>
        </div>

        <!-- Formulaire -->
        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- Champ Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
              placeholder="direction@entreprise.com"
              :disabled="loading"
            />
          </div>

          <!-- Champ Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Mot de passe
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
              placeholder="••••••••"
              :disabled="loading"
            />
          </div>

          <!-- Message d'erreur -->
          <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <p class="text-sm">{{ error }}</p>
          </div>

          <!-- Bouton de connexion -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="!loading">Se connecter</span>
            <span v-else class="flex items-center justify-center">
              <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Connexion en cours...
            </span>
          </button>
        </form>

        <!-- Footer -->
        <div class="mt-6 text-center text-sm text-gray-500">
          <p>Système de gestion interne</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

/**
 * COMPOSANTS RÉACTIFS
 */

const router = useRouter();
const authStore = useAuthStore();

// Données du formulaire
const form = ref({
  email: '',
  password: '',
});

// État du formulaire
const loading = ref(false);
const error = ref(null);

/**
 * MÉTHODES
 */

/**
 * Gère la soumission du formulaire de login
 */
async function handleLogin() {
  // Réinitialisation de l'erreur
  error.value = null;
  loading.value = true;

  try {
    // Appel à l'action login du store
    await authStore.login(form.value);

    // Si succès, redirection vers dashboard
    router.push({ name: 'Dashboard' });
  } catch (err) {
    // Gestion des erreurs
    if (err.response && err.response.data) {
      // Erreur de validation Laravel
      error.value = err.response.data.message || 'Identifiants incorrects';
    } else {
      // Erreur réseau ou autre
      error.value = 'Une erreur est survenue. Veuillez réessayer.';
    }
  } finally {
    loading.value = false;
  }
}
</script>
