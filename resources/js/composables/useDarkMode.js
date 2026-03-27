import { ref, watch, onMounted } from 'vue'

const isDark = ref(false)

const STORAGE_KEY = 'kinhold-dark-mode'
const OLD_STORAGE_KEY = 'q32hub-dark-mode'

export function useDarkMode() {
  const init = () => {
    // Migrate from old storage key if present
    const oldSaved = localStorage.getItem(OLD_STORAGE_KEY)
    if (oldSaved !== null) {
      localStorage.setItem(STORAGE_KEY, oldSaved)
      localStorage.removeItem(OLD_STORAGE_KEY)
    }

    const saved = localStorage.getItem(STORAGE_KEY)
    if (saved !== null) {
      isDark.value = saved === 'true'
    } else {
      // Default to system preference
      isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }
    applyTheme()
  }

  const applyTheme = () => {
    if (isDark.value) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  }

  const toggle = () => {
    isDark.value = !isDark.value
    localStorage.setItem(STORAGE_KEY, isDark.value.toString())
    applyTheme()
  }

  const setDark = (value) => {
    isDark.value = value
    localStorage.setItem(STORAGE_KEY, isDark.value.toString())
    applyTheme()
  }

  watch(isDark, applyTheme)

  return {
    isDark,
    toggle,
    setDark,
    init,
  }
}
