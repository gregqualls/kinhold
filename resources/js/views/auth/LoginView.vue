<template>
  <div class="kin-page min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <router-link to="/" class="inline-flex flex-col items-center gap-3">
          <img src="/images/logo-100.png" alt="Kinhold" class="w-16 h-16 rounded-2xl" />
          <h1 class="text-4xl font-heading font-bold text-kin-gold">Kinhold</h1>
        </router-link>
        <p class="kin-muted mt-2">Sign in to your family hub</p>
      </div>

      <!-- Google Link Confirmation (when existing email/password user tries Google sign-in) -->
      <KinFlatCard v-if="authStore.pendingLink" padding="lg" class="mb-4">
        <div class="text-center mb-4">
          <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-accent-lavender-bold/10 flex items-center justify-center">
            <svg class="w-6 h-6 text-accent-lavender-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
          </div>
          <h2 class="text-lg font-semibold text-ink-primary">Link Google Account</h2>
          <p class="text-sm kin-muted mt-1">
            An account exists for <strong>{{ authStore.pendingLink.email }}</strong>.
            Enter your password to link Google sign-in.
          </p>
        </div>

        <form class="space-y-4" @submit.prevent="handleConfirmLink">
          <KinInput
            v-model="linkPassword"
            label="Password"
            type="password"
            placeholder="Enter your password"
            required
            :error="linkError"
          />

          <div v-if="linkError" class="p-3 bg-status-failed/10 border border-status-failed/30 rounded-[10px]">
            <p class="text-sm text-status-failed">{{ linkError }}</p>
          </div>

          <KinButton type="submit" variant="primary" size="lg" :loading="isLoading" class="w-full">
            Link &amp; Sign In
          </KinButton>

          <KinButton type="button" variant="ghost" size="md" class="w-full" @click="authStore.pendingLink = null">
            Cancel — sign in with password instead
          </KinButton>
        </form>
      </KinFlatCard>

      <!-- Form Card -->
      <KinFlatCard v-else padding="lg">
        <form class="space-y-4" @submit.prevent="handleLogin">
          <!-- Email -->
          <KinInput
            v-model="form.email"
            label="Email"
            type="email"
            placeholder="name@example.com"
            required
            :error="errors.email"
          />

          <!-- Password -->
          <KinInput
            v-model="form.password"
            label="Password"
            type="password"
            placeholder="••••••••"
            required
            :error="errors.password"
          />

          <!-- Remember me -->
          <KinCheckbox v-model="form.remember" label="Remember me" />

          <!-- Error message -->
          <div v-if="errors.general" class="p-3 bg-status-failed/10 border border-status-failed/30 rounded-[10px]">
            <p class="text-sm text-status-failed">{{ errors.general }}</p>
          </div>

          <!-- Submit button -->
          <KinButton
            type="submit"
            variant="primary"
            size="lg"
            :loading="isLoading"
            class="w-full mt-6"
          >
            Sign In
          </KinButton>
        </form>

        <!-- Google Sign In (only shown if Google OAuth is configured on server) -->
        <template v-if="!authStore.appConfig || authStore.appConfig?.services?.google_oauth">
          <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-border-subtle"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-3 bg-surface-raised kin-muted">or continue with</span>
            </div>
          </div>

          <KinButton
            type="button"
            variant="secondary"
            size="lg"
            :disabled="googleLoading"
            class="w-full"
            @click="handleGoogleLogin"
          >
            <template #leading>
              <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" />
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
              </svg>
            </template>
            {{ googleLoading ? 'Redirecting...' : 'Sign in with Google' }}
          </KinButton>
        </template>

        <!-- Divider -->
        <div class="border-t border-border-subtle my-6"></div>

        <!-- Demo link -->
        <p v-if="demoAvailable" class="text-center mb-4">
          <button class="kin-link font-medium text-sm" @click="showDemoModal = true">
            Or try the demo instead
          </button>
        </p>

        <!-- Register link -->
        <p class="text-center kin-muted">
          Don't have an account?
          <RouterLink to="/register" class="kin-link font-medium">
            Sign up
          </RouterLink>
        </p>
      </KinFlatCard>
    </div>
  </div>

  <DemoModal :show="showDemoModal" @close="showDemoModal = false" />
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import KinInput from '@/components/design-system/KinInput.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import KinCheckbox from '@/components/design-system/KinCheckbox.vue'
import KinFlatCard from '@/components/design-system/KinFlatCard.vue'
import DemoModal from '@/components/common/DemoModal.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const { isLoading } = storeToRefs(authStore)
const { error: notificationError } = useNotification()

const demoAvailable = computed(() => authStore.appConfig?.demo_available)
const showDemoModal = ref(false)

const form = reactive({
  email: '',
  password: '',
  remember: false,
})

const googleLoading = ref(false)
const linkPassword = ref('')
const linkError = ref('')

const errors = reactive({
  email: '',
  password: '',
  general: '',
})

onMounted(() => {
  if (route.query.verify_error === 'invalid') {
    errors.general = 'Email verification link is invalid or has expired. Please sign in and request a new verification email.'
  }
})

const validateForm = () => {
  errors.email = ''
  errors.password = ''
  errors.general = ''

  if (!form.email) {
    errors.email = 'Email is required'
  } else if (!form.email.includes('@')) {
    errors.email = 'Invalid email format'
  }

  if (!form.password) {
    errors.password = 'Password is required'
  } else if (form.password.length < 6) {
    errors.password = 'Password must be at least 6 characters'
  }

  return !errors.email && !errors.password
}

const handleLogin = async () => {
  if (!validateForm()) return

  const result = await authStore.login(form.email, form.password)

  if (result.success) {
    router.push({ name: 'Dashboard' })
  } else {
    errors.general = result.error || 'Login failed. Please try again.'
    notificationError(errors.general)
  }
}

const handleConfirmLink = async () => {
  linkError.value = ''
  if (!linkPassword.value) {
    linkError.value = 'Password is required'
    return
  }

  const result = await authStore.confirmGoogleLink(authStore.pendingLink.code, linkPassword.value)
  if (result.success) {
    router.push({ name: 'Dashboard' })
  } else {
    linkError.value = result.error
  }
}

const handleGoogleLogin = async () => {
  googleLoading.value = true
  errors.general = ''

  try {
    const response = await fetch('/auth/google/redirect', {
      headers: { 'Accept': 'application/json' },
    })
    const data = await response.json()

    if (data.url) {
      window.location.href = data.url
    } else {
      errors.general = 'Failed to get Google login URL.'
      googleLoading.value = false
    }
  } catch {
    errors.general = 'Failed to connect to Google. Please try again.'
    googleLoading.value = false
  }
}
</script>
