<template>
    <div class="flex flex-col h-screen overflow-y-hidden">
        <Nav />
        <div class="flex overflow-y-hidden flex-1">
            <Sidebar />
            <div class="overflow-x-hidden w-2/3">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>
<script setup>
import Sidebar from "./Sidebar.vue";
import Nav from "./Nav.vue";
</script>
