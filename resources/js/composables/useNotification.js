import { ref } from 'vue'

const notifications = ref([])
let notificationIdCounter = 0

const createNotification = (message, type = 'info', duration = 5000) => {
  const id = notificationIdCounter++
  const notification = { id, message, type }

  notifications.value.push(notification)

  if (duration > 0) {
    setTimeout(() => {
      notifications.value = notifications.value.filter((n) => n.id !== id)
    }, duration)
  }

  return id
}

export const useNotification = () => ({
  notifications,
  success: (message, duration = 5000) => createNotification(message, 'success', duration),
  error: (message, duration = 5000) => createNotification(message, 'error', duration),
  info: (message, duration = 5000) => createNotification(message, 'info', duration),
  warning: (message, duration = 5000) => createNotification(message, 'warning', duration),
  clear: (id) => {
    notifications.value = notifications.value.filter((n) => n.id !== id)
  },
})
