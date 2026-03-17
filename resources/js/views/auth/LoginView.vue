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
</script>
