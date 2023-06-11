<template>
    <div class="flex flex-col items-center py-4">
        <NewPost />
        
        <p v-if="newsPostsStatus.newsStatus === 'loading'">Now loading...</p>
        <Post v-else v-for="(post, postKey) in posts.data" :postKey="postKey" :post="post"/>
    </div>
</template>

<script>
import { mapGetters  } from "vuex";
import NewPost from "@/components/NewPost.vue";
import Post from "@/components/Post.vue";

export default {
    name: "NewFeed",

    components: {
        NewPost,
        Post,
    },

    mounted() {
        this.$store.dispatch('fetchNewsPosts')
    },

    computed: {
        ...mapGetters({
            posts: 'posts',
            newsPostsStatus: 'newsStatus',
        })
    }
};
</script>