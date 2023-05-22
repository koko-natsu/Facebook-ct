
import './bootstrap';
import { createApp } from 'vue';
import router from '@/router';
import App from '@/components/App.vue';

const app = createApp({
    components: {
        App
    }
});


app.use(router).mount('#app');
