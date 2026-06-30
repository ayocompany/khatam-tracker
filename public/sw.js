const CACHE = 'quran-tracker-v1';

self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE).then((cache) => cache.addAll([
      '/',
      '/manifest.webmanifest',
    ]))
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(clients.claim());
});

self.addEventListener('message', (event) => {
  if (event.data?.type === 'SCHEDULE_REMINDERS') {
    const times = event.data.times;
    // Store in SW's global state
    self.__reminderTimes = times;
    self.__lastNotified = self.__lastNotified || {};
    scheduleNextCheck();
  }
});

function scheduleNextCheck() {
  if (self.__checkInterval) clearInterval(self.__checkInterval);
  self.__checkInterval = setInterval(checkReminders, 30_000);
  checkReminders();
}

function checkReminders() {
  const times = self.__reminderTimes;
  if (!times || times.length === 0) return;

  const now = new Date();
  const currentMinutes = now.getHours() * 60 + now.getMinutes();

  for (const time of times) {
    const [h, m] = time.split(':').map(Number);
    const targetMinutes = h * 60 + m;
    const diff = Math.abs(currentMinutes - targetMinutes);

    // Notify if within 1 minute window and not notified in last 24h for this time
    const lastKey = `${time}`;
    const lastNotified = self.__lastNotified?.[lastKey];
    const todayStr = now.toDateString();

    if (diff <= 1 && lastNotified !== todayStr) {
      self.__lastNotified = self.__lastNotified || {};
      self.__lastNotified[lastKey] = todayStr;

      self.registration.showNotification('📖 Waktunya Baca Al-Qur\'an', {
        body: 'Jangan lupa tilawah hari ini! Sedikit tapi rutin, insyaAllah berkah.',
        icon: '/icons/pwa-192x192.png',
        badge: '/icons/pwa-192x192.png',
        tag: `quran-reminder-${time}`,
        vibrate: [200, 100, 200],
        requireInteraction: true,
        actions: [
          { action: 'open', title: 'Buka Aplikasi' },
          { action: 'done', title: 'Sudah Baca' },
        ],
      });
    }
  }
}

self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'done') {
    // Could log, for now just close
    return;
  }

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      if (clientList.length > 0) {
        clientList[0].focus();
      } else {
        clients.openWindow('/');
      }
    })
  );
});
