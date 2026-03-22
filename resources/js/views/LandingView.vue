<template>
  <div class="min-h-screen bg-lavender-50 dark:bg-prussian-900">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-white/80 dark:bg-prussian-800/80 backdrop-blur-md border-b border-lavender-200 dark:border-prussian-700">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center gap-2">
            <span class="text-2xl">&#x1F3E0;</span>
            <span class="text-xl font-bold text-wisteria-600 dark:text-wisteria-400">Q32 Hub</span>
          </div>
          <div class="flex items-center gap-3">
            <RouterLink
              to="/login"
              class="btn-ghost btn-sm"
            >
              Sign In
            </RouterLink>
            <RouterLink
              to="/register"
              class="btn-primary btn-sm"
            >
              Get Started
            </RouterLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20 sm:pt-24 sm:pb-28">
        <div class="text-center max-w-3xl mx-auto">
          <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-prussian-500 dark:text-lavender-100 leading-tight">
            Your Family's
            <span class="text-wisteria-600 dark:text-wisteria-400"> Digital Hub</span>
          </h1>
          <p class="mt-6 text-lg sm:text-xl text-lavender-600 dark:text-lavender-400 max-w-2xl mx-auto leading-relaxed">
            One place for your family's calendar, tasks, important documents, and more.
            Stay organized, motivated, and connected — together.
          </p>
          <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <RouterLink
              to="/register"
              class="btn-primary btn-lg w-full sm:w-auto text-base"
            >
              Get Started Free
            </RouterLink>
            <RouterLink
              to="/login"
              class="btn-secondary btn-lg w-full sm:w-auto text-base"
            >
              Sign In
            </RouterLink>
          </div>
        </div>

        <!-- Visual element: floating emoji cards -->
        <div class="mt-16 flex justify-center gap-4 sm:gap-6 flex-wrap" aria-hidden="true">
          <div
            v-for="item in heroIcons"
            :key="item.emoji"
            class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white dark:bg-prussian-800 shadow-lg flex items-center justify-center text-2xl sm:text-3xl border border-lavender-200 dark:border-prussian-700 hover:scale-110 transition-transform duration-300"
          >
            {{ item.emoji }}
          </div>
        </div>
      </div>

      <!-- Gradient decoration -->
      <div class="absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-wisteria-200/30 dark:bg-wisteria-600/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full bg-sand-200/30 dark:bg-sand-600/10 blur-3xl"></div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 sm:py-24 bg-white dark:bg-prussian-800 border-t border-b border-lavender-200 dark:border-prussian-700">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16">
          <h2 class="text-3xl sm:text-4xl font-bold text-prussian-500 dark:text-lavender-100">
            Everything your family needs
          </h2>
          <p class="mt-4 text-lg text-lavender-600 dark:text-lavender-400 max-w-xl mx-auto">
            Manage your household from a single dashboard. Built for real families.
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="feature in features"
            :key="feature.title"
            class="group p-6 rounded-2xl bg-lavender-50 dark:bg-prussian-700/50 border border-lavender-200 dark:border-prussian-600 hover:border-wisteria-400 dark:hover:border-wisteria-600 transition-all duration-300 hover:shadow-lg"
          >
            <div class="w-12 h-12 rounded-xl bg-wisteria-100 dark:bg-wisteria-600/20 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform duration-300">
              {{ feature.emoji }}
            </div>
            <h3 class="text-lg font-semibold text-prussian-500 dark:text-lavender-100 mb-2">
              {{ feature.title }}
            </h3>
            <p class="text-sm text-lavender-600 dark:text-lavender-400 leading-relaxed">
              {{ feature.description }}
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- How it Works Section -->
    <section class="py-16 sm:py-24">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl sm:text-4xl font-bold text-prussian-500 dark:text-lavender-100">
            Get started in minutes
          </h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 max-w-3xl mx-auto">
          <div
            v-for="(step, index) in steps"
            :key="step.title"
            class="text-center"
          >
            <div class="w-12 h-12 rounded-full bg-wisteria-600 dark:bg-wisteria-500 text-white font-bold text-lg flex items-center justify-center mx-auto mb-4">
              {{ index + 1 }}
            </div>
            <h3 class="font-semibold text-prussian-500 dark:text-lavender-100 mb-1">
              {{ step.title }}
            </h3>
            <p class="text-sm text-lavender-600 dark:text-lavender-400">
              {{ step.description }}
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Open Source Section -->
    <section class="py-16 sm:py-24 bg-white dark:bg-prussian-800 border-t border-b border-lavender-200 dark:border-prussian-700">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto">
          <div class="text-4xl mb-4" aria-hidden="true">&#x1F4BB;</div>
          <h2 class="text-3xl sm:text-4xl font-bold text-prussian-500 dark:text-lavender-100 mb-4">
            Open Source & Self-Hostable
          </h2>
          <p class="text-lg text-lavender-600 dark:text-lavender-400 leading-relaxed mb-8">
            Q32 Hub is free and open source under the MIT license. Deploy your own instance
            for your family on any hosting provider. Your data, your server, your rules.
          </p>
          <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a
              href="https://github.com/gregqualls/q32hub"
              target="_blank"
              rel="noopener noreferrer"
              class="btn-secondary btn-lg inline-flex items-center gap-2 w-full sm:w-auto"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
              </svg>
              View on GitHub
            </a>
          </div>
          <p class="mt-6 text-sm text-lavender-500 dark:text-lavender-500">
            Built with Laravel, Vue.js, Tailwind CSS, and PostgreSQL
          </p>
        </div>
      </div>
    </section>

    <!-- Final CTA -->
    <section class="py-16 sm:py-24">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold text-prussian-500 dark:text-lavender-100 mb-4">
          Ready to get your family organized?
        </h2>
        <p class="text-lg text-lavender-600 dark:text-lavender-400 mb-8 max-w-lg mx-auto">
          Create your family hub in seconds. Free, private, and open source.
        </p>
        <RouterLink
          to="/register"
          class="btn-primary btn-lg text-base"
        >
          Create Your Family Hub
        </RouterLink>
      </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 border-t border-lavender-200 dark:border-prussian-700">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <p class="text-sm text-lavender-500 dark:text-lavender-500">
            Built with love by the Qualls family
          </p>
          <div class="flex items-center gap-6 text-sm">
            <RouterLink
              to="/privacy"
              class="text-lavender-500 dark:text-lavender-500 hover:text-wisteria-600 dark:hover:text-wisteria-400 transition-colors"
            >
              Privacy
            </RouterLink>
            <RouterLink
              to="/terms"
              class="text-lavender-500 dark:text-lavender-500 hover:text-wisteria-600 dark:hover:text-wisteria-400 transition-colors"
            >
              Terms
            </RouterLink>
            <a
              href="https://github.com/gregqualls/q32hub"
              target="_blank"
              rel="noopener noreferrer"
              class="text-lavender-500 dark:text-lavender-500 hover:text-wisteria-600 dark:hover:text-wisteria-400 transition-colors"
            >
              GitHub
            </a>
            <RouterLink
              to="/login"
              class="text-lavender-500 dark:text-lavender-500 hover:text-wisteria-600 dark:hover:text-wisteria-400 transition-colors"
            >
              Sign In
            </RouterLink>
            <RouterLink
              to="/register"
              class="text-lavender-500 dark:text-lavender-500 hover:text-wisteria-600 dark:hover:text-wisteria-400 transition-colors"
            >
              Sign Up
            </RouterLink>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
