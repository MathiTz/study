<script setup>
import { ref, computed, onMounted } from "vue";
import { useProjectsStore } from "../stores/projects";
import { useApi } from "../composables/useApi";
import Modal from "../components/common/Modal.vue";

const projectsStore = useProjectsStore();
const api = useApi();

const showCreateModal = ref(false);
const newProject = ref({
    name: "",
    description: "",
    color: "#6366f1",
    organization_id: "",
});
const error = ref("");
const creating = ref(false);
const organizations = ref([]);

const adminOrganizations = computed(() => {
    return organizations.value.filter((org) => org.role === "admin");
});

async function fetchOrganizations() {
    try {
        const response = await api.get("/organizations");
        organizations.value = response.data;
    } catch (err) {
        console.error("Failed to fetch organizations:", err);
    }
}

onMounted(() => {
    projectsStore.fetchProjects();
    fetchOrganizations();
});

async function handleCreate() {
    if (!newProject.value.name.trim()) {
        error.value = "Project name is required";
        return;
    }

    error.value = "";
    creating.value = true;

    try {
        const data = { ...newProject.value };
        if (!data.organization_id) {
            delete data.organization_id;
        }
        await projectsStore.createProject(data);
        showCreateModal.value = false;
        newProject.value = {
            name: "",
            description: "",
            color: "#6366f1",
            organization_id: "",
        };
    } catch (err) {
        error.value = err.message;
    } finally {
        creating.value = false;
    }
}

const colors = [
    "#6366f1",
    "#8b5cf6",
    "#ec4899",
    "#ef4444",
    "#f97316",
    "#eab308",
    "#22c55e",
    "#14b8a6",
    "#06b6d4",
    "#3b82f6",
];
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Projects</h1>
            <button @click="showCreateModal = true" class="btn-primary">
                <svg
                    class="w-5 h-5 mr-2"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>
                New Project
            </button>
        </div>

        <div v-if="projectsStore.loading" class="text-center py-12">
            <p class="text-gray-500">Loading projects...</p>
        </div>

        <div v-else-if="!projectsStore.projects.length" class="card">
            <div class="card-body text-center py-12">
                <svg
                    class="w-16 h-16 mx-auto text-gray-400 mb-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
                    />
                </svg>
                <h3 class="text-lg font-medium mb-2">No projects yet</h3>
                <p class="text-gray-500 mb-4">
                    Create your first project to start tracking time
                </p>
                <button @click="showCreateModal = true" class="btn-primary">
                    Create Project
                </button>
            </div>
        </div>

        <div
            v-else
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
        >
            <router-link
                v-for="project in projectsStore.projects"
                :key="project.id"
                :to="`/projects/${project.id}`"
                class="card hover:shadow-md transition-shadow"
            >
                <div class="card-body">
                    <div class="flex items-start gap-3">
                        <div
                            class="w-4 h-4 rounded-full mt-1 flex-shrink-0"
                            :style="{ backgroundColor: project.color }"
                        ></div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold truncate">
                                {{ project.name }}
                            </h3>
                            <p
                                v-if="project.description"
                                class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mt-1"
                            >
                                {{ project.description }}
                            </p>
                            <div
                                class="flex items-center gap-4 mt-3 text-sm text-gray-500 dark:text-gray-400"
                            >
                                <span
                                    v-if="project.user_role"
                                    class="capitalize"
                                    >{{ project.user_role }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </router-link>
        </div>

        <!-- Create Project Modal -->
        <Modal
            v-if="showCreateModal"
            title="New Project"
            @close="showCreateModal = false"
        >
            <form @submit.prevent="handleCreate" class="space-y-4">
                <div
                    v-if="error"
                    class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-400 text-sm"
                >
                    {{ error }}
                </div>

                <div>
                    <label class="label">Name</label>
                    <input
                        v-model="newProject.name"
                        type="text"
                        class="input"
                        placeholder="Project name"
                        required
                    />
                </div>

                <div>
                    <label class="label">Description (optional)</label>
                    <textarea
                        v-model="newProject.description"
                        class="input"
                        rows="3"
                        placeholder="Brief description of the project"
                    ></textarea>
                </div>

                <div>
                    <label class="label">Color</label>
                    <div class="flex gap-2 flex-wrap">
                        <button
                            v-for="color in colors"
                            :key="color"
                            type="button"
                            @click="newProject.color = color"
                            class="w-8 h-8 rounded-full transition-transform hover:scale-110"
                            :class="{
                                'ring-2 ring-offset-2 ring-gray-400':
                                    newProject.color === color,
                            }"
                            :style="{ backgroundColor: color }"
                        ></button>
                    </div>
                </div>

                <div v-if="adminOrganizations.length > 0">
                    <label class="label">Organization (optional)</label>
                    <select v-model="newProject.organization_id" class="input">
                        <option value="">Personal project</option>
                        <option
                            v-for="org in adminOrganizations"
                            :key="org.id"
                            :value="org.id"
                        >
                            {{ org.name }}
                        </option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        Only organizations where you are admin are shown
                    </p>
                </div>
            </form>

            <template #footer>
                <button @click="showCreateModal = false" class="btn-secondary">
                    Cancel
                </button>
                <button
                    @click="handleCreate"
                    :disabled="creating"
                    class="btn-primary"
                >
                    {{ creating ? "Creating..." : "Create Project" }}
                </button>
            </template>
        </Modal>
    </div>
</template>
