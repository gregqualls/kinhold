const familyColors = [
  { name: 'red', bg: 'bg-red-500', text: 'text-red-600', light: 'bg-red-100' },
  { name: 'amber', bg: 'bg-amber-500', text: 'text-amber-600', light: 'bg-amber-100' },
  { name: 'green', bg: 'bg-emerald-600', text: 'text-emerald-600', light: 'bg-green-100' },
  { name: 'blue', bg: 'bg-blue-600', text: 'text-blue-600', light: 'bg-blue-100' },
  { name: 'purple', bg: 'bg-wisteria-600', text: 'text-wisteria-600', light: 'bg-purple-100' },
  { name: 'pink', bg: 'bg-pink-500', text: 'text-pink-600', light: 'bg-pink-100' },
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
