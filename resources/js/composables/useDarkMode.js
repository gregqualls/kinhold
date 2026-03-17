import { ref, watch, onMounted } from 'vue'

const isDark = ref(false)

export function useDarkMode() {
  const init = () => {
    const saved = localStorage.getItem('q32hub-dark-mode')
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
    localStorage.setItem('q32hub-dark-mode', isDark.value.toString())
    applyTheme()
  }

  const setDark = (value) => {
    isDark.value = value
    localStorage.setItem('q32hub-dark-mode', isDark.value.toString())
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
