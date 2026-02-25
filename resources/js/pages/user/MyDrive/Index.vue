<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { ref, onBeforeUnmount, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import { type BreadcrumbItem } from '@/types'

type DriveItem = {
  id: string
  name: string
  type: 'Folder' | 'PDF' | 'DOCX' | 'XLSX' | 'PNG' | 'JPG'
  dateModified: string
  size: string
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'My Drive', href: dashboard().url }]

const items = ref<DriveItem[]>([
  { id: '1', name: 'Requirements', type: 'Folder', dateModified: 'Feb 25, 2026 • 9:10 AM', size: '—' },
  { id: '2', name: 'Internship-Form.pdf', type: 'PDF', dateModified: 'Feb 25, 2026 • 8:40 AM', size: '410 KB' },
  { id: '3', name: 'Resume.docx', type: 'DOCX', dateModified: 'Feb 24, 2026 • 11:13 PM', size: '92 KB' },
  { id: '4', name: 'Grades.xlsx', type: 'XLSX', dateModified: 'Feb 20, 2026 • 3:18 PM', size: '38 KB' },
])

// =====================
// MODALS
// =====================
const showCreateModal = ref(false)
const showUploadModal = ref(false)

const openCreate = () => {
  showCreateModal.value = true
  folderName.value = ''
  createError.value = ''
}
const openUpload = () => {
  showUploadModal.value = true
  selectedFile.value = null
  uploadError.value = ''
}

const closeModals = () => {
  showCreateModal.value = false
  showUploadModal.value = false
  createError.value = ''
  uploadError.value = ''
}

// Close on ESC
const onKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Escape') closeModals()
}
onMounted(() => window.addEventListener('keydown', onKeyDown))
onBeforeUnmount(() => window.removeEventListener('keydown', onKeyDown))

// =====================
// CREATE FOLDER FORM
// =====================
const folderName = ref('')
const createError = ref('')

const createFolder = () => {
  const name = folderName.value.trim()
  if (!name) {
    createError.value = 'Folder name is required.'
    return
  }

  // Front-end demo only: add to list
  items.value.unshift({
    id: String(Date.now()),
    name,
    type: 'Folder',
    dateModified: 'Feb 25, 2026 • 11:00 AM',
    size: '—',
  })

  closeModals()
}

// =====================
// UPLOAD FORM
// =====================
const selectedFile = ref<File | null>(null)
const uploadError = ref('')

const onPickFile = (e: Event) => {
  const input = e.target as HTMLInputElement
  selectedFile.value = input.files?.[0] ?? null
  uploadError.value = ''
}

const onDrop = (e: DragEvent) => {
  const file = e.dataTransfer?.files?.[0]
  if (!file) return
  selectedFile.value = file
  uploadError.value = ''
}

const uploadFile = () => {
  if (!selectedFile.value) {
    uploadError.value = 'Please choose a file first.'
    return
  }

  // Front-end demo only: add to list
  items.value.unshift({
    id: String(Date.now()),
    name: selectedFile.value.name,
    type: guessType(selectedFile.value.name),
    dateModified: 'Feb 25, 2026 • 11:00 AM',
    size: formatBytes(selectedFile.value.size),
  })

  closeModals()
}

function guessType(filename: string): DriveItem['type'] {
  const ext = filename.split('.').pop()?.toLowerCase()
  if (!ext) return 'DOCX'
  if (ext === 'pdf') return 'PDF'
  if (ext === 'doc' || ext === 'docx') return 'DOCX'
  if (ext === 'xls' || ext === 'xlsx') return 'XLSX'
  if (ext === 'png') return 'PNG'
  if (ext === 'jpg' || ext === 'jpeg') return 'JPG'
  return 'DOCX'
}

function formatBytes(bytes: number): string {
  if (!bytes) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  const val = bytes / Math.pow(k, i)
  return `${val.toFixed(i === 0 ? 0 : 1)} ${sizes[i]}`
}
</script>

