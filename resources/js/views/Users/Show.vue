<template>
    <div class="flex flex-col items-center" v-if="data.status.user === 'success' && data.user">
        <div class="relative mb-8">

            <div class="w-100 h-64 overflow-hidden z-10">
                <UploadableImage imageWidth="1200"
                                 imageHeight="500"
                                 location="cover"
                                 :user-image="data.user.data.attributes.cover_image"
                                 classes="object-cover w-full"
                                 alt="user background image"/>
            </div>

            <div class="absolute flex items-center bottom-0 left-0 -mb-8 ml-12 z-20">
                <div>
                    <UploadableImage imageWidth="750"
                                     imageHeight="750"
                                     location="profile"
                                     :user-image="data.user.data.attributes.profile_image"
                                     classes="w-32 h-32 border-4 border-gray-200 shadow-lg object-cover rounded-full"
                                     alt="profile image for user"/>
                </div>

                <p class="text-2xl text-gray-200 ml-4">{{ data.user.data.attributes.name }}</p>
            </div>

            <div class="absolute flex items-center bottom-0 right-0 mb-8 mr-12 z-20">
                <button v-if="data.buttonText && data.buttonText !== 'Accept'"
                        class="py-1 px-3 bg-gray-400 rounded  focus:outline-none focus:ring focus:ring-blue-400"
                        @click="$store.dispatch('sendFriendRequest', $route.params.userId)">
                    {{ data.buttonText }}
                </button>
                <button v-if="data.buttonText && data.buttonText === 'Accept'"
                        class="mr-2 py-1 px-3 bg-blue-500 rounded"
                        @click="$store.dispatch('acceptFriendRequest', $route.params.userId)">
                        Accept
                </button>
                <button v-if="data.buttonText && data.buttonText === 'Accept'"
                        class="py-1 px-3 bg-gray-400 rounded"
                        @click="$store.dispatch('ignoreFriendRequest', $route.params.userId)">
                        Ignore
                </button>
            </div>

        </div>

        <div v-if="data.status.post === 'Loading'">Loading posts...</div>

        <div v-if="`data.posts.data.length` < 1">NO posts found. Get started...</div>

        <Post v-else v-for="(post, postKey) in data.posts.data" :postKey="postKey" :post="post" />

    </div>
</template>


<script setup>
    import { useStore } from 'vuex';
    import { onMounted, watch, reactive } from 'vue';
    import { useRoute } from 'vue-router';
    import Post from "@/components/Post.vue";
    import UploadableImage from "@/components/UploadableImage.vue";

    const store = useStore();
    const route = useRoute();

    onMounted(() => {
        store.dispatch('fetchUser', route.params.userId);
        store.dispatch('fetchUserPosts', route.params.userId);
    });

    const data = reactive({
        user: store.getters.user,
        posts: store.getters.posts,
        status: store.getters.status,
        buttonText: store.getters.buttonText,
    });

    watch(() => store.getters.user,
    (newValue) => {
        data.user = newValue
    });

    watch(() => store.getters.posts,
    (newValue) => {
        data.posts = newValue
    });
    
    watch(() => store.getters.status,
    (newValue) => {
        data.status = newValue
    });
    
    watch(() => store.getters.buttonText,
    (newValue) => {
        data.buttonText = newValue
    });
</script>