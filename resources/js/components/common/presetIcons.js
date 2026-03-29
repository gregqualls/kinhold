/**
 * Curated preset icons for rewards, events, and other user-selectable icons.
 * Uses Phosphor Icons (@phosphor-icons/vue).
 *
 * Icon names stored in the DB are the keys of this map (e.g. 'cake', 'gift').
 * The values are the Phosphor component names (e.g. 'PhCake', 'PhGift').
 */

import {
  PhCake,
  PhCookie,
  PhIceCream,
  PhPizza,
  PhCampfire,
  PhGift,
  PhConfetti,
  PhBalloon,
  PhTrophy,
  PhMedal,
  PhCrown,
  PhStar,
  PhHeart,
  PhHeartStraight,
  PhMoon,
  PhSun,
  PhSnowflake,
  PhLeaf,
  PhTreePalm,
  PhHouse,
  PhBed,
  PhTelevisionSimple,
  PhFilmSlate,
  PhMusicNote,
  PhGameController,
  PhSoccerBall,
  PhFootball,
  PhBicycle,
  PhAirplane,
  PhGraduationCap,
  PhBook,
  PhPencil,
} from '@phosphor-icons/vue'

/**
 * Map of icon keys (stored in DB) → Phosphor Vue component
 */
export const presetIconMap = {
  // Food & Treats
  cake: PhCake,
  cookie: PhCookie,
  'ice-cream': PhIceCream,
  pizza: PhPizza,
  campfire: PhCampfire,

  // Celebration
  gift: PhGift,
  confetti: PhConfetti,
  balloon: PhBalloon,
  trophy: PhTrophy,
  medal: PhMedal,
  crown: PhCrown,
  star: PhStar,

  // Feelings
  heart: PhHeartStraight,

  // Nature / Time of Day
  moon: PhMoon,
  sun: PhSun,
  snowflake: PhSnowflake,
  leaf: PhLeaf,
  palm: PhTreePalm,

  // Home & Life
  house: PhHouse,
  bed: PhBed,

  // Entertainment
  tv: PhTelevisionSimple,
  film: PhFilmSlate,
  music: PhMusicNote,
  game: PhGameController,

  // Sports
  soccer: PhSoccerBall,
  football: PhFootball,
  bicycle: PhBicycle,

  // Travel
  airplane: PhAirplane,

  // School
  graduation: PhGraduationCap,
  book: PhBook,
  pencil: PhPencil,
}

export const presetIconKeys = Object.keys(presetIconMap)
