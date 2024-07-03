import { createRouter, createWebHistory } from 'vue-router';
import UrlShortener from './components/UrlShortener.vue';

const routes = [
    {
        path: '/',
        name: 'urlshortener',
        component: UrlShortener,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
