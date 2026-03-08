<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()

const name = ref(authStore.user?.name || '')
const timezone = ref(authStore.user?.timezone || 'UTC')
const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')

const profileError = ref('')
const profileSuccess = ref('')
const passwordError = ref('')
const passwordSuccess = ref('')
const savingProfile = ref(false)
const savingPassword = ref(false)

const timezones = [
  'UTC',
  'America/New_York',
  'America/Chicago',
  'America/Denver',
  'America/Los_Angeles',
  'America/Sao_Paulo',
  'Europe/London',
  'Europe/Paris',
  'Europe/Berlin',
  'Asia/Tokyo',
  'Asia/Shanghai',
  'Australia/Sydney',
]

async function handleUpdateProfile() {
  profileError.value = ''
  profileSuccess.value = ''
  savingProfile.value = true

  try {
    await authStore.updateProfile({
      name: name.value,
      timezone: timezone.value,
    })
    profileSuccess.value = 'Profile updated successfully'
  } catch (err) {
    profileError.value = err.message
  } finally {
    savingProfile.value = false
  }
}

async function handleUpdatePassword() {
  passwordError.value = ''
  passwordSuccess.value = ''

  if (newPassword.value !== confirmPassword.value) {
    passwordError.value = 'Passwords do not match'
    return
  }

  if (newPassword.value.length < 8) {
    passwordError.value = 'Password must be at least 8 characters'
    return
  }

  savingPassword.value = true

  try {
    await authStore.updatePassword(currentPassword.value, newPassword.value)
    passwordSuccess.value = 'Password updated successfully'
    currentPassword.value = ''
    newPassword.value = ''
    confirmPassword.value = ''
  } catch (err) {
    passwordError.value = err.message
  } finally {
    savingPassword.value = false
  }
}
</script>

<template>
  <div class="max-w-2xl space-y-6">
    <h1 class="text-2xl font-bold">Profile</h1>

    <!-- Profile Settings -->
    <div class="card">
      <div class="card-header">
        <h2 class="font-semibold">Profile Settings</h2>
      </div>
      <div class="card-body">
        <form @submit.prevent="handleUpdateProfile" class="space-y-4">
          <div v-if="profileError" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-400 text-sm">
            {{ profileError }}
          </div>
          <div v-if="profileSuccess" class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-green-600 dark:text-green-400 text-sm">
            {{ profileSuccess }}
          </div>

          <div>
            <label class="label">Email</label>
            <input
              :value="authStore.user?.email"
              type="email"
              class="input bg-gray-100 dark:bg-gray-700"
              disabled
            />
            <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
          </div>

          <div>
            <label class="label">Name</label>
            <input
              v-model="name"
              type="text"
              class="input"
              required
            />
          </div>

          <div>
            <label class="label">Timezone</label>
            <select v-model="timezone" class="input">
              <option v-for="tz in timezones" :key="tz" :value="tz">
                {{ tz }}
              </option>
            </select>
          </div>

          <button type="submit" :disabled="savingProfile" class="btn-primary">
            {{ savingProfile ? 'Saving...' : 'Save Changes' }}
          </button>
        </form>
      </div>
    </div>

    <!-- Change Password -->
    <div class="card">
      <div class="card-header">
        <h2 class="font-semibold">Change Password</h2>
      </div>
      <div class="card-body">
        <form @submit.prevent="handleUpdatePassword" class="space-y-4">
          <div v-if="passwordError" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-400 text-sm">
            {{ passwordError }}
          </div>
          <div v-if="passwordSuccess" class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-green-600 dark:text-green-400 text-sm">
            {{ passwordSuccess }}
          </div>

          <div>
            <label class="label">Current Password</label>
            <input
              v-model="currentPassword"
              type="password"
              class="input"
              required
            />
          </div>

          <div>
            <label class="label">New Password</label>
            <input
              v-model="newPassword"
              type="password"
              class="input"
              placeholder="At least 8 characters"
              required
            />
          </div>

          <div>
            <label class="label">Confirm New Password</label>
            <input
              v-model="confirmPassword"
              type="password"
              class="input"
              required
            />
          </div>

          <button type="submit" :disabled="savingPassword" class="btn-primary">
            {{ savingPassword ? 'Updating...' : 'Update Password' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>
