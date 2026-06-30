import { ref, onMounted } from 'vue';

const supported = ref(false);
const permissionState = ref('default');

export function useReminder() {
  const swReady = ref(false);

  onMounted(async () => {
    supported.value = 'Notification' in window && 'serviceWorker' in navigator;
    if (supported.value) {
      permissionState.value = Notification.permission;

      try {
        const reg = await navigator.serviceWorker.ready;
        swReady.value = true;
      } catch {
        swReady.value = false;
      }
    }
  });

  async function requestPermission() {
    if (!supported.value) return false;
    const result = await Notification.requestPermission();
    permissionState.value = result;
    return result === 'granted';
  }

  async function scheduleReminders(times) {
    if (!supported.value || !swReady.value) return false;

    const reg = await navigator.serviceWorker.ready;
    reg.active.postMessage({ type: 'SCHEDULE_REMINDERS', times });
    return true;
  }

  return {
    supported,
    permissionState,
    swReady,
    requestPermission,
    scheduleReminders,
  };
}
