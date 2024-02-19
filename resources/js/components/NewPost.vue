<template>
    <div class="bg-white rounded shadow w-2/3 p-4">
        <div class="flex justify-between items-center">
            <div>
                <div class="w-8">
                    <img
                        :src="
                            store.getters['Profile/User']?.data?.attributes
                                ?.profile_image?.data?.attributes?.path
                        "
                        alt="profile  pic"
                        class="w-8 h-8 rounded-full object-cover"
                    />
                </div>
            </div>
            <div class="flex-1 flex mx-4">
                <input
                    v-model="postMessage"
                    type="text"
                    name="body"
                    class="w-full rounded-full text-sm focus:outline-none focus:shadow-outline pl-4 h-8 bg-gray-200"
                    placeholder="Add a post"
                />
                <transition name="fade">
                    <button
                        v-if="postMessage"
                        @click="postMessagehandler"
                        class="bg-gray-200 ml-2 px-3 py-1 rounded-full"
                    >
                        Post
                    </button>
                </transition>
            </div>
            <div>
                <button
                    ref="postImage"
                    class="dz-clickable flex justify-center items-center rounded-full w-10 h-10 bg-gray-200"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        class="dz-clickable fill-current w-5 h-5"
                    >
                        <path
                            d="M21.8 4H2.2c-.2 0-.3.2-.3.3v15.3c0 .3.1.4.3.4h19.6c.2 0 .3-.1.3-.3V4.3c0-.1-.1-.3-.3-.3zm-1.6 13.4l-4.4-4.6c0-.1-.1-.1-.2 0l-3.1 2.7-3.9-4.8h-.1s-.1 0-.1.1L3.8 17V6h16.4v11.4zm-4.9-6.8c.9 0 1.6-.7 1.6-1.6 0-.9-.7-1.6-1.6-1.6-.9 0-1.6.7-1.6 1.6.1.9.8 1.6 1.6 1.6z"
                        />
                    </svg>
                </button>
            </div>
        </div>
        <div class="dropzone-previews">
            <div id="dz-template" class="hidden">
                <div class="dz-preview dz-file-preview mt-4">
                    <div class="dz-details">
                        <img data-dz-thumbnail class="w-32 h-32" />
                        <button data-dz-remove class="text-xs">remove</button>
                    </div>
                    <div class="dz-progress">
                        <span class="dz-upload">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { computed, getCurrentInstance, onMounted, ref } from "vue";
import { useStore } from "vuex";
import Dropzone from "dropzone";
const store = useStore();
const dropzone = ref(null);
const postMessage = computed({
    get: () => store.getters["NewsPost/postMessage"],
    set: (value) => store.commit("NewsPost/updateMessage", value),
});
const User = computed(() => store.getters["Profile/User"]);
const postMessagehandler = async () => {
    if (dropzone.value.getAcceptedFiles().length) {
        dropzone.value.processQueue();
    } else {
        await store.dispatch("NewsPost/postMessage");
    }
    store.commit("NewsPost/updateMessage", "");
};
const settings = computed(() => {
    return {
        paramName: "image",
        url: "/api/posts",
        acceptedFiles: "image/*",
        clickable: ".dz-clickable",
        autoProcessQueue: false,
        previewsContainer: ".dropzone-previews",
        previewTemplate: document.querySelector("#dz-template").innerHTML,
        maxFiles: 1,
        params: {
            width: 1000,
            height: 1000,
        },
        headers: {
            "X-CSRF-TOKEN": document.head.querySelector("meta[name=csrf-token]")
                .content,
        },
        sending: function (file, xhr, formData) {
            formData.append("body", store.getters["NewsPost/postMessage"]);
        },
        success: async function (e, res) {
            dropzone.value.removeAllFiles();
            store.commit("NewsPost/pushPosts", res);
            store.commit("NewsPost/updateMessage", "");
        },
        maxFilesexceeded: function (file) {
            dropzone.value.removeAllFiles();
            dropzone.value.addFile(file);
        },
    };
});
onMounted(() => {
    dropzone.value = new Dropzone(
        getCurrentInstance().ctx.$refs.postImage,
        settings.value
    );
});
</script>
<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>
