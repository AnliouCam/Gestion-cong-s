/**
 * Fichier principal Vue.js
 * Configure et initialise l'application Vue avec Vue Router et Pinia
 */

import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';

// Création de l'instance Pinia (state management)
const pinia = createPinia();

// Création de l'application Vue
const app = createApp(App);

// Utilisation des plugins
app.use(pinia);  // Gestion d'état globale
app.use(router); // Navigation entre les pages

// Montage de l'application sur l'élément #app
app.mount('#app');
