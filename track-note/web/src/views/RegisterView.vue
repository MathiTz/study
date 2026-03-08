<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const error = ref('')
const loading = ref(false)

async function handleSubmit() {
  error.value = ''

  if (password.value !== confirmPassword.value) {
    error.value = 'Passwords do not match'
    return
  }

  if (password.value.length < 8) {
    error.value = 'Password must be at least 8 characters'
    return
  }

  loading.value = true

  try {
    await authStore.register(name.value, email.value, password.value)
    router.push('/')
  } catch (err) {
    error.value = err.message || 'Registration failed'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-primary-600 dark:text-primary-400">TrackNote</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Time tracking with notes</p>
      </div>

      <div class="card">
        <div class="card-body">
          <h2 class="text-xl font-semibold mb-6">Create your account</h2>

          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-400 text-sm">
              {{ error }}
            </div>

            <div>
              <label for="name" class="label">Name</label>
              <input
                id="name"
                v-model="name"
                type="text"
                required
                class="input"
                placeholder="Your name"
              />
            </div>

            <div>
              <label for="email" class="label">Email</label>
              <input
                id="email"
                v-model="email"
                type="email"
                required
                class="input"
                placeholder="you@example.com"
              />
            </div>

            <div>
              <label for="password" class="label">Password</label>
              <input
                id="password"
                v-model="password"
                type="password"
                required
                class="input"
                placeholder="At least 8 characters"
              />
            </div>

            <div>
              <label for="confirmPassword" class="label">Confirm Password</label>
              <input
                id="confirmPassword"
                v-model="confirmPassword"
                type="password"
                required
                class="input"
                placeholder="Confirm your password"
              />
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="btn-primary w-full"
            >
              {{ loading ? 'Creating account...' : 'Create account' }}
            </button>
          </form>

          <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
            Already have an account?
            <router-link to="/login" class="link">Sign in</router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
