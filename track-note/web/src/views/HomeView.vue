<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useApi } from "../composables/useApi";
import { useTimerStore } from "../stores/timer";
import { useProjectsStore } from "../stores/projects";
import TimerBar from "../components/timer/TimerBar.vue";
import DayCalendar from "../components/calendar/DayCalendar.vue";
import Modal from "../components/common/Modal.vue";

const api = useApi();
const timerStore = useTimerStore();
const projectsStore = useProjectsStore();

const entries = ref([]);
const loading = ref(true);

// Timeline view settings
const daysToShow = ref(1);
const daysOptions = [
    { value: 1, label: "Today" },
    { value: 3, label: "3 Days" },
    { value: 7, label: "Week" },
];

// Active entry modal (edit current or transition)
const showActiveModal = ref(false);
const activeModalMode = ref("edit"); // 'edit' or 'transition'
const activeForm = ref({
    project_id: "",
    title: "",
    description: "",
});
const transitionForm = ref({
    project_id: "",
    title: "",
});
const activeError = ref("");
const activeLoading = ref(false);

// Edit modal (for completed entries)
const showEditModal = ref(false);
const editingEntry = ref(null);
const editForm = ref({
    project_id: "",
    title: "",
    description: "",
    started_at: "",
    ended_at: "",
});
const editError = ref("");
const editLoading = ref(false);

// New entry modal (for creating manual entries)
const showNewEntryModal = ref(false);
const newEntryForm = ref({
    project_id: "",
    title: "",
    description: "",
    started_at: "",
    ended_at: "",
});
const newEntryError = ref("");
const newEntryLoading = ref(false);

function openNewEntryModal(startTime) {
    newEntryForm.value = {
        project_id: "",
        title: "",
        description: "",
        started_at: formatDateTimeLocal(startTime),
        ended_at: "",
    };
    newEntryError.value = "";
    showNewEntryModal.value = true;
}

function closeNewEntryModal() {
    showNewEntryModal.value = false;
    newEntryForm.value = {
        project_id: "",
        title: "",
        description: "",
        started_at: "",
        ended_at: "",
    };
    newEntryError.value = "";
}

async function handleNewEntrySave() {
    if (!newEntryForm.value.project_id || !newEntryForm.value.title.trim()) {
        newEntryError.value = "Please select a project and enter a title";
        return;
    }

    if (!newEntryForm.value.started_at) {
        newEntryError.value = "Please set a start time";
        return;
    }

    newEntryError.value = "";
    newEntryLoading.value = true;
    try {
        await api.post("/work-entries", {
            project_id: newEntryForm.value.project_id,
            title: newEntryForm.value.title.trim(),
            description: newEntryForm.value.description.trim() || null,
            started_at: new Date(newEntryForm.value.started_at).toISOString(),
            ended_at: newEntryForm.value.ended_at
                ? new Date(newEntryForm.value.ended_at).toISOString()
                : null,
        });
        closeNewEntryModal();
        await fetchEntries();
        // Refresh timer in case we created an active entry
        await timerStore.fetchCurrent();
    } catch (err) {
        newEntryError.value = err.message;
    } finally {
        newEntryLoading.value = false;
    }
}

function handleEmptyClick(clickedTime) {
    openNewEntryModal(clickedTime);
}

// Calculate start date based on days to show
// For "Week" view, start from beginning of the week that contains today
// For other views, start from (today - days + 1) so today is the last day
const startDate = computed(() => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (daysToShow.value === 7) {
        // Start from Monday of current week
        const dayOfWeek = today.getDay();
        const diff = dayOfWeek === 0 ? -6 : 1 - dayOfWeek; // Monday = 1
        const monday = new Date(today);
        monday.setDate(today.getDate() + diff);
        return monday;
    }

    // For other views, show past days leading up to today
    const start = new Date(today);
    start.setDate(today.getDate() - (daysToShow.value - 1));
    return start;
});

const formattedDate = computed(() => {
    const today = new Date();
    if (daysToShow.value === 1) {
        return today.toLocaleDateString("en-US", {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric",
        });
    }

    const endDate = new Date(startDate.value);
    endDate.setDate(endDate.getDate() + daysToShow.value - 1);

    return `${startDate.value.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
    })} - ${endDate.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
    })}`;
});

