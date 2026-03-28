<template>
  <!-- Uploaded photo or Google URL -->
  <img
    v-if="avatarUrl"
    :src="avatarUrl"
    :alt="user?.name"
    class="rounded-full object-cover flex-shrink-0"
    :class="sizeClasses"
  />
  <!-- Phosphor preset icon -->
  <div
    v-else-if="presetIcon"
    class="rounded-full flex items-center justify-center flex-shrink-0"
    :class="[sizeClasses, colorClasses]"
    :title="user?.name"
  >
    <component :is="presetIcon" weight="duotone" class="text-white" :size="iconSize" />
  </div>
  <!-- Initials fallback -->
  <div
    v-else
    class="rounded-full flex items-center justify-center font-semibold text-white flex-shrink-0"
    :class="[sizeClasses, colorClasses]"
    :title="user?.name"
  >
    {{ initials }}
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useFamilyColors } from '@/composables/useFamilyColors'
import { getPreset } from '@/components/common/avatarPresets'

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

const { getColorForUser } = useFamilyColors()

const sizeClasses = computed(() => {
  const sizes = {
    xs: 'w-5 h-5 text-[8px]',
    sm: 'w-8 h-8 text-xs',
    md: 'w-10 h-10 text-sm',
    lg: 'w-12 h-12 text-base',
    xl: 'w-16 h-16 text-lg',
  }
  return sizes[props.size]
})

const iconSize = computed(() => {
  const sizes = { xs: 14, sm: 20, md: 24, lg: 28, xl: 38 }
  return sizes[props.size]
})

const avatarUrl = computed(() => {
  const avatar = props.user?.avatar
  if (avatar && avatar.startsWith('http')) return avatar
  return null
})

const presetIcon = computed(() => {
  const avatar = props.user?.avatar
  if (!avatar || !avatar.startsWith('phosphor:')) return null
  const key = avatar.replace('phosphor:', '')
  const preset = getPreset(key)
  return preset?.component || null
})

const initials = computed(() => {
  if (!props.user?.name) return '?'
  return props.user.name
    .split(' ')
    .map((n) => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

const colorClasses = computed(() => {
  const color = getColorForUser(props.user?.id, props.user?.name, props.user?.avatar_color)
  return color.bg
})
</script>
