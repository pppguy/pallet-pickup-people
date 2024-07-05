import { createRouter, createWebHistory } from 'vue-router';
import LoginComponent from '../components/LoginComponent.vue';
import AdminDashboard from '../components/AdminDashboard.vue';
import DriverDashboard from '../components/DriverDashboard.vue';
import HomeRoute from '../components/HomeRoute.vue';
import TestRoute from '../components/TestRoute.vue';

const routes = [
    { path: '/login', component: LoginComponent },
    { path: '/admin', component: AdminDashboard },
    { path: '/driver', component: DriverDashboard },
    { path: '/home', component: HomeRoute },
    { path: '/test', component: TestRoute },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
