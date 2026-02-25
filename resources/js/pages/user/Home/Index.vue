<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import { type BreadcrumbItem } from '@/types'

type RecentItem =
  | {
      id: string
      type: 'folder'
      name: string
      updatedAt: string
      owner: string
      color?: string
    }
  | {
      id: string
      type: 'file'
      name: string
      updatedAt: string
      owner: string
      size: string
      fileType: 'PDF' | 'DOCX' | 'XLSX' | 'PNG' | 'JPG' | 'ZIP' | 'TXT'
    }

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Home', href: dashboard().url },
]

// ✅ Static demo data (replace later with API data)
const recent: RecentItem[] = [
  { id: 'f1', type: 'folder', name: 'Requirements', updatedAt: 'Today • 9:10 AM', owner: 'You' },
  { id: 'f2', type: 'folder', name: 'OJT Docs', updatedAt: 'Yesterday • 6:22 PM', owner: 'You' },
  { id: 'f3', type: 'folder', name: 'Capstone', updatedAt: 'Feb 21 • 2:05 PM', owner: 'Group' },

  { id: 'd1', type: 'file', name: 'Internship-Form.pdf', updatedAt: 'Today • 8:40 AM', owner: 'You', size: '410 KB', fileType: 'PDF' },
  { id: 'd2', type: 'file', name: 'Resume.docx', updatedAt: 'Feb 24 • 11:13 PM', owner: 'You', size: '92 KB', fileType: 'DOCX' },
  { id: 'd3', type: 'file', name: 'Grades.xlsx', updatedAt: 'Feb 20 • 3:18 PM', owner: 'You', size: '38 KB', fileType: 'XLSX' },
  { id: 'd4', type: 'file', name: 'ID-Photo.png', updatedAt: 'Feb 18 • 1:02 PM', owner: 'You', size: '1.2 MB', fileType: 'PNG' },
]

const folders = recent.filter((x) => x.type === 'folder')
const files = recent.filter((x) => x.type === 'file')


</script>

<template>
  <Head title="Home" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
          <h1 class="text-2xl font-semibold tracking-tight">Home</h1>
          <p class="mt-1 text-sm text-gray-500">Quick access to your recent folders and files.</p>
        </div>

        <div class="flex flex-wrap gap-2">
          <!-- Static buttons (wire later) -->
          <button
            type="button"
            class="rounded-xl border bg-white px-4 py-2 text-sm font-medium shadow-sm transition hover:bg-gray-50"
          >
            New Folder
          </button>
          <button
            type="button"
            class="rounded-xl bg-black px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:opacity-90"
          >
            Upload
          </button>
        </div>
      </div>

      <!-- Search (static) -->
      <div class="rounded-2xl border bg-white p-3 shadow-sm">
        <div class="flex items-center gap-3">
          <div class="grid h-10 w-10 place-items-center rounded-xl bg-gray-100 text-gray-600">
            <!-- magnifier -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3M10.8 18.2a7.4 7.4 0 1 1 0-14.8 7.4 7.4 0 0 1 0 14.8Z" />
            </svg>
          </div>
          <input
            class="h-10 w-full rounded-xl border bg-gray-50 px-3 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-black/10"
            placeholder="Search in Drive (static)"
            disabled
          />
        </div>
      </div>

      <!-- Recent folders -->
      <section class="space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold">Recent</h2>
          <button class="text-sm font-medium text-gray-600 hover:text-black" type="button">View all</button>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="f in folders"
            :key="f.id"
            class="group rounded-2xl border bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
          >
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-center gap-3 min-w-0">
                <div class="grid h-11 w-11 place-items-center rounded-xl bg-amber-100 text-amber-700">
                  <!-- folder icon -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 4a2 2 0 0 1 1.4.6l1.6 1.6H20a2 2 0 0 1 2 2v9.8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h6Z" />
                  </svg>
                </div>

                <div class="min-w-0">
                  <p class="truncate text-sm font-semibold">{{ f.name }}</p>
                  <p class="mt-0.5 text-xs text-gray-500">{{ f.updatedAt }}</p>
                </div>
              </div>

              <button
                type="button"
                class="rounded-xl border bg-white px-2.5 py-2 text-gray-600 shadow-sm opacity-0 transition group-hover:opacity-100 hover:bg-gray-50"
                title="More"
              >
                <!-- dots -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 7a2 2 0 1 0 .001-3.999A2 2 0 0 0 12 7Zm0 7a2 2 0 1 0 .001-3.999A2 2 0 0 0 12 14Zm0 7a2 2 0 1 0 .001-3.999A2 2 0 0 0 12 21Z" />
                </svg>
              </button>
            </div>

            <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
              <span>Owner: {{ f.owner }}</span>
              <span class="rounded-full bg-gray-100 px-2 py-1">Folder</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Recent files table -->
      <section class="rounded-2xl border bg-white shadow-sm">
        <div class="flex items-center justify-between border-b p-4">
          <h3 class="text-base font-semibold">Recent files</h3>
          <div class="flex items-center gap-2">
            <button class="rounded-xl border bg-white px-3 py-2 text-sm font-medium hover:bg-gray-50" type="button">
              Sort
            </button>
            <button class="rounded-xl border bg-white px-3 py-2 text-sm font-medium hover:bg-gray-50" type="button">
              Filter
            </button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full min-w-[720px] text-left text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-600">
              <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Owner</th>
                <th class="px-4 py-3">Last modified</th>
                <th class="px-4 py-3 text-right">Size</th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="file in files"
                :key="file.id"
                class="border-t hover:bg-gray-50"
              >
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <div class="grid h-10 w-10 place-items-center rounded-xl bg-gray-100 text-gray-700">
                      <!-- file icon -->
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 2h7l5 5v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Zm7 1.5V8h4.5" />
                      </svg>
                    </div>
                    <div class="min-w-0">
                      <p class="truncate font-medium">{{ file.name }}</p>
                      <p class="text-xs text-gray-500">Static preview</p>
                    </div>
                  </div>
                </td>

                <td class="px-4 py-3">
                  <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">
                    {{ file.fileType }}
                  </span>
                </td>

                <td class="px-4 py-3">{{ file.owner }}</td>
                <td class="px-4 py-3 text-gray-600">{{ file.updatedAt }}</td>
                <td class="px-4 py-3 text-right text-gray-600">{{ file.size }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Footer hint -->
      <p class="text-xs text-gray-500">
        This page is static UI only. Next step: replace the arrays with API data from your Drive endpoints.
      </p>
    </div>
  </AppLayout>
</template>
