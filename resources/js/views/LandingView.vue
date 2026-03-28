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

      <div class="max-w-4xl mx-auto px-6 pt-24 pb-12 lg:pt-36 lg:pb-16 text-center relative">
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

        <!-- Social proof -->
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
          <div class="flex items-center gap-3 text-sm kin-muted">
            <span>MIT Licensed</span>
            <span class="hidden sm:inline text-kin-border dark:text-kin-border-dark">&middot;</span>
            <span>Open Source</span>
            <span class="hidden sm:inline text-kin-border dark:text-kin-border-dark">&middot;</span>
            <span>Self-Hosted</span>
          </div>
          <a href="https://github.com/gregqualls/kinhold" target="_blank" rel="noopener noreferrer">
            <img
              src="https://img.shields.io/github/stars/gregqualls/kinhold?style=social"
              alt="GitHub stars"
              class="h-5"
            />
          </a>
        </div>

        <!-- Hero screenshot -->
        <div class="mt-12 max-w-5xl mx-auto">
          <div class="rounded-xl shadow-card-xl border border-kin-border dark:border-kin-border-dark overflow-hidden">
            <img
              src="/images/screenshots/dashboard-hero.png"
              alt="Kinhold dashboard"
              class="w-full h-auto"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Product Showcase -->
    <section class="kin-section">
      <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
          <h2 class="kin-heading text-3xl lg:text-4xl text-kin-black dark:text-kin-off-white">
            See it in action
          </h2>
        </div>

        <!-- Tab buttons -->
        <div class="flex items-center justify-center gap-2 mb-10">
          <button
            v-for="tab in showcaseTabs"
            :key="tab.id"
            @click="activeShowcaseTab = tab.id"
            class="kin-btn-ghost text-sm px-5 py-2.5 transition-all duration-200"
            :class="activeShowcaseTab === tab.id
              ? 'bg-kin-gold/10 text-kin-gold border-b-2 border-kin-gold font-semibold'
              : ''"
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- Tab content -->
        <div v-for="tab in showcaseTabs" :key="tab.id" v-show="activeShowcaseTab === tab.id">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center max-w-5xl mx-auto">
            <!-- Screenshot -->
            <div class="rounded-xl shadow-card-xl border border-kin-border dark:border-kin-border-dark overflow-hidden order-1 lg:order-1">
              <img
                :src="tab.image"
                :alt="tab.label + ' preview'"
                class="w-full h-auto"
              />
            </div>
            <!-- Bullets -->
            <div class="order-2 lg:order-2 space-y-5">
              <div v-for="(bullet, i) in tab.bullets" :key="i" class="flex gap-3">
                <div class="kin-icon-box shrink-0 w-8 h-8 mt-0.5">
                  <component :is="tab.icon" class="w-4 h-4" />
                </div>
                <p class="text-sm lg:text-base text-kin-black dark:text-kin-off-white leading-relaxed">
                  {{ bullet }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section id="features" class="kin-section bg-kin-cream dark:bg-kin-surface-dark">
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

    <!-- Gamification Spotlight -->
    <section class="kin-section">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center max-w-6xl mx-auto">
        <!-- Left column: text -->
        <div>
          <h2 class="kin-heading text-3xl lg:text-4xl text-kin-black dark:text-kin-off-white">
            Turn chores into a game your kids want to play
          </h2>
          <p class="mt-4 kin-muted text-lg leading-relaxed">
            A built-in gamification system that turns mundane household tasks into motivating challenges.
            Kids earn, compete, and spend &mdash; all within the family.
          </p>
          <div class="mt-8 space-y-6">
            <div v-for="item in gamificationHighlights" :key="item.title" class="flex gap-4">
              <div class="kin-icon-box shrink-0 mt-0.5">
                <component :is="item.icon" class="w-5 h-5" />
              </div>
              <div>
                <h3 class="font-heading font-semibold text-kin-black dark:text-kin-off-white">
                  {{ item.title }}
                </h3>
                <p class="mt-1 text-sm kin-muted leading-relaxed">
                  {{ item.description }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <!-- Right column: screenshot -->
        <div class="rounded-xl shadow-card-xl border border-kin-border dark:border-kin-border-dark overflow-hidden">
          <img
            src="/images/screenshots/gamification.png"
            alt="Gamification features"
            class="w-full h-auto"
          />
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

    <!-- Self-Hosting CTA -->
    <section class="kin-section">
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="kin-heading text-3xl lg:text-4xl text-kin-black dark:text-kin-off-white">
          Your data, your server
        </h2>
        <p class="mt-4 kin-muted text-lg max-w-2xl mx-auto">
          Fork, configure, deploy. Your family's data never touches our servers.
        </p>

        <!-- Steps -->
        <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-8 max-w-3xl mx-auto">
          <div v-for="step in selfHostSteps" :key="step.title" class="flex flex-col items-center">
            <div class="kin-icon-box w-14 h-14 rounded-xl mb-4">
              <component :is="step.icon" class="w-6 h-6" />
            </div>
            <h3 class="font-heading font-semibold text-kin-black dark:text-kin-off-white text-sm">
              {{ step.title }}
            </h3>
            <p class="mt-1 text-xs kin-muted">{{ step.subtitle }}</p>
          </div>
        </div>

        <!-- CTA buttons -->
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
          <a
            href="https://github.com/gregqualls/kinhold"
            target="_blank"
            rel="noopener noreferrer"
            class="kin-btn-secondary text-base px-8 py-3.5"
          >
            View on GitHub
          </a>
          <router-link to="/register" class="kin-btn-primary text-base px-8 py-3.5">
            Get Started
          </router-link>
        </div>
      </div>
    </section>

    <!-- Final CTA -->
    <section class="kin-section bg-kin-cream dark:bg-kin-surface-dark">
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
import { ref } from 'vue'
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
  GiftIcon,
  ChartBarIcon,
  RocketLaunchIcon,
  WrenchScrewdriverIcon,
} from '@heroicons/vue/24/outline'

const activeShowcaseTab = ref('tasks')

const showcaseTabs = [
  {
    id: 'tasks',
    label: 'Tasks',
    icon: ClipboardDocumentCheckIcon,
    image: '/images/screenshots/tasks-preview.png',
    bullets: [
      'Multiple task lists with priorities and due dates',
      'Assign to family members or leave open for anyone',
      'Recurring tasks auto-generate daily, weekly, or monthly',
    ],
  },
  {
    id: 'calendar',
    label: 'Calendar',
    icon: CalendarDaysIcon,
    image: '/images/screenshots/dashboard-hero.png',
    bullets: [
      'Manual events for the whole family',
      'Google Calendar sync for connected accounts',
      'Color-coded per family member',
    ],
  },
  {
    id: 'gamification',
    label: 'Gamification',
    icon: TrophyIcon,
    image: '/images/screenshots/gamification.png',
    bullets: [
      'Points for completing tasks',
      'Steam-style badges with hidden achievements',
      'Rewards store where kids spend points',
    ],
  },
]

const features = [
  {
    icon: CalendarDaysIcon,
    title: 'Shared Calendar',
    description: 'Add events manually or sync Google Calendars for every family member. Color-coded, with month, week, and day views.',
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
    description: "Ask questions about your family's data in plain English. Powered by Claude, with full MCP integration across every module.",
  },
  {
    icon: UserGroupIcon,
    title: 'Family Profiles',
    description: 'Parent and child roles with fine-grained permissions. Parents control what kids can see and do.',
  },
]

const gamificationHighlights = [
  {
    icon: TrophyIcon,
    title: 'Points',
    description: 'Earn by completing tasks. Parents give kudos for extra effort.',
  },
  {
    icon: ChartBarIcon,
    title: 'Leaderboard',
    description: 'Family rankings that reset weekly. Friendly competition.',
  },
  {
    icon: ShieldCheckIcon,
    title: 'Badges',
    description: '27 achievements \u2014 some hidden until earned. Collect them all.',
  },
  {
    icon: GiftIcon,
    title: 'Rewards',
    description: 'Parents set prizes with point costs. Kids redeem instantly.',
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
    description: 'Manage your family hub through Claude or any AI assistant. Full MCP server with tools for every module.',
  },
  {
    icon: DevicePhoneMobileIcon,
    title: 'Mobile ready',
    description: 'Built mobile-first. Works beautifully on every screen size, from your phone to your desktop.',
  },
]

const selfHostSteps = [
  {
    icon: CodeBracketIcon,
    title: 'Fork the Repo',
    subtitle: 'Clone from GitHub',
  },
  {
    icon: WrenchScrewdriverIcon,
    title: 'Set Your Config',
    subtitle: 'Environment variables',
  },
  {
    icon: RocketLaunchIcon,
    title: 'Deploy Anywhere',
    subtitle: 'Upsun, Docker, or VPS',
  },
]
</script>
