<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useApi } from "../composables/useApi";
import { useProjectsStore } from "../stores/projects";
import { useTimerStore } from "../stores/timer";

const route = useRoute();
const router = useRouter();
const api = useApi();
const projectsStore = useProjectsStore();
const timerStore = useTimerStore();

const entry = ref(null);
const loading = ref(true);
const saving = ref(false);
const error = ref("");

const isEditing = ref(false);
const editForm = ref({
    project_id: "",
    title: "",
    description: "",
    started_at: "",
    ended_at: "",
});

const isActive = computed(() => entry.value && !entry.value.ended_at);

function formatDateTimeLocal(dateStr) {
    if (!dateStr) return "";
    const date = new Date(dateStr);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    const hours = String(date.getHours()).padStart(2, "0");
    const minutes = String(date.getMinutes()).padStart(2, "0");
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

function formatDateTime(dateStr) {
    if (!dateStr) return "—";
    return new Date(dateStr).toLocaleString("en-US", {
        weekday: "short",
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function formatDuration(seconds) {
    if (!seconds || seconds < 0) return "0m";
    const hrs = Math.floor(seconds / 3600);
    const mins = Math.floor((seconds % 3600) / 60);
    const secs = Math.floor(seconds % 60);
    if (hrs > 0) {
        return `${hrs}h ${mins}m ${secs}s`;
    }
    if (mins > 0) {
        return `${mins}m ${secs}s`;
    }
    return `${secs}s`;
}

const duration = computed(() => {
    if (!entry.value) return 0;
    const start = new Date(entry.value.started_at);
    const end = entry.value.ended_at ? new Date(entry.value.ended_at) : new Date();
    return Math.floor((end - start) / 1000);
});

async function fetchEntry() {
    loading.value = true;
    error.value = "";
    try {
        const response = await api.get(`/work-entries/${route.params.id}`);
        entry.value = response.data;
    } catch (err) {
        error.value = err.message;
    } finally {
        loading.value = false;
    }
}

function startEditing() {
    editForm.value = {
        project_id: entry.value.project_id,
        title: entry.value.title,
        description: entry.value.description || "",
        started_at: formatDateTimeLocal(entry.value.started_at),
        ended_at: formatDateTimeLocal(entry.value.ended_at),
    };
    isEditing.value = true;
}

function cancelEditing() {
    isEditing.value = false;
    editForm.value = {
        project_id: "",
        title: "",
        description: "",
        started_at: "",
        ended_at: "",
    };
}

async function saveChanges() {
    if (!editForm.value.project_id || !editForm.value.title.trim()) {
        error.value = "Please select a project and enter a title";
        return;
    }

    saving.value = true;
    error.value = "";
    try {
        await api.put(`/work-entries/${entry.value.id}`, {
            project_id: editForm.value.project_id,
            title: editForm.value.title.trim(),
            description: editForm.value.description.trim() || null,
            started_at: new Date(editForm.value.started_at).toISOString(),
            ended_at: editForm.value.ended_at
                ? new Date(editForm.value.ended_at).toISOString()
                : null,
        });
        await fetchEntry();
        // Refresh timer if this was the active entry
        await timerStore.fetchCurrent();
        isEditing.value = false;
    } catch (err) {
        error.value = err.message;
    } finally {
        saving.value = false;
    }
}

async function deleteEntry() {
    if (!confirm("Are you sure you want to delete this entry?")) return;

    saving.value = true;
    try {
        await api.del(`/work-entries/${entry.value.id}`);
        router.push({ name: "entries" });
    } catch (err) {
        error.value = err.message;
        saving.value = false;
    }
}

async function stopTimer() {
    if (!confirm("Stop the timer for this entry?")) return;

    saving.value = true;
    try {
        await timerStore.stop();
        await fetchEntry();
    } catch (err) {
        error.value = err.message;
    } finally {
        saving.value = false;
    }
}

onMounted(async () => {
    await projectsStore.fetchProjects();
    await fetchEntry();
});
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <button
                @click="router.push({ name: 'entries' })"
                class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
            >
                <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
            </button>
            <h1 class="text-2xl font-bold">Work Entry Details</h1>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="card">
            <div class="card-body text-center py-12">
                <p class="text-gray-500">Loading...</p>
            </div>
        </div>

        <!-- Error -->
        <div
            v-else-if="error && !entry"
            class="card bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800"
        >
            <div class="card-body text-center py-12">
                <p class="text-red-600 dark:text-red-400">{{ error }}</p>
                <button @click="fetchEntry" class="btn-primary mt-4">
                    Try Again
                </button>
            </div>
        </div>

        <!-- Entry Details -->
        <template v-else-if="entry">
            <!-- Error message -->
            <div
                v-if="error"
                class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-400"
            >
                {{ error }}
            </div>

            <!-- Active indicator -->
            <div
                v-if="isActive"
                class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg flex items-center justify-between"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="w-3 h-3 rounded-full bg-amber-500 animate-pulse"
                    ></div>
                    <span class="text-amber-700 dark:text-amber-300 font-medium"
                        >Timer is running</span
                    >
                </div>
                <button
                    @click="stopTimer"
                    :disabled="saving"
                    class="btn-secondary text-sm"
                >
                    Stop Timer
                </button>
            </div>

            <!-- View Mode -->
            <div v-if="!isEditing" class="card">
                <div class="card-header flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-4 h-4 rounded-full"
                            :style="{
                                backgroundColor: entry.project_color,
                            }"
                        ></div>
                        <span class="text-gray-500 dark:text-gray-400">{{
                            entry.project_name
                        }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="startEditing" class="btn-secondary">
                            Edit
                        </button>
                        <button
                            @click="deleteEntry"
                            :disabled="saving"
                            class="btn-danger"
                        >
                            Delete
                        </button>
                    </div>
                </div>

                <div class="card-body space-y-6">
                    <!-- Title -->
                    <div>
                        <h2 class="text-xl font-semibold">{{ entry.title }}</h2>
                    </div>

                    <!-- Description -->
                    <div v-if="entry.description">
                        <label class="label">Description</label>
                        <p
                            class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap"
                        >
                            {{ entry.description }}
                        </p>
                    </div>

                    <!-- Times -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="label">Started</label>
                            <p class="font-medium">
                                {{ formatDateTime(entry.started_at) }}
                            </p>
                        </div>
                        <div>
                            <label class="label">Ended</label>
                            <p class="font-medium">
                                {{
                                    entry.ended_at
                                        ? formatDateTime(entry.ended_at)
                                        : "In progress..."
                                }}
                            </p>
                        </div>
                        <div>
                            <label class="label">Duration</label>
                            <p class="font-medium font-mono text-lg">
                                {{ formatDuration(duration) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Mode -->
            <div v-else class="card">
                <div class="card-header">
                    <h2 class="font-semibold">Edit Entry</h2>
                </div>

                <div class="card-body">
                    <form @submit.prevent="saveChanges" class="space-y-4">
                        <div>
                            <label class="label">Project</label>
                            <select
                                v-model="editForm.project_id"
                                class="input"
                                required
                            >
                                <option value="">Select a project</option>
                                <option
                                    v-for="project in projectsStore.projects"
                                    :key="project.id"
                                    :value="project.id"
                                >
                                    {{ project.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="label">Title</label>
                            <input
                                v-model="editForm.title"
                                type="text"
                                class="input"
                                placeholder="What did you work on?"
                                required
                            />
                        </div>

                        <div>
                            <label class="label"
                                >Description
                                <span class="text-gray-400 font-normal"
                                    >(optional)</span
                                ></label
                            >
                            <textarea
                                v-model="editForm.description"
                                class="input"
                                rows="4"
                                placeholder="Add details about what you worked on..."
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Start Time</label>
                                <input
                                    v-model="editForm.started_at"
                                    type="datetime-local"
                                    class="input"
                                    required
                                />
                            </div>
                            <div>
                                <label class="label"
                                    >End Time
                                    <span class="text-gray-400 font-normal"
                                        >(leave empty for active)</span
                                    ></label
                                >
                                <input
                                    v-model="editForm.ended_at"
                                    type="datetime-local"
                                    class="input"
                                />
                            </div>
                        </div>

                        <p
                            v-if="!editForm.ended_at"
                            class="text-sm text-amber-600 dark:text-amber-400"
                        >
                            Leaving end time empty will make this an active
                            entry (timer running)
                        </p>

                        <div class="flex justify-end gap-3 pt-4">
                            <button
                                type="button"
                                @click="cancelEditing"
                                class="btn-secondary"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="saving"
                                class="btn-primary"
                            >
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</template>
