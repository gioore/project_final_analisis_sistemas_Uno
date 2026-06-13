import { createRouter, createWebHistory } from 'vue-router';
import HomePage from '@/pages/HomePage.vue';
import LoginPage from '@/modules/auth/pages/LoginPage.vue';
import PacienteListPage from '@/modules/pacientes/pages/PacienteListPage.vue';
import PacienteCreatePage from '@/modules/pacientes/pages/PacienteCreatePage.vue';
import PacienteEditPage from '@/modules/pacientes/pages/PacienteEditPage.vue';
import PacienteShowPage from '@/modules/pacientes/pages/PacienteShowPage.vue';
import { authGuard } from '@/router/guards';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomePage,
        },
        {
            path: '/login',
            name: 'login',
            component: LoginPage,
            meta: { guest: true },
        },
        {
            path: '/pacientes',
            name: 'pacientes.index',
            component: PacienteListPage,
            meta: { requiresAuth: true },
        },
        {
            path: '/pacientes/crear',
            name: 'pacientes.create',
            component: PacienteCreatePage,
            meta: { requiresAuth: true },
        },
        {
            path: '/pacientes/:id',
            name: 'pacientes.show',
            component: PacienteShowPage,
            meta: { requiresAuth: true },
        },
        {
            path: '/pacientes/:id/editar',
            name: 'pacientes.edit',
            component: PacienteEditPage,
            meta: { requiresAuth: true },
        },
    ],
});

router.beforeEach(authGuard);

export default router;
