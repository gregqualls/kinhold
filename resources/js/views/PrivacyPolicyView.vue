<template>
  <div class="min-h-screen bg-lavender-50 dark:bg-prussian-900">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-white/80 dark:bg-prussian-800/80 backdrop-blur-md border-b border-lavender-200 dark:border-prussian-700">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <RouterLink to="/dashboard" class="flex items-center gap-2">
            <span class="text-2xl">&#x1F3E0;</span>
            <span class="text-xl font-bold text-wisteria-600 dark:text-wisteria-400">Kinhold</span>
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
      <h1 class="text-3xl font-bold text-prussian-500 dark:text-lavender-100 mb-2">Privacy Policy</h1>
      <p class="text-sm text-prussian-400 dark:text-lavender-400 mb-10">Last updated: March 22, 2026</p>

      <div class="prose-container space-y-8 text-prussian-500 dark:text-lavender-300">
        <section>
          <h2>Overview</h2>
          <p>
            Kinhold ("we", "our", "the app") is an open-source family management application.
            We respect your privacy and are committed to protecting the personal data you share with us.
            This policy explains what data we collect, why, and how we handle it.
          </p>
        </section>

        <section>
          <h2>Data We Collect</h2>

          <h3>Account Information</h3>
          <p>When you create an account, we collect:</p>
          <ul>
            <li>Name and email address</li>
            <li>Password (hashed, never stored in plain text)</li>
            <li>Family role (parent or child)</li>
            <li>Optional: date of birth, avatar, timezone</li>
          </ul>

          <h3>Google Account Data</h3>
          <p>If you sign in with Google or connect Google Calendar, we access:</p>
          <ul>
            <li>Your Google profile (name, email, avatar) for authentication</li>
            <li>Your Google Calendar events (read-only) to display them in the family calendar</li>
          </ul>
          <p>
            We store OAuth tokens (encrypted at rest) to maintain your calendar connection.
            We do <strong>not</strong> read your Gmail, Drive, or any other Google service.
            We only request the minimum scopes needed: profile, email, and calendar read access.
          </p>

          <h3>Family Data</h3>
          <p>Data you create within the app:</p>
          <ul>
            <li>Tasks, task lists, and completion history</li>
            <li>Vault entries (encrypted at rest using Laravel's encryption)</li>
            <li>Chat messages with the AI assistant</li>
            <li>Points, badges, and reward transactions</li>
            <li>Family settings and preferences</li>
          </ul>

          <h3>Automatically Collected</h3>
          <p>Standard web server logs may include IP addresses and browser information. We do not use analytics or tracking scripts.</p>
        </section>

        <section>
          <h2>How We Use Your Data</h2>
          <p>Your data is used exclusively to provide the Kinhold service to your family:</p>
          <ul>
            <li>Authenticate you and manage your session</li>
            <li>Display your family's calendar, tasks, and vault</li>
            <li>Power the AI chat feature (your queries and relevant family data are sent to the AI provider you configure)</li>
            <li>Track points, badges, and rewards within your family</li>
          </ul>
          <p>We do <strong>not</strong> sell, share, or monetize your data. Period.</p>
        </section>

        <section>
          <h2>Data Storage &amp; Security</h2>
          <ul>
            <li>The app is hosted on <a href="https://upsun.com" target="_blank" rel="noopener" class="text-wisteria-600 dark:text-wisteria-400 hover:underline">Upsun</a> (Platform.sh) with infrastructure in the EU</li>
            <li>Vault entries containing sensitive information are encrypted at rest using AES-256 via Laravel's encryption</li>
            <li>Google OAuth tokens are encrypted at rest</li>
            <li>Passwords are hashed with bcrypt</li>
            <li>All connections use HTTPS/TLS</li>
          </ul>
        </section>

        <section>
          <h2>Third-Party Services</h2>
          <table>
            <thead>
              <tr>
                <th>Service</th>
                <th>Purpose</th>
                <th>Data Shared</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Google (OAuth + Calendar API)</td>
                <td>Authentication and calendar sync</td>
                <td>OAuth tokens; calendar events are read, not written</td>
              </tr>
              <tr>
                <td>Anthropic (Claude API)</td>
                <td>AI chat feature</td>
                <td>Your chat messages and relevant family data context (tasks, calendar, vault) to answer questions</td>
              </tr>
              <tr>
                <td>Upsun / Platform.sh</td>
                <td>Application hosting</td>
                <td>All application data resides on their infrastructure</td>
              </tr>
            </tbody>
          </table>
          <p>No advertising networks, analytics platforms, or data brokers receive your data.</p>
        </section>

        <section>
          <h2>Self-Hosting</h2>
          <p>
            Kinhold is open source (MIT license). If you self-host, your data stays entirely on your own infrastructure.
            This privacy policy applies only to the instance at <strong>kinhold.com</strong> operated by Greg Qualls.
          </p>
        </section>

        <section>
          <h2>Children's Privacy</h2>
          <p>
            Kinhold is designed for families with children. Child accounts are created and managed by parents.
            Children under 13 do not provide their own email — parents create managed accounts for them.
            Parents control what data children can access via role-based permissions and feature toggles.
          </p>
        </section>

        <section>
          <h2>Data Retention &amp; Deletion</h2>
          <p>
            Your data is retained as long as your family account is active.
            To delete your account and all associated data, contact
            <a href="mailto:glqualls@gmail.com" class="text-wisteria-600 dark:text-wisteria-400 hover:underline">glqualls@gmail.com</a>.
            We will delete all your family's data within 30 days of the request.
          </p>
        </section>

        <section>
          <h2>Your Rights</h2>
          <p>You have the right to:</p>
          <ul>
            <li>Access all data we store about you and your family</li>
            <li>Correct inaccurate data</li>
            <li>Request deletion of your data</li>
            <li>Disconnect Google Calendar at any time from Settings</li>
            <li>Revoke Google access from your <a href="https://myaccount.google.com/permissions" target="_blank" rel="noopener" class="text-wisteria-600 dark:text-wisteria-400 hover:underline">Google Account permissions</a></li>
          </ul>
        </section>

        <section>
          <h2>Changes to This Policy</h2>
          <p>
            We may update this policy as the app evolves. Significant changes will be communicated
            through the app. The "last updated" date at the top reflects the most recent revision.
          </p>
        </section>

        <section>
          <h2>Contact</h2>
          <p>
            Questions about this policy? Email
            <a href="mailto:glqualls@gmail.com" class="text-wisteria-600 dark:text-wisteria-400 hover:underline">glqualls@gmail.com</a>.
          </p>
        </section>
      </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-lavender-200 dark:border-prussian-700 mt-16 py-8 text-center text-sm text-prussian-400 dark:text-lavender-500">
      <div class="max-w-4xl mx-auto px-4">
        <p>&copy; {{ new Date().getFullYear() }} Kinhold &mdash; Open source under the MIT License</p>
        <div class="mt-2 flex items-center justify-center gap-4">
          <RouterLink to="/dashboard" class="hover:text-wisteria-600 dark:hover:text-wisteria-400 transition-colors">Home</RouterLink>
          <span>&bull;</span>
          <RouterLink to="/terms" class="hover:text-wisteria-600 dark:hover:text-wisteria-400 transition-colors">Terms</RouterLink>
          <span>&bull;</span>
          <a href="https://github.com/gregqualls/kinhold" target="_blank" rel="noopener" class="hover:text-wisteria-600 dark:hover:text-wisteria-400 transition-colors">GitHub</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.prose-container h2 {
  @apply text-xl font-semibold text-prussian-500 dark:text-lavender-100 mt-8 mb-3;
}
.prose-container h3 {
  @apply text-base font-semibold text-prussian-500 dark:text-lavender-200 mt-4 mb-2;
}
.prose-container p {
  @apply leading-relaxed mb-3;
}
.prose-container ul {
  @apply list-disc pl-6 space-y-1 mb-3;
}
.prose-container table {
  @apply w-full text-sm border border-lavender-200 dark:border-prussian-700 rounded-lg overflow-hidden mb-3;
}
.prose-container thead {
  @apply bg-lavender-100 dark:bg-prussian-800;
}
.prose-container th {
  @apply px-4 py-2 text-left font-semibold text-prussian-500 dark:text-lavender-200;
}
.prose-container td {
  @apply px-4 py-2 border-t border-lavender-200 dark:border-prussian-700;
}
</style>