function formatDateForApi(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
}

function formatDateTimeLocal(dateStr) {
    const date = new Date(dateStr);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    const hours = String(date.getHours()).padStart(2, "0");
    const minutes = String(date.getMinutes()).padStart(2, "0");
    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

async function fetchEntries() {
    loading.value = true;
    try {
        const endDate = new Date(startDate.value);
        endDate.setDate(endDate.getDate() + daysToShow.value - 1);

        // Also fetch entries that might have started before the view but end within it
        const fetchStartDate = new Date(startDate.value);
        fetchStartDate.setDate(fetchStartDate.getDate() - 7); // Look back 7 days for cross-day entries

        const response = await api.get("/work-entries", {
            start_date: formatDateForApi(fetchStartDate),
            end_date: formatDateForApi(endDate) + "T23:59:59",
            limit: 200,
        });
        entries.value = response.data;
    } catch (err) {
        console.error("Failed to fetch entries:", err);
    } finally {
        loading.value = false;
    }
}

// Refetch when days change
watch(daysToShow, () => {
    fetchEntries();
});

// Active entry modal functions
function openActiveModal(mode = "edit") {
    activeModalMode.value = mode;
    if (timerStore.timer) {
        activeForm.value = {
            project_id: timerStore.timer.project_id,
            title: timerStore.timer.title,
            description: timerStore.timer.description || "",
        };
        transitionForm.value = {
            project_id: timerStore.timer.project_id,
            title: "",
        };
    }
    activeError.value = "";
    showActiveModal.value = true;
}

function closeActiveModal() {
    showActiveModal.value = false;
    activeForm.value = { project_id: "", title: "", description: "" };
    transitionForm.value = { project_id: "", title: "" };
    activeError.value = "";
}

async function handleActiveEditSave() {
    if (!activeForm.value.project_id || !activeForm.value.title.trim()) {
        activeError.value = "Please select a project and enter a title";
        return;
    }

    activeError.value = "";
    activeLoading.value = true;
    try {
        // Update the work entry associated with the active timer
        await api.put(`/work-entries/${timerStore.timer.work_entry_id}`, {
            project_id: activeForm.value.project_id,
            title: activeForm.value.title.trim(),
            description: activeForm.value.description.trim() || null,
        });
        // Refresh timer to get updated data
        await timerStore.fetchCurrent();
        closeActiveModal();
    } catch (err) {
        activeError.value = err.message;
    } finally {
        activeLoading.value = false;
    }
}

async function handleTransition() {
    if (
        !transitionForm.value.project_id ||
        !transitionForm.value.title.trim()
    ) {
        activeError.value = "Please select a project and enter a title";
        return;
    }

    activeError.value = "";
    activeLoading.value = true;
    try {
        await timerStore.transition(
            transitionForm.value.project_id,
            transitionForm.value.title.trim(),
        );
        closeActiveModal();
        await fetchEntries();
    } catch (err) {
        activeError.value = err.message;
    } finally {
        activeLoading.value = false;
    }
}

// Edit modal functions (for completed entries)
function openEditModal(entry) {
    editingEntry.value = entry;
    editForm.value = {
        project_id: entry.project_id,
        title: entry.title,
        description: entry.description || "",
        started_at: formatDateTimeLocal(entry.started_at),
        ended_at: entry.ended_at ? formatDateTimeLocal(entry.ended_at) : "",
    };
    editError.value = "";
    showEditModal.value = true;
}

function closeEditModal() {
    showEditModal.value = false;
    editingEntry.value = null;
    editForm.value = {
        project_id: "",
        title: "",
        description: "",
        started_at: "",
        ended_at: "",
    };
    editError.value = "";
}

async function handleEditSave() {
    if (!editForm.value.project_id || !editForm.value.title.trim()) {
        editError.value = "Please select a project and enter a title";
        return;
    }

    editError.value = "";
    editLoading.value = true;
    try {
        await api.put(`/work-entries/${editingEntry.value.id}`, {
            project_id: editForm.value.project_id,
            title: editForm.value.title.trim(),
            description: editForm.value.description.trim() || null,
            started_at: new Date(editForm.value.started_at).toISOString(),
            ended_at: editForm.value.ended_at
                ? new Date(editForm.value.ended_at).toISOString()
                : null,
        });
        closeEditModal();
        await fetchEntries();
    } catch (err) {
        editError.value = err.message;
    } finally {
        editLoading.value = false;
    }
}

async function handleDelete() {
    if (!confirm("Are you sure you want to delete this entry?")) return;

    editLoading.value = true;
    try {
        await api.del(`/work-entries/${editingEntry.value.id}`);
        closeEditModal();
        await fetchEntries();
    } catch (err) {
        editError.value = err.message;
    } finally {
        editLoading.value = false;
    }
}

function handleEntryClick(entry) {
    if (!entry.ended_at && timerStore.hasActiveTimer) {
        // Active entry - open modal with edit/transition options
        openActiveModal("edit");
    } else if (entry.ended_at) {
        // Completed entry - open edit modal
        openEditModal(entry);
    }
}

watch(
    () => timerStore.hasActiveTimer,
    (hasTimer, hadTimer) => {
        if (hadTimer && !hasTimer) {
            fetchEntries();
        }
    },
);

onMounted(async () => {
    await projectsStore.fetchProjects();
    await fetchEntries();
});
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">
                    {{ daysToShow === 1 ? "Today" : "Timeline" }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400">
                    {{ formattedDate }}
                </p>
            </div>
        </div>

        <!-- Timer Bar -->
        <TimerBar @transition-click="openActiveModal('transition')" />

        <!-- Calendar -->
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h2 class="font-semibold">Timeline</h2>
                <div class="flex items-center gap-4">
                    <!-- Days filter -->
                    <div
                        class="flex rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
                    >
                        <button
                            v-for="option in daysOptions"
                            :key="option.value"
                            @click="daysToShow = option.value"
                            class="px-3 py-1 text-sm transition-colors"
                            :class="
                                daysToShow === option.value
                                    ? 'bg-primary-500 text-white'
                                    : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
                            "
                        >
                            {{ option.label }}
                        </button>
                    </div>

                    <!-- Legend -->
                    <div class="flex items-center gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-red-500"></div>
                            <span class="text-gray-500 dark:text-gray-400"
                                >Now</span
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="w-2 h-2 rounded bg-indigo-500 animate-pulse"
                            ></div>
                            <span class="text-gray-500 dark:text-gray-400"
                                >Active</span
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="w-2 h-2 rounded bg-gray-500 opacity-70"
                            ></div>
                            <span class="text-gray-500 dark:text-gray-400"
                                >Past</span
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div v-if="loading" class="text-center py-12">
                    <p class="text-gray-500">Loading...</p>
                </div>

                <DayCalendar
                    v-else
                    :start-date="startDate"
                    :days-to-show="daysToShow"
                    :entries="entries"
                    :current-timer="timerStore.timer"
                    :elapsed-seconds="timerStore.elapsedSeconds"
                    @entry-click="handleEntryClick"
                    @empty-click="handleEmptyClick"
                />
            </div>
        </div>

        <!-- Active Entry Modal -->
        <Modal
            v-if="showActiveModal"
            :title="
                activeModalMode === 'edit' ? 'Edit Current Task' : 'Switch Task'
            "
            size="lg"
            @close="closeActiveModal"
        >
            <!-- Tabs -->
            <div
                class="flex border-b border-gray-200 dark:border-gray-700 mb-4 -mt-2"
            >
                <button
                    @click="activeModalMode = 'edit'"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
                    :class="
                        activeModalMode === 'edit'
                            ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                            : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'
                    "
                >
                    Edit Current
                </button>
                <button
                    @click="activeModalMode = 'transition'"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
                    :class="
                        activeModalMode === 'transition'
                            ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                            : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'
                    "
                >
                    Switch to New Task
                </button>
            </div>

            <div
                v-if="activeError"
                class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-400 text-sm"
            >
                {{ activeError }}
            </div>

            <!-- Edit Current Form -->
            <form
                v-if="activeModalMode === 'edit'"
                @submit.prevent="handleActiveEditSave"
                class="space-y-4"
            >
                <div>
                    <label class="label">Project</label>
                    <select
                        v-model="activeForm.project_id"
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
                        v-model="activeForm.title"
                        type="text"
                        class="input"
                        placeholder="What are you working on?"
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
                        v-model="activeForm.description"
                        class="input"
                        rows="3"
                        placeholder="Add details about what you're working on..."
                    ></textarea>
                </div>
            </form>

            <!-- Transition Form -->
            <form v-else @submit.prevent="handleTransition" class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    This will stop the current timer and start a new one
                    immediately.
                </p>

                <div>
                    <label class="label">Project</label>
                    <select
                        v-model="transitionForm.project_id"
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
                    <label class="label">What are you working on?</label>
                    <input
                        v-model="transitionForm.title"
                        type="text"
                        class="input"
                        placeholder="Task description"
                        required
                    />
                </div>
            </form>

            <template #footer>
                <button @click="closeActiveModal" class="btn-secondary">
                    Cancel
                </button>
                <button
                    v-if="activeModalMode === 'edit'"
                    @click="handleActiveEditSave"
                    :disabled="
                        activeLoading ||
                        !activeForm.project_id ||
                        !activeForm.title.trim()
                    "
                    class="btn-primary"
                >
                    Save Changes
                </button>
                <button
                    v-else
                    @click="handleTransition"
                    :disabled="
                        activeLoading ||
                        !transitionForm.project_id ||
                        !transitionForm.title.trim()
                    "
                    class="btn-primary"
                >
                    Switch Task
                </button>
            </template>
        </Modal>

        <!-- Edit Completed Entry Modal -->
        <Modal
            v-if="showEditModal"
            title="Edit Entry"
            size="lg"
            @close="closeEditModal"
        >
            <div
                v-if="editError"
                class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-400 text-sm"
            >
                {{ editError }}
            </div>

            <form @submit.prevent="handleEditSave" class="space-y-4">
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
                        rows="3"
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
                        <label class="label">End Time</label>
                        <input
                            v-model="editForm.ended_at"
                            type="datetime-local"
                            class="input"
                            required
                        />
                    </div>
                </div>
            </form>

            <template #footer>
                <button
                    @click="handleDelete"
                    :disabled="editLoading"
                    class="btn-danger mr-auto"
                >
                    Delete
                </button>
                <button @click="closeEditModal" class="btn-secondary">
                    Cancel
                </button>
                <button
                    @click="handleEditSave"
                    :disabled="
                        editLoading ||
                        !editForm.project_id ||
                        !editForm.title.trim()
                    "
                    class="btn-primary"
                >
                    Save Changes
                </button>
            </template>
        </Modal>

        <!-- New Entry Modal -->
        <Modal
            v-if="showNewEntryModal"
            title="New Entry"
            size="lg"
            @close="closeNewEntryModal"
        >
            <div
                v-if="newEntryError"
                class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-400 text-sm"
            >
                {{ newEntryError }}
            </div>

            <form @submit.prevent="handleNewEntrySave" class="space-y-4">
                <div>
                    <label class="label">Project</label>
                    <select
                        v-model="newEntryForm.project_id"
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
                        v-model="newEntryForm.title"
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
                        v-model="newEntryForm.description"
                        class="input"
                        rows="3"
                        placeholder="Add details about what you worked on..."
                    ></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Start Time</label>
                        <input
                            v-model="newEntryForm.started_at"
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
                            v-model="newEntryForm.ended_at"
                            type="datetime-local"
                            class="input"
                        />
                    </div>
                </div>

                <p
                    v-if="!newEntryForm.ended_at"
                    class="text-sm text-amber-600 dark:text-amber-400"
                >
                    This will create an active entry (timer running)
                </p>
            </form>

            <template #footer>
                <button @click="closeNewEntryModal" class="btn-secondary">
                    Cancel
                </button>
                <button
                    @click="handleNewEntrySave"
                    :disabled="
                        newEntryLoading ||
                        !newEntryForm.project_id ||
                        !newEntryForm.title.trim() ||
                        !newEntryForm.started_at
                    "
                    class="btn-primary"
                >
                    {{ newEntryForm.ended_at ? "Create Entry" : "Start Timer" }}
                </button>
            </template>
        </Modal>
    </div>
</template>
