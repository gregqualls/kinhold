<template>
  <div class="min-h-screen bg-lavender-50 dark:bg-prussian-900">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-white/80 dark:bg-prussian-800/80 backdrop-blur-md border-b border-lavender-200 dark:border-prussian-700">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <RouterLink to="/" class="flex items-center gap-2">
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
    <main class="flex flex-col items-center justify-center px-4 py-24 text-center">
      <p class="text-8xl font-heading font-bold text-wisteria-600 dark:text-wisteria-400">404</p>
      <h1 class="mt-4 text-xl font-semibold text-prussian-500 dark:text-lavender-100">Page not found</h1>
      <p class="mt-2 text-prussian-400 dark:text-lavender-400 max-w-md">
        The page you're looking for doesn't exist or has been moved.
      </p>
      <RouterLink to="/" class="btn-primary mt-8">Go Home</RouterLink>
    </main>
  </div>
</template>
