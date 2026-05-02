import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'

export const useOnboardingStore = defineStore('onboarding', () => {
  const currentStep = ref(0)
  const status = ref(null)
  const isLoading = ref(false)
  const selectedPresets = ref(new Set())
  const isCompleting = ref(false)

  // Upper bound for goToStep; parent flow can include the BillingStep (7) when
  // BILLING_ENABLED. The actual rendered step count comes from
  // OnboardingView's `activeSteps` array — this is just a clamp.
  const totalSteps = 7
  const progress = computed(() => Math.min((currentStep.value / (totalSteps - 1)) * 100, 100))
  const isFirstStep = computed(() => currentStep.value === 0)
  const isLastStep = computed(() => currentStep.value === totalSteps - 1)

  async function fetchStatus() {
    isLoading.value = true
    try {
      const response = await api.get('/onboarding/status')
      status.value = response.data
    } catch {
      // Status fetch failed — defaults are fine
    } finally {
      isLoading.value = false
    }
  }

  function nextStep() {
    if (currentStep.value < totalSteps - 1) {
      currentStep.value++
    }
  }

  function prevStep() {
    if (currentStep.value > 0) {
      currentStep.value--
    }
  }

  function goToStep(step) {
    if (step >= 0 && step < totalSteps) {
      currentStep.value = step
    }
  }

  async function completeOnboarding() {
    isCompleting.value = true
    try {
      const response = await api.post('/onboarding/complete')
      const authStore = useAuthStore()
      if (authStore.user) {
        authStore.user.onboarding_completed_at = response.data.onboarding_completed_at
      }
      return { success: true }
    } catch {
      return { success: false }
    } finally {
      isCompleting.value = false
    }
  }

  async function skipOnboarding() {
    return await completeOnboarding()
  }

  function reset() {
    currentStep.value = 0
    status.value = null
    selectedPresets.value = new Set()
  }

  return {
    currentStep,
    status,
    isLoading,
    selectedPresets,
    isCompleting,
    totalSteps,
    progress,
    isFirstStep,
    isLastStep,
    fetchStatus,
    nextStep,
    prevStep,
    goToStep,
    completeOnboarding,
    skipOnboarding,
    reset,
  }
})
