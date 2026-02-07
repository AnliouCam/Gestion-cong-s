<!--
  Layout.vue
  Composant de mise en page réutilisable avec sidebar

  Fonctionnalités :
  - Sidebar responsive (hamburger sur mobile, fixe sur desktop)
  - Navigation complète vers toutes les sections
  - Bouton de déconnexion
  - Slot pour le contenu de la page
  - Backdrop sur mobile quand sidebar ouverte
  - Gestion automatique de l'item actif du menu
-->

<template>
  <div class="flex h-screen bg-gray-100">
    <!-- BACKDROP (overlay sombre sur mobile quand sidebar ouverte) -->
    <div
      v-if="sidebarOpen"
      @click="closeSidebar"
      class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
    ></div>

    <!-- SIDEBAR (Menu latéral) -->
    <aside
      :class="[
        'fixed md:static inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
      ]"
    >
      <!-- Header Sidebar -->
      <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-xl font-bold text-gray-900">Gest' Congés</h1>
            <p class="text-sm text-gray-600 mt-1">Gestion Employés</p>
          </div>
          <!-- Bouton fermer (visible uniquement sur mobile) -->
          <button
            @click="closeSidebar"
            class="md:hidden text-gray-500 hover:text-gray-700"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="p-4">
        <ul class="space-y-2">
          <!-- Dashboard -->
          <li>
            <router-link
              to="/dashboard"
              @click="closeSidebar"
              class="flex items-center px-4 py-3 rounded-lg transition"
              :class="isActive('/dashboard') ? 'text-gray-700 bg-blue-50 font-medium' : 'text-gray-600 hover:bg-gray-50'"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
              </svg>
              Tableau de bord
            </router-link>
          </li>

          <!-- Équipes -->
          <li>
            <router-link
              to="/equipes"
              @click="closeSidebar"
              class="flex items-center px-4 py-3 rounded-lg transition"
              :class="isActive('/equipes') ? 'text-gray-700 bg-blue-50 font-medium' : 'text-gray-600 hover:bg-gray-50'"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              Équipes
            </router-link>
          </li>

          <!-- Employés -->
          <li>
            <router-link
              to="/employes"
              @click="closeSidebar"
              class="flex items-center px-4 py-3 rounded-lg transition"
              :class="isActive('/employes') ? 'text-gray-700 bg-blue-50 font-medium' : 'text-gray-600 hover:bg-gray-50'"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
              Employés
            </router-link>
          </li>

          <!-- Congés -->
          <li>
            <router-link
              to="/conges"
              @click="closeSidebar"
              class="flex items-center px-4 py-3 rounded-lg transition"
              :class="isActive('/conges') ? 'text-gray-700 bg-blue-50 font-medium' : 'text-gray-600 hover:bg-gray-50'"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              Congés
            </router-link>
          </li>

          <!-- Paies -->
          <li>
            <router-link
              to="/paies"
              @click="closeSidebar"
              class="flex items-center px-4 py-3 rounded-lg transition"
              :class="isActive('/paies') ? 'text-gray-700 bg-blue-50 font-medium' : 'text-gray-600 hover:bg-gray-50'"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Paies
            </router-link>
          </li>

          <!-- Performances -->
          <li>
            <router-link
              to="/performances"
              @click="closeSidebar"
              class="flex items-center px-4 py-3 rounded-lg transition"
              :class="isActive('/performances') ? 'text-gray-700 bg-blue-50 font-medium' : 'text-gray-600 hover:bg-gray-50'"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
              Performances
            </router-link>
          </li>
        </ul>
      </nav>

      <!-- Déconnexion (en bas du sidebar) -->
      <div class="absolute bottom-0 w-64 p-4 border-t border-gray-200 bg-white">
        <button
          @click="handleLogout"
          class="flex items-center w-full px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition font-medium"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          Déconnexion
        </button>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 overflow-y-auto">
      <!-- Header avec hamburger sur mobile -->
      <header class="bg-white shadow-sm md:hidden">
        <div class="px-4 py-4 flex items-center">
          <!-- Bouton hamburger (visible uniquement sur mobile) -->
          <button
            @click="toggleSidebar"
            class="mr-4 text-gray-600 hover:text-gray-900 focus:outline-none"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>

          <div>
            <h1 class="text-lg font-bold text-gray-900">Gest' Congés</h1>
          </div>
        </div>
      </header>

      <!-- Contenu de la page (slot) -->
      <slot></slot>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

// État
const sidebarOpen = ref(false);

// Toggle sidebar (ouvrir/fermer)
function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value;
}

// Fermer sidebar
function closeSidebar() {
  sidebarOpen.value = false;
}

// Vérifier si un menu est actif
function isActive(path) {
  return route.path.startsWith(path);
}

// Déconnexion
async function handleLogout() {
  if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
    await authStore.logout();
    router.push({ name: 'Login' });
  }
}
</script>
