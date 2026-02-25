<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Folder, LayoutGrid, List, Search, ArrowDownUp, Plus, X } from 'lucide-vue-next'
import Button from '@/components/ui/button/Button.vue'
import { useFolderUser } from '@/composables/useFolderUser'
import { debounce } from 'lodash'
import { Folders } from '@/types/Folder'
import folders, { show } from '@/routes/user/folders'

const { form, createFolder, getFolders } = useFolderUser()
// Props
const props = defineProps<{ folders: Folders[] }>()

// UI state
const search = ref('')
const sortBy = ref('recent')
const viewMode = ref<'grid' | 'list'>('grid')
const confirmation = ref('')
const isModalOpen = ref(false)

// Modal functions
const openModal = () => { isModalOpen.value = true }
const closeModal = () => { 
  isModalOpen.value = false
  form.name = ''
}

const searchAndSort = debounce(() => {
  router.get(
    getFolders({ search: search.value, sort: sortBy.value }),
    {},
    { preserveState: true, replace: true }
  )
}, 300)

watch([search, sortBy], searchAndSort)


const handleCreateFolder = () => {
    form.parent_id = null
  createFolder(() => {
    confirmation.value = `Folder "${form.name}" created successfully`
    closeModal()

    setTimeout(() => {
      confirmation.value = ''
    }, 3000)
  })
}


const filteredFolders = computed(() => {
  return props.folders.filter(folder =>
    folder.name.toLowerCase().includes(search.value.toLowerCase())
  )
})
</script>

<template>
    <Head title="My Folders" />
    <AppLayout>
        <div class="p-6 space-y-6">

            <!-- Header -->
             <div v-if="confirmation" class="bg-green-100 text-green-800 p-3 rounded-md mb-4">
                Folder successfully created
            </div>
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-bold">My Folders</h1>
                    <p class="text-sm text-gray-500">Manage and organize your folders</p>
                </div>

                <!-- Controls: Search + Sort + View + Create -->
                <div class="flex gap-3 items-center flex-wrap">

                    <!-- Search -->
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search folders..."
                            class="pl-9 pr-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>

                    <!-- Sort -->
                    <div class="flex items-center gap-2">
                        
                        
                        <select
                            v-model="sortBy"
                            class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="recent">Recent</option>
                            <option value="year">Year</option>
                            <option value="month">Month</option>
                        </select>
                    </div>

                    <!-- View Toggle -->
                    <div class="flex border rounded-lg overflow-hidden">
                        <button
                            @click="viewMode = 'grid'"
                            :class="[
                                'px-3 py-2',
                                viewMode === 'grid' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600'
                            ]"
                        >
                            <LayoutGrid class="w-4 h-4" />
                        </button>

                        <button
                            @click="viewMode = 'list'"
                            :class="[
                                'px-3 py-2',
                                viewMode === 'list' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600'
                            ]"
                        >
                            <List class="w-4 h-4" />
                        </button>
                    </div>

                    <!-- Create Folder -->
                    <Button
                        @click="openModal"
                        class="flex items-center gap-2 bg-blue-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition"
                    >
                        <Plus class="w-4 h-4" />
                        Create Folder
                </Button>

                </div>
            </div>

            <!-- GRID VIEW -->
        <div v-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="folder in filteredFolders"
                @click="router.visit(show.url(folder.id))"
                :key="folder.id"
                class="bg-white rounded-2xl shadow-sm border hover:shadow-md transition p-5 cursor-pointer"
            >
                <div class="flex items-center gap-3 mb-3">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <Folder class="w-6 h-6 text-blue-600" />
                </div>
                <h2 class="font-semibold text-lg truncate">{{ folder.name }}</h2>
                </div>
                <p class="text-xs text-gray-400 mt-2">
                Created: {{ new Date(folder.created_at).toLocaleDateString() }}
                </p>
            </div>
            </div>

            <!-- LIST VIEW -->
            <div v-else class="bg-white border rounded-2xl overflow-hidden">
                <div
                    v-for="folder in props.folders"
                    :key="folder.id"
                    class="flex items-center justify-between px-6 py-4 border-b last:border-b-0 hover:bg-gray-50 transition cursor-pointer"
                >
                    <div class="flex items-center gap-4">
                        <Folder class="w-6 h-6 text-blue-600" />
                        <span class="font-medium text-sm truncate">{{ folder.name }}</span>
                    </div>


                     Created: {{ new Date(folder.created_at).toLocaleDateString() }}
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="filteredFolders.length === 0" class="text-center text-gray-400 py-16">
                No folders found.
            </div>

        </div>
        <!-- CREATE FOLDER MODAL -->
<div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center">

    <!-- Overlay -->
    <div 
        class="absolute inset-0 bg-black/40 backdrop-blur-sm"
        @click="closeModal"
    ></div>

    <!-- Modal -->
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-5 z-10">

        <!-- Header -->
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold">Create New Folder</h2>
            <button @click="closeModal">
                <X class="w-5 h-5 text-gray-500 hover:text-gray-700" />
            </button>
        </div>

        <!-- Input -->
        <div>
            <label class="text-sm text-gray-600">Folder Name</label>
            <input
                v-model="form.name"
                type="text"
                placeholder="Enter folder name"
                class="w-full mt-2 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 pt-2">
            <Button
                variant="outline"
                @click="closeModal"
            >
                Cancel
            </Button>

            <Button
                class="bg-blue-600 hover:bg-blue-700 text-white"
                @click="handleCreateFolder"
                :disabled="form.processing"
            >
                Create
            </Button>
        </div>

    </div>
</div>
    </AppLayout>
</template>