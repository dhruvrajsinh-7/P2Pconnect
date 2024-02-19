<template>
    <img :src="userImage" :alt="alt" ref="userImage" :class="classes" />
</template>

<script setup>
import Dropzone from "dropzone";
import { computed, getCurrentInstance, onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";

const store = useStore();
const route = useRoute();

const props = defineProps([
    "imageWidth",
    "imageHeight",
    "location",
    "classes",
    "alt",
    "userImage",
]);
const dropzone = ref(null);
const uploadedImage = ref(null);
const authUser = computed(() => store.getters["Profile/User"]);
const settings = computed(() => {
    const id = route.params.userId;
    return {
        paramName: "image",
        url: "/api/user-images",
        acceptedFiles: "image/*",
        params: {
            width: props.imageWidth,
            height: props.imageHeight,
            location: props.location,
        },
        headers: {
            "X-CSRF-TOKEN": document.head.querySelector("meta[name=csrf-token]")
                .content,
        },
        success: async function (e, res) {
            alert("uploaded!");
            await store.dispatch("User/fetchAuthUser");
            await store.dispatch("Profile/fetchUser", id);
            await store.dispatch("NewsPost/fetchUserPost", id);
        },
    };
});
onMounted(() => {
    dropzone.value = new Dropzone(
        getCurrentInstance().ctx.$refs.userImage,
        settings.value
    );
});
</script>
