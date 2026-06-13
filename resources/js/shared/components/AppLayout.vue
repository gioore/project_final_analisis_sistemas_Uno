<template>
    <div class="layout">
        <header class="layout__header">
            <div class="layout__brand">
                Hospital HIS
            </div>
            <nav class="layout__nav">
                <router-link class="layout__link" to="/">
                    Inicio
                </router-link>
                <router-link v-if="isAuthenticated" class="layout__link" to="/pacientes">
                    Pacientes
                </router-link>
                <router-link v-if="!isAuthenticated" class="layout__link" to="/login">
                    Acceso
                </router-link>
                <a v-if="isAuthenticated" class="layout__link layout__link--logout" @click.prevent="logout" href="#">
                    Cerrar sesión
                </a>
            </nav>
        </header>
        <main class="layout__main">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const isAuthenticated = computed(() => !!localStorage.getItem('auth_token'));

function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('tenant_id');
    localStorage.removeItem('auth_user');
    router.push('/login');
}
</script>

<style scoped>
.layout {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: #f8fafc;
    color: #0f172a;
}

.layout__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
}

.layout__brand {
    font-weight: 600;
    letter-spacing: 0.02em;
}

.layout__nav {
    display: flex;
    gap: 1rem;
}

.layout__link {
    color: #0f172a;
    text-decoration: none;
    font-weight: 500;
}

.layout__link.router-link-active {
    color: #2563eb;
}

.layout__link--logout {
    cursor: pointer;
    color: #b91c1c;
}

.layout__main {
    flex: 1;
    padding: 1.5rem;
}
</style>
