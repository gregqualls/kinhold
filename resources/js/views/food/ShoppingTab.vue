<template>
  <div class="h-full flex flex-col">
    <!-- Loading -->
    <div v-if="isLoading && !activeList" class="flex items-center justify-center p-12">
      <div class="animate-spin rounded-full h-8 w-8 border-2 border-[#C4975A] border-t-transparent"></div>
    </div>

    <!-- First time: no lists -->
    <CreateListInline v-else-if="lists.length === 0" @create="handleCreateList" />

    <!-- Main shopping view -->
    <template v-else>
      <!-- List Header -->
      <ListHeader
        :lists="lists"
        :active-list="activeList"
        :pre-shop-mode="preShopMode"
        :shopping-window="shoppingStore.shoppingWindow"
        :checked-count="shoppingStore.checkedCount"
        @select-list="handleSelectList"
        @new-list="showNewListModal = true"
        @toggle-preshop="preShopMode = !preShopMode"
        @set-window="shoppingStore.setShoppingWindow"
        @clear-checked="handleClearChecked"
        @rename-list="handleRenameList"
        @delete-list="handleDeleteList"
      />

      <!-- Pre-Shop mode -->
      <PreShopChecklist
        v-if="preShopMode"
        :items="shoppingStore.activeItems"
        :shopping-window="shoppingStore.shoppingWindow"
        @mark-on-hand="handleMarkOnHand"
        @clear-on-hand="handleClearOnHand"
        @done="preShopMode = false"
      />

      <!-- Shopping mode -->
      <div v-else class="flex-1 overflow-y-auto px-4 md:px-6 py-4 space-y-4">
        <!-- Add item (parent only) -->
        <AddItemInput
          v-if="isParent && activeList"
          :list-id="activeList.id"
          :catalog-results="shoppingStore.catalogResults"
          @item-added="handleItemAdded"
          @search="handleSearch"
        />

        <!-- Add from Recipe (parent only) -->
        <button
          v-if="isParent && activeList"
          class="w-full text-left px-3 py-2 text-sm text-[#C4975A] hover:bg-surface-sunken rounded-lg transition-colors"
          @click="showRecipePicker = true"
        >
          + Add ingredients from a recipe
        </button>

        <!-- Empty state -->
        <KinEmptyState
          v-if="Object.keys(shoppingStore.filteredItemsByCategory).length === 0 && shoppingStore.checkedCount === 0"
          :icon="ShoppingCartIcon"
          title="No items yet"
          description="Start adding things you need!"
          accent-color="lavender"
          size="sm"
        />

        <!-- Items grouped by category -->
        <details
          v-for="(items, category) in shoppingStore.filteredItemsByCategory"
          :key="category"
          open
          class="group"
        >
          <summary class="flex items-center justify-between cursor-pointer py-1.5 text-xs font-semibold uppercase tracking-wider text-ink-tertiary">
            <span>{{ category }}</span>
            <span class="text-ink-tertiary">{{ items.length }}</span>
          </summary>
          <div class="space-y-1 mt-1">
            <ShoppingListItem
              v-for="item in items"
              :key="item.id"
              :item="item"
              :other-lists="otherLists"
              :can-manage="isParent"
              mode="shop"
              @check="handleCheck"
              @uncheck="handleUncheck"
              @remove="handleRemove"
              @update="handleUpdate"
              @toggle-recurring="handleToggleRecurring"
              @move-item="handleMoveItem"
            />
          </div>
        </details>

        <!-- Checked / In Cart -->
        <details v-if="shoppingStore.checkedCount > 0" class="mt-6">
          <summary class="flex items-center justify-between cursor-pointer py-1.5 text-xs font-semibold uppercase tracking-wider text-ink-tertiary">
            <span>In Cart</span>
            <span>{{ shoppingStore.checkedCount }}</span>
          </summary>
          <div class="space-y-1 mt-1 opacity-60">
            <ShoppingListItem
              v-for="item in shoppingStore.checkedItems"
              :key="item.id"
              :item="item"
              :other-lists="otherLists"
              :can-manage="isParent"
              mode="shop"
              @check="handleCheck"
              @uncheck="handleUncheck"
              @remove="handleRemove"
              @update="handleUpdate"
              @toggle-recurring="handleToggleRecurring"
              @move-item="handleMoveItem"
            />
          </div>
        </details>
      </div>
    </template>

    <!-- New List Modal -->
    <KinModalSheet
      :model-value="showNewListModal"
      title="Add a Store"
      @update:model-value="(v) => !v && (showNewListModal = false)"
    >
      <KinInput
        v-model="newStoreName"
        type="text"
        placeholder="e.g. Tesco, Costco..."
        class="mb-4"
        @keydown.enter="handleCreateNewList"
      />
      <div class="flex justify-end gap-2">
        <KinButton variant="secondary" size="sm" @click="showNewListModal = false">Cancel</KinButton>
        <KinButton variant="primary" size="sm" :disabled="!newStoreName.trim()" @click="handleCreateNewList">Create</KinButton>
      </div>
    </KinModalSheet>

    <!-- Recipe Picker Modal (two-step: pick recipe → select ingredients) -->
    <KinModalSheet
      :model-value="showRecipePicker"
      :title="selectedRecipeForIngredients ? selectedRecipeForIngredients.title : 'Add from Recipe'"
      @update:model-value="(v) => !v && closeRecipePicker()"
    >
      <!-- Step 1: Pick a recipe -->
      <template v-if="!selectedRecipeForIngredients">
        <div class="flex-1 overflow-y-auto space-y-1 max-h-[60vh]">
          <button
            v-for="recipe in recipeList"
            :key="recipe.id"
            class="w-full text-left px-4 py-3 rounded-lg hover:bg-surface-sunken transition-colors"
            @click="selectRecipeForIngredients(recipe)"
          >
            <span class="text-sm font-medium text-ink-primary">{{ recipe.title }}</span>
          </button>
          <p v-if="recipeList.length === 0" class="text-sm text-ink-tertiary py-4 text-center">No recipes yet</p>
        </div>
        <KinButton variant="secondary" size="sm" class="mt-4 w-full" @click="closeRecipePicker">Cancel</KinButton>
      </template>

      <!-- Step 2: Select ingredients -->
      <template v-else>
        <div class="flex items-center gap-2 mb-4">
          <button
            class="p-1 rounded-md text-ink-tertiary hover:text-ink-primary hover:bg-surface-sunken transition-colors"
            title="Back to recipes"
            @click="selectedRecipeForIngredients = null"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
          </button>
        </div>

        <!-- Loading -->
        <div v-if="loadingIngredients" class="flex items-center justify-center py-8">
          <div class="animate-spin rounded-full h-6 w-6 border-2 border-[#C4975A] border-t-transparent"></div>
        </div>

        <!-- Ingredient list with checkboxes -->
        <template v-else>
          <div class="flex-1 overflow-y-auto mb-4 max-h-[50vh]">
            <RecipeIngredientPicker
              v-model="selectedIngredientIds"
              :ingredients="recipeIngredients"
              default-select-all
            />
          </div>

          <div class="flex gap-2">
            <KinButton variant="secondary" size="sm" class="flex-1" @click="closeRecipePicker">Cancel</KinButton>
            <KinButton
              variant="primary"
              size="sm"
              class="flex-1"
              :disabled="selectedIngredientIds.length === 0"
              @click="handleAddSelectedIngredients"
            >
              Add {{ selectedIngredientIds.length }} item{{ selectedIngredientIds.length === 1 ? '' : 's' }}
            </KinButton>
          </div>
        </template>
      </template>
    </KinModalSheet>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { ShoppingCartIcon } from '@heroicons/vue/24/outline'
