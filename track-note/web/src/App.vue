<script setup>
import { onMounted } from 'vue'
import { useThemeStore } from './stores/theme'
import { useAuthStore } from './stores/auth'

const themeStore = useThemeStore()
const authStore = useAuthStore()

onMounted(async () => {
  themeStore.init()
  await authStore.fetchUser()
})
</script>

<template>
  <router-view />
</template>
