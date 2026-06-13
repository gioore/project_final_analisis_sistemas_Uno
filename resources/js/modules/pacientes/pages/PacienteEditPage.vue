<template>
    <section class="edit">
        <div v-if="loading" class="edit__loading">Cargando datos del paciente…</div>
        <div v-else-if="errorMessage" class="edit__error">{{ errorMessage }}</div>
        <template v-else>
            <h2 class="edit__title">Editar paciente: {{ store.currentPaciente?.nombre }} {{ store.currentPaciente?.apellido }}</h2>
            <PacienteForm :paciente="store.currentPaciente" :on-submit="handleUpdate" />
        </template>
    </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { usePacienteStore } from '@/modules/pacientes/stores/paciente';
import PacienteForm from '@/modules/pacientes/components/PacienteForm.vue';

const route = useRoute();
const router = useRouter();
const store = usePacienteStore();

const loading = ref(true);
const errorMessage = ref('');

onMounted(async () => {
    try {
        await store.fetchById(route.params.id);
    } catch (error) {
        errorMessage.value = error?.response?.data?.message ?? 'Error al cargar el paciente.';
    } finally {
        loading.value = false;
    }
});

async function handleUpdate(payload) {
    await store.update(route.params.id, payload);
    await router.push({ name: 'pacientes.index' });
}
</script>

<style scoped>
.edit {
    max-width: 760px;
}

.edit__title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1.25rem;
}

.edit__loading {
    text-align: center;
    padding: 2rem;
    color: #64748b;
}

.edit__error {
    background: #fee2e2;
    color: #b91c1c;
    padding: 1rem;
    border-radius: 8px;
}
</style>
