<script setup>
import { ref, onMounted } from 'vue'
import { useApi } from '../composables/useApi'
import { useProjectsStore } from '../stores/projects'

const api = useApi()
const projectsStore = useProjectsStore()

const summary = ref(null)
const recentEntries = ref([])
const loading = ref(true)

onMounted(async () => {
  await Promise.all([
    fetchSummary(),
    fetchRecentEntries(),
    projectsStore.fetchProjects(),
  ])
  loading.value = false
})

async function fetchSummary() {
  try {
    const response = await api.get('/reports/summary', { period: 'week' })
    summary.value = response.data
  } catch (err) {
    console.error('Failed to fetch summary:', err)
  }
}

async function fetchRecentEntries() {
  try {
    const response = await api.get('/work-entries', { limit: 5 })
    recentEntries.value = response.data
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
  })
}
</script>

<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold">Summary</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 space-y-6">
        <!-- Week Summary -->
        <div class="card" v-if="summary">
          <div class="card-header">
            <h2 class="font-semibold">This Week</h2>
          </div>
          <div class="card-body">
            <div class="grid grid-cols-3 gap-4 text-center">
              <div>
                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                  {{ summary.total_hours }}h
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Hours</p>
              </div>
              <div>
                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                  {{ summary.total_entries }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Entries</p>
              </div>
              <div>
                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                  {{ summary.total_projects }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Projects</p>
              </div>
            </div>

            <div v-if="summary.daily_breakdown.length" class="mt-6">
              <div class="flex items-end justify-between h-32 gap-2">
                <div
                  v-for="day in summary.daily_breakdown"
                  :key="day.date"
                  class="flex-1 flex flex-col items-center"
                >
                  <div
                    class="w-full bg-primary-500 rounded-t"
                    :style="{ height: `${Math.max(4, (day.hours / Math.max(...summary.daily_breakdown.map(d => d.hours))) * 100)}%` }"
                  ></div>
                  <span class="text-xs text-gray-500 mt-2">
                    {{ new Date(day.date).toLocaleDateString('en-US', { weekday: 'short' }) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Entries -->
        <div class="card">
          <div class="card-header flex items-center justify-between">
            <h2 class="font-semibold">Recent Entries</h2>
            <router-link to="/entries" class="link text-sm">View all</router-link>
          </div>
          <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <div
              v-for="entry in recentEntries"
              :key="entry.id"
              class="px-6 py-4 flex items-center justify-between"
            >
              <div class="flex items-center gap-3">
                <div
                  class="w-3 h-3 rounded-full"
                  :style="{ backgroundColor: entry.project_color }"
                ></div>
                <div>
                  <p class="font-medium">{{ entry.title }}</p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ entry.project_name }} &middot; {{ formatDate(entry.started_at) }}
                  </p>
                </div>
              </div>
              <span class="font-mono text-sm">
                {{ formatDuration(entry.duration_seconds) }}
              </span>
            </div>
            <div v-if="!recentEntries.length" class="px-6 py-8 text-center text-gray-500">
              No entries yet. Start tracking your time!
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-6">
        <!-- Projects List -->
        <div class="card">
          <div class="card-header flex items-center justify-between">
            <h2 class="font-semibold">Projects</h2>
            <router-link to="/projects" class="link text-sm">View all</router-link>
          </div>
          <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <router-link
              v-for="project in projectsStore.projects.slice(0, 5)"
              :key="project.id"
              :to="`/projects/${project.id}`"
              class="px-6 py-3 flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/50"
            >
              <div
                class="w-3 h-3 rounded-full"
                :style="{ backgroundColor: project.color }"
              ></div>
              <span>{{ project.name }}</span>
            </router-link>
            <div v-if="!projectsStore.projects.length" class="px-6 py-4 text-center text-gray-500">
              No projects yet
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
