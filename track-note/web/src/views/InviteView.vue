<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '../composables/useApi'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const router = useRouter()
const api = useApi()
const authStore = useAuthStore()

const loading = ref(true)
const error = ref('')
const invite = ref(null)
const accepting = ref(false)

onMounted(async () => {
  try {
    const response = await api.post(`/invites/${route.params.code}/accept`)

    if (response.data?.requires_auth) {
      invite.value = response.data
    } else {
      router.push(`/organizations/${response.data.id}`)
    }
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
})

async function handleAccept() {
  accepting.value = true
  try {
    const response = await api.post(`/invites/${route.params.code}/accept`)
    router.push(`/organizations/${response.data.id}`)
  } catch (err) {
    error.value = err.message
  } finally {
    accepting.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-primary-600 dark:text-primary-400">TrackNote</h1>
      </div>

      <div class="card">
        <div class="card-body">
          <div v-if="loading" class="text-center py-8">
            <p class="text-gray-500">Loading invite...</p>
          </div>

          <div v-else-if="error" class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h2 class="text-lg font-semibold mb-2">Invalid Invite</h2>
            <p class="text-gray-500 mb-4">{{ error }}</p>
            <router-link to="/login" class="link">Go to login</router-link>
          </div>

          <div v-else-if="invite?.requires_auth" class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-primary-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <h2 class="text-lg font-semibold mb-2">Join {{ invite.organization_name }}</h2>
            <p class="text-gray-500 mb-6">You've been invited to join this organization.</p>

            <div v-if="authStore.isAuthenticated" class="space-y-3">
              <button @click="handleAccept" :disabled="accepting" class="btn-primary w-full">
                {{ accepting ? 'Joining...' : 'Accept Invite' }}
              </button>
            </div>
            <div v-else class="space-y-3">
              <router-link
                :to="{ name: 'login', query: { redirect: route.fullPath } }"
                class="btn-primary w-full inline-block text-center"
              >
                Sign in to accept
              </router-link>
              <p class="text-sm text-gray-500">
                Don't have an account?
                <router-link
                  :to="{ name: 'register', query: { redirect: route.fullPath } }"
                  class="link"
                >
                  Create one
                </router-link>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
