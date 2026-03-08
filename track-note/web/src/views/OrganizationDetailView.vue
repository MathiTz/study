<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '../composables/useApi'
import Modal from '../components/common/Modal.vue'

const route = useRoute()
const router = useRouter()
const api = useApi()

const organization = ref(null)
const loading = ref(true)
const showInviteModal = ref(false)
const inviteEmail = ref('')
const invites = ref([])

const isAdmin = computed(() => organization.value?.user_role === 'admin')

onMounted(async () => {
  await fetchOrganization()
  if (isAdmin.value) {
    await fetchInvites()
  }
  loading.value = false
})

async function fetchOrganization() {
  try {
    const response = await api.get(`/organizations/${route.params.id}`)
    organization.value = response.data
  } catch (err) {
    console.error('Failed to fetch organization:', err)
  }
}

async function fetchInvites() {
  try {
    const response = await api.get(`/organizations/${route.params.id}/invites`)
    invites.value = response.data
  } catch (err) {
    console.error('Failed to fetch invites:', err)
  }
}

async function handleCreateInvite() {
  try {
    const payload = inviteEmail.value ? { email: inviteEmail.value } : {}
    const response = await api.post(`/organizations/${route.params.id}/invites`, payload)
    invites.value.unshift(response.data)
    showInviteModal.value = false
    inviteEmail.value = ''
  } catch (err) {
    alert(err.message)
  }
}

async function handleRevokeInvite(code) {
  if (!confirm('Are you sure you want to revoke this invite?')) return

  try {
    await api.del(`/organizations/${route.params.id}/invites/${code}`)
    invites.value = invites.value.filter(i => i.code !== code)
  } catch (err) {
    alert(err.message)
  }
}

async function handleRemoveMember(userId) {
  if (!confirm('Are you sure you want to remove this member?')) return

  try {
    await api.del(`/organizations/${route.params.id}/members/${userId}`)
    organization.value.members = organization.value.members.filter(m => m.id !== userId)
  } catch (err) {
    alert(err.message)
  }
}

function copyInviteLink(code) {
  const url = `${window.location.origin}/invite/${code}`
  navigator.clipboard.writeText(url)
  alert('Invite link copied to clipboard!')
}
</script>

<template>
  <div v-if="loading" class="text-center py-12">
    <p class="text-gray-500">Loading organization...</p>
  </div>

  <div v-else-if="!organization" class="text-center py-12">
    <p class="text-gray-500">Organization not found</p>
    <router-link to="/organizations" class="link">Back to organizations</router-link>
  </div>

  <div v-else class="space-y-6">
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-4">
        <router-link to="/organizations" class="btn-ghost btn-sm">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </router-link>
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-lg bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
            <span class="text-primary-600 dark:text-primary-400 text-xl font-bold">
              {{ organization.name?.charAt(0).toUpperCase() }}
            </span>
          </div>
          <div>
            <h1 class="text-2xl font-bold">{{ organization.name }}</h1>
            <p class="text-sm text-gray-500 capitalize">{{ organization.user_role }}</p>
          </div>
        </div>
      </div>
      <button v-if="isAdmin" @click="showInviteModal = true" class="btn-primary">
        Invite Member
      </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Members -->
      <div class="card">
        <div class="card-header">
          <h2 class="font-semibold">Members ({{ organization.members?.length || 0 }})</h2>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
          <div
            v-for="member in organization.members"
            :key="member.id"
            class="px-6 py-4 flex items-center justify-between"
          >
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                <span class="text-primary-600 dark:text-primary-400 font-medium">
                  {{ member.name?.charAt(0).toUpperCase() }}
                </span>
              </div>
              <div>
                <p class="font-medium">{{ member.name }}</p>
                <p class="text-sm text-gray-500">{{ member.email }}</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-sm text-gray-500 capitalize">{{ member.role }}</span>
              <button
                v-if="isAdmin && member.role !== 'admin'"
                @click="handleRemoveMember(member.id)"
                class="text-gray-400 hover:text-red-500"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pending Invites (Admin only) -->
      <div v-if="isAdmin" class="card">
        <div class="card-header">
          <h2 class="font-semibold">Pending Invites</h2>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
          <div
            v-for="invite in invites"
            :key="invite.id"
            class="px-6 py-4"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium">{{ invite.email || 'Anyone with link' }}</p>
                <p class="text-sm text-gray-500">
                  Expires {{ new Date(invite.expires_at).toLocaleDateString() }}
                </p>
              </div>
              <div class="flex items-center gap-2">
                <button
                  @click="copyInviteLink(invite.code)"
                  class="btn-ghost btn-sm"
                  title="Copy link"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                  </svg>
                </button>
                <button
                  @click="handleRevokeInvite(invite.code)"
                  class="text-gray-400 hover:text-red-500"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
          <div v-if="!invites.length" class="px-6 py-4 text-center text-gray-500 text-sm">
            No pending invites
          </div>
        </div>
      </div>

      <!-- Projects -->
      <div class="card" :class="{ 'lg:col-span-2': !isAdmin }">
        <div class="card-header">
          <h2 class="font-semibold">Projects</h2>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
          <router-link
            v-for="project in organization.projects"
            :key="project.id"
            :to="`/projects/${project.id}`"
            class="px-6 py-4 flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/50"
          >
            <div
              class="w-3 h-3 rounded-full"
              :style="{ backgroundColor: project.color }"
            ></div>
            <span>{{ project.name }}</span>
          </router-link>
          <div v-if="!organization.projects?.length" class="px-6 py-4 text-center text-gray-500 text-sm">
            No projects yet
          </div>
        </div>
      </div>
    </div>

    <!-- Invite Modal -->
    <Modal v-if="showInviteModal" title="Invite Member" @close="showInviteModal = false">
      <div class="space-y-4">
        <p class="text-gray-600 dark:text-gray-400">
          Send an email invitation or create a shareable link.
        </p>

        <div>
          <label class="label">Email (optional)</label>
          <input
            v-model="inviteEmail"
            type="email"
            class="input"
            placeholder="Leave empty for shareable link"
          />
        </div>
      </div>

      <template #footer>
        <button @click="showInviteModal = false" class="btn-secondary">
          Cancel
        </button>
        <button @click="handleCreateInvite" class="btn-primary">
          Create Invite
        </button>
      </template>
    </Modal>
  </div>
</template>
