import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useApi } from '../composables/useApi'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const initialized = ref(false)
  const api = useApi()

  const isAuthenticated = computed(() => user.value !== null)

  async function fetchUser() {
    try {
      const response = await api.get('/auth/me')
      user.value = response.data
    } catch (err) {
      user.value = null
    } finally {
      initialized.value = true
    }
  }

  async function login(email, password) {
    const response = await api.post('/auth/login', { email, password })
    user.value = response.data
    return response
  }

  async function register(name, email, password) {
    const response = await api.post('/auth/register', { name, email, password })
    user.value = response.data
    return response
  }

  async function logout() {
    await api.post('/auth/logout')
    user.value = null
  }

  async function updateProfile(data) {
    const response = await api.put('/users/profile', data)
    user.value = response.data
    return response
  }

  async function updatePassword(currentPassword, newPassword) {
    return await api.put('/users/password', {
      current_password: currentPassword,
      new_password: newPassword,
    })
  }

  return {
    user,
    initialized,
    isAuthenticated,
    fetchUser,
    login,
    register,
    logout,
    updateProfile,
    updatePassword,
  }
})
