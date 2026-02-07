<!--
  ToastContainer.vue
  Conteneur pour afficher tous les toasts empilés

  Fonctionnalités :
  - Affiche tous les toasts dans le coin supérieur droit
  - Empile les toasts verticalement
  - Gère automatiquement les animations
-->

<template>
  <div class="fixed top-4 right-4 z-50 space-y-3 pointer-events-none">
    <div
      v-for="toast in toasts"
      :key="toast.id"
      class="pointer-events-auto"
    >
      <Toast
        :message="toast.message"
        :type="toast.type"
        :duration="toast.duration"
        :show-progress="true"
        @close="removeToast(toast.id)"
      />
    </div>
  </div>
</template>

<script setup>
import { useToast } from '../composables/useToast';
import Toast from './Toast.vue';

const { toasts, removeToast } = useToast();
</script>
