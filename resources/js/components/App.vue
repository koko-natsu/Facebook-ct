<template>
    <div class="flex flex-col flex-1 h-screen overflow-y-hidden" v-if="authUser">
        <Nav />

        <div class="flex overflow-y-hidden flex-1">
            <Sidebar />

            <div class="overflow-x-hidden w-2/3">
                <router-view :key="$route.fullPath"></router-view>
            </div>
        </div>
    </div>
</template>

<script>
    import Nav from '@/components/Nav.vue';
    import Sidebar from '@/components/Sidebar.vue';
    import { mapGetters } from 'vuex';

    export default {
        name: "App",

        components:{
            Nav,
            Sidebar,
        },

        mounted() {
            this.$store.dispatch('fetchAuthUser');
        },

        computed: {
            ...mapGetters({
                authUser: 'authUser',
            }),
        },

        watch: {
            $route(to, from) {
                this.$store.dispatch('setPageTitle', to.meta.title);
            }
        },
    }
</script>