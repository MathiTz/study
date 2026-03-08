<script setup>
import { ref, onMounted } from 'vue'
import { useApi } from '../composables/useApi'
import Modal from '../components/common/Modal.vue'

const api = useApi()

const organizations = ref([])
const loading = ref(true)
const showCreateModal = ref(false)
const newOrg = ref({ name: '' })
const error = ref('')
const creating = ref(false)

onMounted(async () => {
  await fetchOrganizations()
})

async function fetchOrganizations() {
  loading.value = true
  try {
    const response = await api.get('/organizations')
    organizations.value = response.data
  } catch (err) {
    console.error('Failed to fetch organizations:', err)
  } finally {
    loading.value = false
  }
}

async function handleCreate() {
  if (!newOrg.value.name.trim()) {
    error.value = 'Organization name is required'
    return
  }

  error.value = ''
  creating.value = true

  try {
    const response = await api.post('/organizations', newOrg.value)
    organizations.value.unshift(response.data)
    showCreateModal.value = false
    newOrg.value = { name: '' }
  } catch (err) {
    error.value = err.message
  } finally {
    creating.value = false
  }
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Organizations</h1>
      <button @click="showCreateModal = true" class="btn-primary">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Organization
      </button>
    </div>

    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-500">Loading organizations...</p>
    </div>

    <div v-else-if="!organizations.length" class="card">
      <div class="card-body text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <h3 class="text-lg font-medium mb-2">No organizations yet</h3>
        <p class="text-gray-500 mb-4">Create an organization to collaborate with your team</p>
        <button @click="showCreateModal = true" class="btn-primary">
          Create Organization
        </button>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <router-link
        v-for="org in organizations"
        :key="org.id"
        :to="`/organizations/${org.id}`"
        class="card hover:shadow-md transition-shadow"
      >
        <div class="card-body">
          <div class="flex items-start gap-3">
            <div class="w-12 h-12 rounded-lg bg-primary-100 dark:bg-primary-900 flex items-center justify-center flex-shrink-0">
              <span class="text-primary-600 dark:text-primary-400 text-xl font-bold">
                {{ org.name?.charAt(0).toUpperCase() }}
              </span>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="font-semibold truncate">{{ org.name }}</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 capitalize">
                {{ org.role }}
              </p>
            </div>
          </div>
        </div>
      </router-link>
    </div>

    <!-- Create Organization Modal -->
    <Modal v-if="showCreateModal" title="New Organization" @close="showCreateModal = false">
      <form @submit.prevent="handleCreate" class="space-y-4">
        <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-400 text-sm">
          {{ error }}
        </div>

        <div>
          <label class="label">Name</label>
          <input
            v-model="newOrg.name"
            type="text"
            class="input"
            placeholder="Organization name"
            required
          />
        </div>
      </form>

      <template #footer>
        <button @click="showCreateModal = false" class="btn-secondary">
          Cancel
        </button>
        <button @click="handleCreate" :disabled="creating" class="btn-primary">
          {{ creating ? 'Creating...' : 'Create Organization' }}
        </button>
      </template>
    </Modal>
  </div>
</template>
