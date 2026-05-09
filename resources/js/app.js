import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { registerSW } from 'virtual:pwa-register'
import App from './App.vue'
import router from './router'
import '../css/app.css'

// Stale-build recovery (#278). After a redeploy, hashed asset filenames
// change. A returning user whose tab cached the old `index.html` (via SW or
// browser cache) will try to dynamically import chunks that no longer exist;
// the server returns the SPA fallback HTML, and the browser fails the
// import with a MIME-type error. Reload once to pick up the fresh manifest.
// sessionStorage guards against an infinite reload loop if the failure is
// actually permanent (network down, real bug).
const RELOAD_GUARD_KEY = 'kin-preload-reload-attempted'
window.addEventListener('vite:preloadError', (event) => {
  if (sessionStorage.getItem(RELOAD_GUARD_KEY)) return
  sessionStorage.setItem(RELOAD_GUARD_KEY, String(Date.now()))
  event.preventDefault()
  window.location.reload()
})
window.addEventListener('load', () => {
  setTimeout(() => sessionStorage.removeItem(RELOAD_GUARD_KEY), 5000)
})

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')

// PWA service worker registration (#68). `autoUpdate` + workbox's skipWaiting
// + clientsClaim (vite.config.js) make a new SW take over immediately on the
// next page load instead of waiting for every tab to close — required so
// returning users don't get the stale shell after a deploy (#278).
registerSW({ immediate: true })
