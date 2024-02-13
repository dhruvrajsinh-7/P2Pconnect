import { createRouter, createWebHistory } from "vue-router";
import NewsFeed from "./views/NewsFeed.vue";
const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "NewsFeed",
            component: NewsFeed,
        },
    ],
});
export default router;
