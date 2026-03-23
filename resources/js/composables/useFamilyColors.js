// Muted Kinhold palette — premium, works in light and dark mode
const familyColors = [
  { name: 'teal', bg: 'bg-[#5B8C9C]', text: 'text-[#5B8C9C]', light: 'bg-[#5B8C9C]/10' },
  { name: 'amber', bg: 'bg-[#C48B5B]', text: 'text-[#C48B5B]', light: 'bg-[#C48B5B]/10' },
  { name: 'sage', bg: 'bg-[#5B9C7B]', text: 'text-[#5B9C7B]', light: 'bg-[#5B9C7B]/10' },
  { name: 'steel', bg: 'bg-[#5B6B9C]', text: 'text-[#5B6B9C]', light: 'bg-[#5B6B9C]/10' },
  { name: 'plum', bg: 'bg-[#8B5B9C]', text: 'text-[#8B5B9C]', light: 'bg-[#8B5B9C]/10' },
  { name: 'rose', bg: 'bg-[#9C5B5B]', text: 'text-[#9C5B5B]', light: 'bg-[#9C5B5B]/10' },
  { name: 'sienna', bg: 'bg-[#9C7B5B]', text: 'text-[#9C7B5B]', light: 'bg-[#9C7B5B]/10' },
  { name: 'lavender', bg: 'bg-[#7B6B9C]', text: 'text-[#7B6B9C]', light: 'bg-[#7B6B9C]/10' },
]

const colorMap = new Map()

export const useFamilyColors = () => {
  const getColorForUser = (userId, userName = '') => {
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

  const getColorClasses = (userId, userName = '') => {
    const color = getColorForUser(userId, userName)
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
