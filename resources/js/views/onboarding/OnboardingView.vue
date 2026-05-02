<template>
  <div class="min-h-screen bg-kin-ivory dark:bg-kin-bg-dark flex flex-col">
    <!-- Header -->
    <div class="pt-8 pb-4 px-4 text-center">
      <img src="/images/logo-100.png" alt="Kinhold" class="w-10 h-10 mx-auto rounded-xl" />
    </div>

    <!-- Progress Dots -->
    <div v-if="!isLastStep" class="flex items-center justify-center gap-2 pb-6">
      <button
        v-for="(step, i) in activeSteps.slice(0, -1)"
        :key="i"
        class="w-2.5 h-2.5 rounded-full transition-all duration-300 focus:outline-none"
        :class="dotClass(i)"
        :aria-label="`Step ${i + 1}`"
        @click="store.goToStep(i)"
      ></button>
    </div>

    <!-- Step Content -->
    <div class="flex-1 flex flex-col px-4 pb-4 max-w-lg mx-auto w-full">
      <Transition name="step-fade" mode="out-in">
        <component :is="activeSteps[store.currentStep]" :key="store.currentStep" />
      </Transition>

      <!-- Navigation Footer -->
      <div class="mt-auto pt-6 flex items-center gap-3">
        <KinButton
          v-if="store.currentStep > 0 && !isLastStep"
          variant="ghost"
          @click="store.prevStep()"
        >
          Back
        </KinButton>

        <div class="flex-1"></div>

        <KinButton
          v-if="!isLastStep"
          variant="ghost"
          @click="handleSkip"
        >
          Skip for now
        </KinButton>

        <KinButton
          v-if="!isLastStep"
          variant="primary"
          :loading="stepLoading"
          :disabled="stepLoading"
          @click="handleContinue"
        >
          {{ stepLoading ? 'Saving...' : 'Continue' }}
        </KinButton>

        <KinButton
          v-if="isLastStep"
          variant="primary"
          class="w-full text-center"
          :loading="store.isCompleting"
          :disabled="store.isCompleting"
          @click="handleFinish"
        >
          Go to Dashboard
        </KinButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, provide } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useOnboardingStore } from '@/stores/onboarding'
import WelcomeStep from './steps/WelcomeStep.vue'
import InviteStep from './steps/InviteStep.vue'
import CalendarStep from './steps/CalendarStep.vue'
import TagsStep from './steps/TagsStep.vue'
import FeaturesStep from './steps/FeaturesStep.vue'
import FeaturesExplainerStep from './steps/FeaturesExplainerStep.vue'
import BillingStep from './steps/BillingStep.vue'
import CompleteStep from './steps/CompleteStep.vue'
import KinButton from '@/components/design-system/KinButton.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const store = useOnboardingStore()
const stepLoading = ref(false)

// Parents get the full wizard; joining members get a simplified flow.
// BillingStep is appended for the billing owner (who is always a parent
// today) on hosted instances — self-hosters skip it entirely so the wizard
// length matches the pre-billing flow.
const activeSteps = computed(() => {
  if (authStore.isParent) {
    const billingEnabled = !!authStore.appConfig?.billing_enabled
    const isBillingOwner = authStore.user?.id === authStore.family?.billing_owner_id
    const steps = [WelcomeStep, InviteStep, CalendarStep, TagsStep, FeaturesStep]
    if (billingEnabled && isBillingOwner) {
      steps.push(BillingStep)
    }
    steps.push(CompleteStep)
    return steps
  }
  // Joining member: Welcome → Calendar → What You Can Do → Complete
  return [WelcomeStep, CalendarStep, FeaturesExplainerStep, CompleteStep]
})

const isLastStep = computed(() => store.currentStep === activeSteps.value.length - 1)

// Allow step components to set loading state and register continue handlers
const stepContinueHandler = ref(null)
provide('onboarding', {
  setStepLoading: (val) => { stepLoading.value = val },
  registerContinue: (fn) => { stepContinueHandler.value = fn },
})

function dotClass(index) {
  if (index < store.currentStep) return 'bg-accent-lavender-bold/50'
  if (index === store.currentStep) return 'bg-accent-lavender-bold w-3 h-3'
  return 'bg-surface-sunken'
}

async function handleContinue() {
  if (stepContinueHandler.value) {
    const result = await stepContinueHandler.value()
    if (result === false) return
  }
  if (store.currentStep < activeSteps.value.length - 1) {
    store.currentStep++
  }
}

async function handleSkip() {
  if (store.currentStep < activeSteps.value.length - 1) {
    store.currentStep++
  }
}

async function handleFinish() {
  const result = await store.completeOnboarding()
  if (result.success) {
    router.push({ name: 'Dashboard' })
  }
}

onMounted(async () => {
  store.reset()
  await store.fetchStatus()

  // Handle OAuth return from calendar connection
  const step = route.query.step
  if (step !== undefined) {
    store.goToStep(parseInt(step))
  }

  // Handle Stripe Checkout return: success → jump to CompleteStep so the user
  // doesn't re-walk earlier steps; cancel → land back on BillingStep.
  const billing = route.query.billing
  if (billing === 'success') {
    store.goToStep(activeSteps.value.length - 1)
  } else if (billing === 'cancel') {
    const billingIdx = activeSteps.value.indexOf(BillingStep)
    if (billingIdx >= 0) store.goToStep(billingIdx)
  }
})
</script>

<style scoped>
.step-fade-enter-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.step-fade-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.step-fade-enter-from {
  opacity: 0;
  transform: translateX(12px);
}
.step-fade-leave-to {
  opacity: 0;
  transform: translateX(-12px);
}
</style>
