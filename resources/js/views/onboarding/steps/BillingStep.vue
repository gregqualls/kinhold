<template>
  <div class="flex-1 flex flex-col">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-heading font-bold text-ink-primary mb-2">
        {{ alreadySubscribed ? "You're all set" : 'Pick your plan' }}
      </h1>
      <p class="text-base text-ink-secondary">
        {{ alreadySubscribed
          ? "You're already on a plan. Continue to finish setup."
          : 'Hosted Family includes everything. Add an AI assistant tier or bring your own key.' }}
      </p>
    </div>

    <!-- Already subscribed: short-circuit, no picker -->
    <div
      v-if="alreadySubscribed"
      class="p-4 bg-surface-sunken rounded-lg space-y-2"
      data-testid="billing-step-already"
    >
      <p class="text-sm font-semibold text-ink-primary">Base plan</p>
      <p class="text-xs text-ink-secondary">
        {{ billing.isOnTrial && billing.summary.trial_ends_at
          ? `Trial ends ${formatDate(billing.summary.trial_ends_at)}.`
          : 'Active subscription. Manage anytime in Settings → Billing.' }}
      </p>
    </div>

    <!-- Picker -->
    <div v-else class="space-y-4">
      <!-- Base plan card (always required, single tier) -->
      <div class="p-4 bg-surface-sunken rounded-lg" data-testid="billing-step-base">
        <div class="flex items-baseline justify-between gap-3">
          <p class="text-sm font-semibold text-ink-primary">Hosted Family</p>
          <p class="text-sm font-semibold text-ink-primary">{{ basePriceLabel }}</p>
        </div>
        <p class="text-xs text-ink-secondary mt-1">
          Calendar, tasks, vault, AI assistant, 5 GB storage. Storage scales automatically at $1/GB·month over the included amount.
        </p>
      </div>

      <!-- AI tier picker -->
      <div class="p-4 bg-surface-sunken rounded-lg space-y-3" data-testid="billing-step-ai">
        <p class="text-sm font-semibold text-ink-primary">AI Assistant</p>
        <div role="radiogroup" aria-label="AI tier" class="space-y-1">
          <button
            v-for="opt in aiTierOptions"
            :key="opt.slug"
            type="button"
            role="radio"
            :aria-checked="opt.slug === selectedTier"
            :disabled="opt.disabled"
            class="w-full flex items-center justify-between gap-3 px-3 py-2 rounded-md border text-left transition"
            :class="[
              opt.slug === selectedTier
                ? 'border-accent-lavender-bold bg-accent-lavender-soft/40'
                : 'border-border-subtle bg-surface-base hover:border-accent-lavender-bold',
              opt.disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
            ]"
            @click="selectTier(opt.slug)"
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

      <!-- Total + trial banner -->
      <div class="p-4 bg-surface-sunken rounded-lg space-y-2" data-testid="billing-step-total">
        <div class="flex items-baseline justify-between gap-3">
          <p class="text-sm font-medium text-ink-secondary">Monthly total</p>
          <p class="text-base font-semibold text-ink-primary">{{ totalLabel }}</p>
        </div>
        <div
          v-if="billing.summary.trial_eligible && billing.summary.trial_days"
          class="flex items-center gap-2 px-3 py-2 rounded-md bg-accent-lavender-soft/40 border border-accent-lavender-bold/30"
        >
          <span class="text-sm font-semibold text-accent-lavender-bold">
            {{ billing.summary.trial_days }}-day free trial
          </span>
          <span class="text-sm text-ink-secondary">— no card charged until the trial ends.</span>
        </div>
        <p v-if="billing.lastError" class="text-xs text-rose-700 dark:text-rose-300">
          {{ billing.lastError }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, inject, onMounted, ref } from 'vue'
import { useBillingStore } from '@/stores/billing'

const billing = useBillingStore()
const { setStepLoading, registerContinue } = inject('onboarding')

const selectedTier = ref('byok')
const hasFetched = ref(false)

const alreadySubscribed = computed(() => billing.isSubscribed)

const basePriceLabel = computed(() => {
  const cents = billing.summary.base_price_cents || 1000
  return `$${(cents / 100).toFixed(0)}/mo`
})

const aiTierOptions = computed(() => {
  const tiers = Array.isArray(billing.summary.ai_tier?.tiers) ? billing.summary.ai_tier.tiers : []
  const managed = tiers.map((t) => ({
    slug: t.slug,
    label: t.name,
    detail: `${t.daily_messages} msg/day · $${(t.price_cents / 100).toFixed(0)}/mo`,
    disabled: !t.configured,
    priceCents: t.price_cents,
  }))
  return [
    { slug: 'off', label: 'No AI', detail: 'Skip the AI assistant for now.', disabled: false, priceCents: 0 },
    { slug: 'byok', label: 'Bring your own key', detail: 'Use your own Anthropic API key — no AI charges from us.', disabled: false, priceCents: 0 },
    ...managed,
  ]
})

const totalCents = computed(() => {
  const base = billing.summary.base_price_cents || 1000
  const tier = aiTierOptions.value.find((o) => o.slug === selectedTier.value)
  return base + (tier?.priceCents || 0)
})

const totalLabel = computed(() => `$${(totalCents.value / 100).toFixed(0)}/mo`)

function selectTier(slug) {
  const opt = aiTierOptions.value.find((o) => o.slug === slug)
  if (!opt || opt.disabled) return
  selectedTier.value = slug
}

function formatDate(iso) {
  try {
    return new Date(iso).toLocaleDateString(undefined, {
      year: 'numeric', month: 'short', day: 'numeric',
    })
  } catch {
    return iso
  }
}

registerContinue(async () => {
  // Already subscribed (e.g., webhook beat the user back, or re-running the
  // wizard after manually subscribing) — advance normally.
  if (alreadySubscribed.value) return true

  setStepLoading(true)
  try {
    const origin = window.location.origin
    await billing.startCheckout(
      {
        success: `${origin}/onboarding?billing=success`,
        cancel: `${origin}/onboarding?billing=cancel`,
      },
      { aiTier: selectedTier.value },
    )
    // The store redirects via window.location.assign — block local advance.
    return false
  } catch {
    // Error is on billing.lastError already; surface inline, don't advance.
    return false
  } finally {
    setStepLoading(false)
  }
})

onMounted(async () => {
  try {
    await billing.fetch()
  } catch {
    // Error surfaced via billing.lastError; user can retry by clicking Continue.
  } finally {
    hasFetched.value = true
  }
})
</script>
