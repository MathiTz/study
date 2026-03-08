<script setup>
import { ref } from 'vue'
import { useTimerStore } from '../../stores/timer'

const timerStore = useTimerStore()
const loading = ref(false)

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
</script>

<template>
  <div class="flex items-center justify-center gap-3">
    <button
      v-if="timerStore.isRunning"
      @click="handlePause"
      :disabled="loading"
      class="btn-secondary"
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
      class="btn-primary"
      title="Resume"
    >
      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
      </svg>
    </button>

    <button
      @click="handleStop"
      :disabled="loading"
      class="btn-danger"
      title="Stop"
    >
      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd" />
      </svg>
    </button>
  </div>
</template>