const heroIcons = [
  { emoji: '\u{1F4C5}' },
  { emoji: '\u{2705}' },
  { emoji: '\u{1F512}' },
  { emoji: '\u{1F3C6}' },
  { emoji: '\u{1F916}' },
  { emoji: '\u{1F46A}' },
]

const features = [
  {
    emoji: '\u{1F4C5}',
    title: 'Family Calendar',
    description: 'See everyone\'s schedule in one place. Integrates with Google Calendar so nothing gets missed.',
  },
  {
    emoji: '\u{2705}',
    title: 'Tasks & To-Dos',
    description: 'Assign chores, track homework, and manage grocery lists. Recurring tasks run on autopilot.',
  },
  {
    emoji: '\u{1F512}',
    title: 'Family Vault',
    description: 'Store sensitive documents securely. Medical records, insurance info, and passwords — all encrypted.',
  },
  {
    emoji: '\u{1F3C6}',
    title: 'Points & Rewards',
    description: 'Gamify chores with a points system. Kids earn rewards, climb the leaderboard, and unlock badges.',
  },
  {
    emoji: '\u{1F916}',
    title: 'AI Assistant',
    description: 'Ask questions about your family data in plain English. Powered by Claude, OpenAI, or Gemini.',
  },
  {
    emoji: '\u{1F396}\u{FE0F}',
    title: 'Badges & Achievements',
    description: 'Unlock achievements for completing tasks, earning points, and building streaks. Fun for the whole family.',
  },
]

const steps = [
  {
    title: 'Create your family',
    description: 'Sign up and name your family hub.',
  },
  {
    title: 'Invite members',
    description: 'Share an invite code with your family.',
  },
  {
    title: 'Get organized',
    description: 'Start managing your family life together.',
  },
]
</script>
