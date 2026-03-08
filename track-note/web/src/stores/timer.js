import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { useApi } from "../composables/useApi";

export const useTimerStore = defineStore("timer", () => {
  const timer = ref(null);
  const elapsedSeconds = ref(0);
  const intervalId = ref(null);
  const api = useApi();

  const isRunning = computed(() => timer.value?.status === "running");
  const isPaused = computed(() => timer.value?.status === "paused");
  const hasActiveTimer = computed(() => timer.value !== null);

  function formatTime(seconds) {
    const hrs = Math.floor(seconds / 3600);
    const mins = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    return `${hrs.toString().padStart(2, "0")}:${mins.toString().padStart(2, "0")}:${secs.toString().padStart(2, "0")}`;
  }

  function startTicking() {
    if (intervalId.value) return;
    intervalId.value = setInterval(() => {
      if (isRunning.value) {
        elapsedSeconds.value++;
      }
    }, 1000);
  }

  function stopTicking() {
    if (intervalId.value) {
      clearInterval(intervalId.value);
      intervalId.value = null;
    }
  }

  async function fetchCurrent() {
    try {
      const response = await api.get("/timer/current");
      timer.value = response.data;
      if (timer.value) {
        elapsedSeconds.value = timer.value.elapsed_seconds || 0;
        if (isRunning.value) {
          startTicking();
        }
      } else {
        elapsedSeconds.value = 0;
        stopTicking();
      }
    } catch (err) {
      timer.value = null;
      elapsedSeconds.value = 0;
      stopTicking();
    }
  }

  async function start(projectId, title) {
    const response = await api.post("/timer/start", {
      project_id: projectId,
      title,
    });
    timer.value = response.data;
    elapsedSeconds.value = response.data.elapsed_seconds || 0;
    startTicking();
    return response;
  }

  async function pause() {
    const response = await api.post("/timer/pause");
    timer.value = response.data;
    return response;
  }

  async function resume() {
    const response = await api.post("/timer/resume");
    timer.value = response.data;
    elapsedSeconds.value = response.data.elapsed_seconds || 0;
    return response;
  }

  async function stop() {
    const response = await api.post("/timer/stop");
    timer.value = null;
    elapsedSeconds.value = 0;
    stopTicking();
    return response;
  }

  async function transition(projectId, title) {
    const response = await api.post("/timer/transition", {
      project_id: projectId,
      title,
    });
    timer.value = response.data;
    elapsedSeconds.value = response.data.elapsed_seconds || 0;
    startTicking();
    return response;
  }

  return {
    timer,
    elapsedSeconds,
    isRunning,
    isPaused,
    hasActiveTimer,
    formatTime,
    fetchCurrent,
    start,
    pause,
    resume,
    stop,
    transition,
  };
});
