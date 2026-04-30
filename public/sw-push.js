// Kinhold service-worker push handlers.
//
// Imported into the workbox-generated SW via vite-plugin-pwa's
// workbox.importScripts option (see vite.config.js). This file owns ONLY
// push and notificationclick — workbox owns precaching + runtime caching.
//
// laravel-notification-channels/webpush ships JSON shaped like:
//   { title, body, icon, badge, tag, data, actions, ... }
// (see vendor/.../WebPushMessage::toArray) — we mirror that shape here.

self.addEventListener('push', (event) => {
  if (!event.data) {
    return
  }

  let payload
  try {
    payload = event.data.json()
  } catch (e) {
    payload = { title: 'Kinhold', body: event.data.text() }
  }

  const title = payload.title || 'Kinhold'
  const options = {
    body: payload.body || '',
    icon: payload.icon || '/icons/icon-192.png',
    badge: payload.badge || '/icons/badge-96.png',
    tag: payload.tag,
    data: payload.data || {},
    actions: payload.actions || [],
    requireInteraction: payload.requireInteraction || false,
    silent: payload.silent || false,
  }

  // dir, image, lang, vibrate, renotify — all native Notification options;
  // copy through if present so registry entries can opt in without changing
  // the SW.
  for (const key of ['dir', 'image', 'lang', 'vibrate', 'renotify']) {
    if (payload[key] !== undefined) options[key] = payload[key]
  }

  event.waitUntil(self.registration.showNotification(title, options))
})

self.addEventListener('notificationclick', (event) => {
  event.notification.close()

  const targetUrl = (event.notification.data && event.notification.data.url) || '/'
  const absoluteUrl = new URL(targetUrl, self.location.origin).href

  event.waitUntil(
    self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clients) => {
      // Prefer focusing an existing tab on the same origin and navigating it.
      for (const client of clients) {
        if (client.url.startsWith(self.location.origin)) {
          return client.focus().then(() => {
            if ('navigate' in client) {
              return client.navigate(absoluteUrl)
            }
            // Fallback: postMessage the URL so the SPA router can handle it.
            client.postMessage({ type: 'kinhold:navigate', url: targetUrl })
          })
        }
      }
      // No existing tab — open a new one.
      return self.clients.openWindow(absoluteUrl)
    }),
  )
})
