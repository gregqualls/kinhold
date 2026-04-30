import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import api from '../services/api'
import {
  isPushSupported,
  isSubscribed,
  pushPermission,
  sendTestPush,
  subscribePush,
  unsubscribePush,
  vapidPublicKey,
} from '../services/push'

const DEFAULT_PREFS = () => ({
  email: {},
  push: {},
  quiet_hours: { enabled: false, start: '22:00', end: '07:00' },
  muted: false,
  dinner_reminder_at: '15:00',
})

export const useNotificationsStore = defineStore('notifications', () => {
  const preferences = ref(DEFAULT_PREFS())
  const registry = ref({ categories: {}, types_by_category: {} })
  const pushStatus = ref({ subscriptions: 0, vapid_configured: false })
  const localPermission = ref(typeof Notification !== 'undefined' ? Notification.permission : 'unsupported')
  const localSubscribed = ref(false)
  const loading = ref(false)
  const lastError = ref('')

  const isPushAvailable = computed(() => isPushSupported() && !!vapidPublicKey())
  const isPushActive = computed(
    () => localSubscribed.value && localPermission.value === 'granted',
  )

  async function fetch() {
    loading.value = true
    lastError.value = ''
    try {
      const { data } = await api.get('/settings/notification-preferences')
      preferences.value = { ...DEFAULT_PREFS(), ...(data.preferences || {}) }
      registry.value = data.registry || { categories: {}, types_by_category: {} }
      pushStatus.value = data.push_status || { subscriptions: 0, vapid_configured: false }
      localSubscribed.value = await isSubscribed()
      localPermission.value = pushPermission()
    } catch (e) {
      lastError.value = e?.response?.data?.message || 'Failed to load notification preferences.'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function save(partial) {
    loading.value = true
    lastError.value = ''
    try {
      const { data } = await api.put('/settings/notification-preferences', partial)
      preferences.value = { ...DEFAULT_PREFS(), ...(data.preferences || {}) }
    } catch (e) {
      lastError.value = e?.response?.data?.message || 'Failed to update preferences.'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function setChannelKey(channel, key, value) {
    const next = { ...(preferences.value[channel] || {}), [key]: !!value }
    preferences.value[channel] = next
    await save({ [channel]: next })
  }

  async function setQuietHours(quiet) {
    preferences.value.quiet_hours = { ...preferences.value.quiet_hours, ...quiet }
    await save({ quiet_hours: preferences.value.quiet_hours })
  }

  async function setMuted(muted) {
    preferences.value.muted = !!muted
    await save({ muted: preferences.value.muted })
  }

  async function setDinnerReminderAt(time) {
    preferences.value.dinner_reminder_at = time
    await save({ dinner_reminder_at: time })
  }

  async function enablePush() {
    lastError.value = ''
    try {
      const sub = await subscribePush()
      localPermission.value = pushPermission()
      localSubscribed.value = !!sub
      pushStatus.value.subscriptions = await refreshSubscriptionCount()
    } catch (e) {
      lastError.value = e?.message || 'Could not enable push notifications.'
      throw e
    }
  }

  async function disablePush() {
    lastError.value = ''
    try {
      await unsubscribePush()
      localSubscribed.value = false
      pushStatus.value.subscriptions = await refreshSubscriptionCount()
    } catch (e) {
      lastError.value = e?.message || 'Could not disable push notifications.'
      throw e
    }
  }

  async function refreshSubscriptionCount() {
    try {
      const { data } = await api.get('/settings/notification-preferences')
      return data.push_status?.subscriptions ?? 0
    } catch {
      return pushStatus.value.subscriptions
    }
  }

  async function testPush() {
    lastError.value = ''
    try {
      await sendTestPush()
    } catch (e) {
      lastError.value = e?.response?.data?.message || 'Failed to send test push.'
      throw e
    }
  }

  return {
    preferences,
    registry,
    pushStatus,
    localPermission,
    localSubscribed,
    loading,
    lastError,
    isPushAvailable,
    isPushActive,
    fetch,
    save,
    setChannelKey,
    setQuietHours,
    setMuted,
    setDinnerReminderAt,
    enablePush,
    disablePush,
    testPush,
  }
})
