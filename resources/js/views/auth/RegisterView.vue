<template>
  <div class="min-h-screen bg-gradient-to-br from-lavender-50 to-wisteria-50 dark:from-prussian-900 dark:to-prussian-800 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-wisteria-600 dark:text-wisteria-400 mb-2">Q32 Hub</h1>
        <p class="text-lavender-600 dark:text-lavender-400">Create Your Family Hub</p>
      </div>

      <!-- Form Card -->
      <div class="card-lg">
        <form @submit.prevent="handleRegister" class="space-y-4">
          <!-- Name -->
          <BaseInput
            v-model="form.name"
            label="Full Name"
            type="text"
            placeholder="John Doe"
            required
            :error="errors.name"
          />

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

          <!-- Confirm Password -->
          <BaseInput
            v-model="form.password_confirmation"
            label="Confirm Password"
            type="password"
            placeholder="••••••••"
            required
            :error="errors.password_confirmation"
          />

          <!-- Family Option Tabs -->
          <div class="space-y-3 py-2">
            <p class="text-sm font-medium text-prussian-500 dark:text-lavender-200">Family Setup</p>
            <div class="flex gap-2">
              <button
                type="button"
                @click="familyMode = 'new'"
                :class="[
                  'flex-1 py-2 px-3 rounded-lg font-medium transition-colors',
                  familyMode === 'new'
                    ? 'bg-wisteria-600 text-white'
                    : 'bg-lavender-100 dark:bg-prussian-700 text-prussian-500 dark:text-lavender-200 hover:bg-lavender-200 dark:hover:bg-prussian-600',
                ]"
              >
                Create New
              </button>
              <button
                type="button"
                @click="familyMode = 'join'"
                :class="[
                  'flex-1 py-2 px-3 rounded-lg font-medium transition-colors',
                  familyMode === 'join'
                    ? 'bg-wisteria-600 text-white'
                    : 'bg-lavender-100 dark:bg-prussian-700 text-prussian-500 dark:text-lavender-200 hover:bg-lavender-200 dark:hover:bg-prussian-600',
                ]"
              >
                Join Existing
              </button>
            </div>
          </div>

          <!-- Create New Family -->
          <template v-if="familyMode === 'new'">
            <BaseInput
              v-model="form.family_name"
              label="Family Name"
              type="text"
              placeholder="The Johnsons"
              required
              :error="errors.family_name"
            />
          </template>

          <!-- Join Existing Family -->
          <template v-else>
            <BaseInput
              v-model="form.invite_code"
              label="Family Invite Code"
              type="text"
              placeholder="ABC123XYZ"
              required
              :error="errors.invite_code"
            />
          </template>

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
            Create Account
          </BaseButton>
        </form>

        <!-- Divider with text -->
        <div class="relative my-6">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-lavender-200 dark:border-prussian-600"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-3 bg-white dark:bg-prussian-800 text-lavender-500 dark:text-lavender-400">or sign up with</span>
          </div>
        </div>

        <!-- Google Sign Up -->
        <button
          @click="handleGoogleSignup"
          :disabled="googleLoading"
          class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-lavender-200 dark:border-prussian-600 rounded-lg bg-white dark:bg-prussian-700 hover:bg-lavender-50 dark:hover:bg-prussian-600 transition-colors text-prussian-600 dark:text-lavender-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
          </svg>
          {{ googleLoading ? 'Redirecting...' : 'Sign up with Google' }}
        </button>

        <!-- Divider -->
        <div class="divider my-6" />

        <!-- Login link -->
        <p class="text-center text-lavender-600 dark:text-lavender-400">
          Already have an account?
          <RouterLink to="/login" class="text-wisteria-600 dark:text-wisteria-400 font-medium hover:text-wisteria-500">
            Sign in
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

const familyMode = ref('new')
const googleLoading = ref(false)

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  family_name: '',
  invite_code: '',
})

const errors = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  family_name: '',
  invite_code: '',
  general: '',
})

const validateForm = () => {
  errors.name = ''
  errors.email = ''
  errors.password = ''
  errors.password_confirmation = ''
  errors.family_name = ''
  errors.invite_code = ''
  errors.general = ''

  if (!form.name) {
    errors.name = 'Name is required'
  }

  if (!form.email) {
    errors.email = 'Email is required'
  } else if (!form.email.includes('@')) {
    errors.email = 'Invalid email format'
  }

  if (!form.password) {
    errors.password = 'Password is required'
  } else if (form.password.length < 8) {
    errors.password = 'Password must be at least 8 characters'
  }

  if (form.password !== form.password_confirmation) {
    errors.password_confirmation = 'Passwords do not match'
  }

  if (familyMode.value === 'new' && !form.family_name) {
    errors.family_name = 'Family name is required'
  }

  if (familyMode.value === 'join' && !form.invite_code) {
    errors.invite_code = 'Invite code is required'
  }

  return (
    !errors.name &&
    !errors.email &&
    !errors.password &&
    !errors.password_confirmation &&
    !errors.family_name &&
    !errors.invite_code
  )
}

const handleRegister = async () => {
  if (!validateForm()) return

  const data = {
    name: form.name,
    email: form.email,
    password: form.password,
    password_confirmation: form.password_confirmation,
  }

  if (familyMode.value === 'new') {
    data.family_name = form.family_name
  } else {
    data.invite_code = form.invite_code
  }

  const result = await authStore.register(data)

  if (result.success) {
    router.push({ name: 'Dashboard' })
  } else {
    errors.general = result.error || 'Registration failed. Please try again.'
    notificationError(errors.general)
  }
}

const handleGoogleSignup = async () => {
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
      errors.general = 'Failed to get Google signup URL.'
      googleLoading.value = false
    }
  } catch (err) {
    errors.general = 'Failed to connect to Google. Please try again.'
    googleLoading.value = false
  }
}
</script>