import { useShoppingStore } from '@/stores/shopping'
import { useAuthStore } from '@/stores/auth'
import { useRecipesStore } from '@/stores/recipes'
import { useNotification } from '@/composables/useNotification'
import ListHeader from '@/components/shopping/ListHeader.vue'
import CreateListInline from '@/components/shopping/CreateListInline.vue'
import AddItemInput from '@/components/shopping/AddItemInput.vue'
import ShoppingListItem from '@/components/shopping/ShoppingListItem.vue'
import PreShopChecklist from '@/components/shopping/PreShopChecklist.vue'
import RecipeIngredientPicker from '@/components/food/RecipeIngredientPicker.vue'
import KinButton from '@/components/design-system/KinButton.vue'
import KinInput from '@/components/design-system/KinInput.vue'
import KinModalSheet from '@/components/design-system/KinModalSheet.vue'
import KinEmptyState from '@/components/design-system/KinEmptyState.vue'

const shoppingStore = useShoppingStore()
const authStore = useAuthStore()
const recipesStore = useRecipesStore()
const { success, error: notifyError } = useNotification()

const preShopMode = ref(false)
const showNewListModal = ref(false)
const newStoreName = ref('')
const showRecipePicker = ref(false)
const selectedRecipeForIngredients = ref(null)
const recipeIngredients = ref([])
const selectedIngredientIds = ref([])
const loadingIngredients = ref(false)

const allIngredientsSelected = computed(
  () => recipeIngredients.value.length > 0 && selectedIngredientIds.value.length === recipeIngredients.value.length,
)

const isParent = computed(() => authStore.user?.family_role === 'parent')
const lists = computed(() => shoppingStore.lists)
const activeList = computed(() => shoppingStore.activeList)
const isLoading = computed(() => shoppingStore.isLoading)
const recipeList = computed(() => recipesStore.recipes || [])
const otherLists = computed(() => lists.value.filter((l) => l.id !== activeList.value?.id))

