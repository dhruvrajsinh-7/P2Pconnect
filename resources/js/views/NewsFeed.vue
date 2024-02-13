<template>
    <div class="flex flex-col items-center py-4">
        <NewPost />
        <div v-if="errorFetchingPosts">
            <p class="text-red-500">
                Error fetching posts. Please try again later.
            </p>
        </div>
        <!-- <ul v-else-if="posts.length > 0"> -->
        <Post v-for="(post, postKey) in posts" :key="postKey" :post="post" />
        <!-- </ul> -->
        <!-- <p v-else class="text-gray-500">No posts available.</p> -->
    </div>
</template>

<script setup>
import NewPost from "../components/NewPost.vue";
import Post from "../components/Post.vue";
import { ref, onMounted } from "vue";
const isLoadingPosts = ref(true);
const errorFetchingPosts = ref(false);
const posts = ref([]);

const fetchPosts = async () => {
    try {
        const response = await axios.get("/api/posts");
        posts.value = response?.data;
    } catch (error) {
        console.error("Error fetching posts:", error);
        errorFetchingPosts.value = true;
    } finally {
        isLoadingPosts.value = false;
    }
};

onMounted(fetchPosts);
</script>
