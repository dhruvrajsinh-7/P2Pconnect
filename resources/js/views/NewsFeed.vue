<template>
    <div class="flex flex-col items-center py-4">
        <NewPost />
        <div v-if="errorFetchingPosts">
            <p class="text-red-500">
                Error fetching posts. Please try again later.
            </p>
        </div>
        <p v-else-if="newsStatus.newsPostsStatus === 'loading'">
            <span class="animate-pulse">Loading posts...</span>
        </p>
        <Post
            v-for="(post, postKey) in Posts?.data"
            :key="postKey"
            :post="post.data"
            :postKey="postKey"
        />
        <!-- </ul> -->
        <!-- <p v-else class="text-gray-500">No posts available.</p> -->
    </div>
</template>

<script setup>
import NewPost from "../components/NewPost.vue";
import Post from "../components/Post.vue";
import { ref, onMounted, computed } from "vue";
import { useStore } from "vuex";
const isLoadingPosts = ref(true);
const errorFetchingPosts = ref(false);
const store = useStore();
const Posts = computed(() => store.getters["NewsPost/posts"]);
const newsStatus = computed(() => store.getters["NewsPost/newsStatus"]);
onMounted(async () => {
    await store.dispatch("NewsPost/fetchposts");
});
</script>
