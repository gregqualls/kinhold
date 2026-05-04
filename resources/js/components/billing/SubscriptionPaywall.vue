<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-prussian-900/70 backdrop-blur-sm overflow-y-auto p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="paywall-title"
    @keydown.tab="onTab"
  >
    <!-- Focus trap sentinel: tabbing backward from here loops to the last
         focusable element so keyboard users can't escape the (intentionally
         non-dismissible) overlay into the still-mounted underlying view. -->
    <span tabindex="0" aria-hidden="true" @focus="focusLast"></span>

    <div ref="dialogRef" class="w-full max-w-md bg-white dark:bg-prussian-800 rounded-2xl shadow-2xl p-6 md:p-8">
      <div class="flex items-center gap-3 mb-5">
        <img src="/images/logo-100.png" alt="Kinhold" class="w-10 h-10 rounded-xl" />
        <span class="text-base font-semibold text-prussian-700 dark:text-lavender-200">Kinhold</span>
      </div>

      <h1
        id="paywall-title"
        class="text-xl md:text-2xl font-semibold text-prussian-900 dark:text-white mb-3"
      >
        {{ variant.heading }}
      </h1>
      <p class="text-sm text-prussian-600 dark:text-lavender-300 mb-6 leading-relaxed">
        {{ variant.body }}
      </p>

      <!-- Billing-owner CTAs -->
      <template v-if="isBillingOwner">
        <div v-if="lastError" class="mb-4 px-3 py-2 rounded-lg bg-red-50 dark:bg-red-900/30 text-sm text-red-700 dark:text-red-300">
          {{ lastError }}
        </div>
        <button
          ref="primaryCtaRef"
          type="button"
          :disabled="loading"
          class="w-full px-4 py-3 rounded-xl bg-prussian-900 dark:bg-lavender-200 text-white dark:text-prussian-900 font-medium hover:bg-prussian-800 dark:hover:bg-lavender-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-wisteria-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          @click="onPrimaryCta"
        >
          {{ loading ? 'Working…' : variant.ctaLabel }}
        </button>
      </template>

      <!-- Non-owner read-only -->
      <template v-else>
        <div class="px-4 py-3 rounded-xl bg-lavender-50 dark:bg-prussian-700 text-sm text-prussian-700 dark:text-lavender-200">
          {{ variant.ownerLine }}
        </div>
      </template>

      <!-- Escape hatches — sign out, or exercise GDPR rights without reactivating -->
      <div class="mt-6 pt-5 border-t border-lavender-200 dark:border-prussian-700 text-center space-y-2">
        <button
          ref="logoutRef"
          type="button"
          :disabled="loggingOut"
          class="text-sm text-lavender-600 dark:text-lavender-400 hover:text-prussian-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-wisteria-500 rounded disabled:opacity-50 transition-colors"
          @click="onLogout"
        >
          {{ loggingOut ? 'Signing out…' : 'Sign out' }}
        </button>
        <div class="text-xs text-lavender-500 dark:text-lavender-400 space-x-3">
          <button
            type="button"
            class="hover:text-prussian-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-wisteria-500 rounded transition-colors"
            @click="onManageAccount('your-data')"
          >
            Export my data
          </button>
          <span aria-hidden="true">·</span>
          <button
            type="button"
            class="hover:text-prussian-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-wisteria-500 rounded transition-colors"
            @click="onManageAccount('danger')"
          >
            Delete my account
          </button>
        </div>
      </div>
    </div>

    <span tabindex="0" aria-hidden="true" @focus="focusFirst"></span>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import { useBillingStore } from '@/stores/billing'
import { useBillingGate } from '@/composables/useBillingGate'

const router = useRouter()
const authStore = useAuthStore()
const billing = useBillingStore()
const { lastError, loading } = storeToRefs(billing)
const { paywallReason, isBillingOwner, billingOwnerName, cancelledEndsAt } = useBillingGate()

const loggingOut = ref(false)
const dialogRef = ref(null)
const primaryCtaRef = ref(null)
const logoutRef = ref(null)

function formatDate(iso) {
  try {
    return new Date(iso).toLocaleDateString(undefined, {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    })
  } catch {
    return null
  }
}

const VARIANTS = {
  trial_expired: {
    heading: 'Welcome back — your trial has ended.',
    body: () => 'Pick up where you left off by reactivating your plan.',
    ctaLabel: 'Reactivate plan',
    action: 'checkout',
  },
  past_due: {
    heading: "Your last payment didn't go through.",
    body: () => 'Update your card to keep everything running.',
    ctaLabel: 'Update payment method',
    action: 'portal',
  },
  cancelled_expired: {
    heading: 'Your subscription has ended.',
    body: () => {
      const date = cancelledEndsAt.value ? formatDate(cancelledEndsAt.value) : null
      return date
        ? `Your subscription ended on ${date}. Reactivate to continue.`
        : 'Reactivate to continue using Kinhold.'
    },
    ctaLabel: 'Reactivate plan',
    action: 'checkout',
  },
}

const FALLBACK_VARIANT = {
  heading: 'Subscription required.',
  body: () => 'Reactivate to continue using Kinhold.',
  ctaLabel: 'Reactivate plan',
  action: 'checkout',
}

const variant = computed(() => {
  const v = VARIANTS[paywallReason.value] || FALLBACK_VARIANT
  const ownerName = billingOwnerName.value || 'Your billing contact'
  return {
    heading: v.heading,
    body: v.body(),
    ctaLabel: v.ctaLabel,
    action: v.action,
    ownerLine: `Your family's subscription needs attention. ${ownerName} is the billing contact and will need to take care of it.`,
  }
})

const successUrl = `${window.location.origin}/settings#billing`
const cancelUrl = successUrl
const returnUrl = successUrl

async function onPrimaryCta() {
  try {
    if (variant.value.action === 'portal') {
      await billing.openPortal(returnUrl)
    } else {
      await billing.startCheckout({ success: successUrl, cancel: cancelUrl })
    }
  } catch {
    // Error surfaced via store.lastError
  }
}

function onManageAccount(section) {
  router.push({ name: 'Settings', query: { gdpr: '1' }, hash: `#${section}` })
}

async function onLogout() {
  loggingOut.value = true
  try {
    await authStore.logout()
    router.push({ name: 'Login' })
  } finally {
    loggingOut.value = false
  }
}

// --- Focus management ---
//
// The overlay is intentionally non-dismissible (sign-out is the only escape),
// so we have to keep keyboard focus inside the dialog — otherwise a Tab key
// press lands on something in the still-mounted underlying view.

function focusFirst() {
  const el = primaryCtaRef.value || logoutRef.value
  el?.focus()
}

function focusLast() {
  ;(logoutRef.value || primaryCtaRef.value)?.focus()
}

function onTab(event) {
  if (!dialogRef.value) return
  const focusable = dialogRef.value.querySelectorAll(
    'button:not([disabled]), [href], input:not([disabled]), [tabindex="0"]'
  )
  if (focusable.length === 0) return
  const first = focusable[0]
  const last = focusable[focusable.length - 1]
  if (event.shiftKey && document.activeElement === first) {
    event.preventDefault()
    last.focus()
  } else if (!event.shiftKey && document.activeElement === last) {
    event.preventDefault()
    first.focus()
  }
}

onMounted(async () => {
  await nextTick()
  focusFirst()
})
</script>
