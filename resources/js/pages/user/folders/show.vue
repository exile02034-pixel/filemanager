<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Folder, FileText, LayoutGrid, List, Plus, X } from 'lucide-vue-next'
import Button from '@/components/ui/button/Button.vue'
import { useFolderUser } from '@/composables/useFolderUser'

// Props from backend
const props = defineProps<{
  folder: { id: string; name: string }
  subfolders: { id: string; name: string; created_at: string }[]
  files: { id: string; name: string; created_at: string }[]
}>()

const { form, createFolder } = useFolderUser()

// UI states
const viewMode = ref<'grid' | 'list'>('grid')
const isModalOpen = ref(false)
const confirmation = ref('')
const newFolderName = ref('')

// Context menu
const showContextMenu = ref(false)
const contextX = ref(0)
const contextY = ref(0)

// Duplicate folder warning
const duplicateFolder = ref<string | null>(null)

// Combine items for display
const items = computed(() => [
  ...props.subfolders.map(f => ({ ...f, type: 'folder' })),
  ...props.files.map(f => ({ ...f, type: 'file' })),
])

// Modal controls
const openModal = () => {
  isModalOpen.value = true
  showContextMenu.value = false
  duplicateFolder.value = null
}

const closeModal = () => {
  isModalOpen.value = false
  newFolderName.value = ''
  form.name = ''
}

// Context menu
const handleRightClick = (event: MouseEvent) => {
  event.preventDefault()
  contextX.value = event.clientX
  contextY.value = event.clientY
  showContextMenu.value = true
}

const closeContextMenu = () => (showContextMenu.value = false)

// Create folder handler
const createSubFolder = (action: 'overwrite' | 'version' | null = null) => {
  form.name = newFolderName.value
  form.parent_id = props.folder.id
  form.action = action  // always set, even if null

  createFolder(
    () => {
      closeModal()
      confirmation.value = `Folder "${newFolderName.value}" created successfully`
      duplicateFolder.value = null
      setTimeout(() => (confirmation.value = ''), 3000)
    },
    (errors: any) => {
      if (errors?.duplicate) {
        duplicateFolder.value = errors.duplicate  // this is the folder name string from withErrors()
        isModalOpen.value = false  // close modal, show the warning banner instead
      }
    }
  )
}
</script>

<template>
  <Head :title="props.folder.name" />
  <AppLayout>
    <div class="p-6 space-y-6" @click="closeContextMenu">

      <!-- Duplicate Folder Warning -->
      <div v-if="duplicateFolder" class="bg-yellow-100 p-4 rounded-md mb-4 space-y-3">
        <p>Folder "{{ duplicateFolder }}" already exists. What would you like to do?</p>
        <div class="flex gap-3">
          <Button class="bg-blue-600 text-white" @click="createSubFolder('overwrite')">Overwrite</Button>
          <Button class="bg-gray-200" @click="createSubFolder('version')">Create New Version</Button>
        </div>
      </div>

      <!-- Success Confirmation -->
      <div v-if="confirmation" class="bg-green-100 text-green-800 p-3 rounded-md mb-4">
        {{ confirmation }}
      </div>

      <!-- Header -->
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
          <Folder class="w-7 h-7 text-blue-600" />
          <h1 class="text-2xl font-bold">{{ props.folder.name }}</h1>
        </div>

        <!-- View Toggle + New Folder -->
        <div class="flex gap-3 items-center">
          <div class="flex border rounded-lg overflow-hidden">
            <button
              @click="viewMode = 'grid'"
              :class="['px-3 py-2', viewMode === 'grid' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600']"
            >
              <LayoutGrid class="w-4 h-4" />
            </button>

            <button
              @click="viewMode = 'list'"
              :class="['px-3 py-2', viewMode === 'list' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600']"
            >
              <List class="w-4 h-4" />
            </button>
          </div>

          <Button @click="openModal" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
            <Plus class="w-4 h-4" />
            New Folder
          </Button>
        </div>
      </div>

      <!-- File/Folder Grid -->
      <div class="min-h-[400px] bg-white border rounded-2xl p-6 relative" @contextmenu="handleRightClick">
        <!-- GRID VIEW -->
        <div v-if="viewMode === 'grid'" class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <div v-for="item in items" :key="item.id" class="p-4 border rounded-xl hover:shadow cursor-pointer text-center">
            <div class="flex justify-center mb-2">
              <Folder v-if="item.type === 'folder'" class="w-8 h-8 text-blue-600" />
              <FileText v-else class="w-8 h-8 text-gray-600" />
            </div>
            <p class="text-sm truncate">{{ item.name }}</p>
            <p class="text-xs text-gray-400">{{ new Date(item.created_at).toLocaleDateString() }}</p>
          </div>
        </div>

        <!-- LIST VIEW -->
        <div v-else>
          <div v-for="item in items" :key="item.id" class="flex items-center justify-between px-4 py-3 border-b last:border-b-0 hover:bg-gray-50">
            <div class="flex items-center gap-3">
              <Folder v-if="item.type === 'folder'" class="w-5 h-5 text-blue-600" />
              <FileText v-else class="w-5 h-5 text-gray-600" />
              <span class="text-sm truncate">{{ item.name }}</span>
            </div>
            <span class="text-xs text-gray-400">{{ new Date(item.created_at).toLocaleDateString() }}</span>
          </div>
        </div>
      </div>

      <!-- Context Menu -->
      <div v-if="showContextMenu" :style="{ top: contextY + 'px', left: contextX + 'px' }" class="fixed bg-white border rounded-lg shadow-md z-50 w-40">
        <button @click="openModal" class="flex items-center gap-2 w-full px-4 py-2 text-sm hover:bg-gray-100">
          <Plus class="w-4 h-4" /> New Folder
        </button>
      </div>

      <!-- Create Subfolder Modal -->
      <div v-if="isModalOpen" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeModal"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-5 z-50">
          <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold">Create Subfolder</h2>
            <button @click="closeModal">
              <X class="w-5 h-5 text-gray-500 hover:text-gray-700" />
            </button>
          </div>
          <input
            v-model="newFolderName"
            type="text"
            placeholder="Folder name"
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
          />
          <div class="flex justify-end gap-3">
            <Button variant="outline" @click="closeModal">Cancel</Button>
            <Button class="bg-blue-600 text-white" @click="createSubFolder">Create</Button>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>