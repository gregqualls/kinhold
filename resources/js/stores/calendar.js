import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { DateTime } from 'luxon'
import api from '@/services/api'

export const useCalendarStore = defineStore('calendar', () => {
  const events = ref([])
  const connections = ref([])
  const currentMonth = ref(DateTime.now())
  const viewMode = ref(localStorage.getItem('calendar-view-mode') || 'month') // month, week, day
  const isLoading = ref(false)
  const error = ref(null)

  // Computed
  const eventsForDate = computed(() => (date) => {
    const dateStr = date.toISODate()
    return events.value.filter((event) => {
      const eventDate = DateTime.fromISO(event.start).toISODate()
      return eventDate === dateStr
    })
  })

  const eventsForWeek = computed(() => (startDate) => {
    const endDate = startDate.plus({ days: 6 })
    return events.value.filter((event) => {
      const eventDate = DateTime.fromISO(event.start)
      return eventDate >= startDate && eventDate <= endDate
    })
  })

  const todayEvents = computed(() => {
    const today = DateTime.now().toISODate()
    return events.value.filter((event) => {
      const eventDate = DateTime.fromISO(event.start).toISODate()
      return eventDate === today
    })
  })

  const upcomingEvents = computed(() => {
    const today = DateTime.now()
    return events.value
      .filter((event) => {
        const eventDate = DateTime.fromISO(event.start)
        return eventDate >= today
      })
      .sort((a, b) => new Date(a.start) - new Date(b.start))
      .slice(0, 5)
  })

  // Actions
  const fetchEvents = async (startDate, endDate) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.get('/calendar/events', {
        params: {
          start: startDate.toISODate(),
          end: endDate.toISODate(),
        },
      })

      events.value = response.data.events || []
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch events'
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const fetchConnections = async () => {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.get('/calendar/connections')
      connections.value = response.data.connections
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch connections'
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const connect = async (provider = 'google') => {
    try {
      const response = await api.post('/calendar/connect', { provider })
      return { success: true, authUrl: response.data.auth_url }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const disconnect = async (connectionId) => {
    try {
      await api.delete(`/calendar/connections/${connectionId}`)
      connections.value = connections.value.filter((c) => c.id !== connectionId)
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  const subscribeUrl = async (url, name = null) => {
    try {
      const response = await api.post('/calendar/subscribe', { url, name })
      // Refresh connections list
      await fetchConnections()
      return { success: true, connection: response.data.connection, message: response.data.message }
    } catch (err) {
      return { success: false, error: err.response?.data?.message || 'Failed to subscribe to calendar' }
    }
  }

  const sync = async () => {
    try {
      await api.post('/calendar/sync')
      // Refetch events for current month
      const endOfMonth = currentMonth.value.endOf('month')
      await fetchEvents(currentMonth.value.startOf('month'), endOfMonth)
      return { success: true }
    } catch (err) {
      return { success: false, error: err.response?.data?.message }
    }
  }

  // Helper to re-fetch events for the current view period
  const refetchCurrentPeriod = async () => {
    let start, end
    if (viewMode.value === 'month') {
      start = currentMonth.value.startOf('month').startOf('week')
      end = currentMonth.value.endOf('month').endOf('week')
    } else if (viewMode.value === 'week') {
      start = currentMonth.value.startOf('week')
      end = start.plus({ days: 6 })
    } else {
      start = currentMonth.value.startOf('day')
      end = currentMonth.value.plus({ days: 1 }).startOf('day')
    }
    await fetchEvents(start, end)
  }

  const createEvent = async (data) => {
    error.value = null
    try {
      const response = await api.post('/calendar/events', data)
      await refetchCurrentPeriod()
      return { success: true, event: response.data.event }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create event'
      return { success: false, error: error.value }
    }
  }

  const updateEvent = async (id, data) => {
    error.value = null
    try {
      const response = await api.put(`/calendar/events/${id}`, data)
      await refetchCurrentPeriod()
      return { success: true, event: response.data.event }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update event'
      return { success: false, error: error.value }
    }
  }

  const deleteEvent = async (id) => {
    error.value = null
    try {
      await api.delete(`/calendar/events/${id}`)
      await refetchCurrentPeriod()
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete event'
      return { success: false, error: error.value }
    }
  }

  const setViewMode = (mode) => {
    viewMode.value = mode
    localStorage.setItem('calendar-view-mode', mode)
  }

  const navigateMonth = (direction) => {
    if (direction === 'next') {
      currentMonth.value = currentMonth.value.plus({ months: 1 })
    } else if (direction === 'prev') {
      currentMonth.value = currentMonth.value.minus({ months: 1 })
    }
  }

  const navigateWeek = (direction) => {
    if (direction === 'next') {
      currentMonth.value = currentMonth.value.plus({ weeks: 1 })
    } else if (direction === 'prev') {
      currentMonth.value = currentMonth.value.minus({ weeks: 1 })
    }
  }

  const navigateDay = (direction) => {
    if (direction === 'next') {
      currentMonth.value = currentMonth.value.plus({ days: 1 })
    } else if (direction === 'prev') {
      currentMonth.value = currentMonth.value.minus({ days: 1 })
    }
  }

  const navigateToToday = () => {
    currentMonth.value = DateTime.now()
  }

  return {
    // State
    events,
    connections,
    currentMonth,
    viewMode,
    isLoading,
    error,

    // Computed
    eventsForDate,
    eventsForWeek,
    todayEvents,
    upcomingEvents,

    // Actions
    fetchEvents,
    fetchConnections,
    connect,
    disconnect,
    subscribeUrl,
    sync,
    createEvent,
    updateEvent,
    deleteEvent,
    refetchCurrentPeriod,
    setViewMode,
    navigateMonth,
    navigateWeek,
    navigateDay,
    navigateToToday,
  }
})
