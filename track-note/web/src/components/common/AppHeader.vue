<script setup>
import { useAuthStore } from '../../stores/auth'
import { useTimerStore } from '../../stores/timer'
import ThemeToggle from './ThemeToggle.vue'

const authStore = useAuthStore()
const timerStore = useTimerStore()
</script>

<template>
  <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
      <h1 class="text-xl font-bold text-primary-600 dark:text-primary-400">TrackNote</h1>

      <div v-if="timerStore.hasActiveTimer" class="flex items-center gap-3 ml-6 px-4 py-2 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
        <div class="flex items-center gap-2">
          <span
            class="w-2 h-2 rounded-full"
            :class="timerStore.isRunning ? 'bg-green-500 animate-pulse' : 'bg-yellow-500'"
          ></span>
          <span class="font-mono text-lg font-medium">
            {{ timerStore.formatTime(timerStore.elapsedSeconds) }}
          </span>
        </div>
        <span class="text-sm text-gray-600 dark:text-gray-400 truncate max-w-[200px]">
          {{ timerStore.timer?.title }}
        </span>
      </div>
    </div>

    <div class="flex items-center gap-4">
      <ThemeToggle />

      <div class="flex items-center gap-3">
        <div class="text-right">
          <p class="text-sm font-medium">{{ authStore.user?.name }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400">{{ authStore.user?.email }}</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
          <span class="text-primary-600 dark:text-primary-400 font-medium">
            {{ authStore.user?.name?.charAt(0).toUpperCase() }}
          </span>
        </div>
      </div>
    </div>
  </header>
</template>
