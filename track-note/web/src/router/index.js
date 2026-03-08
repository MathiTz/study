import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";

const routes = [
  {
    path: "/login",
    name: "login",
    component: () => import("../views/LoginView.vue"),
    meta: { guest: true },
  },
  {
    path: "/register",
    name: "register",
    component: () => import("../views/RegisterView.vue"),
    meta: { guest: true },
  },
  {
    path: "/",
    component: () => import("../views/AppLayout.vue"),
    meta: { auth: true },
    children: [
      {
        path: "",
        name: "home",
        component: () => import("../views/HomeView.vue"),
      },
      {
        path: "summary",
        name: "summary",
        component: () => import("../views/SummaryView.vue"),
      },
      {
        path: "projects",
        name: "projects",
        component: () => import("../views/ProjectsView.vue"),
      },
      {
        path: "projects/:id",
        name: "project",
        component: () => import("../views/ProjectDetailView.vue"),
      },
      {
        path: "entries",
        name: "entries",
        component: () => import("../views/WorkEntriesView.vue"),
      },
      {
        path: "entries/:id",
        name: "entry",
        component: () => import("../views/WorkEntryDetailView.vue"),
      },
      {
        path: "organizations",
        name: "organizations",
        component: () => import("../views/OrganizationsView.vue"),
      },
      {
        path: "organizations/:id",
        name: "organization",
        component: () => import("../views/OrganizationDetailView.vue"),
      },
      {
        path: "reports",
        name: "reports",
        component: () => import("../views/ReportsView.vue"),
      },
      {
        path: "profile",
        name: "profile",
        component: () => import("../views/ProfileView.vue"),
      },
    ],
  },
  {
    path: "/invite/:code",
    name: "invite",
    component: () => import("../views/InviteView.vue"),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  if (!authStore.initialized) {
    await authStore.fetchUser();
  }

  if (to.meta.auth && !authStore.isAuthenticated) {
    next({ name: "login", query: { redirect: to.fullPath } });
    return;
  }

  if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: "home" });
    return;
  }

  next();
});

export default router;
