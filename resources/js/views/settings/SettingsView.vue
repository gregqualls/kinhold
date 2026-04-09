<template>
  <div class="p-4 md:p-6 max-w-4xl">
    <!-- Header -->
    <h1 class="text-2xl font-bold font-heading text-prussian-500 dark:text-lavender-200 mb-6">{{ isParent ? 'Family Settings' : 'My Settings' }}</h1>

    <!-- Kid-friendly Profile Section (shown for non-parent users) -->
    <div v-if="!isParent" class="card-lg mb-6">
      <h2 class="text-lg font-semibold font-heading text-prussian-500 dark:text-lavender-200 mb-4">My Profile</h2>
      <div class="flex items-center gap-4 p-4 bg-lavender-50 dark:bg-prussian-700 rounded-lg">
        <button class="flex-shrink-0 rounded-full hover:ring-2 hover:ring-[#C4975A] hover:ring-offset-2 dark:hover:ring-offset-prussian-700 transition-all" title="Change avatar" @click="openAvatarEditor(currentUser)">
          <UserAvatar :user="currentUser" size="lg" />
        </button>
        <div>
          <p class="text-lg font-semibold text-prussian-500 dark:text-lavender-200">{{ currentUser?.name }}</p>
          <p v-if="currentUser?.email" class="text-sm text-lavender-700 dark:text-lavender-400">{{ currentUser?.email }}</p>
          <p v-if="family" class="text-sm text-lavender-600 dark:text-lavender-400 mt-1">{{ family?.name }}</p>
        </div>
      </div>
    </div>

    <!-- ============================================ -->
    <!-- PARENT VIEW — Collapsible Sections           -->
    <!-- ============================================ -->
    <template v-if="isParent">
      <!-- Section 1: Family -->
      <SettingsSection
        id="family"
        title="Family"
        description="Manage your family info, members, and invites"
        :icon="UsersIcon"
        :model-value="expandedSections.has('family')"
        @update:model-value="val => toggleSection('family', val)"
      >
        <!-- Family Name -->
        <form class="space-y-4 mb-6" @submit.prevent="updateFamily">
          <BaseInput
            v-model="familyForm.name"
            label="Family Name"
            placeholder="The Johnsons"
            :error="familyErrors.name"
          />
          <div class="flex gap-3 justify-end">
            <BaseButton variant="ghost" @click="cancelEditFamily">Cancel</BaseButton>
            <BaseButton variant="primary" :loading="savingFamily">Save Changes</BaseButton>
          </div>
        </form>

        <!-- Invite Code -->
        <div class="border-t border-lavender-200 dark:border-prussian-700 pt-4 mb-6">
          <h3 class="font-semibold text-prussian-500 dark:text-lavender-200 mb-2">Family Invite Code</h3>
          <p class="text-sm text-lavender-700 dark:text-lavender-400 mb-4">
            Share this code with family members so they can join during registration.
          </p>

          <div class="flex items-center gap-3">
            <div class="flex-1 px-4 py-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg font-mono text-lg tracking-widest text-prussian-500 dark:text-lavender-200 text-center">
              {{ inviteCode || '...' }}
            </div>
            <BaseButton variant="secondary" size="sm" :disabled="!inviteCode" @click="copyInviteCode">
              <ClipboardDocumentIcon class="w-4 h-4 mr-1" />
              {{ copied ? 'Copied!' : 'Copy' }}
            </BaseButton>
          </div>

          <!-- Send invite by email -->
          <div class="mt-4 pt-4 border-t border-lavender-200 dark:border-prussian-700">
            <p class="text-sm font-medium text-prussian-400 dark:text-lavender-300 mb-2">Send Invite by Email</p>
            <form class="flex items-end gap-3" @submit.prevent="handleSendInviteEmail">
              <div class="flex-1">
                <input
                  v-model="inviteEmail"
                  type="email"
                  placeholder="name@example.com"
                  class="input-base"
                  required
                />
              </div>
              <BaseButton variant="secondary" size="sm" :loading="sendingInvite" :disabled="!inviteCode">
                <EnvelopeIcon class="w-4 h-4 mr-1" />
                Send
              </BaseButton>
            </form>
            <p v-if="inviteEmailSent" class="text-sm text-green-600 dark:text-green-400 mt-2">
              Invite sent!
            </p>
          </div>
        </div>

        <!-- Family Members -->
        <div class="border-t border-lavender-200 dark:border-prussian-700 pt-4 mb-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-prussian-500 dark:text-lavender-200">Family Members</h3>
            <BaseButton variant="secondary" size="sm" @click="openAddMemberModal">
              <PlusIcon class="w-4 h-4 mr-2" />
              Add Member
            </BaseButton>
          </div>

          <div class="space-y-3">
            <div
              v-for="member in familyMembers"
              :key="member.id"
              class="flex items-center justify-between p-4 bg-lavender-50 dark:bg-prussian-700 rounded-lg"
            >
              <div class="flex items-center gap-3">
                <button
                  class="flex-shrink-0 rounded-full hover:ring-2 hover:ring-[#C4975A] hover:ring-offset-2 dark:hover:ring-offset-prussian-700 transition-all"
                  title="Change avatar"
                  @click="openAvatarEditor(member)"
                >
                  <UserAvatar :user="member" size="md" />
                </button>
                <div>
                  <p class="font-semibold text-prussian-500 dark:text-lavender-200">{{ member.name }}</p>
                  <p v-if="member.email" class="text-xs text-lavender-700 dark:text-lavender-400">{{ member.email }}</p>
                  <p v-else class="text-xs text-lavender-600 dark:text-lavender-500 italic">Managed account</p>
                  <div class="flex items-center gap-2 mt-1">
                    <span
                      :class="[
                        'text-xs px-2 py-0.5 rounded-full font-medium',
                        member.family_role === 'parent' || member.role === 'parent'
                          ? 'bg-wisteria-100 text-wisteria-700 dark:bg-wisteria-900/30 dark:text-wisteria-300'
                          : 'bg-lavender-200 text-lavender-700 dark:bg-prussian-600 dark:text-lavender-300'
                      ]"
                    >
                      {{ (member.family_role || member.role) === 'parent' ? 'Parent' : 'Child' }}
                    </span>
                    <span v-if="member.is_managed" class="text-xs px-2 py-0.5 rounded-full bg-sand-100 text-sand-700 dark:bg-sand-900/30 dark:text-sand-300 font-medium">
                      Managed
                    </span>
                  </div>
                </div>
              </div>

              <div v-if="member.id !== currentUser?.id" class="flex items-center gap-1">
                <button
                  v-if="member.is_managed"
                  class="p-2 hover:bg-wisteria-100 dark:hover:bg-wisteria-900/20 rounded-lg transition-colors"
                  title="Switch to this profile"
                  @click="openSwitchToModal(member)"
                >
                  <ArrowsRightLeftIcon class="w-4 h-4 text-wisteria-600 dark:text-wisteria-400" />
                </button>
                <button
                  class="p-2 hover:bg-lavender-100 dark:hover:bg-prussian-600 rounded-lg transition-colors"
                  title="Edit member"
                  @click="openEditMemberModal(member)"
                >
                  <PencilIcon class="w-4 h-4 text-prussian-400 dark:text-lavender-400" />
                </button>
                <button
                  class="p-2 hover:bg-red-100 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                  title="Remove member"
                  @click="confirmRemoveMember(member)"
                >
                  <TrashIcon class="w-4 h-4 text-red-600" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Setup Wizard (relocated here from bottom) -->
        <div class="border-t border-lavender-200 dark:border-prussian-700 pt-4">
          <h3 class="font-semibold text-prussian-500 dark:text-lavender-200 mb-2">Setup Wizard</h3>
          <p class="text-sm text-lavender-700 dark:text-lavender-400 mb-4">
            Re-run the setup wizard to invite members, connect calendars, or configure features.
          </p>
          <BaseButton variant="secondary" @click="$router.push({ name: 'Onboarding' })">
            Re-run Setup Wizard
          </BaseButton>
        </div>
      </SettingsSection>

      <!-- Section 2: Tasks & Points -->
      <SettingsSection
        v-if="moduleToggles.tasks || moduleToggles.points"
        id="tasks-points"
        title="Tasks & Points"
        description="Configure task behavior, points, and rewards"
        :icon="ClipboardDocumentListIcon"
        :model-value="expandedSections.has('tasks-points')"
        @update:model-value="val => toggleSection('tasks-points', val)"
      >
        <!-- Tasks module access -->
        <div class="mb-6">
          <div
            v-for="module in tasksPointsModules"
            :key="module.id"
            class="p-4 bg-lavender-50 dark:bg-prussian-700 rounded-lg mb-3"
          >
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
              <div>
                <p class="font-medium text-prussian-500 dark:text-lavender-200">{{ module.name }}</p>
                <p class="text-xs text-lavender-700 dark:text-lavender-400">{{ module.description }}</p>
              </div>
              <div class="flex gap-1.5 shrink-0">
                <button
                  :class="['px-2.5 py-1 text-xs font-medium rounded-full transition-colors', moduleAccessState[module.id]?.mode === 'all' ? 'bg-wisteria-500 text-white' : 'bg-lavender-200 dark:bg-prussian-600 text-lavender-700 dark:text-lavender-300 hover:bg-lavender-300 dark:hover:bg-prussian-500']"
                  @click="setModuleMode(module.id, 'all')"
                >
                  Everyone
                </button>
                <button
                  :class="['px-2.5 py-1 text-xs font-medium rounded-full transition-colors', moduleAccessState[module.id]?.mode === 'roles' ? 'bg-wisteria-500 text-white' : 'bg-lavender-200 dark:bg-prussian-600 text-lavender-700 dark:text-lavender-300 hover:bg-lavender-300 dark:hover:bg-prussian-500']"
                  @click="setModuleMode(module.id, 'roles', ['parent'])"
                >
                  Parents Only
                </button>
                <button
                  :class="['px-2.5 py-1 text-xs font-medium rounded-full transition-colors', moduleAccessState[module.id]?.mode === 'off' ? 'bg-red-500 text-white' : 'bg-lavender-200 dark:bg-prussian-600 text-lavender-700 dark:text-lavender-300 hover:bg-lavender-300 dark:hover:bg-prussian-500']"
                  @click="setModuleMode(module.id, 'off')"
                >
                  Off
                </button>
                <button
                  :class="['px-2.5 py-1 text-xs font-medium rounded-full transition-colors', moduleAccessState[module.id]?.mode === 'users' ? 'bg-wisteria-500 text-white' : 'bg-lavender-200 dark:bg-prussian-600 text-lavender-700 dark:text-lavender-300 hover:bg-lavender-300 dark:hover:bg-prussian-500']"
                  @click="setModuleMode(module.id, 'users', getSelectedUserIds(module.id))"
                >
                  Custom
                </button>
              </div>
            </div>

            <!-- Per-member checkboxes -->
            <div v-if="moduleAccessState[module.id]?.mode === 'users'" class="mt-3 pt-3 border-t border-lavender-200 dark:border-prussian-600">
              <p class="text-xs font-medium text-prussian-400 dark:text-lavender-300 mb-2">Select family members:</p>
              <div class="flex flex-wrap gap-2">
                <label
                  v-for="member in familyMembers"
                  :key="member.id"
                  class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-prussian-800 rounded-lg cursor-pointer hover:bg-lavender-100 dark:hover:bg-prussian-600 transition-colors"
                >
                  <input type="checkbox" :checked="isMemberSelected(module.id, member.id)" class="rounded" :disabled="(member.family_role || member.role) === 'parent'" @change="toggleMemberAccess(module.id, member.id)" />
                  <UserAvatar :user="member" size="xs" />
                  <span class="text-sm text-prussian-500 dark:text-lavender-200">{{ member.name }}</span>
                  <span v-if="(member.family_role || member.role) === 'parent'" class="text-xs text-lavender-500 dark:text-lavender-400 italic">(always)</span>
                </label>
              </div>
            </div>

            <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-2">
              <template v-if="moduleAccessState[module.id]?.mode === 'all'">All family members can access this feature.</template>
              <template v-else-if="moduleAccessState[module.id]?.mode === 'off'">This feature is disabled for everyone.</template>
              <template v-else-if="moduleAccessState[module.id]?.mode === 'roles'">Only parents can access this feature.</template>
              <template v-else-if="moduleAccessState[module.id]?.mode === 'users'">{{ getSelectedMemberNames(module.id) || 'No members selected (parents always have access).' }}</template>
            </p>
          </div>
        </div>

        <!-- Leaderboard Period -->
        <div v-if="moduleToggles.points" class="border-t border-lavender-200 dark:border-prussian-700 pt-4 mb-4">
          <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300 mb-2">
            Leaderboard Reset Period
          </label>
          <select v-model="leaderboardPeriod" class="input-base w-full max-w-xs">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
          </select>
          <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-1">
            How often the leaderboard resets. Does not affect point balances.
          </p>

          <!-- Kudos Cost Toggle -->
          <div class="mt-4">
            <ToggleSwitch
              :model-value="kudosCostEnabled"
              label="Kudos cost points"
              description="Giving kudos deducts 1 point from the giver's bank. Prevents trading kudos back and forth."
              @update:model-value="kudosCostEnabled = $event"
            />
          </div>
        </div>

        <!-- Default Task Points -->
        <div v-if="moduleToggles.tasks && moduleToggles.points" class="border-t border-lavender-200 dark:border-prussian-700 pt-4 mb-4">
          <h3 class="font-semibold text-prussian-500 dark:text-lavender-200 mb-2">Default Task Points</h3>
          <p class="text-sm text-lavender-700 dark:text-lavender-400 mb-4">
            Set how many points are awarded by default for each task priority level. Tasks with explicitly set points are not affected.
          </p>

          <div class="space-y-3">
            <div class="flex items-center gap-4 p-4 bg-lavender-50 dark:bg-prussian-700 rounded-lg">
              <div class="flex-1">
                <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300">Low Priority</label>
              </div>
              <input v-model.number="defaultPoints.low" type="number" min="0" max="1000" class="input-base w-24 text-center" />
              <span class="text-sm text-lavender-600 dark:text-lavender-400">pts</span>
            </div>
            <div class="flex items-center gap-4 p-4 bg-lavender-50 dark:bg-prussian-700 rounded-lg">
              <div class="flex-1">
                <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300">Medium Priority</label>
              </div>
              <input v-model.number="defaultPoints.medium" type="number" min="0" max="1000" class="input-base w-24 text-center" />
              <span class="text-sm text-lavender-600 dark:text-lavender-400">pts</span>
            </div>
            <div class="flex items-center gap-4 p-4 bg-lavender-50 dark:bg-prussian-700 rounded-lg">
              <div class="flex-1">
                <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300">High Priority</label>
              </div>
              <input v-model.number="defaultPoints.high" type="number" min="0" max="1000" class="input-base w-24 text-center" />
              <span class="text-sm text-lavender-600 dark:text-lavender-400">pts</span>
            </div>
          </div>
        </div>

        <!-- Task Assignment -->
        <div v-if="moduleToggles.tasks" class="border-t border-lavender-200 dark:border-prussian-700 pt-4 mb-4">
          <h3 class="font-semibold text-prussian-500 dark:text-lavender-200 mb-2">Task Assignment</h3>
          <p class="text-sm text-lavender-700 dark:text-lavender-400 mb-4">
            Control which family members can assign tasks to others. Parents can always assign tasks to anyone.
          </p>

          <div class="space-y-3">
            <label
              v-for="option in taskAssignmentOptions"
              :key="option.value"
              class="flex items-start gap-3 p-4 bg-lavender-50 dark:bg-prussian-700 rounded-lg cursor-pointer hover:bg-lavender-100 dark:hover:bg-prussian-600 transition-colors"
            >
              <input v-model="taskAssignment.mode" type="radio" :value="option.value" name="task_assignment_mode" class="mt-0.5 rounded-full" />
              <div class="flex-1">
                <p class="font-medium text-prussian-500 dark:text-lavender-200">{{ option.label }}</p>
                <p class="text-xs text-lavender-700 dark:text-lavender-400">{{ option.description }}</p>
              </div>
            </label>
          </div>

          <div v-if="taskAssignment.mode === 'users'" class="mt-4 pl-4 border-l-2 border-wisteria-300 dark:border-wisteria-700">
            <p class="text-sm font-medium text-prussian-400 dark:text-lavender-300 mb-3">Select which children can assign tasks to others:</p>
            <div class="space-y-2">
              <label
                v-for="child in childMembers"
                :key="child.id"
                class="flex items-center gap-3 p-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg cursor-pointer hover:bg-lavender-100 dark:hover:bg-prussian-600 transition-colors"
              >
                <input v-model="taskAssignment.users" type="checkbox" :value="child.id" class="rounded" />
                <span class="text-sm font-medium text-prussian-500 dark:text-lavender-200">{{ child.name }}</span>
              </label>
              <p v-if="childMembers.length === 0" class="text-sm text-lavender-600 dark:text-lavender-400 italic">
                No child members in the family yet.
              </p>
            </div>
          </div>
        </div>

        <!-- Save button -->
        <div class="flex justify-end pt-4 border-t border-lavender-200 dark:border-prussian-700">
          <BaseButton variant="primary" :loading="savingTasksPoints" @click="saveTasksPointsSection">
            Save Changes
          </BaseButton>
        </div>
      </SettingsSection>

      <!-- Section 3: AI & Integrations -->
      <SettingsSection
        id="ai-integrations"
        title="AI & Integrations"
        description="API keys, calendar connections, and MCP"
        :icon="CpuChipIcon"
        :model-value="expandedSections.has('ai-integrations')"
        @update:model-value="val => toggleSection('ai-integrations', val)"
      >
        <!-- AI Mode Toggle -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300 mb-3">
            AI Access
          </label>

          <!-- Two-tab selector -->
          <div class="grid grid-cols-2 gap-3 mb-5">
            <!-- Kinhold AI tab -->
            <button
              :class="[
                'relative flex flex-col items-start p-4 rounded-xl border-2 transition-all duration-200 text-left',
                aiMode === 'kinhold'
                  ? 'border-wisteria-500 dark:border-wisteria-400 ring-2 ring-wisteria-500/20 bg-lavender-50 dark:bg-prussian-800'
                  : 'border-lavender-200 dark:border-prussian-700 hover:border-lavender-400 bg-white dark:bg-prussian-800/50',
              ]"
              @click="aiMode = 'kinhold'"
            >
              <div v-if="aiMode === 'kinhold'" class="absolute top-2 right-2 w-5 h-5 rounded-full bg-wisteria-500 flex items-center justify-center">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div class="w-8 h-8 rounded-lg bg-wisteria-100 dark:bg-wisteria-900/40 flex items-center justify-center mb-2">
                <svg class="w-4 h-4 text-wisteria-600 dark:text-wisteria-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
              <p class="text-sm font-semibold text-prussian-500 dark:text-lavender-200">Use Kinhold AI</p>
              <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-0.5">Powered by Claude · Free in beta</p>
            </button>

            <!-- BYOK tab -->
            <button
              :class="[
                'relative flex flex-col items-start p-4 rounded-xl border-2 transition-all duration-200 text-left',
                aiMode === 'byok'
                  ? 'border-wisteria-500 dark:border-wisteria-400 ring-2 ring-wisteria-500/20 bg-lavender-50 dark:bg-prussian-800'
                  : 'border-lavender-200 dark:border-prussian-700 hover:border-lavender-400 bg-white dark:bg-prussian-800/50',
              ]"
              @click="aiMode = 'byok'"
            >
              <div v-if="aiMode === 'byok'" class="absolute top-2 right-2 w-5 h-5 rounded-full bg-wisteria-500 flex items-center justify-center">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div class="w-8 h-8 rounded-lg bg-golden-100 dark:bg-golden-900/40 flex items-center justify-center mb-2">
                <svg class="w-4 h-4 text-golden-600 dark:text-golden-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
              </div>
              <p class="text-sm font-semibold text-prussian-500 dark:text-lavender-200">My Own API Key</p>
              <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-0.5">Anthropic, OpenAI, or Google</p>
            </button>
          </div>

          <!-- Kinhold AI panel -->
          <div v-if="aiMode === 'kinhold'" class="p-4 bg-wisteria-50 dark:bg-wisteria-900/20 border border-wisteria-200 dark:border-wisteria-800 rounded-lg">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-wisteria-600 dark:text-wisteria-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div>
                <p class="text-sm font-medium text-wisteria-800 dark:text-wisteria-200">You're all set</p>
                <p class="text-xs text-wisteria-700 dark:text-wisteria-300 mt-0.5">
                  Kinhold AI is powered by Anthropic's Claude. No API key needed — we handle it for you.
                  This is free during the beta period.
                </p>
              </div>
            </div>
          </div>

          <!-- BYOK panel -->
          <div v-if="aiMode === 'byok'" class="space-y-4">
            <!-- Provider selection -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <button
                v-for="provider in aiProviders"
                :key="provider.slug"
                :class="[
                  'relative flex flex-col items-center p-3 rounded-xl border-2 transition-all duration-200 text-center',
                  aiConfig.provider === provider.slug
                    ? 'border-wisteria-500 dark:border-wisteria-400 ring-2 ring-wisteria-500/20 bg-lavender-50 dark:bg-prussian-800'
                    : 'border-lavender-200 dark:border-prussian-700 hover:border-lavender-400 bg-white dark:bg-prussian-800/50',
                ]"
                @click="selectAiProvider(provider.slug)"
              >
                <div class="w-8 h-8 mb-1.5 flex items-center justify-center rounded-lg" :class="providerIconClass(provider.slug)">
                  <span class="text-base font-bold">{{ providerIcon(provider.slug) }}</span>
                </div>
                <p class="text-xs font-semibold text-prussian-500 dark:text-lavender-200">{{ provider.name }}</p>
              </button>
            </div>

            <!-- API Key -->
            <div>
              <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300 mb-1.5">
                {{ selectedProviderName }} API Key
              </label>
              <div class="relative">
                <input
                  v-model="aiConfig.apiKey"
                  :type="showAiKey ? 'text' : 'password'"
                  :placeholder="selectedProviderPlaceholder"
                  class="input-base pr-20"
                />
                <button
                  type="button" class="absolute right-2 top-1/2 -translate-y-1/2 px-2 py-1 text-xs font-medium text-lavender-600 dark:text-lavender-400 hover:text-prussian-500 transition-colors"
                  @click="showAiKey = !showAiKey"
                >
                  {{ showAiKey ? 'Hide' : 'Show' }}
                </button>
              </div>
              <div class="flex items-center justify-between mt-1">
                <p class="text-xs text-lavender-700 dark:text-lavender-400">
                  <template v-if="aiConfig.hasSavedKey && !aiConfig.apiKey">
                    Current key: <span class="font-mono">{{ aiConfig.maskedKey }}</span>
                  </template>
                  <template v-else>Keep this secret — stored encrypted.</template>
                </p>
                <a
                  :href="selectedProviderHelpUrl" target="_blank" rel="noopener noreferrer"
                  class="text-xs text-wisteria-600 dark:text-wisteria-400 hover:underline whitespace-nowrap ml-2"
                >
                  Get API key →
                </a>
              </div>
            </div>

            <!-- Model override -->
            <div>
              <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300 mb-1.5">
                Model Override <span class="font-normal text-lavender-500">(optional)</span>
              </label>
              <input v-model="aiConfig.model" type="text" :placeholder="selectedProviderDefaultModel" class="input-base" />
              <p class="text-xs text-lavender-700 dark:text-lavender-400 mt-1">
                Leave blank to use {{ selectedProviderDefaultModel }}.
              </p>
            </div>
          </div>

          <div class="flex gap-3 justify-end pt-4">
            <BaseButton variant="ghost" @click="resetAiConfig">Reset</BaseButton>
            <BaseButton variant="primary" :loading="savingAi" @click="saveAiSettings">Save AI Settings</BaseButton>
          </div>
        </div>

        <!-- Connect AI Assistant (MCP Token) -->
        <div class="border-t border-lavender-200 dark:border-prussian-700 pt-4 mb-6">
          <h3 class="font-semibold text-prussian-500 dark:text-lavender-200 mb-2">Connect AI Assistant</h3>
          <p class="text-sm text-lavender-700 dark:text-lavender-400 mb-4">
            Connect any MCP-compatible AI assistant to manage your family hub.
          </p>

          <!-- OAuth quick-connect info -->
          <div class="p-4 bg-wisteria-50 dark:bg-wisteria-900/20 border border-wisteria-200 dark:border-wisteria-800 rounded-lg mb-4">
            <p class="text-sm font-medium text-wisteria-800 dark:text-wisteria-200 mb-1">Quick Connect (Claude Desktop / ChatGPT)</p>
            <p class="text-xs text-wisteria-700 dark:text-wisteria-300 mb-2">
              Add Kinhold as a custom connector using OAuth — no token needed:
            </p>
            <div class="space-y-1 text-xs text-wisteria-600 dark:text-wisteria-300">
              <div class="flex items-start gap-2">
                <span class="font-medium min-w-[40px]">Name</span>
                <code class="font-mono bg-wisteria-100 dark:bg-wisteria-900/40 px-1.5 py-0.5 rounded">Kinhold</code>
              </div>
              <div class="flex items-start gap-2">
                <span class="font-medium min-w-[40px]">URL</span>
                <code class="font-mono bg-wisteria-100 dark:bg-wisteria-900/40 px-1.5 py-0.5 rounded break-all">{{ mcpGenerated.mcpUrl || (appOrigin + '/mcp') }}</code>
              </div>
            </div>
            <p class="text-xs text-wisteria-600 dark:text-wisteria-300 mt-2">
              Leave OAuth fields blank. You'll be prompted to sign in with Google when you connect.
            </p>
          </div>

          <!-- Bearer token fallback (Claude Code / manual) -->
          <p class="text-xs text-lavender-600 dark:text-lavender-400 mb-2 font-medium">Advanced: Bearer Token (for Claude Code / manual setup)</p>
          <div class="flex items-center justify-between p-4 bg-lavender-50 dark:bg-prussian-700 rounded-lg">
            <div>
              <div class="flex items-center gap-2">
                <p class="font-medium text-prussian-500 dark:text-lavender-200">MCP Connection</p>
                <span v-if="mcpToken.hasToken" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                  Connected
                </span>
                <span v-else class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-lavender-100 text-lavender-700 dark:bg-prussian-600 dark:text-lavender-400">
                  Not Connected
                </span>
              </div>
              <p v-if="mcpToken.lastUsedAt" class="text-xs text-lavender-600 dark:text-lavender-400 mt-1">
                Last used: {{ new Date(mcpToken.lastUsedAt).toLocaleDateString() }}
              </p>
            </div>
            <div class="flex items-center gap-2">
              <BaseButton v-if="mcpToken.hasToken" variant="ghost" size="sm" :loading="mcpRevoking" @click="handleRevokeMcpToken">
                Revoke
              </BaseButton>
              <BaseButton variant="secondary" size="sm" :loading="mcpGenerating" @click="handleGenerateMcpToken">
                {{ mcpToken.hasToken ? 'Regenerate Token' : 'Generate Token' }}
              </BaseButton>
            </div>
          </div>

          <!-- One-time token display -->
          <div v-if="mcpGenerated.show" class="mt-4 space-y-4">
            <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
              <p class="text-sm text-amber-800 dark:text-amber-200 font-medium">
                This token is shown only once. Copy what you need now — you won't be able to see it again.
              </p>
            </div>

            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300">Bearer Token</label>
                <BaseButton variant="ghost" size="sm" @click="copyMcpSnippet('token', mcpGenerated.plainToken)">
                  <ClipboardDocumentIcon class="w-4 h-4 mr-1" />
                  {{ mcpCopied.token ? 'Copied!' : 'Copy' }}
                </BaseButton>
              </div>
              <div class="font-mono text-sm bg-prussian-800 dark:bg-prussian-900 text-green-400 p-3 rounded-lg overflow-x-auto">
                {{ mcpGenerated.plainToken }}
              </div>
            </div>

            <div>
              <div class="flex flex-wrap gap-1 border-b border-lavender-200 dark:border-prussian-700 mb-3">
                <button
                  v-for="client in mcpGenerated.clients"
                  :key="client.id"
                  :class="[
                    'px-3 py-1.5 text-sm font-medium rounded-t-lg transition-colors -mb-px',
                    mcpActiveClient === client.id
                      ? 'border border-b-white dark:border-b-prussian-800 border-lavender-200 dark:border-prussian-700 text-prussian-500 dark:text-lavender-200 bg-white dark:bg-prussian-800'
                      : 'text-lavender-600 dark:text-lavender-400 hover:text-prussian-500 dark:hover:text-lavender-200',
                  ]"
                  @click="mcpActiveClient = client.id"
                >
                  {{ client.name }}
                </button>
              </div>

              <div v-for="client in mcpGenerated.clients" v-show="mcpActiveClient === client.id" :key="client.id">
                <p class="text-xs text-lavender-600 dark:text-lavender-400 mb-2">{{ client.instructions }}</p>

                <template v-if="client.steps">
                  <ol class="list-decimal list-inside space-y-2 text-sm text-prussian-500 dark:text-lavender-200 mb-3">
                    <li v-for="(step, i) in client.steps" :key="i" class="leading-relaxed">{{ step }}</li>
                  </ol>
                </template>

                <template v-else-if="client.details">
                  <div class="space-y-2 mb-2">
                    <div v-for="(value, label) in client.details" :key="label" class="flex items-start gap-2">
                      <span class="text-xs font-medium text-lavender-600 dark:text-lavender-400 uppercase min-w-[80px]">{{ label.replace('_', ' ') }}</span>
                      <code class="text-sm font-mono text-prussian-500 dark:text-lavender-200 break-all">{{ value }}</code>
                    </div>
                  </div>
                  <BaseButton variant="ghost" size="sm" @click="copyMcpSnippet(client.id, Object.entries(client.details).map(([k, v]) => k + ': ' + v).join('\n'))">
                    <ClipboardDocumentIcon class="w-4 h-4 mr-1" />
                    {{ mcpCopied[client.id] ? 'Copied!' : 'Copy Details' }}
                  </BaseButton>
                </template>

                <template v-else>
                  <div class="flex items-center justify-end mb-1">
                    <BaseButton variant="ghost" size="sm" @click="copyMcpSnippet(client.id, client.command || client.configJson)">
                      <ClipboardDocumentIcon class="w-4 h-4 mr-1" />
                      {{ mcpCopied[client.id] ? 'Copied!' : 'Copy' }}
                    </BaseButton>
                  </div>
                  <pre class="font-mono text-sm bg-prussian-800 dark:bg-prussian-900 text-lavender-200 p-3 rounded-lg overflow-x-auto whitespace-pre">{{ client.command || client.configJson }}</pre>
                </template>
              </div>
            </div>
          </div>
        </div>

        <!-- Google Account Linking -->
        <div class="border-t border-lavender-200 dark:border-prussian-700 pt-4 mb-6">
          <h3 class="font-semibold text-prussian-500 dark:text-lavender-200 mb-3">Google Account</h3>
          <p class="text-sm text-lavender-700 dark:text-lavender-400 mb-4">
            Link your Google account for quick sign-in. Your Google email must match your account email.
          </p>

          <div v-if="currentUser?.google_id" class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
              <span class="text-sm font-medium text-green-800 dark:text-green-300">Google account linked</span>
            </div>
            <BaseButton variant="ghost" size="sm" :loading="unlinkingGoogle" @click="handleUnlinkGoogle">Unlink</BaseButton>
          </div>

          <div v-else class="flex items-center justify-between p-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg">
            <div>
              <p class="text-sm font-medium text-prussian-500 dark:text-lavender-200">Not linked</p>
              <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-0.5">Sign in faster with Google</p>
            </div>
            <BaseButton variant="secondary" size="sm" :loading="linkingGoogle" @click="handleLinkGoogle">Link Google</BaseButton>
          </div>

          <div v-if="googleLinkError" class="mt-2 p-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-xs text-red-700 dark:text-red-300">{{ googleLinkError }}</p>
          </div>
          <div v-if="googleLinked" class="mt-2 p-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <p class="text-xs text-green-700 dark:text-green-300">Google account linked successfully!</p>
          </div>
        </div>

        <!-- Google Calendar -->
        <div class="border-t border-lavender-200 dark:border-prussian-700 pt-4 mb-6">
          <h3 class="font-semibold text-prussian-500 dark:text-lavender-200 mb-3">Google Calendar Sync</h3>
          <p class="text-sm text-lavender-700 dark:text-lavender-400 mb-4">
            Connect your Google Calendar to sync events into the family hub.
          </p>

          <!-- Google OAuth not configured notice -->
          <div v-if="authStore.services && !authStore.services.google_calendar" class="p-4 bg-golden-50 dark:bg-golden-900/20 rounded-xl mb-4">
            <p class="text-sm font-medium text-golden-800 dark:text-golden-300 mb-1">Google Calendar Not Configured</p>
            <p class="text-xs text-golden-700 dark:text-golden-400">
              This server doesn't have Google OAuth credentials set up. You can still create manual events from the calendar page.
              <template v-if="authStore.isParent"> Set <code class="bg-golden-100 dark:bg-golden-900/40 px-1 rounded">GOOGLE_CLIENT_ID</code> and <code class="bg-golden-100 dark:bg-golden-900/40 px-1 rounded">GOOGLE_CLIENT_SECRET</code> in your environment to enable Google Calendar sync.</template>
            </p>
          </div>

          <div v-else class="space-y-2">
            <div
              v-for="conn in userCalendarConnections"
              :key="conn.id"
              class="flex items-center justify-between p-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg"
            >
              <div>
                <p class="font-medium text-prussian-500 dark:text-lavender-200">{{ conn.calendar_name || 'Google Calendar' }}</p>
                <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-0.5">{{ currentUser?.name }}</p>
              </div>
              <BaseButton variant="ghost" size="sm" @click="handleDisconnectCalendar(conn.id)">Disconnect</BaseButton>
            </div>

            <div v-if="userCalendarConnections.length === 0" class="flex items-center justify-between p-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg">
              <div>
                <p class="font-medium text-prussian-500 dark:text-lavender-200">{{ currentUser?.name }}</p>
                <p class="text-xs text-lavender-700 dark:text-lavender-400 mt-0.5">
                  <span class="badge badge-warning">Not Connected</span>
                </p>
              </div>
              <BaseButton variant="secondary" size="sm" :loading="connectingCalendar" @click="handleConnectCalendar">Connect</BaseButton>
            </div>

            <div v-if="userCalendarConnections.length > 0" class="flex justify-end">
              <BaseButton variant="secondary" size="sm" :loading="connectingCalendar" @click="handleConnectCalendar">
                Reconnect / Add Calendars
              </BaseButton>
            </div>

            <div
              v-for="conn in otherMemberConnections"
              :key="conn.id"
              class="flex items-center justify-between p-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg"
            >
              <div>
                <p class="font-medium text-prussian-500 dark:text-lavender-200">{{ conn.calendar_name || 'Google Calendar' }}</p>
                <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-0.5">{{ conn.user?.name || 'Family Member' }}</p>
              </div>
            </div>
          </div>

          <div v-if="calendarError" class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-sm text-red-700 dark:text-red-300">{{ calendarError }}</p>
          </div>
        </div>

        <!-- ICS URL Subscription -->
        <div class="border-t border-lavender-200 dark:border-prussian-700 pt-4">
          <h3 class="font-semibold text-prussian-500 dark:text-lavender-200 mb-3">Subscribe via URL</h3>
          <p class="text-sm text-lavender-700 dark:text-lavender-400 mb-4">
            Add a calendar by pasting its ICS feed URL (works with any .ics calendar link).
          </p>

          <form class="space-y-3" @submit.prevent="handleSubscribeUrl">
            <div>
              <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300 mb-1">Calendar URL</label>
              <input v-model="icsForm.url" type="url" placeholder="https://example.com/calendar.ics" class="input-base" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300 mb-1">Calendar Name (optional)</label>
              <input v-model="icsForm.name" type="text" placeholder="My Calendar" class="input-base" />
              <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-1">If left blank, the name will be auto-detected from the calendar data.</p>
            </div>
            <div class="flex justify-end">
              <BaseButton variant="secondary" size="sm" :loading="subscribingUrl">Subscribe</BaseButton>
            </div>
          </form>

          <div v-if="icsError" class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-sm text-red-700 dark:text-red-300">{{ icsError }}</p>
          </div>

          <div v-if="icsConnections.length > 0" class="mt-4 space-y-2">
            <p class="text-sm font-medium text-prussian-400 dark:text-lavender-300">Subscribed Calendars</p>
            <div
              v-for="conn in icsConnections"
              :key="conn.id"
              class="flex items-center justify-between p-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg"
            >
              <div>
                <p class="font-medium text-prussian-500 dark:text-lavender-200">{{ conn.calendar_name || 'ICS Calendar' }}</p>
                <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-0.5">URL subscription</p>
              </div>
              <BaseButton variant="ghost" size="sm" @click="handleDisconnectCalendar(conn.id)">Unsubscribe</BaseButton>
            </div>
          </div>
        </div>
      </SettingsSection>

      <!-- Section 4: Feature Access -->
      <SettingsSection
        id="feature-access"
        title="Feature Access"
        description="Control which features each family member can access"
        :icon="ShieldCheckIcon"
        badge="Parent"
        :model-value="expandedSections.has('feature-access')"
        @update:model-value="val => toggleSection('feature-access', val)"
      >
        <div class="space-y-4">
          <div
            v-for="module in otherModules"
            :key="module.id"
            class="p-4 bg-lavender-50 dark:bg-prussian-700 rounded-lg"
          >
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
              <div>
                <p class="font-medium text-prussian-500 dark:text-lavender-200">{{ module.name }}</p>
                <p class="text-xs text-lavender-700 dark:text-lavender-400">{{ module.description }}</p>
              </div>
              <div class="flex gap-1.5 shrink-0">
                <button
                  :class="['px-2.5 py-1 text-xs font-medium rounded-full transition-colors', moduleAccessState[module.id]?.mode === 'all' ? 'bg-wisteria-500 text-white' : 'bg-lavender-200 dark:bg-prussian-600 text-lavender-700 dark:text-lavender-300 hover:bg-lavender-300 dark:hover:bg-prussian-500']"
                  @click="setModuleMode(module.id, 'all')"
                >
                  Everyone
                </button>
                <button
                  :class="['px-2.5 py-1 text-xs font-medium rounded-full transition-colors', moduleAccessState[module.id]?.mode === 'roles' ? 'bg-wisteria-500 text-white' : 'bg-lavender-200 dark:bg-prussian-600 text-lavender-700 dark:text-lavender-300 hover:bg-lavender-300 dark:hover:bg-prussian-500']"
                  @click="setModuleMode(module.id, 'roles', ['parent'])"
                >
                  Parents Only
                </button>
                <button
                  :class="['px-2.5 py-1 text-xs font-medium rounded-full transition-colors', moduleAccessState[module.id]?.mode === 'off' ? 'bg-red-500 text-white' : 'bg-lavender-200 dark:bg-prussian-600 text-lavender-700 dark:text-lavender-300 hover:bg-lavender-300 dark:hover:bg-prussian-500']"
                  @click="setModuleMode(module.id, 'off')"
                >
                  Off
                </button>
                <button
                  :class="['px-2.5 py-1 text-xs font-medium rounded-full transition-colors', moduleAccessState[module.id]?.mode === 'users' ? 'bg-wisteria-500 text-white' : 'bg-lavender-200 dark:bg-prussian-600 text-lavender-700 dark:text-lavender-300 hover:bg-lavender-300 dark:hover:bg-prussian-500']"
                  @click="setModuleMode(module.id, 'users', getSelectedUserIds(module.id))"
                >
                  Custom
                </button>
              </div>
            </div>

            <div v-if="moduleAccessState[module.id]?.mode === 'users'" class="mt-3 pt-3 border-t border-lavender-200 dark:border-prussian-600">
              <p class="text-xs font-medium text-prussian-400 dark:text-lavender-300 mb-2">Select family members:</p>
              <div class="flex flex-wrap gap-2">
                <label
                  v-for="member in familyMembers"
                  :key="member.id"
                  class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-prussian-800 rounded-lg cursor-pointer hover:bg-lavender-100 dark:hover:bg-prussian-600 transition-colors"
                >
                  <input type="checkbox" :checked="isMemberSelected(module.id, member.id)" class="rounded" :disabled="(member.family_role || member.role) === 'parent'" @change="toggleMemberAccess(module.id, member.id)" />
                  <UserAvatar :user="member" size="xs" />
                  <span class="text-sm text-prussian-500 dark:text-lavender-200">{{ member.name }}</span>
                  <span v-if="(member.family_role || member.role) === 'parent'" class="text-xs text-lavender-500 dark:text-lavender-400 italic">(always)</span>
                </label>
              </div>
            </div>

            <p class="text-xs text-lavender-600 dark:text-lavender-400 mt-2">
              <template v-if="moduleAccessState[module.id]?.mode === 'all'">All family members can access this feature.</template>
              <template v-else-if="moduleAccessState[module.id]?.mode === 'off'">This feature is disabled for everyone.</template>
              <template v-else-if="moduleAccessState[module.id]?.mode === 'roles'">Only parents can access this feature.</template>
              <template v-else-if="moduleAccessState[module.id]?.mode === 'users'">{{ getSelectedMemberNames(module.id) || 'No members selected (parents always have access).' }}</template>
            </p>
          </div>
        </div>

        <div class="flex gap-3 justify-end pt-4 border-t border-lavender-200 dark:border-prussian-700 mt-4">
          <BaseButton variant="primary" :loading="savingModules" @click="saveModuleSettings">
            Save Preferences
          </BaseButton>
        </div>
      </SettingsSection>

      <!-- Section 5: Appearance -->
      <SettingsSection
        id="appearance"
        title="Appearance"
        description="Dark mode and color themes"
        :icon="SwatchIcon"
        :model-value="expandedSections.has('appearance')"
        @update:model-value="val => toggleSection('appearance', val)"
      >
        <!-- Dark Mode -->
        <div class="flex items-center justify-between p-4 bg-lavender-50 dark:bg-prussian-800 rounded-lg">
          <div>
            <p class="font-medium text-prussian-500 dark:text-lavender-200">Dark Mode</p>
            <p class="text-xs text-lavender-700 dark:text-lavender-400 mt-0.5">Switch between light and dark themes</p>
          </div>
          <ToggleSwitch :model-value="isDark" @update:model-value="toggleDarkMode">
            <template #thumb>
              <MoonIcon v-if="isDark" class="w-3.5 h-3.5 text-wisteria-500" />
              <SunIcon v-else class="w-3.5 h-3.5 text-sand-500" />
            </template>
          </ToggleSwitch>
        </div>

        <!-- Color Theme Picker -->
        <div class="mt-4 pt-4 border-t border-lavender-200 dark:border-prussian-700">
          <p class="font-medium text-prussian-500 dark:text-lavender-200 mb-1">Color Theme</p>
          <p class="text-xs text-lavender-700 dark:text-lavender-400 mb-3">Choose a color palette for the app</p>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <button
              v-for="theme in availableThemes"
              :key="theme.id"
              :class="[
                'relative flex flex-col p-3 rounded-xl border-2 transition-all duration-200 text-left',
                currentTheme === theme.id
                  ? 'border-wisteria-500 dark:border-wisteria-400 ring-2 ring-wisteria-500/20 dark:ring-wisteria-400/20 bg-lavender-50 dark:bg-prussian-800'
                  : 'border-lavender-200 dark:border-prussian-700 hover:border-lavender-400 dark:hover:border-prussian-500 bg-white dark:bg-prussian-800/50',
              ]"
              @click="selectTheme(theme.id)"
            >
              <div
                v-if="currentTheme === theme.id"
                class="absolute top-2 right-2 w-5 h-5 rounded-full bg-wisteria-500 dark:bg-wisteria-400 flex items-center justify-center"
              >
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div class="flex gap-1.5 mb-2">
                <span class="w-8 h-8 rounded-lg shadow-sm" :style="{ backgroundColor: theme.colors.primary }"></span>
                <span class="w-8 h-8 rounded-lg shadow-sm" :style="{ backgroundColor: theme.colors.accent }"></span>
                <span class="w-8 h-8 rounded-lg shadow-sm border border-gray-200 dark:border-gray-600" :style="{ backgroundColor: theme.colors.surface }"></span>
                <span class="w-8 h-8 rounded-lg shadow-sm" :style="{ backgroundColor: theme.colors.highlight }"></span>
              </div>
              <p class="text-sm font-semibold text-prussian-500 dark:text-lavender-200">{{ theme.name }}</p>
              <p class="text-xs text-lavender-600 dark:text-lavender-400">{{ theme.description }}</p>
            </button>
          </div>
        </div>
      </SettingsSection>

      <!-- Section 6: Notifications -->
      <SettingsSection
        v-if="currentUser?.email"
        id="notifications"
        title="Notifications"
        description="Email notification preferences"
        :icon="BellIcon"
        :model-value="expandedSections.has('notifications')"
        @update:model-value="val => toggleSection('notifications', val)"
      >
        <div class="space-y-3">
          <ToggleSwitch
            v-for="pref in emailPreferenceOptions"
            :key="pref.key"
            :model-value="emailPrefs[pref.key]"
            :label="pref.label"
            :description="pref.description"
            @update:model-value="emailPrefs[pref.key] = $event"
          />
        </div>

        <div class="flex gap-3 justify-end pt-4 mt-4 border-t border-lavender-200 dark:border-prussian-700">
          <BaseButton variant="primary" :loading="savingEmailPrefs" @click="saveEmailPreferences">
            Save Email Preferences
          </BaseButton>
        </div>
      </SettingsSection>

      <!-- Section 7: About -->
      <SettingsSection
        id="about"
        title="About Kinhold"
        description="Version info and updates"
        :icon="InformationCircleIcon"
        :model-value="expandedSections.has('about')"
        @update:model-value="val => toggleSection('about', val)"
      >
        <!-- Update Available Banner -->
        <div
          v-if="updateAvailable && !updateDismissed"
          class="flex items-start gap-3 p-4 mb-4 bg-sand-50 dark:bg-sand-900/30 border border-sand-300 dark:border-sand-700 rounded-lg"
        >
          <div class="flex-1">
            <p class="font-semibold text-sand-800 dark:text-sand-200">
              Update available: v{{ updateAvailable.latest_version }}
            </p>
            <p class="text-sm text-sand-700 dark:text-sand-400 mt-1">
              You're running v{{ appVersion }}. A newer version is available on GitHub.
            </p>
            <a
              :href="updateAvailable.url"
              target="_blank"
              rel="noopener"
              class="inline-flex items-center gap-1 text-sm font-medium text-wisteria-600 dark:text-wisteria-400 hover:underline mt-2"
            >
              View release notes
              <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
            </a>
          </div>
          <button
            class="p-1 text-sand-500 hover:text-sand-700 dark:text-sand-400 dark:hover:text-sand-200 rounded transition-colors"
            title="Dismiss"
            @click="dismissUpdate"
          >
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Version Info -->
        <div class="space-y-3">
          <div class="flex items-center justify-between p-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg">
            <span class="text-sm font-medium text-prussian-500 dark:text-lavender-200">Version</span>
            <span class="text-sm font-mono text-lavender-700 dark:text-lavender-400">v{{ appVersion }}</span>
          </div>
          <div class="flex items-center justify-between p-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg">
            <span class="text-sm font-medium text-prussian-500 dark:text-lavender-200">License</span>
            <span class="text-sm text-lavender-700 dark:text-lavender-400">Elastic License 2.0</span>
          </div>
          <div class="flex items-center gap-4 pt-2">
            <a
              :href="`https://github.com/${githubRepo}`"
              target="_blank"
              rel="noopener"
              class="inline-flex items-center gap-1.5 text-sm text-wisteria-600 dark:text-wisteria-400 hover:underline"
            >
              GitHub
              <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
            </a>
            <a
              :href="`https://github.com/${githubRepo}/releases`"
              target="_blank"
              rel="noopener"
              class="inline-flex items-center gap-1.5 text-sm text-wisteria-600 dark:text-wisteria-400 hover:underline"
            >
              Release Notes
              <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
            </a>
            <a
              href="https://kinhold.app"
              target="_blank"
              rel="noopener"
              class="inline-flex items-center gap-1.5 text-sm text-wisteria-600 dark:text-wisteria-400 hover:underline"
            >
              Website
              <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
            </a>
          </div>
        </div>
      </SettingsSection>
    </template>

    <!-- ============================================ -->
    <!-- CHILD VIEW — Flat cards (unchanged)          -->
    <!-- ============================================ -->
    <template v-if="!isParent">
      <!-- Appearance -->
      <div class="card-lg mb-6">
        <h2 class="text-lg font-semibold font-heading text-prussian-500 dark:text-lavender-200 mb-4">Appearance</h2>
        <div class="flex items-center justify-between p-4 bg-lavender-50 dark:bg-prussian-800 rounded-lg">
          <div>
            <p class="font-medium text-prussian-500 dark:text-lavender-200">Dark Mode</p>
            <p class="text-xs text-lavender-700 dark:text-lavender-400 mt-0.5">Switch between light and dark themes</p>
          </div>
          <ToggleSwitch :model-value="isDark" @update:model-value="toggleDarkMode">
            <template #thumb>
              <MoonIcon v-if="isDark" class="w-3.5 h-3.5 text-wisteria-500" />
              <SunIcon v-else class="w-3.5 h-3.5 text-sand-500" />
            </template>
          </ToggleSwitch>
        </div>
      </div>

      <!-- Email Notifications (if they have an email) -->
      <div v-if="currentUser?.email" class="card-lg mb-6">
        <div class="flex items-center gap-2 mb-4">
          <EnvelopeIcon class="w-5 h-5 text-wisteria-500 dark:text-wisteria-400" />
          <h2 class="text-lg font-semibold font-heading text-prussian-500 dark:text-lavender-200">Email Notifications</h2>
        </div>
        <div class="space-y-3">
          <ToggleSwitch
            v-for="pref in emailPreferenceOptions"
            :key="pref.key"
            :model-value="emailPrefs[pref.key]"
            :label="pref.label"
            :description="pref.description"
            @update:model-value="emailPrefs[pref.key] = $event"
          />
        </div>
        <div class="flex gap-3 justify-end pt-4 mt-4 border-t border-lavender-200 dark:border-prussian-700">
          <BaseButton variant="primary" :loading="savingEmailPrefs" @click="saveEmailPreferences">
            Save Email Preferences
          </BaseButton>
        </div>
      </div>

      <!-- About (child version — just version number) -->
      <div class="card-lg mb-6">
        <div class="flex items-center gap-2 mb-3">
          <InformationCircleIcon class="w-5 h-5 text-wisteria-500 dark:text-wisteria-400" />
          <h2 class="text-lg font-semibold font-heading text-prussian-500 dark:text-lavender-200">About</h2>
        </div>
        <div class="flex items-center justify-between p-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg">
          <span class="text-sm font-medium text-prussian-500 dark:text-lavender-200">Version</span>
          <span class="text-sm font-mono text-lavender-700 dark:text-lavender-400">v{{ appVersion }}</span>
        </div>
      </div>
    </template>

    <!-- ============================================ -->
    <!-- MODALS (always at root level)                -->
    <!-- ============================================ -->

    <!-- Add/Edit Member Modal -->
    <BaseModal
      :show="showMemberModal"
      :title="editingMember ? 'Edit Family Member' : 'Add Family Member'"
      @close="closeMemberModal"
    >
      <form class="space-y-4" @submit.prevent="handleSaveMember">
        <BaseInput
          v-model="memberForm.name"
          label="Name"
          placeholder="First name"
          required
          :error="memberErrors.name"
        />

        <BaseInput
          v-model="memberForm.email"
          label="Email (optional for managed accounts)"
          type="email"
          placeholder="email@example.com"
          :error="memberErrors.email"
        />
        <p class="text-xs text-lavender-600 dark:text-lavender-400 -mt-2">
          Leave blank for young kids — creates a managed account you can switch into.
        </p>

        <BaseInput
          v-if="!editingMember && memberForm.email"
          v-model="memberForm.password"
          label="Password (optional)"
          type="password"
          placeholder="Leave blank to set later"
          :error="memberErrors.password"
        />

        <label
          v-if="!editingMember && memberForm.email"
          class="flex items-center gap-3 p-3 bg-lavender-50 dark:bg-prussian-700 rounded-lg cursor-pointer"
        >
          <input v-model="memberForm.sendEmail" type="checkbox" class="rounded" />
          <div>
            <p class="text-sm font-medium text-prussian-500 dark:text-lavender-200">Send welcome email</p>
            <p class="text-xs text-lavender-600 dark:text-lavender-400">Send an email with login instructions</p>
          </div>
        </label>

        <div>
          <label class="block text-sm font-medium text-prussian-400 dark:text-lavender-300 mb-2">Role</label>
          <select v-model="memberForm.role" class="input-base w-full" required>
            <option value="child">Child</option>
            <option value="parent">Parent</option>
          </select>
        </div>

        <BaseInput
          v-model="memberForm.date_of_birth"
          label="Date of Birth (optional)"
          type="date"
        />

        <div class="flex gap-2 justify-end pt-4">
          <BaseButton variant="ghost" @click="closeMemberModal">Cancel</BaseButton>
          <BaseButton variant="primary" :loading="savingMember">
            {{ editingMember ? 'Save Changes' : 'Add Member' }}
          </BaseButton>
        </div>
      </form>
    </BaseModal>

    <!-- Remove Member Confirm -->
    <BaseModal
      :show="showRemoveConfirm"
      title="Remove Family Member"
      @close="showRemoveConfirm = false"
    >
      <p class="text-prussian-500 dark:text-lavender-300">
        Are you sure you want to remove <strong>{{ removingMember?.name }}</strong> from your family?
      </p>
      <p v-if="removingMember?.is_managed" class="text-sm text-red-600 dark:text-red-400 mt-2">
        This is a managed account and will be permanently deleted.
      </p>
      <p v-else class="text-sm text-lavender-600 dark:text-lavender-400 mt-2">
        Their account will be unlinked from your family but not deleted.
      </p>
      <div class="flex gap-2 justify-end pt-4">
        <BaseButton variant="ghost" @click="showRemoveConfirm = false">Cancel</BaseButton>
        <BaseButton variant="danger" :loading="removingLoading" @click="handleRemoveMember">Remove</BaseButton>
      </div>
    </BaseModal>

    <!-- Switch To Child Confirmation Modal -->
    <BaseModal
      :show="showSwitchToModal"
      title="Switch to Child Profile"
      @close="closeSwitchToModal"
    >
      <div class="space-y-3">
        <p class="text-sm text-prussian-500 dark:text-lavender-300">
          You're about to switch this device to <strong>{{ switchingToMember?.name }}</strong>'s profile.
        </p>
        <div class="p-3 bg-sand-50 dark:bg-sand-900/20 border border-sand-200 dark:border-sand-800 rounded-lg">
          <p class="text-sm text-sand-800 dark:text-sand-200 font-medium">What will happen:</p>
          <ul class="text-sm text-sand-700 dark:text-sand-300 mt-1 space-y-1 list-disc list-inside">
            <li>This device will be logged in as {{ switchingToMember?.name }}</li>
            <li>To switch back, sign out and sign back in as your parent account</li>
          </ul>
        </div>
      </div>
      <div class="flex gap-2 justify-end pt-4">
        <BaseButton variant="ghost" @click="closeSwitchToModal">Cancel</BaseButton>
        <BaseButton variant="primary" :loading="switchingTo" @click="handleSwitchToProfile">
          Switch to {{ switchingToMember?.name }}
        </BaseButton>
      </div>
    </BaseModal>

    <!-- Avatar Editor Modal -->
    <AvatarEditor
      :show="showAvatarEditor"
      :target-user="avatarEditTarget"
      :can-change="isParent || childrenCanChangeAvatar"
      @close="showAvatarEditor = false"
      @updated="handleAvatarUpdated"
      @color-changed="authStore.fetchUser()"
    />
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import { useCalendarStore } from '@/stores/calendar'
import api from '@/services/api'
import { useNotification } from '@/composables/useNotification'
import { useDarkMode } from '@/composables/useDarkMode'
import { useTheme, themes as availableThemes } from '@/composables/useTheme'
import BaseButton from '@/components/common/BaseButton.vue'
import BaseInput from '@/components/common/BaseInput.vue'
import BaseModal from '@/components/common/BaseModal.vue'
import UserAvatar from '@/components/common/UserAvatar.vue'
import AvatarEditor from '@/components/common/AvatarEditor.vue'
import ToggleSwitch from '@/components/common/ToggleSwitch.vue'
import SettingsSection from '@/components/settings/SettingsSection.vue'
import {
  PlusIcon,
  TrashIcon,
  SunIcon,
  MoonIcon,
  PencilIcon,
  ClipboardDocumentIcon,
  ArrowsRightLeftIcon,
  EnvelopeIcon,
  UsersIcon,
  ClipboardDocumentListIcon,
  CpuChipIcon,
  ShieldCheckIcon,
  SwatchIcon,
  BellIcon,
  InformationCircleIcon,
  ArrowTopRightOnSquareIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const appOrigin = window.location.origin
const authStore = useAuthStore()
const calendarStore = useCalendarStore()
const { success, error: notificationError } = useNotification()
const { isDark, toggle: toggleDarkMode } = useDarkMode()
const { currentTheme, setTheme: selectTheme } = useTheme()

const { family, familyMembers, currentUser, isParent, appConfig } = storeToRefs(authStore)
const { connections } = storeToRefs(calendarStore)

// ---- Version & Update Check ----
const appVersion = computed(() => appConfig.value?.version ?? '—')
const updateAvailable = computed(() => appConfig.value?.update_available ?? null)
const updateDismissed = ref(false)
const githubRepo = 'gregqualls/kinhold'

const checkDismissedUpdate = () => {
  const update = updateAvailable.value
  if (!update) return
  const key = `kinhold:dismissed_update:${update.latest_version}`
  updateDismissed.value = localStorage.getItem(key) === 'true'
}

const dismissUpdate = () => {
  const update = updateAvailable.value
  if (!update) return
  localStorage.setItem(`kinhold:dismissed_update:${update.latest_version}`, 'true')
  updateDismissed.value = true
}

// ---- Section expand/collapse state ----
const expandedSections = ref(new Set())

const toggleSection = (id, open) => {
  const next = new Set(expandedSections.value)
  if (open) next.add(id)
  else next.delete(id)
  expandedSections.value = next
}

// Avatar editor
const showAvatarEditor = ref(false)
const avatarEditTarget = ref(null)
const childrenCanChangeAvatar = computed(() => {
  const mode = moduleAccessState.avatars?.mode
  return mode === 'all' || mode === undefined
})

const openAvatarEditor = (user) => {
  avatarEditTarget.value = user
  showAvatarEditor.value = true
}

const handleAvatarUpdated = async (newAvatar) => {
  const targetId = avatarEditTarget.value?.id
  if (targetId && targetId !== currentUser.value?.id) {
    // Updated another member — refresh the member in the family list
    if (family.value?.members) {
      const member = family.value.members.find((m) => m.id === targetId)
      if (member) member.avatar = newAvatar
    }
    await authStore.fetchUser()
  } else {
    await authStore.updateUserAvatar(newAvatar)
  }
  showAvatarEditor.value = false
}

// Family form
const savingFamily = ref(false)
const familyForm = reactive({ name: family.value?.name || '' })
const familyErrors = reactive({ name: '' })

// AI config
const savingAi = ref(false)
const showAiKey = ref(false)
const aiProviders = ref([])
const aiMode = ref('kinhold') // 'kinhold' = use our key, 'byok' = bring your own
const aiConfig = reactive({
  provider: 'anthropic',
  apiKey: '',
  model: '',
  maskedKey: '',
  hasSavedKey: false,
})

// MCP Token
const mcpLoading = ref(false)
const mcpGenerating = ref(false)
const mcpRevoking = ref(false)
const mcpToken = reactive({
  hasToken: false,
  createdAt: null,
  lastUsedAt: null,
})
const mcpGenerated = reactive({
  show: false,
  plainToken: '',
  clients: [],
  mcpUrl: '',
})
const mcpActiveClient = ref('claude_desktop')
const mcpCopied = reactive({})

// Module access (granular)
const savingModules = ref(false)
const moduleAccessState = reactive({
  calendar: { mode: 'all' },
  tasks: { mode: 'all' },
  vault: { mode: 'all' },
  chat: { mode: 'all' },
  points: { mode: 'all' },
  badges: { mode: 'all' },
  avatars: { mode: 'all' },
})
// Legacy compat: moduleToggles is derived from moduleAccessState
const moduleToggles = computed(() => {
  const result = {}
  for (const mod of Object.keys(moduleAccessState)) {
    result[mod] = moduleAccessState[mod]?.mode !== 'off'
  }
  return result
})
const leaderboardPeriod = ref('weekly')
const kudosCostEnabled = ref(false)

// Default task points
const defaultPoints = reactive({
  low: 5,
  medium: 10,
  high: 20,
})

// Task assignment
const taskAssignment = reactive({
  mode: 'all',
  users: [],
})
const taskAssignmentOptions = [
  { value: 'all', label: 'Everyone', description: 'All family members can assign tasks to anyone.' },
  { value: 'parents_only', label: 'Parents Only', description: 'Only parents can assign tasks to other members. Children can only create tasks for themselves.' },
  { value: 'users', label: 'Custom', description: 'Choose which children can assign tasks to others.' },
]
const childMembers = computed(() =>
  familyMembers.value.filter((m) => (m.family_role || m.role) === 'child')
)

// Split modules: tasks/points vs everything else
const availableModules = [
  { id: 'calendar', name: 'Calendar', description: 'View and manage family events' },
  { id: 'tasks', name: 'Tasks', description: 'Create and assign tasks' },
  { id: 'vault', name: 'Family Vault', description: 'Secure information storage' },
  { id: 'chat', name: 'Kinhold AI', description: 'AI-powered assistant' },
  { id: 'points', name: 'Points & Rewards', description: 'Earn points, give kudos, purchase rewards' },
  { id: 'badges', name: 'Badges', description: 'Achievement badges and milestones' },
  { id: 'avatars', name: 'Avatar Changes', description: 'Who can change profile avatars' },
]
const tasksPointsModules = computed(() =>
  availableModules.filter((m) => m.id === 'tasks' || m.id === 'points')
)
const otherModules = computed(() =>
  availableModules.filter((m) => m.id !== 'tasks' && m.id !== 'points')
)

// Invite code
const inviteCode = ref(family.value?.invite_code || '')
const copied = ref(false)

// Invite email
const inviteEmail = ref('')
const sendingInvite = ref(false)
const inviteEmailSent = ref(false)

// Google account linking
const linkingGoogle = ref(false)
const unlinkingGoogle = ref(false)
const googleLinkError = ref('')
const googleLinked = ref(false)

// Calendar
const connectingCalendar = ref(false)
const disconnectingCalendar = ref(false)
const subscribingUrl = ref(false)
const calendarError = ref('')
const icsError = ref('')
const icsForm = reactive({ url: '', name: '' })

const userCalendarConnections = computed(() =>
  (connections.value || []).filter((c) => c.user_id === currentUser.value?.id && c.provider !== 'ics')
)
const otherMemberConnections = computed(() =>
  (connections.value || []).filter((c) => c.user_id !== currentUser.value?.id)
)
const icsConnections = computed(() =>
  (connections.value || []).filter((c) => c.user_id === currentUser.value?.id && c.provider === 'ics')
)

// Member management
const showMemberModal = ref(false)
const editingMember = ref(null)
const savingMember = ref(false)
const memberForm = reactive({ name: '', email: '', password: '', role: 'child', date_of_birth: '', sendEmail: false })
const memberErrors = reactive({ name: '', email: '', password: '' })

// Remove member
const showRemoveConfirm = ref(false)
const removingMember = ref(null)
const removingLoading = ref(false)

// Profile switching
const showSwitchToModal = ref(false)
const switchingToMember = ref(null)
const switchingTo = ref(false)

// Email preferences
const savingEmailPrefs = ref(false)
const emailPrefs = reactive({
  email_task_completed: true,
  email_task_assigned: true,
  email_weekly_digest: true,
  email_family_invite: true,
})

const emailPreferenceOptions = [
  {
    key: 'email_task_completed',
    label: 'Task Completed',
    description: 'Get notified when a family member completes a task',
  },
  {
    key: 'email_task_assigned',
    label: 'Task Assigned',
    description: 'Get notified when a task is assigned to you',
  },
  {
    key: 'email_weekly_digest',
    label: 'Weekly Digest',
    description: 'Receive a weekly summary of family activity',
  },
  {
    key: 'email_family_invite',
    label: 'Family Invites',
    description: 'Receive email when invited to join a family',
  },
]

const loadEmailPreferences = async () => {
  try {
    const { data } = await api.get('/settings/email-preferences')
    const prefs = data.email_preferences || {}
    emailPrefs.email_task_completed = prefs.email_task_completed !== false
    emailPrefs.email_task_assigned = prefs.email_task_assigned !== false
    emailPrefs.email_weekly_digest = prefs.email_weekly_digest !== false
    emailPrefs.email_family_invite = prefs.email_family_invite !== false
  } catch {
    // Use defaults on error
  }
}

const saveEmailPreferences = async () => {
  savingEmailPrefs.value = true
  try {
    await api.put('/settings/email-preferences', { ...emailPrefs })
    success('Email preferences saved!')
  } catch (err) {
    notificationError(err.response?.data?.message || 'Failed to save email preferences')
  }
  savingEmailPrefs.value = false
}

// ---- Module access helpers ----
const setModuleMode = (moduleId, mode, extra = []) => {
  if (mode === 'all' || mode === 'off') {
    moduleAccessState[moduleId] = { mode }
  } else if (mode === 'roles') {
    moduleAccessState[moduleId] = { mode: 'roles', roles: extra.length ? extra : ['parent'] }
  } else if (mode === 'users') {
    const parentIds = (familyMembers.value || [])
      .filter((m) => (m.family_role || m.role) === 'parent')
      .map((m) => m.id)
    const existing = extra.length ? extra : parentIds
    moduleAccessState[moduleId] = { mode: 'users', users: [...new Set([...parentIds, ...existing])] }
  }
}

const getSelectedUserIds = (moduleId) => {
  const state = moduleAccessState[moduleId]
  if (state?.mode === 'users') return state.users || []
  return []
}

const isMemberSelected = (moduleId, memberId) => {
  const state = moduleAccessState[moduleId]
  if (state?.mode !== 'users') return false
  return (state.users || []).includes(memberId)
}

const toggleMemberAccess = (moduleId, memberId) => {
  const state = moduleAccessState[moduleId]
  if (state?.mode !== 'users') return
  const users = state.users || []
  const idx = users.indexOf(memberId)
  if (idx >= 0) {
    users.splice(idx, 1)
  } else {
    users.push(memberId)
  }
  moduleAccessState[moduleId] = { ...state, users: [...users] }
}

const getSelectedMemberNames = (moduleId) => {
  const state = moduleAccessState[moduleId]
  if (state?.mode !== 'users') return ''
  const userIds = state.users || []
  const members = (familyMembers.value || []).filter((m) => userIds.includes(m.id))
  if (members.length === 0) return ''
  const names = members.map((m) => m.name).join(', ')
  return `Access: ${names}`
}

// ---- Family name ----
const updateFamily = async () => {
  familyErrors.name = ''
  if (!familyForm.name) {
    familyErrors.name = 'Family name is required'
    return
  }
  savingFamily.value = true
  const result = await authStore.updateFamilyName(familyForm.name)
  if (result.success) {
    success('Family name updated!')
  } else {
    notificationError(result.error)
  }
  savingFamily.value = false
}

const cancelEditFamily = () => {
  familyForm.name = family.value?.name || ''
  familyErrors.name = ''
}

// ---- Invite code ----
const loadInviteCode = async () => {
  if (family.value?.invite_code) {
    inviteCode.value = family.value.invite_code
    return
  }
  const result = await authStore.getInviteCode()
  if (result.success) {
    inviteCode.value = result.invite_code
  }
}

const copyInviteCode = async () => {
  try {
    await navigator.clipboard.writeText(inviteCode.value)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
  } catch {
    notificationError('Failed to copy')
  }
}

// ---- Invite email ----
const handleSendInviteEmail = async () => {
  if (!inviteEmail.value) return
  sendingInvite.value = true
  inviteEmailSent.value = false
  try {
    await api.post('/family/invite', { email: inviteEmail.value })
    inviteEmailSent.value = true
    inviteEmail.value = ''
    success('Invite email sent!')
    setTimeout(() => { inviteEmailSent.value = false }, 3000)
  } catch (err) {
    notificationError(err.response?.data?.message || 'Failed to send invite email')
  }
  sendingInvite.value = false
}

// ---- Member management ----
const openAddMemberModal = () => {
  editingMember.value = null
  memberForm.name = ''
  memberForm.email = ''
  memberForm.password = ''
  memberForm.role = 'child'
  memberForm.date_of_birth = ''
  memberForm.sendEmail = false
  memberErrors.name = ''
  memberErrors.email = ''
  memberErrors.password = ''
  showMemberModal.value = true
}

const openEditMemberModal = (member) => {
  editingMember.value = member
  memberForm.name = member.name
  memberForm.email = member.email || ''
  memberForm.password = ''
  memberForm.role = member.family_role || member.role || 'child'
  memberForm.date_of_birth = member.date_of_birth || ''
  memberErrors.name = ''
  memberErrors.email = ''
  memberErrors.password = ''
  showMemberModal.value = true
}

const closeMemberModal = () => {
  showMemberModal.value = false
  editingMember.value = null
}

const handleSaveMember = async () => {
  memberErrors.name = ''
  memberErrors.email = ''
  memberErrors.password = ''

  if (!memberForm.name) {
    memberErrors.name = 'Name is required'
    return
  }

  savingMember.value = true

  if (editingMember.value) {
    const data = {
      name: memberForm.name,
      role: memberForm.role,
      date_of_birth: memberForm.date_of_birth || null,
    }
    if (memberForm.email) data.email = memberForm.email

    const result = await authStore.updateFamilyMember(editingMember.value.id, data)
    if (result.success) {
      success('Member updated!')
      closeMemberModal()
    } else {
      notificationError(result.error)
    }
  } else {
    const data = {
      name: memberForm.name,
      role: memberForm.role,
      date_of_birth: memberForm.date_of_birth || null,
    }
    if (memberForm.email) data.email = memberForm.email
    if (memberForm.password) data.password = memberForm.password
    if (memberForm.sendEmail) data.send_email = true

    const result = await authStore.addFamilyMember(data)
    if (result.success) {
      success(result.message || 'Member added!')
      closeMemberModal()
    } else {
      notificationError(result.error)
    }
  }

  savingMember.value = false
}

// ---- Remove member ----
const confirmRemoveMember = (member) => {
  removingMember.value = member
  showRemoveConfirm.value = true
}

const handleRemoveMember = async () => {
  removingLoading.value = true
  const result = await authStore.removeFamilyMember(removingMember.value.id)
  if (result.success) {
    success('Member removed!')
    showRemoveConfirm.value = false
    removingMember.value = null
  } else {
    notificationError(result.error)
  }
  removingLoading.value = false
}

// ---- Profile switching ----
const openSwitchToModal = (member) => {
  switchingToMember.value = member
  showSwitchToModal.value = true
}

const closeSwitchToModal = () => {
  showSwitchToModal.value = false
  switchingToMember.value = null
}

const handleSwitchToProfile = async () => {
  switchingTo.value = true
  const result = await authStore.switchToProfile(switchingToMember.value.id)
  if (result.success) {
    success(result.message)
    closeSwitchToModal()
    router.push('/dashboard')
  } else {
    notificationError(result.error)
    closeSwitchToModal()
  }
  switchingTo.value = false
}

// ---- Calendar ----
const handleLinkGoogle = async () => {
  linkingGoogle.value = true
  googleLinkError.value = ''
  try {
    const response = await api.get('/auth/google/link')
    if (response.data.url) {
      window.location.href = response.data.url
    }
  } catch {
    googleLinkError.value = 'Failed to start Google linking. Please try again.'
  }
  linkingGoogle.value = false
}

const handleUnlinkGoogle = async () => {
  unlinkingGoogle.value = true
  googleLinkError.value = ''
  try {
    await api.delete('/auth/google/unlink')
    await authStore.fetchUser()
    success('Google account unlinked')
  } catch (err) {
    googleLinkError.value = err.response?.data?.message || 'Failed to unlink Google account.'
  }
  unlinkingGoogle.value = false
}

const handleConnectCalendar = async () => {
  connectingCalendar.value = true
  calendarError.value = ''
  const result = await calendarStore.connect('google')
  if (result.success && result.authUrl) {
    window.location.href = result.authUrl
  } else {
    calendarError.value = result.error || 'Failed to start Google Calendar connection.'
  }
  connectingCalendar.value = false
}

const handleDisconnectCalendar = async (connectionId) => {
  disconnectingCalendar.value = true
  calendarError.value = ''
  const result = await calendarStore.disconnect(connectionId)
  if (result.success) {
    success('Calendar disconnected!')
  } else {
    calendarError.value = result.error || 'Failed to disconnect calendar'
  }
  disconnectingCalendar.value = false
}

const handleSubscribeUrl = async () => {
  subscribingUrl.value = true
  icsError.value = ''
  const result = await calendarStore.subscribeUrl(icsForm.url, icsForm.name || null)
  if (result.success) {
    success(result.message || 'Calendar subscribed!')
    icsForm.url = ''
    icsForm.name = ''
  } else {
    icsError.value = result.error || 'Failed to subscribe to calendar'
  }
  subscribingUrl.value = false
}

// ---- AI Provider settings ----
const selectedProvider = computed(() =>
  aiProviders.value.find((p) => p.slug === aiConfig.provider) || aiProviders.value[0] || {}
)
const selectedProviderName = computed(() => selectedProvider.value?.name || 'AI')
const selectedProviderPlaceholder = computed(() => selectedProvider.value?.key_placeholder || 'API key...')
const selectedProviderDefaultModel = computed(() => selectedProvider.value?.default_model || '')
const selectedProviderHelpUrl = computed(() => selectedProvider.value?.help_url || '#')

const providerIcon = (slug) => {
  const icons = { anthropic: 'A', openai: 'O', google: 'G' }
  return icons[slug] || '?'
}
const providerIconClass = (slug) => {
  const classes = {
    anthropic: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
    openai: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
    google: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
  }
  return classes[slug] || 'bg-lavender-100 text-lavender-700'
}

const selectAiProvider = (slug) => {
  aiConfig.provider = slug
}

const resetAiConfig = () => {
  aiConfig.apiKey = ''
  aiConfig.model = ''
}

const saveAiSettings = async () => {
  savingAi.value = true
  try {
    const payload = {
      ai_mode: aiMode.value,
      ai_provider: aiConfig.provider,
      ai_model: aiConfig.model || '',
    }
    if (aiMode.value === 'byok' && aiConfig.apiKey) {
      payload.ai_api_key = aiConfig.apiKey
    }
    const { data } = await api.put('/settings', payload)
    if (data.settings) {
      aiConfig.maskedKey = data.settings.ai_api_key_masked || ''
      aiConfig.hasSavedKey = data.settings.ai_has_key || false
      aiProviders.value = data.settings.ai_providers || aiProviders.value
    }
    aiConfig.apiKey = ''
    showAiKey.value = false
    await authStore.fetchUser()
    success('AI settings saved!')
  } catch (err) {
    notificationError(err.response?.data?.message || 'Failed to save AI settings')
  }
  savingAi.value = false
}

// ---- Module settings ----
const saveModuleSettings = async () => {
  savingModules.value = true
  try {
    const module_access = {}
    for (const mod of availableModules) {
      const state = moduleAccessState[mod.id]
      if (!state) continue
      const rule = { mode: state.mode }
      if (state.mode === 'roles') rule.roles = state.roles || ['parent']
      if (state.mode === 'users') rule.users = state.users || []
      module_access[mod.id] = rule
    }

    // Translate avatars module access to legacy boolean for backward compat
    const avatarMode = moduleAccessState.avatars?.mode
    const children_can_change_avatar = avatarMode === 'all' || avatarMode === undefined

    await api.put('/settings', {
      module_access,
      leaderboard_period: leaderboardPeriod.value,
      kudos_cost_enabled: kudosCostEnabled.value,
      children_can_change_avatar,
    })
    await authStore.fetchUser()
    success('Preferences saved!')
  } catch (err) {
    notificationError(err.response?.data?.message || 'Failed to save preferences')
  }
  savingModules.value = false
}

// ---- Combined Tasks & Points save ----
const savingTasksPoints = ref(false)
const saveTasksPointsSection = async () => {
  savingTasksPoints.value = true
  try {
    // Build a single payload with all tasks/points settings
    const module_access = {}
    for (const mod of availableModules) {
      const state = moduleAccessState[mod.id]
      if (!state) continue
      const rule = { mode: state.mode }
      if (state.mode === 'roles') rule.roles = state.roles || ['parent']
      if (state.mode === 'users') rule.users = state.users || []
      module_access[mod.id] = rule
    }

    const avatarMode = moduleAccessState.avatars?.mode
    const children_can_change_avatar = avatarMode === 'all' || avatarMode === undefined

    const payload = {
      module_access,
      leaderboard_period: leaderboardPeriod.value,
      kudos_cost_enabled: kudosCostEnabled.value,
      children_can_change_avatar,
      default_points_low: defaultPoints.low,
      default_points_medium: defaultPoints.medium,
      default_points_high: defaultPoints.high,
      task_assignment: {
        mode: taskAssignment.mode,
        users: taskAssignment.users,
      },
    }

    await api.put('/settings', payload)
    await authStore.fetchUser()
    success('Tasks & points settings saved!')
  } catch (err) {
    notificationError(err.response?.data?.message || 'Failed to save settings')
  }
  savingTasksPoints.value = false
}



// ---- MCP Token ----
const fetchMcpTokenStatus = async () => {
  mcpLoading.value = true
  try {
    const { data } = await api.get('/mcp/token')
    mcpToken.hasToken = data.has_token
    mcpToken.createdAt = data.created_at
    mcpToken.lastUsedAt = data.last_used_at
  } catch {
    // Silent — defaults are fine
  }
  mcpLoading.value = false
}

const handleGenerateMcpToken = async () => {
  mcpGenerating.value = true
  try {
    const { data } = await api.post('/mcp/token')
    mcpToken.hasToken = true
    mcpToken.createdAt = data.created_at
    mcpToken.lastUsedAt = null
    mcpGenerated.show = true
    mcpGenerated.plainToken = data.plain_token
    mcpGenerated.mcpUrl = data.mcp_url
    mcpGenerated.clients = data.clients.map(c => ({
      ...c,
      configJson: c.config ? JSON.stringify(c.config, null, 2) : null,
    }))
    mcpActiveClient.value = data.clients[0]?.id || 'claude_desktop'
    mcpCopied.token = false
    data.clients.forEach(c => { mcpCopied[c.id] = false })
    success('MCP token generated!')
  } catch (err) {
    notificationError(err.response?.data?.message || 'Failed to generate token')
  }
  mcpGenerating.value = false
}

const handleRevokeMcpToken = async () => {
  mcpRevoking.value = true
  try {
    await api.delete('/mcp/token')
    mcpToken.hasToken = false
    mcpToken.createdAt = null
    mcpToken.lastUsedAt = null
    mcpGenerated.show = false
    mcpGenerated.plainToken = ''
    success('MCP token revoked.')
  } catch (err) {
    notificationError(err.response?.data?.message || 'Failed to revoke token')
  }
  mcpRevoking.value = false
}

const copyMcpSnippet = async (field, text) => {
  try {
    await navigator.clipboard.writeText(text)
    mcpCopied[field] = true
    setTimeout(() => { mcpCopied[field] = false }, 2000)
  } catch {
    notificationError('Failed to copy to clipboard')
  }
}

// ---- Init ----
onMounted(async () => {
  familyForm.name = family.value?.name || ''

  // Initialize module access state from the API-provided module_access map
  const settings = family.value?.settings || {}
  const moduleAccessFromApi = family.value?.module_access || {}
  const legacyModules = settings.modules || {}

  for (const mod of availableModules) {
    if (moduleAccessFromApi[mod.id]) {
      moduleAccessState[mod.id] = { ...moduleAccessFromApi[mod.id] }
    } else {
      moduleAccessState[mod.id] = { mode: legacyModules[mod.id] === false ? 'off' : 'all' }
    }
  }
  leaderboardPeriod.value = settings.leaderboard_period || 'weekly'
  kudosCostEnabled.value = settings.kudos_cost_enabled ?? false

  // Initialize default task points
  defaultPoints.low = settings.default_points_low ?? 5
  defaultPoints.medium = settings.default_points_medium ?? 10
  defaultPoints.high = settings.default_points_high ?? 20

  // Initialize task assignment
  const ta = settings.task_assignment || {}
  taskAssignment.mode = ta.mode || 'all'
  taskAssignment.users = ta.users || []

  // Initialize avatar access from module_access or legacy boolean
  if (moduleAccessFromApi.avatars) {
    moduleAccessState.avatars = { ...moduleAccessFromApi.avatars }
  } else {
    // Convert legacy boolean to module access mode
    const canChange = settings.children_can_change_avatar ?? true
    moduleAccessState.avatars = { mode: canChange ? 'all' : 'roles', roles: canChange ? [] : ['parent'] }
  }

  // Load AI settings from the settings API
  if (isParent.value) {
    try {
      const { data } = await api.get('/settings')
      const s = data.settings || {}
      aiConfig.provider = s.ai_provider || 'anthropic'
      aiConfig.model = s.ai_model || ''
      aiConfig.maskedKey = s.ai_api_key_masked || ''
      aiConfig.hasSavedKey = s.ai_has_key || false
      aiProviders.value = s.ai_providers || []
      aiMode.value = s.ai_mode || (s.ai_has_key ? 'byok' : 'kinhold')
    } catch {
      // Defaults are fine
    }
  }

  await calendarStore.fetchConnections()
  fetchMcpTokenStatus()

  // Check if update was previously dismissed
  checkDismissedUpdate()

  // Load email preferences
  if (currentUser.value?.email) {
    await loadEmailPreferences()
  }

  // Load invite code for parents
  if (isParent.value) {
    await loadInviteCode()
  }

  // Handle OAuth redirect results
  if (route.query.google_linked) {
    googleLinked.value = true
    await authStore.fetchUser()
    success('Google account linked!')
    router.replace({ path: '/settings' })
  } else if (route.query.google_error) {
    googleLinkError.value = route.query.google_error
    router.replace({ path: '/settings' })
  } else if (route.query.calendar_connected) {
    success('Google Calendar connected successfully!')
    router.replace({ path: '/settings' })
  } else if (route.query.calendar_error) {
    calendarError.value = route.query.calendar_error
    router.replace({ path: '/settings' })
  }

  // Handle hash deep-linking — expand the target section
  const hash = window.location.hash.slice(1)
  if (hash) {
    expandedSections.value.add(hash)
    await nextTick()
    document.getElementById(hash)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
})
</script>
