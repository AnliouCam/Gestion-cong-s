<!--
  ConfirmModal.vue
  Modal de confirmation réutilisable pour les actions destructives

  Fonctionnalités :
  - Design moderne avec icône d'avertissement
  - Backdrop semi-transparent
  - Animation d'apparition/disparition (fade + scale)
  - Boutons Annuler (gris) et Confirmer (rouge pour suppressions)
  - Fermeture sur Echap
  - Focus automatique sur bouton Annuler
  - Props personnalisables (titre, message, texte boutons, type)
-->

<template>
  <!-- Backdrop -->
  <Transition name="fade">
    <div
      v-if="show"
      @click="cancel"
      class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
    >
      <!-- Modal -->
      <Transition name="scale">
        <div
          v-if="show"
          @click.stop
          class="bg-white rounded-lg shadow-xl max-w-md w-full p-6"
          role="dialog"
          aria-modal="true"
        >
          <!-- Icône + Titre -->
          <div class="flex items-start">
            <!-- Icône -->
            <div
              :class="[
                'flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full',
                type === 'danger' ? 'bg-red-100' : 'bg-yellow-100'
              ]"
            >
              <svg
                class="h-6 w-6"
                :class="type === 'danger' ? 'text-red-600' : 'text-yellow-600'"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                />
              </svg>
            </div>

            <!-- Titre + Message -->
            <div class="ml-4 flex-1">
              <h3 class="text-lg font-medium text-gray-900">
                {{ title }}
              </h3>
              <div class="mt-2 text-sm text-gray-500">
                {{ message }}
              </div>
            </div>
          </div>

          <!-- Boutons d'action -->
          <div class="mt-6 flex justify-end space-x-3">
            <!-- Bouton Annuler -->
            <button
              ref="cancelButton"
              @click="cancel"
              type="button"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition"
            >
              {{ cancelText }}
            </button>

            <!-- Bouton Confirmer -->
            <button
              @click="confirm"
              :disabled="loading"
              type="button"
              :class="[
                'px-4 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition',
                type === 'danger'
                  ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
                  : 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
                loading ? 'opacity-50 cursor-not-allowed' : ''
              ]"
            >
              <span v-if="!loading">{{ confirmText }}</span>
              <span v-else class="flex items-center">
                <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ loadingText }}
              </span>
            </button>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue';

// Props
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Confirmer la suppression'
  },
  message: {
    type: String,
    default: 'Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.'
  },
  confirmText: {
    type: String,
    default: 'Supprimer'
  },
  cancelText: {
    type: String,
    default: 'Annuler'
  },
  loadingText: {
    type: String,
    default: 'Suppression...'
  },
  type: {
    type: String,
    default: 'danger', // danger | warning
    validator: (value) => ['danger', 'warning'].includes(value)
  },
  loading: {
    type: Boolean,
    default: false
  }
});

// Emits
const emit = defineEmits(['confirm', 'cancel', 'update:show']);

// Refs
const cancelButton = ref(null);

// Méthodes
const confirm = () => {
  if (!props.loading) {
    emit('confirm');
  }
};

const cancel = () => {
  if (!props.loading) {
    emit('cancel');
    emit('update:show', false);
  }
};

// Focus sur bouton Annuler quand modal s'ouvre
watch(() => props.show, async (newValue) => {
  if (newValue) {
    await nextTick();
    cancelButton.value?.focus();
  }
});

// Fermer sur Echap
const handleKeydown = (e) => {
  if (e.key === 'Escape' && props.show && !props.loading) {
    cancel();
  }
};

// Écouter les touches clavier
if (typeof window !== 'undefined') {
  window.addEventListener('keydown', handleKeydown);
}
</script>

<style scoped>
/* Animation fade pour backdrop */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Animation scale pour modal */
.scale-enter-active,
.scale-leave-active {
  transition: all 0.2s ease;
}

.scale-enter-from,
.scale-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
