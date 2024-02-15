<template>
    <div class="flex flex-col h-screen overflow-y-hidden">
        <Nav />
        <div class="flex overflow-y-hidden flex-1">
            <Sidebar />
            <div class="overflow-x-hidden w-2/3">
                <router-view :key="$route.fullpath"></router-view>
            </div>
        </div>
    </div>
</template>
<script setup>
import Sidebar from "./Sidebar.vue";
import Nav from "./Nav.vue";
import { useStore } from "vuex";
import { onMounted, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
const store = useStore();
const router = useRouter();
onMounted(() => {
    store.dispatch("User/fetchAuthUser");
    store.dispatch("Title/setPageTitle", router.currentRoute.value.meta.title);
});
watch(
    () => router.currentRoute.value.meta.title,
    (newTitle) => {
        store.dispatch("Title/setPageTitle", newTitle);
    }
);
</script>
