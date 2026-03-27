<template>
  <div class="kin-page min-h-screen">
    <!-- Navigation -->
    <nav class="kin-nav">
      <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
        <router-link to="/" class="flex items-center gap-2">
          <img src="/images/logo-100.png" alt="Kinhold" class="w-8 h-8 rounded-lg" />
          <span class="kin-heading text-xl text-kin-black dark:text-kin-off-white">Kinhold</span>
        </router-link>
        <div class="flex items-center gap-3">
          <router-link to="/login" class="kin-btn-ghost text-sm">
            Sign In
          </router-link>
          <router-link to="/register" class="kin-btn-primary text-sm">
            Get Started
          </router-link>
        </div>
      </div>
    </nav>

    <!-- Hero -->
    <section class="relative overflow-hidden">
      <!-- Decorative gradient orbs -->
      <div class="absolute top-20 -left-32 w-96 h-96 bg-kin-gold/5 rounded-full blur-3xl" aria-hidden="true"></div>
      <div class="absolute bottom-10 -right-32 w-80 h-80 bg-kin-gold/5 rounded-full blur-3xl" aria-hidden="true"></div>

      <div class="max-w-4xl mx-auto px-6 pt-24 pb-20 lg:pt-36 lg:pb-32 text-center relative">
        <h1 class="kin-heading text-5xl sm:text-6xl lg:text-7xl text-kin-black dark:text-kin-off-white leading-tight">
          Your family's<br>private hub
        </h1>
        <p class="mt-6 text-lg lg:text-xl kin-muted max-w-xl mx-auto leading-relaxed">
          Calendar, tasks, vault, and AI &mdash; all in one place, all under your control.
          Open source. Self-hostable. Encrypted.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
          <router-link to="/register" class="kin-btn-primary text-base px-8 py-3.5">
            Create Your Family Hub
          </router-link>
          <a href="#features" class="kin-btn-secondary text-base px-8 py-3.5">
            See Features
          </a>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section id="features" class="kin-section">
      <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="kin-heading text-3xl lg:text-4xl text-kin-black dark:text-kin-off-white">
            Built for families who value their time
          </h2>
          <p class="mt-4 kin-muted text-lg max-w-2xl mx-auto">
            Everything your family needs to stay organized, in one calm, secure space.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="feature in features"
            :key="feature.title"
            class="kin-feature-card"
          >
            <div class="kin-icon-box mb-4">
              <component :is="feature.icon" class="w-5 h-5" />
            </div>
            <h3 class="font-heading font-semibold text-lg text-kin-black dark:text-kin-off-white">
              {{ feature.title }}
            </h3>
            <p class="mt-2 text-sm kin-muted leading-relaxed">
              {{ feature.description }}
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Differentiators -->
    <section class="kin-section bg-kin-cream dark:bg-kin-surface-dark">
      <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="kin-heading text-3xl lg:text-4xl text-kin-black dark:text-kin-off-white">
            Why families choose Kinhold
          </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
          <div
            v-for="diff in differentiators"
            :key="diff.title"
            class="flex gap-4"
          >
            <div class="kin-icon-box shrink-0 mt-0.5">
              <component :is="diff.icon" class="w-5 h-5" />
            </div>
            <div>
              <h3 class="font-heading font-semibold text-kin-black dark:text-kin-off-white">
                {{ diff.title }}
              </h3>
              <p class="mt-1 text-sm kin-muted leading-relaxed">
                {{ diff.description }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Final CTA -->
    <section class="kin-section">
      <div class="max-w-3xl mx-auto text-center">
        <h2 class="kin-heading text-3xl lg:text-4xl text-kin-black dark:text-kin-off-white">
          Get your family organized
        </h2>
        <p class="mt-4 kin-muted text-lg max-w-lg mx-auto">
          Free and open source. Set up your family hub in under two minutes.
        </p>
        <div class="mt-8">
          <router-link to="/register" class="kin-btn-primary text-base px-8 py-3.5">
            Create Your Family Hub
          </router-link>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-kin-border dark:border-kin-border-dark">
      <div class="max-w-6xl mx-auto px-6 py-10 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-3">
          <img src="/images/logo-100.png" alt="Kinhold" class="w-6 h-6 rounded-md" />
          <span class="font-heading font-semibold text-kin-black dark:text-kin-off-white">Kinhold</span>
          <span class="text-sm kin-muted">&copy; {{ new Date().getFullYear() }}</span>
        </div>
        <nav class="flex items-center gap-6 text-sm kin-muted" aria-label="Footer navigation">
          <router-link to="/privacy" class="hover:text-kin-black dark:hover:text-kin-off-white transition-colors">
            Privacy
          </router-link>
          <router-link to="/terms" class="hover:text-kin-black dark:hover:text-kin-off-white transition-colors">
            Terms
          </router-link>
          <a
            href="https://github.com/gregqualls/kinhold"
            target="_blank"
            rel="noopener noreferrer"
            class="hover:text-kin-black dark:hover:text-kin-off-white transition-colors"
          >
            GitHub
          </a>
          <router-link to="/login" class="hover:text-kin-black dark:hover:text-kin-off-white transition-colors">
            Sign In
          </router-link>
        </nav>
      </div>
    </footer>
  </div>
</template>

<script setup>
import {
  CalendarDaysIcon,
  ClipboardDocumentCheckIcon,
  ShieldCheckIcon,
  TrophyIcon,
  SparklesIcon,
  UserGroupIcon,
  LockClosedIcon,
  CodeBracketIcon,
  DevicePhoneMobileIcon,
} from '@heroicons/vue/24/outline'

const features = [
  {
    icon: CalendarDaysIcon,
    title: 'Shared Calendar',
    description: 'Aggregate Google Calendars for every family member. Color-coded, with month, week, and day views.',
  },
  {
    icon: ClipboardDocumentCheckIcon,
    title: 'Tasks & To-Dos',
    description: 'Family task lists with assignments, due dates, priorities, and recurring tasks that just work.',
  },
  {
    icon: ShieldCheckIcon,
    title: 'Encrypted Vault',
    description: 'Store SSNs, medical records, insurance, and financial docs. Encrypted at rest, with per-item access controls.',
  },
  {
    icon: TrophyIcon,
    title: 'Points & Rewards',
    description: 'Gamification that motivates everyone. Earn points, unlock badges, climb the leaderboard, redeem rewards.',
  },
  {
    icon: SparklesIcon,
    title: 'AI Assistant',
    description: "Ask questions about your family's data in plain English. Powered by Claude, with full MCP integration.",
  },
  {
    icon: UserGroupIcon,
    title: 'Family Profiles',
    description: 'Parent and child roles with fine-grained permissions. Parents control what kids can see and do.',
  },
]

const differentiators = [
  {
    icon: LockClosedIcon,
    title: 'Privacy first',
    description: 'Your data stays yours. Encrypted vault, no ads, no tracking. Self-host it or use our hosted service.',
  },
  {
    icon: CodeBracketIcon,
    title: 'Open source',
    description: 'MIT licensed. Fork it, host it, own it. No vendor lock-in, ever. Pull updates from upstream anytime.',
  },
  {
    icon: SparklesIcon,
    title: 'AI-native',
    description: 'Manage your family hub through Claude or any AI assistant. Full MCP server with 26 tools built in.',
  },
  {
    icon: DevicePhoneMobileIcon,
    title: 'Mobile ready',
    description: 'Built mobile-first. Works beautifully on every screen size, from your phone to your desktop.',
  },
]
</script>
