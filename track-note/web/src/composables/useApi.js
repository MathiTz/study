import { ref } from 'vue'

const BASE_URL = '/api'

export function useApi() {
  const loading = ref(false)
  const error = ref(null)

  async function request(endpoint, options = {}) {
    loading.value = true
    error.value = null

    try {
      const url = `${BASE_URL}${endpoint}`
      const config = {
        headers: {
          'Content-Type': 'application/json',
          ...options.headers,
        },
        credentials: 'include',
        ...options,
      }

      if (config.body && typeof config.body === 'object') {
        config.body = JSON.stringify(config.body)
      }

      const response = await fetch(url, config)
      const data = await response.json()

      if (!response.ok) {
        throw new ApiError(data.message || 'Request failed', response.status, data.errors)
      }

      return data
    } catch (err) {
      if (err instanceof ApiError) {
        error.value = err
        throw err
      }
      error.value = new ApiError(err.message || 'Network error', 0)
      throw error.value
    } finally {
      loading.value = false
    }
  }

  function get(endpoint, params = {}) {
    const query = new URLSearchParams(params).toString()
    const url = query ? `${endpoint}?${query}` : endpoint
    return request(url, { method: 'GET' })
  }

  function post(endpoint, body = {}) {
    return request(endpoint, { method: 'POST', body })
  }

  function put(endpoint, body = {}) {
    return request(endpoint, { method: 'PUT', body })
  }

  function del(endpoint) {
    return request(endpoint, { method: 'DELETE' })
  }

  return {
    loading,
    error,
    request,
    get,
    post,
    put,
    del,
  }
}

class ApiError extends Error {
  constructor(message, status, errors = null) {
    super(message)
    this.name = 'ApiError'
    this.status = status
    this.errors = errors
  }
}

export { ApiError }
