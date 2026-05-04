import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'

/**
 * SPA-side paywall gate for 70-I (#223). Reads the lightweight `family.billing`
 * block injected by AuthController::user() and exposes the signals the App.vue
 * shell + SubscriptionPaywall.vue need.
 *
 * Self-host and BILLING_ENABLED=false return the `family.billing === null`
 * shape from the server, so `requiresPayment` resolves false everywhere off
 * the hosted product.
 *
 * `needsOnboarding` is a special case (#245): the family exists and has a
 * billing owner, but no Stripe customer. The SPA should redirect the owner to
 * /onboarding (where BillingStep takes over) instead of showing the lapsed-
 * subscription paywall overlay.
 */
export function useBillingGate() {
  const auth = useAuthStore()
  const { family } = storeToRefs(auth)

  const billing = computed(() => family.value?.billing || null)
  const requiresPayment = computed(() => !!billing.value?.requires_payment)
  const paywallReason = computed(() => billing.value?.paywall_reason || null)
  const needsOnboarding = computed(() => paywallReason.value === 'needs_onboarding')
  const isBillingOwner = computed(() => !!billing.value?.is_billing_owner)
  const billingOwnerName = computed(() => billing.value?.billing_owner_name || null)
  const cancelledEndsAt = computed(() => billing.value?.cancelled_ends_at || null)

  return {
    requiresPayment,
    paywallReason,
    needsOnboarding,
    isBillingOwner,
    billingOwnerName,
    cancelledEndsAt,
  }
}
