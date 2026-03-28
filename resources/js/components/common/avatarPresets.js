import {
  PhHorse, PhBird, PhDog, PhCat, PhFish, PhButterfly, PhPawPrint,
  PhTree, PhFlowerLotus, PhMountains, PhSun, PhMoonStars,
  PhRocket, PhPlanet, PhShootingStar, PhAlien,
  PhCrown, PhDiamond, PhLightning, PhShieldStar, PhSword, PhGuitar, PhGameController,
  PhSmiley, PhHeart, PhPeace,
} from '@phosphor-icons/vue'

export const avatarPresets = [
  // Animals
  { key: 'horse', label: 'Horse', category: 'Animals', component: PhHorse },
  { key: 'bird', label: 'Bird', category: 'Animals', component: PhBird },
  { key: 'dog', label: 'Dog', category: 'Animals', component: PhDog },
  { key: 'cat', label: 'Cat', category: 'Animals', component: PhCat },
  { key: 'fish', label: 'Fish', category: 'Animals', component: PhFish },
  { key: 'butterfly', label: 'Butterfly', category: 'Animals', component: PhButterfly },
  { key: 'paw-print', label: 'Paw Print', category: 'Animals', component: PhPawPrint },

  // Nature
  { key: 'tree', label: 'Tree', category: 'Nature', component: PhTree },
  { key: 'flower-lotus', label: 'Lotus', category: 'Nature', component: PhFlowerLotus },
  { key: 'mountains', label: 'Mountains', category: 'Nature', component: PhMountains },
  { key: 'sun', label: 'Sun', category: 'Nature', component: PhSun },
  { key: 'moon-stars', label: 'Moon', category: 'Nature', component: PhMoonStars },

  // Space
  { key: 'rocket', label: 'Rocket', category: 'Space', component: PhRocket },
  { key: 'planet', label: 'Planet', category: 'Space', component: PhPlanet },
  { key: 'shooting-star', label: 'Star', category: 'Space', component: PhShootingStar },
  { key: 'alien', label: 'Alien', category: 'Space', component: PhAlien },

  // Objects
  { key: 'crown', label: 'Crown', category: 'Style', component: PhCrown },
  { key: 'diamond', label: 'Diamond', category: 'Style', component: PhDiamond },
  { key: 'lightning', label: 'Lightning', category: 'Style', component: PhLightning },
  { key: 'shield-star', label: 'Shield', category: 'Style', component: PhShieldStar },
  { key: 'sword', label: 'Sword', category: 'Style', component: PhSword },
  { key: 'guitar', label: 'Guitar', category: 'Style', component: PhGuitar },
  { key: 'game-controller', label: 'Gamer', category: 'Style', component: PhGameController },

  // Abstract
  { key: 'smiley', label: 'Smiley', category: 'Vibes', component: PhSmiley },
  { key: 'heart', label: 'Heart', category: 'Vibes', component: PhHeart },
  { key: 'peace', label: 'Peace', category: 'Vibes', component: PhPeace },
]

/**
 * Look up a preset by key. Returns the entry or null.
 */
export function getPreset(key) {
  return avatarPresets.find((p) => p.key === key) || null
}

/**
 * Get presets grouped by category.
 */
export function getPresetsByCategory() {
  const groups = {}
  for (const preset of avatarPresets) {
    if (!groups[preset.category]) groups[preset.category] = []
    groups[preset.category].push(preset)
  }
  return groups
}
