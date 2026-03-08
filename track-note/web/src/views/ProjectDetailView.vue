<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProjectsStore } from '../stores/projects'
import { useApi } from '../composables/useApi'

const route = useRoute()
const router = useRouter()
const projectsStore = useProjectsStore()
const api = useApi()

const entries = ref([])
const loading = ref(true)

const project = computed(() => projectsStore.currentProject)

onMounted(async () => {
  await projectsStore.fetchProject(route.params.id)
  await fetchEntries()
  loading.value = false
})

async function fetchEntries() {
  try {
    const response = await api.get('/work-entries', {
      project_id: route.params.id,
      limit: 20
    })
    entries.value = response.data
  } catch (err) {
    console.error('Failed to fetch entries:', err)
  }
}

function formatDuration(seconds) {
  const hrs = Math.floor(seconds / 3600)
  const mins = Math.floor((seconds % 3600) / 60)
  if (hrs > 0) {
    return `${hrs}h ${mins}m`
  }
  return `${mins}m`
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

async function handleArchive() {
  if (!confirm('Are you sure you want to archive this project?')) return

  await projectsStore.archiveProject(project.value.id)
  router.push('/projects')
}
</script>

<template>
  <div v-if="loading" class="text-center py-12">
    <p class="text-gray-500">Loading project...</p>
  </div>

  <div v-else-if="!project" class="text-center py-12">
    <p class="text-gray-500">Project not found</p>
    <router-link to="/projects" class="link">Back to projects</router-link>
  </div>

  <div v-else class="space-y-6">
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-4">
        <router-link to="/projects" class="btn-ghost btn-sm">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </router-link>
        <div class="flex items-center gap-3">
          <div
            class="w-4 h-4 rounded-full"
            :style="{ backgroundColor: project.color }"
          ></div>
          <h1 class="text-2xl font-bold">{{ project.name }}</h1>
        </div>
      </div>
      <div class="flex gap-2">
        <button @click="handleArchive" class="btn-ghost text-red-600 dark:text-red-400">
          Archive
        </button>
      </div>
    </div>

    <p v-if="project.description" class="text-gray-600 dark:text-gray-400">
      {{ project.description }}
    </p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2">
        <div class="card">
          <div class="card-header">
            <h2 class="font-semibold">Work Entries</h2>
          </div>
          <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <div
              v-for="entry in entries"
              :key="entry.id"
              class="px-6 py-4"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="font-medium">{{ entry.title }}</p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ formatDate(entry.started_at) }}
                  </p>
                </div>
                <span class="font-mono">
                  {{ formatDuration(entry.duration_seconds) }}
                </span>
              </div>
            </div>
            <div v-if="!entries.length" class="px-6 py-8 text-center text-gray-500">
              No entries for this project yet
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-6">
        <div class="card">
          <div class="card-header">
            <h2 class="font-semibold">Members</h2>
          </div>
          <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <div
              v-for="member in project.members"
              :key="member.id"
              class="px-6 py-3 flex items-center gap-3"
            >
              <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                <span class="text-primary-600 dark:text-primary-400 text-sm font-medium">
                  {{ member.name?.charAt(0).toUpperCase() }}
                </span>
              </div>
              <div class="flex-1">
                <p class="text-sm font-medium">{{ member.name }}</p>
                <p class="text-xs text-gray-500 capitalize">{{ member.role }}</p>
              </div>
            </div>
            <div v-if="!project.members?.length" class="px-6 py-4 text-center text-gray-500 text-sm">
              No members
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
