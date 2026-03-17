const familyColors = [
  { name: 'red', bg: 'bg-family-red', text: 'text-family-red', light: 'bg-red-100' },
  { name: 'amber', bg: 'bg-family-amber', text: 'text-family-amber', light: 'bg-amber-100' },
  { name: 'green', bg: 'bg-family-green', text: 'text-family-green', light: 'bg-green-100' },
  { name: 'blue', bg: 'bg-family-blue', text: 'text-family-blue', light: 'bg-blue-100' },
  { name: 'purple', bg: 'bg-family-purple', text: 'text-family-purple', light: 'bg-purple-100' },
  { name: 'pink', bg: 'bg-family-pink', text: 'text-family-pink', light: 'bg-pink-100' },
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
