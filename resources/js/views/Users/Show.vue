<template>
    <div
        class="flex flex-col items-center"
        v-if="status?.user === 'success' && User"
    >
        <div class="relative mb-8">
            <div class="w-100 h-64 overflow-hidden z-10">
                <UploadableImages
                    image-width="1500"
                    image-height="300"
                    location="cover"
                    alt="cover image"
                    classes="object-cover w-full "
                    :user-image="User?.data?.attributes?.cover_image"
                />
            </div>
            <div
                class="flex ml-12 items-center absolute bottom-0 left-0 -mb-8 z-20"
            >
                <div class="w-32">
                    <UploadableImages
                        image-width="1500"
                        image-height="300"
                        location="profile"
                        alt="profile pic user"
                        classes="object-cover w-32 h-32 border-4 border-gray-200 rounded-full shadow-lg"
                        :user-image="
                            User?.data?.attributes?.profile_image?.data
                                ?.attributes?.path
                        "
                    />
                </div>
                <p class="text-2xl text-gray-100 ml-4">
                    {{ User?.data?.attributes?.name }}
                </p>
            </div>
            <div
                class="flex mr-12 items-center absolute bottom-0 right-0 mb-4 z-20"
            >
                <button
                    v-if="FriendButtonText && FriendButtonText !== 'Accept'"
                    @click="
                        store.dispatch(
                            'Profile/sendRequest',
                            route.params.userId
                        )
                    "
                    class="py-1 px-3 bg-gray-300 rounded"
                >
                    {{ FriendButtonText }}
                </button>
                <button
                    v-if="FriendButtonText && FriendButtonText == 'Accept'"
                    @click="
                        store.dispatch(
                            'Profile/acceptRequest',
                            route.params.userId
                        )
                    "
                    class="mr-2 py-1 px-3 bg-blue-500 rounded"
                >
                    {{ FriendButtonText }}
                </button>
                <button
                    v-if="FriendButtonText && FriendButtonText == 'Accept'"
                    @click="
                        store.dispatch(
                            'Profile/ignoreRequest',
                            route.params.userId
                        )
                    "
                    class="py-1 px-3 bg-gray-400 rounded"
                >
                    {{ FriendButtonText }}
                </button>
            </div>
        </div>
        <p v-if="status?.posts === 'loading'">
            <span class="animate-pulse">Loading posts...</span>
        </p>
        <div v-if="posts?.length < 1">
            <p class="text-red-500">
                Error fetching posts. Please try again later.
            </p>
        </div>
        <Post
            v-else
            v-for="(post, postKey) in posts?.data"
            :key="postKey"
            :post="post.data"
        />
    </div>
</template>
<script setup>
import Post from "../../components/Post.vue";
import { ref, onMounted, computed } from "vue";
import UploadableImages from "../../components/UploadableImages.vue";
import axios from "axios";
import { useRoute } from "vue-router";
import { useStore } from "vuex";

const route = useRoute();
const Postloading = ref(true);
const errorFetchingPosts = ref(false);
const store = useStore();
const User = computed(() => store.getters["Profile/User"]);
const posts = computed(() => store.getters["NewsPost/posts"]);
const status = computed(() => store.getters["Profile/status"]);
const FriendButtonText = computed(
    () => store.getters["Profile/FriendbuttonText"]
);

onMounted(async () => {
    const id = route.params.userId;
    await store.dispatch("Profile/fetchUser", id);
    await store.dispatch("NewsPost/fetchUserPost", id);
});
</script>
