<template>
  <div class="space-y-5">
    <!-- Push permission row -->
    <div class="p-4 bg-surface-sunken rounded-lg">
      <div class="flex items-start justify-between gap-3">
        <div class="flex-1">
          <p class="text-sm font-semibold text-ink-primary">Push notifications on this device</p>
          <p class="text-xs text-ink-secondary mt-1">
            <template v-if="!notifications.isPushAvailable">
              Push isn't available in this browser, or VAPID keys aren't configured on the server.
            </template>
            <template v-else-if="notifications.localPermission === 'denied'">
              Permission was denied. Re-enable in your browser site settings, then return here.
            </template>
            <template v-else-if="notifications.isPushActive">
              Active — {{ notifications.pushStatus.subscriptions }} device{{ notifications.pushStatus.subscriptions === 1 ? '' : 's' }} connected.
            </template>
            <template v-else>
              Get pinged about new tasks, kudos, and reminders even when Kinhold isn't open.
            </template>
          </p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
          <BaseButton
            v-if="!notifications.isPushActive"
            variant="primary"
            :disabled="!notifications.isPushAvailable || notifications.localPermission === 'denied'"
            :loading="enabling"
            @click="enable"
          >
            Enable
          </BaseButton>
          <template v-else>
            <BaseButton variant="ghost" :loading="testing" @click="test">Send test</BaseButton>
            <BaseButton variant="secondary" :loading="disabling" @click="disable">Disable</BaseButton>
          </template>
        </div>
      </div>
      <p v-if="notifications.lastError" class="mt-2 text-xs text-rose-600 dark:text-rose-400">
        {{ notifications.lastError }}
      </p>
    </div>

    <!-- Global mute -->
    <div class="p-4 bg-surface-sunken rounded-lg">
      <KinSwitch
        :model-value="notifications.preferences.muted"
        label="Mute all push notifications"
        description="Email still arrives. Useful when you don't want any pushes for a stretch."
        color="lavender"
        @update:model-value="setMuted"
      />
    </div>

    <!-- Quiet hours -->
    <div class="p-4 bg-surface-sunken rounded-lg space-y-3">
      <KinSwitch
        :model-value="notifications.preferences.quiet_hours.enabled"
        label="Quiet hours"
        description="Suppress push (not email) during these hours, in your timezone."
        color="lavender"
        @update:model-value="setQuietEnabled"
      />
      <div v-if="notifications.preferences.quiet_hours.enabled" class="flex items-center gap-3 pl-1">
        <label class="flex items-center gap-2 text-sm text-ink-secondary">
          From
          <input
            type="time"
            class="px-2 py-1 rounded border border-border-subtle bg-surface text-ink-primary text-sm"
            :value="notifications.preferences.quiet_hours.start"
            @change="setQuietStart($event.target.value)"
          />
        </label>
        <label class="flex items-center gap-2 text-sm text-ink-secondary">
          to
          <input
            type="time"
            class="px-2 py-1 rounded border border-border-subtle bg-surface text-ink-primary text-sm"
            :value="notifications.preferences.quiet_hours.end"
            @change="setQuietEnd($event.target.value)"
          />
        </label>
      </div>
    </div>

    <!-- Per-category groups -->
    <div
      v-for="(types, categoryKey) in groupedTypes"
      :key="categoryKey"
      class="rounded-lg border border-border-subtle"
    >
      <header class="px-4 py-2 bg-surface-sunken/60 rounded-t-lg">
        <h3 class="text-sm font-semibold text-ink-primary">
          {{ notifications.registry.categories[categoryKey] || categoryKey }}
        </h3>
      </header>
      <div class="divide-y divide-border-subtle">
        <div
          v-for="type in types"
          :key="type.key"
          class="px-4 py-3 flex items-center justify-between gap-4"
        >
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-ink-primary truncate">{{ type.label }}</p>
            <p v-if="type.description" class="text-xs text-ink-secondary mt-0.5">{{ type.description }}</p>
          </div>
          <div class="flex items-center gap-4 flex-shrink-0">
            <label v-if="type.channels.includes('email')" class="flex items-center gap-1 text-xs">
              <input
                type="checkbox"
                class="rounded border-border-subtle disabled:opacity-40 disabled:cursor-not-allowed"
                :checked="emailValue(type.key, type.default_email)"
                :disabled="!hasEmail"
                @change="onEmailToggle(type.key, $event.target.checked)"
              />
              <span class="text-ink-secondary">Email</span>
            </label>
            <label v-if="type.channels.includes('push')" class="flex items-center gap-1 text-xs">
              <input
                type="checkbox"
                class="rounded border-border-subtle disabled:opacity-40 disabled:cursor-not-allowed"
                :checked="pushValue(type.key, type.default_push)"
                :disabled="!notifications.isPushActive"
                @change="onPushToggle(type.key, $event.target.checked)"
              />
              <span class="text-ink-secondary">Push</span>
            </label>
            <button
              v-if="canTest(type.key)"
              type="button"
              class="text-xs px-2 py-1 rounded border border-border-subtle text-ink-secondary hover:text-ink-primary hover:bg-surface-sunken/60 disabled:opacity-40 disabled:cursor-not-allowed"
              :disabled="!notifications.isPushActive || testingKey === type.key"
              :title="`Send a sample push for: ${type.label}`"
              @click="testForKey(type.key)"
            >
              {{ testingKey === type.key ? '…' : 'Test' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <p v-if="!hasEmail" class="text-xs text-ink-secondary">
      Email channels are disabled because this account has no email address.
    </p>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useNotificationsStore } from '@/stores/notifications'
import KinSwitch from '@/components/design-system/KinSwitch.vue'
import BaseButton from '@/components/common/BaseButton.vue'

const auth = useAuthStore()
const notifications = useNotificationsStore()

const hasEmail = computed(() => !!auth.user?.email)
const groupedTypes = computed(() => notifications.registry.types_by_category || {})

const enabling = ref(false)
const disabling = ref(false)
const testing = ref(false)
const testingKey = ref('')

// Registry keys that have a server-side sample dispatcher. Keep in sync with
// PushSubscriptionController::testType().
const TESTABLE_KEYS = new Set([
  'task_due_soon',
  'shopping_item_added',
  'calendar_event_reminder',
  'dinner_reminder',
])

function canTest(key) {
  return TESTABLE_KEYS.has(key)
}

async function testForKey(key) {
  testingKey.value = key
  try {
    await notifications.testPushForKey(key)
  } finally {
    testingKey.value = ''
  }
}

function emailValue(key, def) {
  const v = notifications.preferences.email?.[key]
  return v === undefined ? def : !!v
}

function pushValue(key, def) {
  const v = notifications.preferences.push?.[key]
  return v === undefined ? def : !!v
}

async function onEmailToggle(key, value) {
  await notifications.setChannelKey('email', key, value)
}

async function onPushToggle(key, value) {
  await notifications.setChannelKey('push', key, value)
}

async function setMuted(value) {
  await notifications.setMuted(value)
}

async function setQuietEnabled(value) {
  await notifications.setQuietHours({ enabled: value })
}

async function setQuietStart(value) {
  await notifications.setQuietHours({ start: value })
}

async function setQuietEnd(value) {
  await notifications.setQuietHours({ end: value })
}

async function enable() {
  enabling.value = true
  try {
    await notifications.enablePush()
  } finally {
    enabling.value = false
  }
}

async function disable() {
  disabling.value = true
  try {
    await notifications.disablePush()
  } finally {
    disabling.value = false
  }
}

async function test() {
  testing.value = true
  try {
    await notifications.testPush()
  } finally {
    testing.value = false
  }
}

onMounted(async () => {
  try {
    await notifications.fetch()
  } catch {
    /* error surfaced via store.lastError */
  }
})
</script>
