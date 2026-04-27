<template>
  <div class="kin-page min-h-screen flex items-center justify-center p-4 py-10">
    <div class="w-full max-w-3xl">
      <!-- Logo -->
      <div class="text-center mb-8">
        <router-link to="/login" class="inline-flex flex-col items-center gap-3">
          <img src="/images/logo-100.png" alt="Kinhold" class="w-16 h-16 rounded-2xl" />
          <h1 class="text-4xl font-heading font-bold text-kin-gold">Kinhold</h1>
        </router-link>
        <p class="kin-muted mt-2">Try the live demo</p>
      </div>

      <KinFlatCard padding="lg">
        <!-- Intro -->
        <div class="text-center mb-6">
          <h2 class="text-2xl font-heading font-bold text-ink-primary mb-2">
            Meet the Johnson family
          </h2>
          <p class="kin-muted max-w-xl mx-auto">
            The demo is a fully working Kinhold instance pre-loaded with a family of five —
            calendar events, tasks, vault entries, points, badges, recipes, the works.
            Pick any family member below to log in as them and explore the app from their
            perspective. Switch between accounts anytime by signing out and coming back here.
          </p>
        </div>

        <!-- Member picker -->
        <div v-if="demoAvailable" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
          <button
            v-for="member in members"
            :key="member.key"
            :disabled="loadingMember !== null"
            class="relative flex flex-col items-center gap-2 p-4 rounded-xl border border-border-subtle hover:border-accent-lavender-bold hover:bg-surface-sunken transition-all text-left disabled:opacity-50 disabled:cursor-not-allowed"
            @click="handleSelect(member.key)"
          >
            <!-- Loading overlay -->
            <div
              v-if="loadingMember === member.key"
              class="absolute inset-0 flex items-center justify-center bg-surface-raised/60 rounded-xl"
            >
              <LoadingSpinner size="sm" />
            </div>

            <!-- Avatar circle -->
            <div
              class="w-14 h-14 rounded-full flex items-center justify-center text-white font-bold text-xl"
              :style="{ backgroundColor: member.color }"
            >
              {{ member.name[0] }}
            </div>

            <!-- Name & role -->
            <div class="text-center">
              <div class="font-semibold text-sm text-ink-primary">{{ member.name }}</div>
              <span
                class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full"
                :class="
                  member.role === 'Parent'
                    ? 'bg-accent-lavender-soft/40 text-accent-lavender-bold'
                    : 'bg-accent-sun-soft/40 text-accent-sun-bold'
                "
              >
                {{ member.role }}
              </span>
              <div class="text-xs kin-muted mt-1">{{ member.description }}</div>
            </div>
          </button>
        </div>

        <!-- Demo unavailable -->
        <div
          v-else
          class="p-4 bg-status-failed/10 border border-status-failed/30 rounded-[10px] text-center"
        >
          <p class="text-sm text-status-failed">
            The demo isn't available on this instance right now. Please try again later.
          </p>
        </div>

        <!-- Error -->
        <p v-if="errorMsg" class="text-sm text-status-failed text-center mb-4">
          {{ errorMsg }}
        </p>

        <!-- What you'll see -->
        <div class="border-t border-border-subtle pt-6 mt-2">
          <h3 class="text-sm font-semibold text-ink-primary mb-3 text-center">
            What's inside the demo
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div
              v-for="item in highlights"
              :key="item.title"
              class="p-3 rounded-[10px] bg-surface-sunken"
            >
              <div class="text-sm font-semibold text-ink-primary">{{ item.title }}</div>
              <div class="text-xs kin-muted mt-1">{{ item.description }}</div>
            </div>
          </div>
        </div>

        <!-- Footer links -->
        <div class="border-t border-border-subtle mt-6 pt-6 text-center">
          <p class="kin-muted text-sm">
            Already have an account?
            <RouterLink to="/login" class="kin-link font-medium">Sign in</RouterLink>
            &nbsp;·&nbsp;
            <RouterLink to="/register" class="kin-link font-medium">Create one</RouterLink>
          </p>
        </div>
      </KinFlatCard>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import KinFlatCard from '@/components/design-system/KinFlatCard.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const router = useRouter()
const authStore = useAuthStore()

const demoAvailable = computed(() => authStore.appConfig?.demo_available)
const loadingMember = ref(null)
const errorMsg = ref('')

const members = [
  { key: 'mike', name: 'Mike', role: 'Parent', description: 'Dad', color: '#5B8C9C' },
  { key: 'sarah', name: 'Sarah', role: 'Parent', description: 'Mom', color: '#8B5B9C' },
  { key: 'emma', name: 'Emma', role: 'Teen', description: 'Age 16', color: '#9C5B7B' },
  { key: 'jake', name: 'Jake', role: 'Kid', description: 'Age 13', color: '#5B6B9C' },
  { key: 'lily', name: 'Lily', role: 'Kid', description: 'Age 9', color: '#5B9C7B' },
]

const highlights = [
  {
    title: 'Calendar & tasks',
    description: 'A populated week, recurring chores, kid-assigned tasks with points.',
  },
  {
    title: 'Vault & recipes',
    description: 'Encrypted family docs, meal plan, shopping list, and a recipe library.',
  },
  {
    title: 'Points & badges',
    description: 'Live leaderboard, kudos, the rewards shop, and earned achievements.',
  },
]

const handleSelect = async (key) => {
  loadingMember.value = key
  errorMsg.value = ''

  const result = await authStore.demoLogin(key)

  if (result.success) {
    router.push({ name: 'Dashboard' })
  } else {
    errorMsg.value = result.error || 'Something went wrong. Please try again.'
    loadingMember.value = null
  }
}
</script>
