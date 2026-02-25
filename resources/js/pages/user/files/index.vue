<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { FileText, Image, File, Search, ArrowDownUp, LayoutGrid, List,Plus } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
type FileItem = {
    id: number
    name: string
    type: string
    size: string
    date: string
}


const files = ref<FileItem[]>([
    { id: 1, name: 'Project Proposal.docx', type: 'document', size: '120 KB', date: '2026-02-01' },
    { id: 2, name: 'Capstone Diagram.png', type: 'image', size: '2.4 MB', date: '2026-01-15' },
    { id: 3, name: 'Budget.xlsx', type: 'document', size: '300 KB', date: '2025-12-10' },
    { id: 4, name: 'Presentation.pptx', type: 'document', size: '5 MB', date: '2026-02-20' },
])

const search = ref('')
const sortBy = ref('recent')
const viewMode = ref<'grid' | 'list'>('grid') 

const filteredFiles = computed(() => {
    let filtered = files.value.filter(file =>
        file.name.toLowerCase().includes(search.value.toLowerCase())
    )

    if (sortBy.value === 'recent') {
        filtered.sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime())
    }

    if (sortBy.value === 'year') {
        filtered.sort((a, b) =>
            new Date(b.date).getFullYear() - new Date(a.date).getFullYear()
        )
    }

    if (sortBy.value === 'month') {
        filtered.sort((a, b) =>
            new Date(b.date).getMonth() - new Date(a.date).getMonth()
        )
    }

    return filtered
})

const getIcon = (type: string) => {
    if (type === 'image') return Image
    if (type === 'document') return FileText
    return File
}
</script>

<template>
    <Head title="My Files" />

    <AppLayout>
        <div class="p-6 space-y-6">

            <!-- Header -->
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-bold">My Files</h1>
                    <p class="text-sm text-gray-500">Manage and organize your files</p>
                </div>

                <!-- Search + Sort + View Toggle -->
                <div class="flex gap-3 items-center flex-wrap">

                    <!-- Search -->
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search files..."
                            class="pl-9 pr-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>

                    <!-- Sort -->
                    <div class="flex items-center gap-2">
                        <ArrowDownUp class="w-4 h-4 text-gray-500" />
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
                                viewMode === 'grid'
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-white text-gray-600'
                            ]"
                        >
                            <LayoutGrid class="w-4 h-4" />
                        </button>

                        <button
                            @click="viewMode = 'list'"
                            :class="[
                                'px-3 py-2',
                                viewMode === 'list'
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-white text-gray-600'
                            ]"
                        >
                            <List class="w-4 h-4" />
                        </button>

                         <Button
                        
                        class="flex items-center gap-2 bg-blue-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition"
                    >
                        <Plus class="w-4 h-4" />
                        Create Folder
                </Button>
                    </div>

                </div>
            </div>

            <!-- 🔷 GRID VIEW -->
            <div
                v-if="viewMode === 'grid'"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
            >
                <div
                    v-for="file in filteredFiles"
                    :key="file.id"
                    class="bg-white border rounded-2xl p-4 hover:shadow-md transition cursor-pointer"
                >
                    <div class="flex items-center gap-3 mb-4">
                        <component
                            :is="getIcon(file.type)"
                            class="w-8 h-8 text-blue-600"
                        />
                        <div>
                            <h2 class="font-medium text-sm truncate">
                                {{ file.name }}
                            </h2>
                            <p class="text-xs text-gray-400">
                                {{ file.size }}
                            </p>
                        </div>
                    </div>

                    <div class="text-xs text-gray-400">
                        Uploaded: {{ file.date }}
                    </div>
                </div>
            </div>

            <!-- 🔶 LIST VIEW -->
            <div v-else class="bg-white border rounded-2xl overflow-hidden">
                <div
                    v-for="file in filteredFiles"
                    :key="file.id"
                    class="flex items-center justify-between px-6 py-4 border-b last:border-b-0 hover:bg-gray-50 transition"
                >
                    <div class="flex items-center gap-4">
                        <component
                            :is="getIcon(file.type)"
                            class="w-6 h-6 text-blue-600"
                        />
                        <span class="font-medium text-sm">
                            {{ file.name }}
                        </span>
                    </div>

                    <div class="text-sm text-gray-500">
                        {{ file.size }}
                    </div>

                    <div class="text-sm text-gray-400">
                        {{ file.date }}
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="filteredFiles.length === 0"
                class="text-center text-gray-400 py-16"
            >
                No files found.
            </div>

        </div>
    </AppLayout>
</template>