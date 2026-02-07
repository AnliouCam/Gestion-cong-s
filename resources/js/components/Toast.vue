<!--
  Toast.vue
  Composant de notification toast réutilisable

  Fonctionnalités :
  - 3 types : success (vert), error (rouge), info (bleu)
  - Position : coin supérieur droit
  - Animation d'apparition depuis le haut
  - Disparition automatique après délai
  - Icône selon le type
  - Fermeture manuelle
  - Plusieurs toasts empilables
-->

<template>
  <Transition name="slide-fade">
    <div
      v-if="visible"
      :class="[
        'fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 overflow-hidden',
        type === 'success' ? 'border-green-500' : type === 'error' ? 'border-red-500' : 'border-blue-500'
      ]"
      role="alert"
    >
      <div class="p-4 flex items-start">
        <!-- Icône -->
        <div class="flex-shrink-0">
          <!-- Success -->
          <svg
            v-if="type === 'success'"
            class="h-6 w-6 text-green-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>

          <!-- Error -->
          <svg
            v-else-if="type === 'error'"
            class="h-6 w-6 text-red-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>

          <!-- Info -->
          <svg
            v-else
            class="h-6 w-6 text-blue-500"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
        </div>

        <!-- Message -->
        <div class="ml-3 flex-1">
          <p
            :class="[
              'text-sm font-medium',
              type === 'success' ? 'text-green-900' : type === 'error' ? 'text-red-900' : 'text-blue-900'
            ]"
          >
            {{ message }}
          </p>
        </div>

        <!-- Bouton fermer -->
        <button
          @click="close"
          type="button"
          class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500 focus:outline-none transition"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Barre de progression (optionnel) -->
      <div
        v-if="showProgress"
        class="h-1 bg-gray-200"
      >
        <div
          :class="[
            'h-full transition-all ease-linear',
            type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
          ]"
          :style="{ width: progressWidth + '%' }"
        ></div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

// Props
const props = defineProps({
  message: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'info', // success | error | info
    validator: (value) => ['success', 'error', 'info'].includes(value)
  },
  duration: {
    type: Number,
    default: 3000 // Durée en ms avant disparition auto
  },
  showProgress: {
    type: Boolean,
    default: false // Afficher la barre de progression
  }
});

// Emits
const emit = defineEmits(['close']);

// État
const visible = ref(false);
const progressWidth = ref(100);
let timer = null;
let progressTimer = null;

// Méthodes
const close = () => {
  visible.value = false;
  clearTimers();
  setTimeout(() => {
    emit('close');
  }, 200); // Attend la fin de l'animation
};

const clearTimers = () => {
  if (timer) clearTimeout(timer);
  if (progressTimer) clearInterval(progressTimer);
};

// Apparition et disparition auto
onMounted(() => {
  // Apparition
  visible.value = true;

  // Disparition automatique
  if (props.duration > 0) {
    timer = setTimeout(() => {
      close();
    }, props.duration);

    // Barre de progression
    if (props.showProgress) {
      const interval = 50; // Mise à jour toutes les 50ms
      const step = (interval / props.duration) * 100;
      progressTimer = setInterval(() => {
        progressWidth.value = Math.max(0, progressWidth.value - step);
      }, interval);
    }
  }
});

onUnmounted(() => {
  clearTimers();
});
</script>

<style scoped>
/* Animation slide + fade depuis le haut */
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.2s ease-in;
}

.slide-fade-enter-from {
  transform: translateY(-20px);
  opacity: 0;
}

.slide-fade-leave-to {
  transform: translateY(-10px);
  opacity: 0;
}
</style>
