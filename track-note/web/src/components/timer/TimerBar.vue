<script setup>
import { ref, onMounted } from 'vue'
import { useTimerStore } from '../../stores/timer'
import { useProjectsStore } from '../../stores/projects'

const emit = defineEmits(['transition-click'])

const timerStore = useTimerStore()
const projectsStore = useProjectsStore()

const selectedProject = ref('')
const title = ref('')
const error = ref('')
const loading = ref(false)

onMounted(async () => {
  await projectsStore.fetchProjects()
  if (projectsStore.projects.length > 0) {
    selectedProject.value = projectsStore.projects[0].id
  }
})

async function handleStart() {
  if (!selectedProject.value || !title.value.trim()) {
    error.value = 'Please select a project and enter a title'
    return
  }

  error.value = ''
  loading.value = true
  try {
    await timerStore.start(selectedProject.value, title.value.trim())
    title.value = ''
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

async function handlePause() {
  loading.value = true
  try {
    await timerStore.pause()
  } finally {
    loading.value = false
  }
}

async function handleResume() {
  loading.value = true
  try {
    await timerStore.resume()
  } finally {
    loading.value = false
  }
}

async function handleStop() {
  loading.value = true
  try {
    await timerStore.stop()
  } finally {
    loading.value = false
  }
}

function handleTransitionClick() {
  emit('transition-click')
}
</script>

<template>
  <div class="card">
    <div class="card-body">
      <!-- Timer Running State -->
      <div v-if="timerStore.hasActiveTimer" class="flex items-center gap-4">
        <!-- Timer Display -->
        <div class="flex items-center gap-3 flex-1 min-w-0">
          <div
            class="w-3 h-3 rounded-full flex-shrink-0"
            :style="{ backgroundColor: timerStore.timer?.project_color }"
          ></div>
          <div class="min-w-0 flex-1">
            <p class="font-medium truncate">{{ timerStore.timer?.title }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
              {{ timerStore.timer?.project_name }}
            </p>
          </div>
        </div>

        <!-- Timer -->
        <div class="timer-display text-2xl">
          {{ timerStore.formatTime(timerStore.elapsedSeconds) }}
        </div>

        <!-- Controls -->
        <div class="flex items-center gap-2">
          <button
            v-if="timerStore.isRunning"
            @click="handlePause"
            :disabled="loading"
            class="btn-secondary btn-sm"
            title="Pause"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </button>

          <button
            v-if="timerStore.isPaused"
            @click="handleResume"
            :disabled="loading"
            class="btn-primary btn-sm"
            title="Resume"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
            </svg>
          </button>

          <button
            @click="handleStop"
            :disabled="loading"
            class="btn-danger btn-sm"
            title="Stop"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd" />
            </svg>
          </button>

          <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>

          <button
            @click="handleTransitionClick"
            :disabled="loading"
            class="btn-secondary btn-sm"
            title="Continue with new task"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Start Timer Form -->
      <div v-else>
        <div v-if="error" class="mb-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-400 text-sm">
          {{ error }}
        </div>

        <form @submit.prevent="handleStart" class="flex items-center gap-3">
          <select v-model="selectedProject" class="input w-48 flex-shrink-0">
            <option value="">Select project</option>
            <option
              v-for="project in projectsStore.projects"
              :key="project.id"
              :value="project.id"
            >
              {{ project.name }}
            </option>
          </select>

          <input
            v-model="title"
            type="text"
            class="input flex-1"
            placeholder="What are you working on?"
            required
          />

          <button
            type="submit"
            :disabled="loading || !selectedProject || !title.trim()"
            class="btn-primary flex-shrink-0"
          >
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
            </svg>
            Start
          </button>
        </form>
      </div>
    </div>
  </div>
</template>
