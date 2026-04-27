<!--
  UserAvatar — thin compatibility shim over KinAvatar.

  Reads `:user` (id, name, avatar, avatar_color, google_avatar) and resolves
  to the right KinAvatar props:
    • Photo URL (avatar starts with http/https) → src=URL
    • Preset icon (avatar starts with `phosphor:`) → src is passed through
      and KinAvatar resolves it natively
    • Stored avatar_color → mapped via kinAccentFor() to one of the 4 Kin
      accent families (lavender / peach / mint / sun). Legacy 12-color names
      (teal/amber/sage/etc.) fall through to the closest accent.
    • No avatar_color → hash of user.id chooses a stable accent so each
      family member is visually distinct across the app.

  Every consumer of `<UserAvatar :user="...">` now renders a KinAvatar
  underneath, keeping avatars consistent everywhere (topbar, sidebar,
  leaderboard, feeds, meal cards, task rows, settings, etc.).
-->
<template>
  <KinAvatar
    :src="resolvedSrc"
    :name="user?.name"
    :color="resolvedColor"
    :size="size"
  />
</template>

<script setup>
import { computed } from 'vue'
import KinAvatar from '@/components/design-system/KinAvatar.vue'
import { kinAccentFor } from '@/composables/useFamilyColors'

const props = defineProps({
  user: {
    type: Object,
    default: null,
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(v),
  },
})

// Photo URL OR phosphor:<key> preset — KinAvatar handles both natively.
// Avatars starting with `/` or anything else returned as-is.
const resolvedSrc = computed(() => props.user?.avatar || null)

// Hash a stable string to one of the 4 Kin accent families. Used when the
// user has no explicit avatar_color set, so each family member still gets
// a consistent distinct color across the app.
const HASH_ACCENTS = ['lavender', 'peach', 'mint', 'sun']
function hashToAccent(str) {
  if (!str) return 'lavender'
  let h = 0
  for (let i = 0; i < str.length; i++) h = (h * 31 + str.charCodeAt(i)) | 0
  return HASH_ACCENTS[Math.abs(h) % HASH_ACCENTS.length]
}

const resolvedColor = computed(() => {
  const stored = props.user?.avatar_color
  if (stored) return kinAccentFor(stored)
  return hashToAccent(props.user?.id || props.user?.name || '')
})
</script>
