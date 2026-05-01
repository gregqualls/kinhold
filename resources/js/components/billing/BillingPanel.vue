<template>
  <div class="space-y-5">
    <!-- Loading state on first fetch -->
    <div v-if="billing.loading && !hasFetched" class="p-4 text-sm text-ink-secondary">
      Loading billing details…
    </div>

    <!-- Error banner -->
    <div
      v-else-if="billing.lastError"
      class="p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 rounded-lg"
    >
      <p class="text-sm text-rose-700 dark:text-rose-300">{{ billing.lastError }}</p>
    </div>

    <!-- Plan summary card -->
    <div v-else class="p-4 bg-surface-sunken rounded-lg space-y-3">
      <div class="flex items-start justify-between gap-3">
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-ink-primary">{{ planLabel }}</p>
          <p class="text-xs text-ink-secondary mt-1">{{ planDescription }}</p>
        </div>
        <span
          v-if="statusBadge"
          class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium whitespace-nowrap"
          :class="statusBadge.class"
        >
          {{ statusBadge.label }}
        </span>
      </div>

      <!-- Payment method (only when subscribed) -->
      <div
        v-if="billing.summary.payment_method"
        class="text-xs text-ink-secondary border-t border-border-subtle pt-3"
      >
        Paying with {{ formatBrand(billing.summary.payment_method.brand) }}
        ending in {{ billing.summary.payment_method.last4 }}.
      </div>

      <!-- Lifecycle dates -->
      <div v-if="lifecycleNote" class="text-xs text-ink-secondary">
        {{ lifecycleNote }}
      </div>
    </div>

    <!-- Action buttons -->
    <div class="flex flex-wrap gap-2">
      <!-- No active subscription: trial-eligible families see a free-trial CTA;
           returning families re-subscribe at the regular price. -->
      <BaseButton
        v-if="!billing.isSubscribed"
        variant="primary"
        :loading="billing.loading"
        @click="onCheckout"
      >
        {{ checkoutCtaLabel }}
      </BaseButton>

      <BaseButton
        v-if="billing.isSubscribed"
        variant="secondary"
        :loading="billing.loading"
        @click="onPortal"
      >
        Manage payment &amp; invoices
      </BaseButton>

      <BaseButton
        v-if="billing.isSubscribed && !billing.isOnGracePeriod"
        variant="ghost"
        :loading="billing.loading"
        @click="onCancel"
      >
        Cancel subscription
      </BaseButton>

      <BaseButton
        v-if="billing.isOnGracePeriod"
        variant="primary"
        :loading="billing.loading"
        @click="onResume"
      >
        Resume subscription
      </BaseButton>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useBillingStore } from '@/stores/billing'
import BaseButton from '@/components/common/BaseButton.vue'

const billing = useBillingStore()
const hasFetched = ref(false)

onMounted(async () => {
  try {
    await billing.fetch()
  } finally {
    hasFetched.value = true
  }
})

const formattedPrice = computed(() => {
  const cents = billing.summary.base_price_cents || 0
  if (cents === 0) return null
  return `$${(cents / 100).toFixed(0)}/mo`
})

const planLabel = computed(() => {
  if (billing.isSubscribed) return 'Base plan'
  if (billing.summary.trial_eligible) return 'Start your free trial'
  return 'No active subscription'
})

const planDescription = computed(() => {
  if (billing.isSubscribed) {
    return 'Includes hosting, calendar, tasks, vault, and 5 GB of storage.'
  }
  if (billing.summary.trial_eligible) {
    const days = billing.summary.trial_days || 14
    const after = formattedPrice.value ? ` After your trial, ${formattedPrice.value}.` : ''
    return `Try the full Kinhold experience free for ${days} days.${after}`
  }
  return 'Your trial has ended. Subscribe to continue using Kinhold.'
})

const statusBadge = computed(() => {
  if (billing.isOnGracePeriod) {
    return {
      label: 'Cancelling',
      class: 'bg-rose-100 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300',
    }
  }
  if (billing.isOnTrial) {
    return {
      label: 'Trial',
      class: 'bg-sand-100 dark:bg-sand-900/40 text-sand-700 dark:text-sand-300',
    }
  }
  if (billing.isSubscribed) {
    return {
      label: 'Active',
      class: 'bg-accent-lavender-soft/60 text-accent-lavender-bold',
    }
  }
  return null
})

const lifecycleNote = computed(() => {
  if (billing.isOnGracePeriod && billing.summary.ends_at) {
    return `Cancels on ${formatDate(billing.summary.ends_at)}. Resume any time before then.`
  }
  if (billing.isOnTrial && billing.summary.trial_ends_at) {
    return `Trial ends ${formatDate(billing.summary.trial_ends_at)}.`
  }
  return null
})

const checkoutCtaLabel = computed(() => {
  if (billing.summary.trial_eligible) {
    const days = billing.summary.trial_days || 14
    return `Start ${days}-day free trial`
  }
  return formattedPrice.value ? `Subscribe — ${formattedPrice.value}` : 'Subscribe to base plan'
})

const successUrl = `${window.location.origin}/settings#billing`
const cancelUrl = successUrl
const returnUrl = successUrl

async function onCheckout() {
  try {
    await billing.startCheckout({ success: successUrl, cancel: cancelUrl })
  } catch {
    // Error already on store; nothing to do here.
  }
}

async function onPortal() {
  try {
    await billing.openPortal(returnUrl)
  } catch {
    // Error already on store.
  }
}

async function onCancel() {
  if (!window.confirm('Cancel your subscription at the end of the current billing period?')) {
    return
  }
  try {
    await billing.cancel()
  } catch {
    // Error already on store.
  }
}

async function onResume() {
  try {
    await billing.resume()
  } catch {
    // Error already on store.
  }
}

function formatBrand(brand) {
  if (!brand) return 'card'
  return brand.charAt(0).toUpperCase() + brand.slice(1)
}

function formatDate(iso) {
  try {
    return new Date(iso).toLocaleDateString(undefined, {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    })
  } catch {
    return iso
  }
}
</script>
