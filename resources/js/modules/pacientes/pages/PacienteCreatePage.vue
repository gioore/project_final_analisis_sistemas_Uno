<template>
    <section class="create">
        <h2 class="create__title">Nuevo paciente</h2>
        <PacienteForm @submit="handleCreate" />
    </section>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { usePacienteStore } from '@/modules/pacientes/stores/paciente';
import PacienteForm from '@/modules/pacientes/components/PacienteForm.vue';

const router = useRouter();
const store = usePacienteStore();

async function handleCreate(payload) {
    await store.create(payload);
    await router.push({ name: 'pacientes.index' });
}
</script>

<style scoped>
.create {
    max-width: 760px;
}

.create__title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1.25rem;
}
</style>
