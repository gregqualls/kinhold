// Web push subscription helpers (#69).
//
// Wraps the browser PushManager + Notification APIs. Reads the VAPID public
// key from the <meta name="vapid-public-key"> tag injected by app.blade.php
// — bypasses an extra /api/v1/config round-trip since the key is a build-time
// constant per deploy.

import api from './api'

export function isPushSupported() {
  return (
    typeof window !== 'undefined' &&
    'serviceWorker' in navigator &&
    'PushManager' in window &&
    'Notification' in window
  )
}

export function vapidPublicKey() {
  const meta = document.querySelector('meta[name="vapid-public-key"]')
  return meta?.content || ''
}

export function pushPermission() {
  if (!('Notification' in window)) return 'unsupported'
  return Notification.permission
}

export async function isSubscribed() {
  if (!isPushSupported()) return false
  try {
    const reg = await navigator.serviceWorker.getRegistration()
    if (!reg) return false
    const sub = await reg.pushManager.getSubscription()
    return !!sub
  } catch (e) {
    return false
  }
}

/**
 * Prompts for permission, subscribes via the browser PushManager, and posts
 * the subscription to the Laravel API. Returns the PushSubscription on
 * success or null on user denial / unsupported environment.
 */
export async function subscribePush() {
  if (!isPushSupported()) {
    throw new Error('Push notifications are not supported in this browser.')
  }
  const key = vapidPublicKey()
  if (!key) {
    throw new Error('Push notifications are not configured for this server.')
  }

  // Permission. Safe to call when already 'granted' — returns immediately.
  const permission = await Notification.requestPermission()
  if (permission !== 'granted') {
    return null
  }

  const reg = await navigator.serviceWorker.ready
  let sub = await reg.pushManager.getSubscription()
  if (!sub) {
    sub = await reg.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array(key),
    })
  }

  await registerSubscriptionWithServer(sub)
  return sub
}

/**
 * Unsubscribes locally and removes the subscription from the server.
 * Idempotent — fine to call when no subscription exists.
 */
export async function unsubscribePush() {
  if (!isPushSupported()) return
  const reg = await navigator.serviceWorker.getRegistration()
  if (!reg) return
  const sub = await reg.pushManager.getSubscription()
  if (!sub) return

  const endpoint = sub.endpoint
  await sub.unsubscribe().catch(() => {})
  try {
    await api.delete('/push/subscriptions', { data: { endpoint } })
  } catch (e) {
    // Silent: even if server delete fails, the local subscription is gone.
  }
}

/**
 * Posts an existing PushSubscription to the API. Useful on app boot when a
 * subscription already exists in the browser (e.g. user reinstalled the SW)
 * but the server may have lost the row.
 */
export async function registerSubscriptionWithServer(sub) {
  const json = sub.toJSON()
  await api.post('/push/subscriptions', {
    endpoint: json.endpoint,
    keys: {
      p256dh: json.keys?.p256dh,
      auth: json.keys?.auth,
    },
    content_encoding: 'aesgcm',
  })
}

export async function sendTestPush() {
  await api.post('/push/subscriptions/test')
}

// VAPID public keys are base64url; PushManager.subscribe needs Uint8Array.
function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - (base64String.length % 4)) % 4)
  const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/')
  const raw = window.atob(base64)
  const output = new Uint8Array(raw.length)
  for (let i = 0; i < raw.length; ++i) {
    output[i] = raw.charCodeAt(i)
  }
  return output
}
