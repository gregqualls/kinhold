import { ref } from 'vue'

const STORAGE_KEY = 'kinhold-theme'
const OLD_STORAGE_KEY = 'q32hub-theme'
const DEFAULT_THEME = 'classic'

const currentTheme = ref(DEFAULT_THEME)

/**
 * Available color themes with metadata for the theme picker.
 * Each theme has 4 preview colors (primary, accent, surface, highlight)
 * shown as the 500-level shade for swatches.
 */
export const themes = [
  {
    id: 'classic',
    name: 'Kinhold',
    description: 'Warm gold and ivory',
    colors: {
      primary: '#05204A',
      accent: '#9b75c5',
      surface: '#E1E2EF',
      highlight: '#a5a84e',
    },
  },
]

export function useTheme() {
  const init = () => {
    // Migrate from old storage key if present
    const oldSaved = localStorage.getItem(OLD_STORAGE_KEY)
    if (oldSaved) {
      localStorage.setItem(STORAGE_KEY, oldSaved)
      localStorage.removeItem(OLD_STORAGE_KEY)
    }

    const saved = localStorage.getItem(STORAGE_KEY)
    if (saved && themes.some((t) => t.id === saved)) {
      currentTheme.value = saved
    } else {
      currentTheme.value = DEFAULT_THEME
    }
    applyTheme()
  }

  const applyTheme = () => {
    document.documentElement.setAttribute('data-theme', currentTheme.value)
  }

  const setTheme = (themeId) => {
    if (!themes.some((t) => t.id === themeId)) return
    currentTheme.value = themeId
    localStorage.setItem(STORAGE_KEY, themeId)
    applyTheme()
  }

  return {
    currentTheme,
    themes,
    init,
    setTheme,
  }
}
