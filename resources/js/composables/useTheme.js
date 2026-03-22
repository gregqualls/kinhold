import { ref } from 'vue'

const STORAGE_KEY = 'q32hub-theme'
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
    name: 'Q32 Classic',
    description: 'Prussian blue and wisteria purple',
    colors: {
      primary: '#05204A',
      accent: '#9b75c5',
      surface: '#E1E2EF',
      highlight: '#a5a84e',
    },
  },
  {
    id: 'ocean',
    name: 'Ocean Breeze',
    description: 'Deep teal with cyan accents',
    colors: {
      primary: '#0a4a69',
      accent: '#06b6d4',
      surface: '#D8E4EC',
      highlight: '#ec5438',
    },
  },
  {
    id: 'forest',
    name: 'Forest',
    description: 'Rich greens with amber warmth',
    colors: {
      primary: '#0e502a',
      accent: '#22c580',
      surface: '#D8E8DA',
      highlight: '#f4aa32',
    },
  },
  {
    id: 'sunset',
    name: 'Sunset',
    description: 'Warm charcoal with orange glow',
    colors: {
      primary: '#42342a',
      accent: '#f6822a',
      surface: '#ECE4DA',
      highlight: '#e04666',
    },
  },
  {
    id: 'midnight',
    name: 'Midnight',
    description: 'Deep indigo with electric blue',
    colors: {
      primary: '#1e1660',
      accent: '#1e6af5',
      surface: '#DADAE8',
      highlight: '#f5c334',
    },
  },
]

export function useTheme() {
  const init = () => {
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
