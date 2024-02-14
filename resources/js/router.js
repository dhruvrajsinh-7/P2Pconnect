import { createRouter, createWebHistory } from "vue-router";
import NewsFeed from "./views/NewsFeed.vue";
import UserShow from "./views/Users/Show.vue";
const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "NewsFeed",
            component: NewsFeed,
        },
        {
            path: "/users/:userId",
            name: "user.show",
            component: UserShow,
        },
    ],
});
export default router;
