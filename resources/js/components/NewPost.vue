<template>
    <div class="bg-white rounded shadow w-2/3 p-4">
        <div class="flex justify-between items-center">
            <div>
                <img :src="authUser.data.attributes.profile_image.data.attributes.path" alt="profile image for user" class="w-8 h-8 object-cover rounded-full">
            </div>
            <div class="flex-1 flex mx-4">
                <input 
                    v-model="postMessage"
                    type="text"
                    name="body" 
                    class="w-full pl-4 h-8 bg-gray-200 rounded-full focus:outline-none focus:ring focus:ring-blue-400 text-sm"
                    placeholder="Add a post" />
                <transition name="fade">
                    <button
                        v-if="postMessage || form.image"
                        @click="postHandler(); postMessage=''; form.image=''"
                        class="bg-gray-200 ml-2 px-2 pl-1 rounded-full focus:outline-none focus:ring focus:ring-blue-400">
                        Post
                    </button>
                </transition>
            </div>
            <div v-bind="getRootProps()">
                <input v-bind="getInputProps()" />
                <button class="flex justify-center items-center w-10 h-10 rounded-full bg-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="fill-current w-5 h-5"><path d="M21.8 4H2.2c-.2 0-.3.2-.3.3v15.3c0 .3.1.4.3.4h19.6c.2 0 .3-.1.3-.3V4.3c0-.1-.1-.3-.3-.3zm-1.6 13.4l-4.4-4.6c0-.1-.1-.1-.2 0l-3.1 2.7-3.9-4.8h-.1s-.1 0-.1.1L3.8 17V6h16.4v11.4zm-4.9-6.8c.9 0 1.6-.7 1.6-1.6 0-.9-.7-1.6-1.6-1.6-.9 0-1.6.7-1.6 1.6.1.9.8 1.6 1.6 1.6z"/></svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useDebouncedRef } from '@/debounced';
import { useDropzone } from 'vue3-dropzone';
import { useStore } from 'vuex';
import { reactive } from 'vue';

const store = useStore();
const authUser = store.getters.authUser;

const form = reactive({
    body: '',
    image: '',
    width: 1200,
    height: 700,
});

const postMessage = useDebouncedRef(store, '', 800);

const postHandler = () => {
    form.body = store.getters.postMessage;

    axios.post('/api/posts', form, {
        headers: {
            "Content-Type": "multipart/form-data",
            'X-CSRF-TOKEN': document.head.querySelector('meta[name=csrf-token]').content,
        }
    })
    .then(res => {
        store.dispatch('fetchNewsPosts');
        store.commit('updateMessage', '');
    })
    .catch(error => {
        console.error(error);
    })
};

const saveFiles = (files) => {
    const formData = new FormData();

    for (var i = 0; i < files.length; i++) {
        formData.append('image', files[i]);
    };
    form.image = formData.get('image');
}

const onDrop = (acceptFiles, rejectReasons) => {
    saveFiles(acceptFiles);
};

const { getRootProps, getInputProps, ...rest } = useDropzone({ onDrop });
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity .8s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>