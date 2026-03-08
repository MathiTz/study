<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useApi } from "../composables/useApi";
import { useProjectsStore } from "../stores/projects";
import Modal from "../components/common/Modal.vue";

const router = useRouter();
const api = useApi();
const projectsStore = useProjectsStore();

const entries = ref([]);
const loading = ref(true);
const showManualEntry = ref(false);
const selectedEntry = ref(null);

const filters = ref({
    project_id: "",
    start_date: "",
    end_date: "",
});

const manualEntry = ref({
    project_id: "",
    title: "",
    started_at: "",
    ended_at: "",
});

onMounted(async () => {
    await projectsStore.fetchProjects();
    await fetchEntries();
});

async function fetchEntries() {
    loading.value = true;
    try {
        const params = { limit: 50 };
        if (filters.value.project_id)
            params.project_id = filters.value.project_id;
        if (filters.value.start_date)
            params.start_date = filters.value.start_date;
        if (filters.value.end_date) params.end_date = filters.value.end_date;

        const response = await api.get("/work-entries", params);
        entries.value = response.data;
    } catch (err) {
        console.error("Failed to fetch entries:", err);
    } finally {
        loading.value = false;
    }
}

async function handleManualSubmit() {
    try {
        await api.post("/work-entries", {
            ...manualEntry.value,
            started_at: new Date(manualEntry.value.started_at).toISOString(),
            ended_at: new Date(manualEntry.value.ended_at).toISOString(),
        });
        showManualEntry.value = false;
        manualEntry.value = {
            project_id: "",
            title: "",
            started_at: "",
            ended_at: "",
        };
        await fetchEntries();
    } catch (err) {
        alert(err.message);
    }
}

async function handleDelete(id) {
    if (!confirm("Are you sure you want to delete this entry?")) return;

    try {
        await api.del(`/work-entries/${id}`);
        entries.value = entries.value.filter((e) => e.id !== id);
    } catch (err) {
        alert(err.message);
    }
}

function formatDuration(seconds) {
    const hrs = Math.floor(seconds / 3600);
    const mins = Math.floor((seconds % 3600) / 60);
    if (hrs > 0) {
        return `${hrs}h ${mins}m`;
    }
    return `${mins}m`;
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString("en-US", {
        weekday: "short",
        month: "short",
        day: "numeric",
    });
}

function formatTime(dateStr) {
    return new Date(dateStr).toLocaleTimeString("en-US", {
        hour: "2-digit",
        minute: "2-digit",
    });
}

const groupedEntries = computed(() => {
    const groups = {};
    for (const entry of entries.value) {
        const date = formatDate(entry.started_at);
        if (!groups[date]) {
            groups[date] = [];
        }
        groups[date].push(entry);
    }
    return groups;
});
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Work Entries</h1>
            <button @click="showManualEntry = true" class="btn-primary">
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
                Add Manual Entry
            </button>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="label">Project</label>
                        <select
                            v-model="filters.project_id"
                            @change="fetchEntries"
                            class="input"
                        >
                            <option value="">All Projects</option>
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
                        <label class="label">From</label>
                        <input
                            v-model="filters.start_date"
                            type="date"
                            class="input"
                            @change="fetchEntries"
                        />
                    </div>
                    <div>
                        <label class="label">To</label>
                        <input
                            v-model="filters.end_date"
                            type="date"
                            class="input"
                            @change="fetchEntries"
                        />
                    </div>
                    <div class="flex items-end">
                        <button
                            @click="
                                filters = {
                                    project_id: '',
                                    start_date: '',
                                    end_date: '',
                                };
                                fetchEntries();
                            "
                            class="btn-secondary"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Entries List -->
        <div v-if="loading" class="text-center py-12">
            <p class="text-gray-500">Loading entries...</p>
        </div>

        <div v-else-if="!entries.length" class="card">
            <div class="card-body text-center py-12">
                <p class="text-gray-500">No entries found</p>
            </div>
        </div>

        <div v-else class="space-y-6">
            <div
                v-for="(dayEntries, date) in groupedEntries"
                :key="date"
                class="card"
            >
                <div class="card-header">
                    <h2 class="font-semibold">{{ date }}</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <div
                        v-for="entry in dayEntries"
                        :key="entry.id"
                        class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors"
                        @click="
                            router.push({
                                name: 'entry',
                                params: { id: entry.id },
                            })
                        "
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-3 h-3 rounded-full"
                                    :style="{
                                        backgroundColor: entry.project_color,
                                    }"
                                ></div>
                                <div>
                                    <p class="font-medium">{{ entry.title }}</p>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        {{ entry.project_name }} &middot;
                                        {{ formatTime(entry.started_at) }}
                                        <template v-if="entry.ended_at">
                                            - {{ formatTime(entry.ended_at) }}
                                        </template>
                                        <template v-else>
                                            <span
                                                class="ml-2 text-xs bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 px-2 py-0.5 rounded"
                                            >
                                                active
                                            </span>
                                        </template>
                                        <span
                                            v-if="entry.is_manual"
                                            class="ml-2 text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded"
                                        >
                                            manual
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="font-mono">
                                    {{ formatDuration(entry.duration_seconds) }}
                                </span>
                                <button
                                    @click.stop="handleDelete(entry.id)"
                                    class="text-gray-400 hover:text-red-500 transition-colors"
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
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manual Entry Modal -->
        <Modal
            v-if="showManualEntry"
            title="Add Manual Entry"
            @close="showManualEntry = false"
        >
            <form @submit.prevent="handleManualSubmit" class="space-y-4">
                <div>
                    <label class="label">Project</label>
                    <select
                        v-model="manualEntry.project_id"
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
                        v-model="manualEntry.title"
                        type="text"
                        class="input"
                        placeholder="What did you work on?"
                        required
                    />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Start Time</label>
                        <input
                            v-model="manualEntry.started_at"
                            type="datetime-local"
                            class="input"
                            required
                        />
                    </div>
                    <div>
                        <label class="label">End Time</label>
                        <input
                            v-model="manualEntry.ended_at"
                            type="datetime-local"
                            class="input"
                            required
                        />
                    </div>
                </div>
            </form>

            <template #footer>
                <button @click="showManualEntry = false" class="btn-secondary">
                    Cancel
                </button>
                <button @click="handleManualSubmit" class="btn-primary">
                    Add Entry
                </button>
            </template>
        </Modal>
    </div>
</template>