<template>
  <Head title="My Drive" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">
      <!-- HEADER -->
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-semibold">My Drive</h1>

        <div class="flex gap-2">
          <button
            class="rounded-xl border bg-white px-4 py-2 text-sm font-medium hover:bg-gray-50"
            @click="openCreate"
          >
            Create
          </button>
          <button
            class="rounded-xl bg-black px-4 py-2 text-sm font-medium text-white hover:opacity-90"
            @click="openUpload"
          >
            Upload
          </button>
        </div>
      </div>

      <!-- SEARCH -->
      <div class="rounded-2xl border bg-white p-3 shadow-sm">
        <input
          class="h-10 w-full rounded-xl border bg-gray-50 px-3 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-black/10"
          placeholder="Search in Drive (static)"
          disabled
        />
      </div>

      <!-- TABLE -->
      <section class="rounded-2xl border bg-white shadow-sm">
        <!-- SORT -->
        <div class="flex items-center justify-between border-b p-4">
          <h2 class="text-base font-semibold">Files & Folders</h2>

          <div class="flex gap-2">
            <select class="rounded-xl border bg-white px-3 py-2 text-sm">
              <option>Type</option>
              <option>Folder</option>
              <option>PDF</option>
              <option>DOCX</option>
              <option>XLSX</option>
            </select>

            <select class="rounded-xl border bg-white px-3 py-2 text-sm">
              <option>Modified</option>
              <option>Recently Modified</option>
            </select>

            <select class="rounded-xl border bg-white px-3 py-2 text-sm">
              <option>Date</option>
              <option>Newest First</option>
              <option>Oldest First</option>
            </select>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-600">
              <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Date modified</th>
                <th class="px-4 py-3 text-right">File size</th>
                <th class="px-4 py-3 text-right">Actions</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="item in items" :key="item.id" class="border-t hover:bg-gray-50">
                <!-- NAME WITH ICON -->
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <!-- Folder icon -->
                    <svg
                      v-if="item.type === 'Folder'"
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-amber-500"
                      viewBox="0 0 24 24"
                      fill="currentColor"
                    >
                      <path
                        d="M10 4a2 2 0 0 1 1.4.6l1.6 1.6H20a2 2 0 0 1 2 2v9.8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h6Z"
                      />
                    </svg>

                    <!-- File icon -->
                    <svg
                      v-else
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-gray-500"
                      viewBox="0 0 24 24"
                      fill="currentColor"
                    >
                      <path d="M6 2h7l5 5v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Zm7 1.5V8h4.5" />
                    </svg>

                    <span class="font-medium text-gray-900">{{ item.name }}</span>
                  </div>
                </td>

                <!-- DATE -->
                <td class="px-4 py-3 text-gray-600">{{ item.dateModified }}</td>

                <!-- SIZE -->
                <td class="px-4 py-3 text-right text-gray-600">{{ item.size }}</td>

                <!-- ACTIONS -->
                <td class="px-4 py-3 text-right">
                  <button class="rounded-xl border bg-white px-3 py-2 hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                      <path
                        d="M12 7a2 2 0 1 0 .001-3.999A2 2 0 0 0 12 7Zm0 7a2 2 0 1 0 .001-3.999A2 2 0 0 0 12 14Zm0 7a2 2 0 1 0 .001-3.999A2 2 0 0 0 12 21Z"
                      />
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <!-- =========================
         MODAL BACKDROP (shared)
    ========================== -->
    <div
      v-if="showCreateModal || showUploadModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      aria-modal="true"
      role="dialog"
    >
      <!-- backdrop -->
      <div class="absolute inset-0 bg-black/40" @click="closeModals"></div>

      <!-- =========================
           CREATE MODAL
      ========================== -->
      <div
        v-if="showCreateModal"
        class="relative w-full max-w-md rounded-2xl border bg-white p-5 shadow-xl"
      >
        <div class="flex items-start justify-between">
          <div>
            <h3 class="text-lg font-semibold">Create</h3>
            <p class="mt-1 text-sm text-gray-600">Create a new folder in your drive.</p>
          </div>

          <button class="rounded-xl p-2 hover:bg-gray-100" @click="closeModals" aria-label="Close">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.29 19.71 2.88 18.3 9.17 12 2.88 5.71 4.29 4.29l6.3 6.3 6.29-6.3 1.42 1.42Z"
              />
            </svg>
          </button>
        </div>

        <div class="mt-4 space-y-2">
          <label class="text-sm font-medium text-gray-700">Folder name</label>
          <input
            v-model="folderName"
            class="h-11 w-full rounded-xl border bg-gray-50 px-3 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-black/10"
            placeholder="e.g. OJT Requirements"
            @keydown.enter.prevent="createFolder"
            autofocus
          />
          <p v-if="createError" class="text-sm text-red-600">{{ createError }}</p>
        </div>

        <div class="mt-5 flex justify-end gap-2">
          <button class="rounded-xl border bg-white px-4 py-2 text-sm font-medium hover:bg-gray-50" @click="closeModals">
            Cancel
          </button>
          <button class="rounded-xl bg-black px-4 py-2 text-sm font-medium text-white hover:opacity-90" @click="createFolder">
            Create folder
          </button>
        </div>
      </div>

      <!-- =========================
           UPLOAD MODAL
      ========================== -->
      <div
        v-if="showUploadModal"
        class="relative w-full max-w-md rounded-2xl border bg-white p-5 shadow-xl"
        @dragover.prevent
        @drop.prevent="onDrop"
      >
        <div class="flex items-start justify-between">
          <div>
            <h3 class="text-lg font-semibold">Upload</h3>
            <p class="mt-1 text-sm text-gray-600">Upload a file to your drive.</p>
          </div>

          <button class="rounded-xl p-2 hover:bg-gray-100" @click="closeModals" aria-label="Close">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.29 19.71 2.88 18.3 9.17 12 2.88 5.71 4.29 4.29l6.3 6.3 6.29-6.3 1.42 1.42Z"
              />
            </svg>
          </button>
        </div>

        <div class="mt-4">
          <!-- Dropzone -->
          <div
            class="rounded-2xl border border-dashed bg-gray-50 p-5 text-center"
          >
            <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-white">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                <path
                  d="M12 3a1 1 0 0 1 1 1v9.59l2.3-2.3 1.4 1.42L12 17.41l-4.7-4.7 1.4-1.42 2.3 2.3V4a1 1 0 0 1 1-1Zm-7 17h14v2H5v-2Z"
                />
              </svg>
            </div>

            <p class="text-sm font-medium text-gray-900">Drag & drop a file here</p>
            <p class="mt-1 text-xs text-gray-600">or click to choose a file</p>

            <label class="mt-3 inline-flex cursor-pointer items-center justify-center rounded-xl border bg-white px-4 py-2 text-sm font-medium hover:bg-gray-50">
              Choose file
              <input type="file" class="hidden" @change="onPickFile" />
            </label>

            <div v-if="selectedFile" class="mt-3 rounded-xl border bg-white p-3 text-left">
              <p class="text-sm font-medium text-gray-900 truncate">{{ selectedFile.name }}</p>
              <p class="text-xs text-gray-600">{{ formatBytes(selectedFile.size) }}</p>
            </div>

            <p v-if="uploadError" class="mt-2 text-sm text-red-600">{{ uploadError }}</p>
          </div>
        </div>

        <div class="mt-5 flex justify-end gap-2">
          <button class="rounded-xl border bg-white px-4 py-2 text-sm font-medium hover:bg-gray-50" @click="closeModals">
            Cancel
          </button>
          <button class="rounded-xl bg-black px-4 py-2 text-sm font-medium text-white hover:opacity-90" @click="uploadFile">
            Upload
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
