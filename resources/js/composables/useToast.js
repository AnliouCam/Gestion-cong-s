/**
 * useToast - Composable pour gérer les notifications toast
 *
 * Utilisation :
 * import { useToast } from '@/composables/useToast'
 * const toast = useToast()
 *
 * toast.success('Opération réussie !')
 * toast.error('Une erreur est survenue')
 * toast.info('Information importante')
 */

import { ref } from 'vue';

// État global des toasts (partagé entre toutes les instances)
const toasts = ref([]);
let toastId = 0;

export function useToast() {
  /**
   * Ajouter un toast
   * @param {string} message - Message à afficher
   * @param {string} type - Type de toast (success, error, info)
   * @param {number} duration - Durée en ms (0 = ne se ferme pas auto)
   */
  const addToast = (message, type = 'info', duration = 3000) => {
    const id = ++toastId;
    toasts.value.push({
      id,
      message,
      type,
      duration
    });
    return id;
  };

  /**
   * Supprimer un toast
   * @param {number} id - ID du toast à supprimer
   */
  const removeToast = (id) => {
    const index = toasts.value.findIndex(t => t.id === id);
    if (index !== -1) {
      toasts.value.splice(index, 1);
    }
  };

  /**
   * Toast de succès
   * @param {string} message - Message de succès
   * @param {number} duration - Durée en ms
   */
  const success = (message, duration = 3000) => {
    return addToast(message, 'success', duration);
  };

  /**
   * Toast d'erreur
   * @param {string} message - Message d'erreur
   * @param {number} duration - Durée en ms
   */
  const error = (message, duration = 4000) => {
    return addToast(message, 'error', duration);
  };

  /**
   * Toast d'information
   * @param {string} message - Message d'info
   * @param {number} duration - Durée en ms
   */
  const info = (message, duration = 3000) => {
    return addToast(message, 'info', duration);
  };

  /**
   * Effacer tous les toasts
   */
  const clearAll = () => {
    toasts.value = [];
  };

  return {
    toasts,
    success,
    error,
    info,
    addToast,
    removeToast,
    clearAll
  };
}
