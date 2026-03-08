import { defineStore } from 'pinia'
import { ref } from 'vue'
import { useApi } from '../composables/useApi'

export const useProjectsStore = defineStore('projects', () => {
  const projects = ref([])
  const currentProject = ref(null)
  const loading = ref(false)
  const api = useApi()

  async function fetchProjects(includeArchived = false) {
    loading.value = true
    try {
      const response = await api.get('/projects', { include_archived: includeArchived })
      projects.value = response.data
      return response.data
    } finally {
      loading.value = false
    }
  }

  async function fetchProject(id) {
    loading.value = true
    try {
      const response = await api.get(`/projects/${id}`)
      currentProject.value = response.data
      return response.data
    } finally {
      loading.value = false
    }
  }

  async function createProject(data) {
    const response = await api.post('/projects', data)
    projects.value.unshift(response.data)
    return response.data
  }

  async function updateProject(id, data) {
    const response = await api.put(`/projects/${id}`, data)
    const index = projects.value.findIndex(p => p.id === id)
    if (index !== -1) {
      projects.value[index] = response.data
    }
    if (currentProject.value?.id === id) {
      currentProject.value = response.data
    }
    return response.data
  }

  async function archiveProject(id) {
    await api.del(`/projects/${id}`)
    projects.value = projects.value.filter(p => p.id !== id)
    if (currentProject.value?.id === id) {
      currentProject.value = null
    }
  }

  async function addMember(projectId, userId, role = 'member') {
    return await api.post(`/projects/${projectId}/members`, { user_id: userId, role })
  }

  async function updateMember(projectId, userId, role) {
    return await api.put(`/projects/${projectId}/members/${userId}`, { role })
  }

  async function removeMember(projectId, userId) {
    return await api.del(`/projects/${projectId}/members/${userId}`)
  }

  return {
    projects,
    currentProject,
    loading,
    fetchProjects,
    fetchProject,
    createProject,
    updateProject,
    archiveProject,
    addMember,
    updateMember,
    removeMember,
  }
})
