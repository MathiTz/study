<script setup>
import { ref, onMounted } from "vue";
import { useTimerStore } from "../../stores/timer";
import { useProjectsStore } from "../../stores/projects";
import TimerControls from "./TimerControls.vue";

const timerStore = useTimerStore();
const projectsStore = useProjectsStore();

const showStartForm = ref(false);
const showTransitionForm = ref(false);
const selectedProject = ref("");
const title = ref("");
const error = ref("");

onMounted(async () => {
    await projectsStore.fetchProjects();
});

async function handleStart() {
    if (!selectedProject.value || !title.value.trim()) {
        error.value = "Please select a project and enter a title";
        return;
    }

    error.value = "";
    try {
        await timerStore.start(selectedProject.value, title.value.trim());
        showStartForm.value = false;
        selectedProject.value = "";
        title.value = "";
    } catch (err) {
        error.value = err.message;
    }
}

async function handleTransition() {
    if (!selectedProject.value || !title.value.trim()) {
        error.value = "Please select a project and enter a title";
        return;
    }

    error.value = "";
    try {
        await timerStore.transition(selectedProject.value, title.value.trim());
        showTransitionForm.value = false;
        selectedProject.value = "";
        title.value = "";
    } catch (err) {
        error.value = err.message;
    }
}

function openStartForm() {
    showStartForm.value = true;
}

function openTransitionForm() {
    selectedProject.value = timerStore.timer?.project_id || "";
    title.value = "";
    error.value = "";
    showTransitionForm.value = true;
}

function cancelTransitionForm() {
    showTransitionForm.value = false;
    selectedProject.value = "";
    title.value = "";
    error.value = "";
}
</script>

<template>
    <div class="card">
        <div class="card-body">
            <div v-if="timerStore.hasActiveTimer && !showTransitionForm">
                <div class="text-center mb-4">
                    <div class="timer-display mb-2">
                        {{ timerStore.formatTime(timerStore.elapsedSeconds) }}
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ timerStore.timer?.title }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">
                        {{ timerStore.timer?.project_name }}
                    </p>
                </div>
                <TimerControls />
                <div
                    class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700"
                >
                    <button
                        @click="openTransitionForm"
                        class="btn-secondary w-full text-sm"
                    >
                        <svg
                            class="w-4 h-4 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 5l7 7-7 7M5 5l7 7-7 7"
                            />
                        </svg>
                        Continue with new task
                    </button>
                </div>
            </div>

            <div v-else-if="showTransitionForm" class="space-y-4">
                <h3 class="font-semibold">Continue with new task</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    This will stop the current timer and start a new one
                    immediately.
                </p>

                <div
                    v-if="error"
                    class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-400 text-sm"
                >
                    {{ error }}
                </div>

                <div>
                    <label class="label">Project</label>
                    <select v-model="selectedProject" class="input">
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
                        v-model="title"
                        type="text"
                        class="input"
                        placeholder="Task description"
                        @keyup.enter="handleTransition"
                    />
                </div>

                <div class="flex gap-2">
                    <button
                        @click="handleTransition"
                        class="btn-primary flex-1"
                    >
                        Switch Task
                    </button>
                    <button @click="cancelTransitionForm" class="btn-secondary">
                        Cancel
                    </button>
                </div>
            </div>

            <div v-else-if="showStartForm" class="space-y-4">
                <h3 class="font-semibold">Start Timer</h3>

                <div
                    v-if="error"
                    class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-400 text-sm"
                >
                    {{ error }}
                </div>

                <div>
                    <label class="label">Project</label>
                    <select v-model="selectedProject" class="input">
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
                        v-model="title"
                        type="text"
                        class="input"
                        placeholder="Task description"
                        @keyup.enter="handleStart"
                    />
                </div>

                <div class="flex gap-2">
                    <button @click="handleStart" class="btn-primary flex-1">
                        Start
                    </button>
                    <button
                        @click="showStartForm = false"
                        class="btn-secondary"
                    >
                        Cancel
                    </button>
                </div>
            </div>

            <div v-else class="text-center py-4">
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    No timer running
                </p>
                <button @click="openStartForm" class="btn-primary">
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
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    Start Timer
                </button>
            </div>
        </div>
    </div>
</template>
