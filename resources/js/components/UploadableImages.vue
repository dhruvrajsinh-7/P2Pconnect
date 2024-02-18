<template>
    <img
        src="https://cdn.pixabay.com/photo/2024/02/11/12/43/alcazar-de-segovia-8566449_1280.jpg"
        alt=""
        ref="userImage"
        class="object-cover w-full"
    />
</template>

<script setup>
import { ref, onMounted } from "vue";
import Dropzone from "dropzone";

defineProps({
    imageWidth: Number,
    imageHeight: Number,
    imageLocation: String,
});

const userImage = ref(null);
let dropzoneInstance;

onMounted(() => {
    if (userImage.value) {
        dropzoneInstance = new Dropzone(userImage.value, settings.value);
    }
});

const settings = () => {
    return {
        paramName: "image",
        url: "/api/user-images",
        params: {
            width: props.imageWidth,
            height: props.imageHeight,
            location: props.imageLocation,
        },
        acceptedFiles: "image/*",
        headers: {
            "X-CSRF-TOKEN": document.head.querySelector(
                'meta[name="csrf-token"]'
            ).content,
        },
        success: (file, response) => {
            alert("Uploaded successfully");
        },
    };
};
</script>
