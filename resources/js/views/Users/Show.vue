<template>
    <div class="flex flex-col items-center">
        <div class="relative mb-8">
            <div class="w-100 h-64 overflow-hidden z-10">
                <img
                    src="https://cdn.pixabay.com/photo/2024/02/11/12/43/alcazar-de-segovia-8566449_1280.jpg"
                    alt=""
                    class="object-cover w-full"
                />
            </div>
            <div
                class="flex ml-12 items-center absolute bottom-0 left-0 -mb-8 z-20"
            >
                <div class="w-32">
                    <img
                        src="https://cdn-icons-png.freepik.com/512/3177/3177440.png"
                        alt="profile pic user"
                        class="object-cover w-32 h-32 border-4 border-gray-200 rounded-full shadow-lg"
                    />
                </div>
                <p class="text-2xl text-gray-100 ml-4">
                    {{ user.data?.attributes?.name }}
                </p>
            </div>
        </div>
        <div v-if="errorFetchingPosts">
            <p class="text-red-500">
                Error fetching posts. Please try again later.
            </p>
        </div>
        <p v-else-if="Postloading">
            <span class="animate-pulse">Loading posts...</span>
        </p>
        <Post
            v-for="(post, postKey) in posts.data"
            :key="postKey"
            :post="post.data"
        />
    </div>
</template>
<script setup>
import Post from "../../components/Post.vue";
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRoute } from "vue-router";
const user = ref([]);
const posts = ref([]);
const route = useRoute();
const Postloading = ref(true);
const Userloading = ref(true);
const errorFetchingPosts = ref(false);
onMounted(async () => {
    const id = route.params.userId;
    try {
        const res = await axios.get("/api/users/" + id);
        user.value = res.data;
        console.log(user.value);
    } catch (error) {
        console.log(error + "unable to fetch user from server");
    } finally {
        Userloading.value = false;
    }

    try {
        const res = await axios.get("/api/users/" + id + "/posts");
        posts.value = res.data;
        console.log(posts.value);
    } catch (error) {
        console.log(error + "unable to fetch user from server");
        errorFetchingPosts.value = true;
    } finally {
        Postloading.value = false;
    }
});
</script>
