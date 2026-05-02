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

    <!-- Storage usage (hidden until we've actually measured something) -->
    <div
      v-if="!billing.lastError && hasFetched && storage.last_calculated_at"
      class="p-4 bg-surface-sunken rounded-lg space-y-2"
      data-testid="billing-storage"
    >
      <div class="flex items-baseline justify-between gap-3">
        <p class="text-sm font-semibold text-ink-primary">Storage</p>
        <p class="text-xs text-ink-secondary">
          {{ formatBytes(storage.used_bytes) }} of {{ formatBytes(storage.included_bytes) }} included
        </p>
      </div>

      <div
        class="h-2 w-full rounded-full bg-surface-base overflow-hidden"
        role="progressbar"
        :aria-valuenow="storagePercent"
        aria-valuemin="0"
        aria-valuemax="100"
        :aria-label="storageAriaLabel"
      >
        <div
          class="h-full transition-all"
          :class="storage.over_limit ? 'bg-amber-500 dark:bg-amber-400' : 'bg-accent-lavender-bold'"
          :style="{ width: storageBarWidth }"
        ></div>
      </div>

      <p
        v-if="storage.over_limit"
        class="text-xs text-amber-700 dark:text-amber-300"
      >
        +{{ storage.overage_gb }} GB at $1/GB·month — adds
        ${{ (storage.overage_cents / 100).toFixed(2) }} to your next invoice.
      </p>
    </div>

    <!-- AI Assistant tier picker (only when subscribed to base plan) -->
    <div
      v-if="!billing.lastError && hasFetched && billing.isSubscribed"
      class="p-4 bg-surface-sunken rounded-lg space-y-3"
      data-testid="billing-ai-tier"
    >
      <div class="flex items-baseline justify-between gap-3">
        <p class="text-sm font-semibold text-ink-primary">AI Assistant</p>
        <p v-if="aiUsageNote" class="text-xs text-ink-secondary">{{ aiUsageNote }}</p>
      </div>

      <div role="radiogroup" aria-label="AI tier" class="space-y-1">
        <button
          v-for="opt in aiTierOptions"
          :key="opt.slug"
          type="button"
          role="radio"
          :aria-checked="opt.slug === currentAiTier"
          :disabled="opt.disabled || billing.loading"
          class="w-full flex items-center justify-between gap-3 px-3 py-2 rounded-md border text-left transition"
          :class="[
            opt.slug === currentAiTier
              ? 'border-accent-lavender-bold bg-accent-lavender-soft/40'
              : 'border-border-subtle bg-surface-base hover:border-accent-lavender-bold',
            (opt.disabled || billing.loading) ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
          ]"
          @click="onAiTier(opt.slug)"
        >
          <span class="flex-1 min-w-0">
            <span class="block text-sm font-medium text-ink-primary">{{ opt.label }}</span>
            <span v-if="opt.detail" class="block text-xs text-ink-secondary">{{ opt.detail }}</span>
          </span>
          <span
            v-if="opt.disabled"
            class="text-[11px] uppercase tracking-wide text-ink-secondary"
          >Coming soon</span>
        </button>
      </div>
    </div>

    <div
      v-else-if="!billing.lastError && hasFetched && !billing.isSubscribed"
      class="p-4 bg-surface-sunken rounded-lg"
    >
      <p class="text-sm font-semibold text-ink-primary">AI Assistant</p>
      <p class="text-xs text-ink-secondary mt-1">
        Subscribe to a plan to add an AI tier.
      </p>
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

const storage = computed(() => billing.summary.storage || {})

const storagePercent = computed(() => {
  const used = Number(storage.value.used_bytes) || 0
  const included = Number(storage.value.included_bytes) || 1
  return Math.round(Math.min(100, Math.max(0, (used / included) * 100)))
})

const storageBarWidth = computed(() => `${storagePercent.value.toFixed(1)}%`)

const storageAriaLabel = computed(
  () => `Storage usage: ${formatBytes(storage.value.used_bytes)} of ${formatBytes(storage.value.included_bytes)} included`,
)

function formatBytes(bytes) {
  const n = Number(bytes) || 0
  if (n < 1024) return `${n} B`
  const units = ['KB', 'MB', 'GB', 'TB']
  let value = n / 1024
  let unit = 'KB'
  for (const u of units) {
    unit = u
    if (value < 1024) break
    value /= 1024
  }
  return `${value.toFixed(value >= 100 ? 0 : value >= 10 ? 1 : 2)} ${unit}`
}

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

const aiTier = computed(() => billing.summary.ai_tier || {})

const currentAiTier = computed(() => {
  const t = aiTier.value
  if (t.mode === 'byok') return 'byok'
  const managed = ['lite', 'standard', 'pro']
  if (t.mode === 'kinhold' && managed.includes(t.plan)) return t.plan
  return 'off'
})

const aiTierOptions = computed(() => {
  const tiers = Array.isArray(aiTier.value.tiers) ? aiTier.value.tiers : []
  const managed = tiers.map((t) => ({
    slug: t.slug,
    label: t.name,
    detail: `${t.daily_messages} msg/day · $${(t.price_cents / 100).toFixed(0)}/mo`,
    disabled: !t.configured,
  }))
  return [
    { slug: 'off', label: 'Off', detail: 'No AI assistant.', disabled: false },
    { slug: 'byok', label: 'BYOK — Bring your own key', detail: 'Use your own Anthropic API key.', disabled: false },
    ...managed,
  ]
})

const aiUsageNote = computed(() => {
  const u = aiTier.value.usage
  if (!u || !u.enforced) return null
  return `${u.count} / ${u.limit} messages today`
})

async function onAiTier(slug) {
  if (slug === currentAiTier.value) return
  try {
    await billing.selectAiTier(slug)
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
