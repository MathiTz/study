<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";

const props = defineProps({
    startDate: {
        type: Date,
        default: () => new Date(),
    },
    daysToShow: {
        type: Number,
        default: 1,
    },
    entries: {
        type: Array,
        default: () => [],
    },
    currentTimer: {
        type: Object,
        default: null,
    },
    elapsedSeconds: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(["entry-click", "empty-click"]);

// 1 minute = 1px, so 1 hour = 60px
const MINUTE_HEIGHT = 1;
const HOUR_HEIGHT = 60 * MINUTE_HEIGHT;
const DAY_HEIGHT = 24 * HOUR_HEIGHT;
const hours = Array.from({ length: 24 }, (_, i) => i);

const now = ref(new Date());
let timeInterval = null;

// Generate array of days to display
const days = computed(() => {
    const result = [];
    for (let i = 0; i < props.daysToShow; i++) {
        const date = new Date(props.startDate);
        date.setHours(0, 0, 0, 0);
        date.setDate(date.getDate() + i);
        result.push(date);
    }
    return result;
});

// Check if a date is today
function isToday(date) {
    return date.toDateString() === now.value.toDateString();
}

// Check if a date is in the past (before today)
function isPastDay(date) {
    const today = new Date(now.value);
    today.setHours(0, 0, 0, 0);
    const checkDate = new Date(date);
    checkDate.setHours(0, 0, 0, 0);
    return checkDate < today;
}

// Check if a date is in the future
function isFutureDay(date) {
    const today = new Date(now.value);
    today.setHours(0, 0, 0, 0);
    const checkDate = new Date(date);
    checkDate.setHours(0, 0, 0, 0);
    return checkDate > today;
}

// Current time position (minutes from midnight)
const currentTimeMinutes = computed(() => {
    return now.value.getHours() * 60 + now.value.getMinutes();
});

function updateCurrentTime() {
    now.value = new Date();
}

onMounted(() => {
    updateCurrentTime();
    timeInterval = setInterval(updateCurrentTime, 1000);
});

onUnmounted(() => {
    if (timeInterval) {
        clearInterval(timeInterval);
    }
});

// Get entries for a specific day
function getEntriesForDay(dayDate) {
    const dayStart = new Date(dayDate);
    dayStart.setHours(0, 0, 0, 0);
    const dayEnd = new Date(dayDate);
    dayEnd.setHours(23, 59, 59, 999);

    return props.entries.filter((entry) => {
        const entryStart = new Date(entry.started_at);
        const entryEnd = entry.ended_at ? new Date(entry.ended_at) : now.value;

        // Entry overlaps with this day if it starts before day ends AND ends after day starts
        return entryStart <= dayEnd && entryEnd >= dayStart;
    });
}

// Calculate entry style for a specific day column
function getEntryStyleForDay(entry, dayDate) {
    const entryStart = new Date(entry.started_at);
    const entryEnd = entry.ended_at ? new Date(entry.ended_at) : now.value;

    const dayStart = new Date(dayDate);
    dayStart.setHours(0, 0, 0, 0);
    const dayEnd = new Date(dayDate);
    dayEnd.setHours(23, 59, 59, 999);

    // Clamp entry to this day's bounds
    const displayStart = entryStart < dayStart ? dayStart : entryStart;
    const displayEnd = entryEnd > dayEnd ? dayEnd : entryEnd;

    // Calculate position within the day (0-1440 minutes)
    const startMinuteOfDay =
        displayStart.getHours() * 60 + displayStart.getMinutes();
    const endMinuteOfDay =
        displayEnd.getHours() * 60 +
        displayEnd.getMinutes() +
        (displayEnd > dayEnd ? 0 : 0);

    // If entry ends at midnight of next day, show full day
    const effectiveEndMinute = displayEnd > dayEnd ? 1440 : endMinuteOfDay;

    const top = startMinuteOfDay * MINUTE_HEIGHT;
    const durationMinutes = Math.max(effectiveEndMinute - startMinuteOfDay, 1);
    const height = durationMinutes * MINUTE_HEIGHT;

    // Determine if this is a past entry (ended before now)
    const isPast = entry.ended_at && new Date(entry.ended_at) < now.value;

    // Check if entry continues from previous day or to next day
    const continuesFromPrevious = entryStart < dayStart;
    const continuesToNext = entryEnd > dayEnd;

    return {
        top: `${top}px`,
        height: `${height}px`,
        backgroundColor: isPast ? "#6b7280" : entry.project_color || "#6366f1",
        opacity: isPast ? 0.7 : 1,
        borderTopLeftRadius: continuesFromPrevious ? "0" : "4px",
        borderTopRightRadius: continuesFromPrevious ? "0" : "4px",
        borderBottomLeftRadius: continuesToNext ? "0" : "4px",
        borderBottomRightRadius: continuesToNext ? "0" : "4px",
    };
}

// Get active entry style for a specific day
function getActiveEntryStyleForDay(dayDate) {
    if (!props.currentTimer) return null;

    const entryStart = new Date(props.currentTimer.started_at);
    const entryEnd = now.value;

    const dayStart = new Date(dayDate);
    dayStart.setHours(0, 0, 0, 0);
    const dayEnd = new Date(dayDate);
    dayEnd.setHours(23, 59, 59, 999);

    // Check if active entry overlaps with this day
    if (entryStart > dayEnd || entryEnd < dayStart) {
        return null;
    }

    // Clamp to day bounds
    const displayStart = entryStart < dayStart ? dayStart : entryStart;
    const displayEnd = entryEnd > dayEnd ? dayEnd : entryEnd;

    // Calculate minutes from midnight for this specific day
    const startMinuteOfDay =
        displayStart <= dayStart
            ? 0
            : displayStart.getHours() * 60 + displayStart.getMinutes();

    const endMinuteOfDay =
        displayEnd >= dayEnd
            ? 1440
            : displayEnd.getHours() * 60 + displayEnd.getMinutes();

    const top = startMinuteOfDay * MINUTE_HEIGHT;
    const durationMinutes = Math.max(endMinuteOfDay - startMinuteOfDay, 1);
    const height = durationMinutes * MINUTE_HEIGHT;

    const continuesFromPrevious = entryStart < dayStart;
    const continuesToNext = entryEnd > dayEnd;

    return {
        top: `${top}px`,
        height: `${height}px`,
        backgroundColor: props.currentTimer.project_color || "#6366f1",
        borderTopLeftRadius: continuesFromPrevious ? "0" : "4px",
        borderTopRightRadius: continuesFromPrevious ? "0" : "4px",
        borderBottomLeftRadius: continuesToNext ? "0" : "4px",
        borderBottomRightRadius: continuesToNext ? "0" : "4px",
    };
}

function formatHour(hour) {
    return `${hour.toString().padStart(2, "0")}:00`;
}

function formatDayHeader(date) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const checkDate = new Date(date);
    checkDate.setHours(0, 0, 0, 0);

    const diffDays = Math.round((checkDate - today) / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return "Today";
    if (diffDays === -1) return "Yesterday";
    if (diffDays === 1) return "Tomorrow";

    return date.toLocaleDateString("en-US", {
        weekday: "short",
        month: "short",
        day: "numeric",
    });
}

function formatDuration(entry) {
    let seconds;
    if (entry.ended_at) {
        seconds =
            (new Date(entry.ended_at) - new Date(entry.started_at)) / 1000;
    } else {
        seconds = (now.value - new Date(entry.started_at)) / 1000;
    }
    const hrs = Math.floor(seconds / 3600);
    const mins = Math.floor((seconds % 3600) / 60);
    if (hrs > 0) {
        return `${hrs}h ${mins}m`;
    }
    return `${mins}m`;
}

function handleEntryClick(entry, event) {
    event.stopPropagation();
    emit("entry-click", entry);
}

function handleActiveEntryClick(event) {
    event.stopPropagation();
    if (props.currentTimer) {
        emit("entry-click", { ...props.currentTimer, ended_at: null });
    }
}

function handleColumnClick(event, dayDate) {
    // Don't trigger if clicking on an entry block
    if (event.target.closest(".entry-block")) return;

    const rect = event.currentTarget.getBoundingClientRect();
    const clickY = event.clientY - rect.top;
    const clickedMinutes = Math.floor(clickY / MINUTE_HEIGHT);
    const hours = Math.floor(clickedMinutes / 60);
    const minutes = clickedMinutes % 60;

    const clickedTime = new Date(dayDate);
    clickedTime.setHours(hours, minutes, 0, 0);

    emit("empty-click", clickedTime);
}

// Force re-render when elapsedSeconds changes
watch(
    () => props.elapsedSeconds,
    () => {
        now.value = new Date();
    },
);
</script>

<template>
    <div
        class="day-calendar relative bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
    >
        <div class="flex">
            <!-- Hour labels column (sticky left) -->
            <div
                class="hour-labels flex-shrink-0 w-14 bg-white dark:bg-gray-800 z-20"
            >
                <!-- Empty header space -->
                <div
                    v-if="daysToShow > 1"
                    class="h-10 border-b border-gray-200 dark:border-gray-700"
                ></div>

                <!-- Hour labels -->
                <div class="relative" :style="{ height: `${DAY_HEIGHT}px` }">
                    <div
                        v-for="hour in hours"
                        :key="hour"
                        class="absolute left-0 right-0"
                        :style="{
                            top: `${hour * HOUR_HEIGHT}px`,
                            height: `${HOUR_HEIGHT}px`,
                        }"
                    >
                        <span
                            class="absolute left-2 top-0 text-xs text-gray-400 dark:text-gray-500 -translate-y-1/2 bg-white dark:bg-gray-800 px-1"
                        >
                            {{ formatHour(hour) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Scrollable days container -->
            <div class="flex-1 overflow-x-auto">
                <div
                    class="flex"
                    :style="{
                        minWidth:
                            daysToShow > 1 ? `${daysToShow * 150}px` : '100%',
                    }"
                >
                    <!-- Day columns -->
                    <div
                        v-for="(day, dayIndex) in days"
                        :key="dayIndex"
                        class="day-column flex-1 min-w-[150px] border-l border-gray-200 dark:border-gray-700"
                        :class="{ 'first:border-l-0': true }"
                    >
                        <!-- Day header (only show if multiple days) -->
                        <div
                            v-if="daysToShow > 1"
                            class="day-header sticky top-0 z-10 h-10 px-2 flex items-center justify-center text-sm font-medium border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800"
                            :class="{
                                'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20':
                                    isToday(day),
                                'text-gray-400 dark:text-gray-500':
                                    isPastDay(day),
                                'text-gray-700 dark:text-gray-300':
                                    !isToday(day) && !isPastDay(day),
                            }"
                        >
                            {{ formatDayHeader(day) }}
                        </div>

                        <!-- Day content -->
                        <div
                            class="day-content relative cursor-pointer"
                            :style="{ height: `${DAY_HEIGHT}px` }"
                            @click="handleColumnClick($event, day)"
                        >
                            <!-- Hour grid lines -->
                            <div
                                v-for="hour in hours"
                                :key="hour"
                                class="absolute left-0 right-0 border-t border-gray-100 dark:border-gray-700/50"
                                :style="{
                                    top: `${hour * HOUR_HEIGHT}px`,
                                    height: `${HOUR_HEIGHT}px`,
                                }"
                            ></div>

                            <!-- Current time line (across all columns) -->
                            <div
                                class="current-time-line absolute left-0 right-0 z-30 pointer-events-none -translate-y-px"
                                :style="{
                                    top: `${currentTimeMinutes * MINUTE_HEIGHT}px`,
                                }"
                            >
                                <div class="h-0.5 bg-red-500"></div>
                            </div>

                            <!-- Entries for this day -->
                            <div class="absolute inset-x-1 top-0 bottom-0">
                                <!-- Completed entries -->
                                <div
                                    v-for="entry in getEntriesForDay(
                                        day,
                                    ).filter((e) => e.ended_at)"
                                    :key="entry.id"
                                    class="entry-block absolute left-0 right-0 px-1 py-0.5 overflow-hidden cursor-pointer hover:brightness-110 transition-all shadow-sm hover:shadow-md z-10"
                                    :style="getEntryStyleForDay(entry, day)"
                                    @click="handleEntryClick(entry, $event)"
                                >
                                    <p
                                        class="text-xs font-medium text-white truncate"
                                    >
                                        {{ entry.title }}
                                    </p>
                                    <span
                                        v-if="
                                            parseInt(
                                                getEntryStyleForDay(entry, day)
                                                    .height,
                                            ) > 20
                                        "
                                        class="text-[10px] text-white/80 font-mono"
                                    >
                                        {{ formatDuration(entry) }}
                                    </span>
                                </div>

                                <!-- Active entry -->
                                <div
                                    v-if="
                                        currentTimer &&
                                        getActiveEntryStyleForDay(day)
                                    "
                                    class="entry-block active-entry absolute left-0 right-0 px-1 py-0.5 overflow-hidden border-2 border-white/50 shadow-lg cursor-pointer hover:shadow-xl transition-shadow z-20"
                                    :style="getActiveEntryStyleForDay(day)"
                                    @click="handleActiveEntryClick($event)"
                                >
                                    <p
                                        class="text-xs font-medium text-white truncate"
                                    >
                                        {{ currentTimer.title }}
                                    </p>
                                    <span
                                        v-if="
                                            parseInt(
                                                getActiveEntryStyleForDay(day)
                                                    ?.height || '0',
                                            ) > 20
                                        "
                                        class="text-[10px] text-white/80 font-mono"
                                    >
                                        {{ formatDuration(currentTimer) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.day-calendar {
    max-height: calc(100vh - 320px);
    overflow-y: auto;
}

.hour-labels {
    position: sticky;
    left: 0;
}

.day-header {
    position: sticky;
    top: 0;
}

.entry-block {
    min-height: 1px;
}

.active-entry {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.85;
    }
}
</style>
