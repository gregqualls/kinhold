// Muted Kinhold palette — all 12 brand colors, premium, works in light and dark mode
const familyColors = [
  { name: 'teal', hex: '#5B8C9C', bg: 'bg-[#5B8C9C]', text: 'text-[#5B8C9C]', light: 'bg-[#5B8C9C]/10' },
  { name: 'amber', hex: '#C48B5B', bg: 'bg-[#C48B5B]', text: 'text-[#C48B5B]', light: 'bg-[#C48B5B]/10' },
  { name: 'sage', hex: '#5B9C7B', bg: 'bg-[#5B9C7B]', text: 'text-[#5B9C7B]', light: 'bg-[#5B9C7B]/10' },
  { name: 'steel', hex: '#5B6B9C', bg: 'bg-[#5B6B9C]', text: 'text-[#5B6B9C]', light: 'bg-[#5B6B9C]/10' },
  { name: 'plum', hex: '#8B5B9C', bg: 'bg-[#8B5B9C]', text: 'text-[#8B5B9C]', light: 'bg-[#8B5B9C]/10' },
  { name: 'rose', hex: '#9C5B5B', bg: 'bg-[#9C5B5B]', text: 'text-[#9C5B5B]', light: 'bg-[#9C5B5B]/10' },
  { name: 'sienna', hex: '#9C7B5B', bg: 'bg-[#9C7B5B]', text: 'text-[#9C7B5B]', light: 'bg-[#9C7B5B]/10' },
  { name: 'lavender', hex: '#7B6B9C', bg: 'bg-[#7B6B9C]', text: 'text-[#7B6B9C]', light: 'bg-[#7B6B9C]/10' },
  { name: 'cyan', hex: '#5B9C9C', bg: 'bg-[#5B9C9C]', text: 'text-[#5B9C9C]', light: 'bg-[#5B9C9C]/10' },
  { name: 'olive', hex: '#9C8B5B', bg: 'bg-[#9C8B5B]', text: 'text-[#9C8B5B]', light: 'bg-[#9C8B5B]/10' },
  { name: 'berry', hex: '#9C5B7B', bg: 'bg-[#9C5B7B]', text: 'text-[#9C5B7B]', light: 'bg-[#9C5B7B]/10' },
  { name: 'forest', hex: '#6B8B5B', bg: 'bg-[#6B8B5B]', text: 'text-[#6B8B5B]', light: 'bg-[#6B8B5B]/10' },
]

// Lookup by name for user-chosen colors
const colorByName = Object.fromEntries(familyColors.map((c) => [c.name, c]))

const colorMap = new Map()

export const useFamilyColors = () => {
  /**
   * Get the color for a user. If the user has a chosen avatar_color, use it.
   * Otherwise fall back to the hash-based automatic assignment.
   */
  const getColorForUser = (userId, userName = '', avatarColor = null) => {
    // User chose a color — always respect it
    if (avatarColor && colorByName[avatarColor]) {
      const color = colorByName[avatarColor]
      colorMap.set(userId, color)
      return color
    }

    // Return cached color if exists
    if (colorMap.has(userId)) {
      return colorMap.get(userId)
    }

    // Assign new color based on hash
    const str = userId || userName || 'default'
    const hash = str.charCodeAt(0) + str.length
    const color = familyColors[hash % familyColors.length]
    colorMap.set(userId, color)

    return color
  }

  const getColorClasses = (userId, userName = '', avatarColor = null) => {
    const color = getColorForUser(userId, userName, avatarColor)
    return {
      bg: color.bg,
      text: color.text,
      light: color.light,
    }
  }

  const getAllColors = () => familyColors

  const resetColors = () => {
    colorMap.clear()
  }

  return {
    getColorForUser,
    getColorClasses,
    getAllColors,
    resetColors,
  }
}

// ── Kin design-system accent families ────────────────────────────────────────
//
// These are the 4 accent families KinAvatar accepts (`color` prop). They map
// 1:1 to the Kin design tokens (`accent-lavender-soft` / `accent-peach-soft` /
// `accent-mint-soft` / `accent-sun-soft`) and are the canonical palette for
// avatar tinting going forward. Storage uses the family name; legacy values
// like 'teal' / 'amber' / 'sage' / etc. fall through `kinAccentFor()` to a
// best-fit Kin family so existing user choices still render sensibly.
export const KIN_AVATAR_ACCENTS = [
  { name: 'lavender', hex: '#6856B2', bg: 'bg-accent-lavender-soft', label: 'Lavender' },
  { name: 'peach',    hex: '#C4713A', bg: 'bg-accent-peach-soft',    label: 'Peach'    },
  { name: 'mint',     hex: '#2E7D55', bg: 'bg-accent-mint-soft',     label: 'Mint'     },
  { name: 'sun',      hex: '#A07A10', bg: 'bg-accent-sun-soft',      label: 'Sun'      },
]

const LEGACY_TO_KIN = {
  teal:     'mint',
  amber:    'sun',
  sage:     'mint',
  steel:    'lavender',
  plum:     'lavender',
  rose:     'peach',
  sienna:   'peach',
  lavender: 'lavender',
  cyan:     'mint',
  olive:    'sun',
  berry:    'peach',
  forest:   'mint',
}

/**
 * Resolve a stored avatar_color to one of the 4 Kin accent family names,
 * suitable for `<KinAvatar :color="...">`. Falls back to `'lavender'` when
 * the input is empty / unknown.
 */
export function kinAccentFor(avatarColor) {
  if (!avatarColor) return 'lavender'
  if (KIN_AVATAR_ACCENTS.some((c) => c.name === avatarColor)) return avatarColor
  return LEGACY_TO_KIN[avatarColor] ?? 'lavender'
}
