import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { DateTime } from 'luxon'
import api from '@/services/api'

export const useFeaturedEventsStore = defineStore('featuredEvents', () => {
  const events = ref([])
  const countdownEvent = ref(null)
  const isLoading = ref(false)
  const error = ref(null)

  const upcomingEvents = computed(() => {
    const now = DateTime.now().startOf('day')
    return events.value
      .filter((e) => {
        const eventDate = DateTime.fromISO(e.event_date)
        return eventDate >= now
      })
      .sort((a, b) => {
        const dateA = DateTime.fromISO(a.event_date)
        const dateB = DateTime.fromISO(b.event_date)
        return dateA.toMillis() - dateB.toMillis()
      })
  })

  const todayEvents = computed(() => {
    const today = DateTime.now().toISODate()
    return events.value.filter((e) => e.event_date === today)
  })

  const getCountdown = (eventDate) => {
    const now = DateTime.now().startOf('day')
    const target = DateTime.fromISO(eventDate).startOf('day')
    const diff = target.diff(now, 'days').days
    const days = Math.round(diff)

    if (days < 0) return 'passed'
    if (days === 0) return 'today!'
    if (days === 1) return 'tomorrow'
    if (days <= 7) return `in ${days} days`
    if (days <= 14) return 'next week'
    return `in ${days} days`
  }

  const fetchEvents = async () => {
    isLoading.value = true
    error.value = null
    try {
      const response = await api.get('/featured-events')
      events.value = response.data.featured_events
    } catch (err) {
      console.error('Failed to fetch featured events:', err)
      error.value = err.response?.data?.message || 'Failed to load events'
    } finally {
      isLoading.value = false
    }
  }

  const createEvent = async (eventData) => {
    error.value = null
    try {
      const response = await api.post('/featured-events', eventData)
      events.value.push(response.data.featured_event)
      return response.data.featured_event
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create event'
      throw err
    }
  }

  const updateEvent = async (id, eventData) => {
    error.value = null
    try {
      const response = await api.put(`/featured-events/${id}`, eventData)
      const index = events.value.findIndex((e) => e.id === id)
      if (index !== -1) {
        events.value[index] = response.data.featured_event
      }
      return response.data.featured_event
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update event'
      throw err
    }
  }

  const deleteEvent = async (id) => {
    error.value = null
    try {
      await api.delete(`/featured-events/${id}`)
      events.value = events.value.filter((e) => e.id !== id)
      // If the deleted event was the countdown, clear it
      if (countdownEvent.value?.id === id) {
        countdownEvent.value = null
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete event'
      throw err
    }
  }

  const fetchCountdown = async () => {
    try {
      const response = await api.get('/featured-events/countdown')
      countdownEvent.value = response.data.countdown_event
    } catch (err) {
      console.error('Failed to fetch countdown event:', err)
    }
  }

  const setCountdown = async (id) => {
    error.value = null
    try {
      const response = await api.put(`/featured-events/${id}/countdown`)
      const updatedEvent = response.data.featured_event

      // Update the event in the events list
      const index = events.value.findIndex((e) => e.id === id)
      if (index !== -1) {
        events.value[index] = updatedEvent
      }

      // Clear is_countdown on all other events in local state
      events.value.forEach((e) => {
        if (e.id !== id) {
          e.is_countdown = false
        }
      })

      // Update countdown event ref
      if (updatedEvent.is_countdown) {
        countdownEvent.value = updatedEvent
      } else {
        countdownEvent.value = null
      }

      return updatedEvent
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to set countdown'
      throw err
    }
  }

  return {
    events,
    countdownEvent,
    isLoading,
    error,
    upcomingEvents,
    todayEvents,
    getCountdown,
    fetchEvents,
    fetchCountdown,
    setCountdown,
    createEvent,
    updateEvent,
    deleteEvent,
  }
})
