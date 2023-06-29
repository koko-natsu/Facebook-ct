<template>
    <div v-if="authUser.data.user_id.toString() === $route.params.userId" v-bind="getRootProps()">
        <input v-bind="getInputProps()" />
        <img :src="data.userImage.data.attributes.path"
            :alt="alt"
            :class="classes">
    </div>
    <div v-else>
        <img :src="data.userImage.data.attributes.path"
            :alt="alt"
            :class="classes">
    </div>
</template>

<script setup>
    import { useDropzone } from "vue3-dropzone";
    import { computed, reactive } from "vue";
    import { useStore } from 'vuex';
    import { useRoute } from 'vue-router';

    const store = useStore();
    const route = useRoute();
    const url = '/api/user-images';
    const authUser = computed(() => store.getters.authUser);

    const props = defineProps({
        userImage: Object,
        imageWidth: String,
        imageHeight: String,
        location: String,
        classes: String,
        alt: String,
    });

    const data = reactive({
        userImage: props.userImage,
    });

    const saveFiles = (files) => {
        const formData = new FormData();

        const params = {
            width: props.imageWidth,
            height: props.imageHeight,
            location: props.location,
        }

        for (var i = 0; i < files.length; i++) {
            formData.append('image', files[i]);
        };

        Object.entries(params).forEach(([key, value]) => {
            formData.append(key, value);
        });

        axios.post(url, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
                'X-CSRF-TOKEN': document.head.querySelector('meta[name=csrf-token]').content,
            },
        })
        .then(res => {
            store.dispatch('fetchAuthUser');
            store.dispatch('fetchUser', route.params.userId);
            store.dispatch('fetchUserPosts', route.params.userId);
        })
        .catch(error => {
            console.error(error);
        });
    };

    const onDrop = (acceptFiles, rejectReasons) => {
        saveFiles(acceptFiles);
    };

    const { getRootProps, getInputProps, ...rest } = useDropzone({ onDrop });
</script>