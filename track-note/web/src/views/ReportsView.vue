<script setup>
import { ref, onMounted, computed } from 'vue'
import { useApi } from '../composables/useApi'

const api = useApi()

const period = ref('week')
const summary = ref(null)
const projectReport = ref(null)
const loading = ref(true)

onMounted(async () => {
  await fetchReports()
})

async function fetchReports() {
  loading.value = true
  try {
    const [summaryRes, projectRes] = await Promise.all([
      api.get('/reports/summary', { period: period.value }),
      api.get('/reports/by-project'),
    ])
    summary.value = summaryRes.data
    projectReport.value = projectRes.data
  } catch (err) {
    console.error('Failed to fetch reports:', err)
  } finally {
    loading.value = false
  }
}

async function handleExport() {
  try {
    const response = await fetch('/api/reports/export?format=csv', {
      credentials: 'include',
    })
    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = 'tracknote-export.csv'
    a.click()
    window.URL.revokeObjectURL(url)
  } catch (err) {
    alert('Failed to export')
  }
}

const periodOptions = [
  { value: 'day', label: 'Today' },
  { value: 'week', label: 'This Week' },
  { value: 'month', label: 'This Month' },
  { value: 'year', label: 'This Year' },
]
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Reports</h1>
      <div class="flex items-center gap-4">
        <select v-model="period" @change="fetchReports" class="input w-auto">
          <option v-for="opt in periodOptions" :key="opt.value" :value="opt.value">
            {{ opt.label }}
          </option>
        </select>
        <button @click="handleExport" class="btn-secondary">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          Export CSV
        </button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-500">Loading reports...</p>
    </div>

    <template v-else>
      <!-- Summary Cards -->
      <div v-if="summary" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card">
          <div class="card-body text-center">
            <p class="text-4xl font-bold text-primary-600 dark:text-primary-400">
              {{ summary.total_hours }}h
            </p>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Total Hours</p>
          </div>
        </div>
        <div class="card">
          <div class="card-body text-center">
            <p class="text-4xl font-bold text-primary-600 dark:text-primary-400">
              {{ summary.total_entries }}
            </p>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Total Entries</p>
          </div>
        </div>
        <div class="card">
          <div class="card-body text-center">
            <p class="text-4xl font-bold text-primary-600 dark:text-primary-400">
              {{ summary.total_projects }}
            </p>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Projects Worked On</p>
          </div>
        </div>
      </div>

      <!-- Daily Breakdown -->
      <div v-if="summary?.daily_breakdown?.length" class="card">
        <div class="card-header">
          <h2 class="font-semibold">Daily Breakdown</h2>
        </div>
        <div class="card-body">
          <div class="flex items-end justify-between h-48 gap-2">
            <div
              v-for="day in summary.daily_breakdown"
              :key="day.date"
              class="flex-1 flex flex-col items-center"
            >
              <div class="w-full flex flex-col items-center">
                <span class="text-xs text-gray-500 mb-1">{{ day.hours }}h</span>
                <div
                  class="w-full bg-primary-500 rounded-t min-h-[4px]"
                  :style="{
                    height: `${Math.max(4, (day.hours / Math.max(...summary.daily_breakdown.map(d => d.hours || 1))) * 150)}px`
                  }"
                ></div>
              </div>
              <span class="text-xs text-gray-500 mt-2">
                {{ new Date(day.date).toLocaleDateString('en-US', { weekday: 'short' }) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- By Project -->
      <div v-if="projectReport?.projects?.length" class="card">
        <div class="card-header">
          <h2 class="font-semibold">Time by Project</h2>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
          <div
            v-for="project in projectReport.projects"
            :key="project.project_id"
            class="px-6 py-4"
          >
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center gap-3">
                <div
                  class="w-3 h-3 rounded-full"
                  :style="{ backgroundColor: project.project_color }"
                ></div>
                <span class="font-medium">{{ project.project_name }}</span>
              </div>
              <div class="text-right">
                <span class="font-mono">{{ project.total_hours }}h</span>
                <span class="text-gray-500 ml-2">({{ project.percentage }}%)</span>
              </div>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div
                class="h-2 rounded-full"
                :style="{
                  width: `${project.percentage}%`,
                  backgroundColor: project.project_color
                }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="!projectReport?.projects?.length" class="card">
        <div class="card-body text-center py-12">
          <p class="text-gray-500">No data for the selected period</p>
        </div>
      </div>
    </template>
  </div>
</template>
