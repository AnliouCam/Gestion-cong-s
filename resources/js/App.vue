<!--
  App.vue
  Composant racine de l'application Vue.js

  Fonctionnalités :
  - RouterView : affiche le composant de la route actuelle (Login ou Dashboard)
  - ToastContainer : conteneur global pour les notifications toast
-->

<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- RouterView affiche le composant correspondant à la route actuelle -->
    <RouterView />

    <!-- Conteneur de toasts (notifications) -->
    <ToastContainer />
  </div>
</template>

<script setup>
import ToastContainer from './components/ToastContainer.vue';
</script>
