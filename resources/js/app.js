import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { registerSW } from 'virtual:pwa-register'
import App from './App.vue'
import router from './router'
import '../css/app.css'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')

// PWA service worker registration (#68). `autoUpdate` here mirrors the
// vite.config.js setting — new SW activates automatically on next reload, so
// self-hosters with stale tabs don't have to manually click an "update" button.
registerSW({ immediate: true })
