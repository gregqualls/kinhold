<template>
  <div class="min-h-screen bg-surface-app">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-surface-raised/80 backdrop-blur-md border-b border-border-subtle">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <RouterLink to="/login" class="flex items-center gap-2">
            <span class="text-2xl">&#x1F3E0;</span>
            <span class="text-xl font-bold text-accent-lavender-bold">Kinhold</span>
          </RouterLink>
          <div class="flex items-center gap-3">
            <RouterLink to="/login" class="btn-ghost btn-sm">Sign In</RouterLink>
            <RouterLink to="/register" class="btn-primary btn-sm">Get Started</RouterLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <h1 class="text-3xl font-bold text-ink-primary mb-2">Terms of Service</h1>
      <p class="text-sm text-ink-secondary mb-10">Last updated: March 22, 2026</p>

      <div class="prose-container space-y-8 text-ink-secondary">
        <section>
          <h2>1. What Kinhold Is</h2>
          <p>
            Kinhold is an open-source family management application that helps households organize tasks,
            calendars, sensitive documents, and family activities. It is provided as-is under the
            <a href="https://github.com/gregqualls/kinhold/blob/main/LICENSE" target="_blank" rel="noopener" class="text-accent-lavender-bold hover:underline">Elastic License 2.0</a>.
          </p>
        </section>

        <section>
          <h2>2. Acceptance of Terms</h2>
          <p>
            By creating an account or using Kinhold at <strong>kinhold.app</strong>,
            you agree to these Terms of Service and our
            <RouterLink to="/privacy" class="text-accent-lavender-bold hover:underline">Privacy Policy</RouterLink>.
            If you do not agree, do not use the service.
          </p>
        </section>

        <section>
          <h2>3. Accounts</h2>
          <ul>
            <li>You must provide accurate information when creating an account.</li>
            <li>You are responsible for maintaining the security of your account credentials.</li>
            <li>Parent accounts can create and manage child accounts for family members under 18.</li>
            <li>One family per account. Each family gets their own isolated data space.</li>
          </ul>
        </section>

        <section>
          <h2>4. Acceptable Use</h2>
          <p>You agree to use Kinhold only for its intended purpose: managing your family's household. You will not:</p>
          <ul>
            <li>Use the service for any unlawful purpose</li>
            <li>Attempt to access other families' data</li>
            <li>Interfere with or disrupt the service</li>
            <li>Use automated tools to scrape or abuse the service</li>
            <li>Upload malicious files or content</li>
          </ul>
        </section>

        <section>
          <h2>5. Your Data</h2>
          <p>
            You own your data. We do not claim any intellectual property rights over the content
            you store in Kinhold (tasks, vault entries, chat messages, etc.).
          </p>
          <p>
            See our <RouterLink to="/privacy" class="text-accent-lavender-bold hover:underline">Privacy Policy</RouterLink>
            for details on how we collect, store, and protect your data.
          </p>
        </section>

        <section>
          <h2>6. Third-Party Services</h2>
          <p>Kinhold integrates with third-party services that have their own terms:</p>
          <ul>
            <li><strong>Google</strong> — For OAuth sign-in and Google Calendar integration. Subject to <a href="https://policies.google.com/terms" target="_blank" rel="noopener" class="text-accent-lavender-bold hover:underline">Google's Terms of Service</a>.</li>
            <li><strong>Anthropic</strong> — For AI chat features. Your chat queries and relevant family data are sent to Anthropic's API to generate responses.</li>
          </ul>
          <p>
            You can disconnect third-party services at any time from Settings.
            Disabling the AI chat feature prevents any data from being sent to Anthropic.
          </p>
        </section>

        <section>
          <h2>7. Google API Services</h2>
          <p>
            Kinhold's use and transfer of information received from Google APIs adheres to the
            <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener" class="text-accent-lavender-bold hover:underline">Google API Services User Data Policy</a>,
            including the Limited Use requirements.
          </p>
          <p>Specifically:</p>
          <ul>
            <li>We only request the minimum Google API scopes needed (profile, email, calendar read-only).</li>
            <li>We do not use Google data for advertising or sell it to third parties.</li>
            <li>We do not use Google data to build user profiles for purposes unrelated to the app's functionality.</li>
            <li>Access to Google Calendar data is used solely to display your calendar events within Kinhold.</li>
          </ul>
        </section>

        <section>
          <h2>8. Children</h2>
          <p>
            Kinhold is designed for families with children. Parent accounts are responsible for
            creating and managing child accounts. Children under 13 use managed accounts created
            by their parents and do not provide personal information directly.
          </p>
        </section>

        <section>
          <h2>9. Availability &amp; Warranties</h2>
          <p>
            Kinhold is provided <strong>"as is"</strong> without warranty of any kind.
            We do not guarantee that the service will be available at all times, uninterrupted, or error-free.
            We may modify, suspend, or discontinue the service at any time.
          </p>
        </section>

        <section>
          <h2>10. Limitation of Liability</h2>
          <p>
            To the maximum extent permitted by law, Kinhold and its creator shall not be liable
            for any indirect, incidental, special, consequential, or punitive damages arising from
            your use of the service. This includes but is not limited to loss of data, loss of
            profits, or interruption of service.
          </p>
        </section>

        <section>
          <h2>11. Account Termination</h2>
          <p>
            You may delete your account at any time by contacting
            <a href="mailto:glqualls@gmail.com" class="text-accent-lavender-bold hover:underline">glqualls@gmail.com</a>.
            We reserve the right to suspend or terminate accounts that violate these terms.
          </p>
        </section>

        <section>
          <h2>12. Self-Hosting</h2>
          <p>
            Kinhold is open-source software. If you self-host your own instance, these Terms of Service
            apply only to the hosted instance at <strong>kinhold.app</strong>.
            Self-hosted instances are your responsibility.
          </p>
        </section>

        <section>
          <h2>13. Changes to These Terms</h2>
          <p>
            We may update these terms as the service evolves. Continued use of Kinhold after
            changes constitutes acceptance of the updated terms. We will communicate significant
            changes through the app.
          </p>
        </section>

        <section>
          <h2>14. Contact</h2>
          <p>
            Questions about these terms? Email
            <a href="mailto:glqualls@gmail.com" class="text-accent-lavender-bold hover:underline">glqualls@gmail.com</a>.
          </p>
        </section>
      </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-border-subtle mt-16 py-8 text-center text-sm text-ink-tertiary">
      <div class="max-w-4xl mx-auto px-4">
        <p>&copy; {{ new Date().getFullYear() }} Kinhold &mdash; Open source under the Elastic License 2.0</p>
        <div class="mt-2 flex items-center justify-center gap-4">
          <RouterLink to="/login" class="hover:text-accent-lavender-bold transition-colors">Home</RouterLink>
          <span>&bull;</span>
          <RouterLink to="/privacy" class="hover:text-accent-lavender-bold transition-colors">Privacy</RouterLink>
          <span>&bull;</span>
          <a href="https://github.com/gregqualls/kinhold" target="_blank" rel="noopener" class="hover:text-accent-lavender-bold transition-colors">GitHub</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.prose-container h2 {
  @apply text-xl font-semibold text-ink-primary mt-8 mb-3;
}
.prose-container h3 {
  @apply text-base font-semibold text-ink-primary mt-4 mb-2;
}
.prose-container p {
  @apply leading-relaxed mb-3;
}
.prose-container ul {
  @apply list-disc pl-6 space-y-1 mb-3;
}
</style>
