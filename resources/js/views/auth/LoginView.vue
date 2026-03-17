<template>
  <div class="min-h-screen bg-gradient-to-br from-lavender-50 to-wisteria-50 dark:from-prussian-900 dark:to-prussian-800 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-wisteria-600 dark:text-wisteria-400 mb-2">Q32 Hub</h1>
        <p class="text-lavender-600 dark:text-lavender-400">Family Hub Login</p>
      </div>

      <!-- Form Card -->
      <div class="card-lg">
        <form @submit.prevent="handleLogin" class="space-y-4">
          <!-- Email -->
          <BaseInput
            v-model="form.email"
            label="Email"
            type="email"
            placeholder="name@example.com"
            required
            :error="errors.email"
          />

          <!-- Password -->
          <BaseInput
            v-model="form.password"
            label="Password"
            type="password"
            placeholder="••••••••"
            required
            :error="errors.password"
          />

          <!-- Remember me -->
          <label class="flex items-center gap-2 cursor-pointer">
            <input v-model="form.remember" type="checkbox" class="rounded border-lavender-300 text-wisteria-600 focus:ring-wisteria-400" />
            <span class="text-sm text-prussian-500 dark:text-lavender-200">Remember me</span>
          </label>

          <!-- Error message -->
          <div v-if="errors.general" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-sm text-red-800 dark:text-red-300">{{ errors.general }}</p>
          </div>

          <!-- Submit button -->
          <BaseButton
            variant="primary"
            size="lg"
            :loading="isLoading"
            class="w-full mt-6"
          >
            Sign In
          </BaseButton>
        </form>

        <!-- Divider with text -->
        <div class="relative my-6">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-lavender-200 dark:border-prussian-600"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-3 bg-white dark:bg-prussian-800 text-lavender-500 dark:text-lavender-400">or continue with</span>
          </div>
        </div>

        <!-- Google Sign In -->
        <button
          @click="handleGoogleLogin"
          :disabled="googleLoading"
          class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-lavender-200 dark:border-prussian-600 rounded-lg bg-white dark:bg-prussian-700 hover:bg-lavender-50 dark:hover:bg-prussian-600 transition-colors text-prussian-600 dark:text-lavender-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
          </svg>
          {{ googleLoading ? 'Redirecting...' : 'Sign in with Google' }}
        </button>

        <!-- Divider -->
        <div class="divider my-6" />

        <!-- Register link -->
        <p class="text-center text-lavender-600 dark:text-lavender-400">
          Don't have an account?
          <RouterLink to="/register" class="text-wisteria-600 dark:text-wisteria-400 font-medium hover:text-wisteria-500">
            Sign up
          </RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import BaseInput from '@/components/common/BaseInput.vue'
import BaseButton from '@/components/common/BaseButton.vue'

const router = useRouter()
const authStore = useAuthStore()
const { isLoading } = storeToRefs(authStore)
const { error: notificationError } = useNotification()

const form = reactive({
  email: '',
  password: '',
  remember: false,
})

const googleLoading = ref(false)

const errors = reactive({
  email: '',
  password: '',
  general: '',
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
  } catch (err) {
    errors.general = 'Failed to connect to Google. Please try again.'
    googleLoading.value = false
  }
}
</script>