onMounted(async () => {
  await shoppingStore.fetchLists()
  // Load recipes for the picker
  if (recipesStore.recipes.length === 0) {
    recipesStore.fetchRecipes()
  }
})

async function handleCreateList(storeName) {
  const result = await shoppingStore.createList(storeName)
  if (result.success) {
    success(`${storeName} list created`)
  } else {
    notifyError(result.error)
  }
}

async function handleCreateNewList() {
  if (!newStoreName.value.trim()) return
  await handleCreateList(newStoreName.value.trim())
  newStoreName.value = ''
  showNewListModal.value = false
}

async function handleSelectList(listId) {
  await shoppingStore.fetchList(listId)
}

async function handleRenameList({ listId, name }) {
  const result = await shoppingStore.renameList(listId, name)
  if (result.success) {
    success('List renamed')
  } else {
    notifyError(result.error)
  }
}

async function handleDeleteList(listId) {
  const result = await shoppingStore.deleteList(listId)
  if (result.success) {
    success('List deleted')
  } else {
    notifyError(result.error)
  }
}

async function handleClearChecked() {
  if (!activeList.value) return
  const result = await shoppingStore.clearChecked(activeList.value.id)
  if (result.success) {
    const msg = result.data?.recurring_reset
      ? `Cleared ${result.data.cleared} items. ${result.data.recurring_reset} recurring items reset.`
      : `Cleared ${result.data?.cleared || 0} items`
    success(msg)
  } else {
    notifyError(result.error)
  }
}

async function handleItemAdded(data) {
  if (!activeList.value) return
  const result = await shoppingStore.addItem(activeList.value.id, data)
  if (!result.success) notifyError(result.error)
}

function handleSearch(query) {
  shoppingStore.searchCatalog(query)
}

async function handleCheck(itemId) {
  await shoppingStore.checkItem(itemId)
}

async function handleUncheck(itemId) {
  await shoppingStore.uncheckItem(itemId)
}

async function handleRemove(itemId) {
  await shoppingStore.removeItem(itemId)
}

async function handleUpdate(itemId, data) {
  await shoppingStore.updateItem(itemId, data)
}

async function handleToggleRecurring(itemId) {
  const result = await shoppingStore.toggleRecurring(itemId)
  if (result.success) {
    const item = shoppingStore.activeItems.find((i) => i.id === itemId)
    success(item?.is_recurring ? 'Item will reappear after clearing' : 'Item will be removed when cleared')
  }
}

async function handleMoveItem({ itemId, targetListId }) {
  const targetList = lists.value.find((l) => l.id === targetListId)
  const result = await shoppingStore.moveItem(itemId, targetListId)
  if (result.success) {
    success(`Moved to ${targetList?.name || 'other list'}`)
  }
}

async function handleMarkOnHand(itemId) {
  await shoppingStore.markOnHand(itemId)
}

async function handleClearOnHand(itemId) {
  await shoppingStore.clearOnHand(itemId)
}

async function selectRecipeForIngredients(recipe) {
  selectedRecipeForIngredients.value = recipe
  loadingIngredients.value = true

  const result = await recipesStore.fetchRecipe(recipe.id)
  if (result.success && result.recipe?.ingredients) {
    // Annotate each ingredient with already_on_list (case-insensitive name match
    // against the active list's items) so the picker can de-emphasize duplicates.
    const existing = new Set(
      (activeList.value?.items || []).map(item => (item.name || '').trim().toLowerCase())
    )
    recipeIngredients.value = result.recipe.ingredients.map(i => ({
      ...i,
      already_on_list: existing.has((i.name || '').trim().toLowerCase()),
    }))
    // Default-select only ingredients that aren't already on the list.
    selectedIngredientIds.value = recipeIngredients.value
      .filter(i => !i.already_on_list)
      .map(i => i.id)
  } else {
    recipeIngredients.value = []
    selectedIngredientIds.value = []
  }

  loadingIngredients.value = false
}

function closeRecipePicker() {
  showRecipePicker.value = false
  selectedRecipeForIngredients.value = null
  recipeIngredients.value = []
  selectedIngredientIds.value = []
}

async function handleAddSelectedIngredients() {
  if (!activeList.value || selectedIngredientIds.value.length === 0) return

  const ingredientIds = allIngredientsSelected.value ? null : selectedIngredientIds.value
  const result = await shoppingStore.addRecipeToList(activeList.value.id, selectedRecipeForIngredients.value.id, ingredientIds)

  if (result.success) {
    const count = ingredientIds ? ingredientIds.length : recipeIngredients.value.length
    success(`Added ${count} ingredient${count === 1 ? '' : 's'} from ${selectedRecipeForIngredients.value.title}`)
    closeRecipePicker()
  } else {
    notifyError(result.error)
  }
}
</script>
