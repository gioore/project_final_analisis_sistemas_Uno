<template>
    <section class="paciente-list">
        <header class="paciente-list__header">
            <h2 class="paciente-list__title">Gestión de Pacientes</h2>
            <router-link
                :to="{ name: 'pacientes.create' }"
                class="paciente-list__create"
            >
                + Nuevo paciente
            </router-link>
        </header>

        <div class="paciente-list__filters">
            <input
                v-model="search"
                class="paciente-list__search"
                type="text"
                placeholder="Buscar por nombre, expediente o teléfono..."
                @input="onSearch"
            >
            <select v-model="generoFilter" class="paciente-list__select" @change="onFilter">
                <option value="">Todos los géneros</option>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
                <option value="Otro">Otro</option>
            </select>
        </div>

        <div v-if="store.loading" class="paciente-list__loading">
            Cargando pacientes…
        </div>

        <div v-else-if="errorMessage" class="paciente-list__error">
            {{ errorMessage }}
        </div>

        <PacienteTable
            v-else
            :pacientes="store.pacientes"
            @delete="confirmDelete"
        />

        <div v-if="showConfirm" class="paciente-list__modal">
            <div class="paciente-list__modal-content">
                <p>¿Eliminar a <strong>{{ pacienteToDelete?.nombre }} {{ pacienteToDelete?.apellido }}</strong>?</p>
                <div class="paciente-list__modal-actions">
                    <button class="paciente-list__btn-cancel" @click="showConfirm = false">
                        Cancelar
                    </button>
                    <button class="paciente-list__btn-confirm" @click="executeDelete">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePacienteStore } from '@/modules/pacientes/stores/paciente';
import PacienteTable from '@/modules/pacientes/components/PacienteTable.vue';

const store = usePacienteStore();

const search = ref('');
const generoFilter = ref('');
const errorMessage = ref('');
const showConfirm = ref(false);
const pacienteToDelete = ref(null);

let searchTimeout = null;

onMounted(async () => {
    await loadPacientes();
});

async function loadPacientes() {
    errorMessage.value = '';

    try {
        const params = {};

        if (search.value) {
            params.search = search.value;
        }

        if (generoFilter.value) {
            params.genero = generoFilter.value;
        }

        await store.fetchAll(params);
    } catch (error) {
        errorMessage.value = error?.response?.data?.message ?? 'Error al cargar pacientes.';
    }
}

function onSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(loadPacientes, 400);
}

function onFilter() {
    loadPacientes();
}

function confirmDelete(paciente) {
    pacienteToDelete.value = paciente;
    showConfirm.value = true;
}

async function executeDelete() {
    if (pacienteToDelete.value === null) {
        return;
    }

    try {
        await store.delete(pacienteToDelete.value.id);
        showConfirm.value = false;
        pacienteToDelete.value = null;
        await loadPacientes();
    } catch (error) {
        errorMessage.value = error?.response?.data?.message ?? 'Error al eliminar paciente.';
        showConfirm.value = false;
    }
}
</script>

<style scoped>
.paciente-list {
    max-width: 960px;
}

.paciente-list__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
}

.paciente-list__title {
    font-size: 1.5rem;
    font-weight: 700;
}

.paciente-list__create {
    background: #2563eb;
    color: #ffffff;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
}

.paciente-list__filters {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.paciente-list__search {
    flex: 1;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 0.6rem 0.75rem;
    font-size: 0.95rem;
}

.paciente-list__select {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 0.6rem 0.75rem;
    font-size: 0.95rem;
    background: #ffffff;
}

.paciente-list__loading {
    text-align: center;
    padding: 2rem;
    color: #64748b;
}

.paciente-list__error {
    background: #fee2e2;
    color: #b91c1c;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.paciente-list__modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
}

.paciente-list__modal-content {
    background: #ffffff;
    border-radius: 12px;
    padding: 1.5rem;
    max-width: 400px;
    width: 90%;
}

.paciente-list__modal-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1rem;
    justify-content: flex-end;
}

.paciente-list__btn-cancel {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    background: #ffffff;
    cursor: pointer;
}

.paciente-list__btn-confirm {
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    background: #b91c1c;
    color: #ffffff;
    cursor: pointer;
}
</style>
