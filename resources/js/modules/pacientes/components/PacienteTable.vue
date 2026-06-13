<template>
    <div class="table-wrapper">
        <table class="table">
            <thead class="table__head">
                <tr>
                    <th class="table__th">Expediente</th>
                    <th class="table__th">Nombre</th>
                    <th class="table__th">Apellido</th>
                    <th class="table__th">Teléfono</th>
                    <th class="table__th">Género</th>
                    <th class="table__th">Acciones</th>
                </tr>
            </thead>
            <tbody class="table__body">
                <tr v-for="paciente in pacientes" :key="paciente.id" class="table__row">
                    <td class="table__td">{{ paciente.numero_expediente }}</td>
                    <td class="table__td">{{ paciente.nombre }}</td>
                    <td class="table__td">{{ paciente.apellido }}</td>
                    <td class="table__td">{{ paciente.telefono ?? '—' }}</td>
                    <td class="table__td">{{ generoLabel(paciente.genero) }}</td>
                    <td class="table__td table__actions">
                        <router-link
                            :to="{ name: 'pacientes.show', params: { id: paciente.id } }"
                            class="table__action table__action--view"
                        >
                            Ver
                        </router-link>
                        <router-link
                            :to="{ name: 'pacientes.edit', params: { id: paciente.id } }"
                            class="table__action table__action--edit"
                        >
                            Editar
                        </router-link>
                        <button
                            class="table__action table__action--delete"
                            @click="$emit('delete', paciente)"
                        >
                            Eliminar
                        </button>
                    </td>
                </tr>
                <tr v-if="pacientes.length === 0">
                    <td class="table__td table__td--empty" colspan="6">
                        No se encontraron pacientes.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    pacientes: {
        type: Array,
        required: true,
    },
});

defineEmits(['delete']);

function generoLabel(genero) {
    const labels = { M: 'Masculino', F: 'Femenino', Otro: 'Otro' };

    return labels[genero] ?? genero;
}
</script>

<style scoped>
.table-wrapper {
    overflow-x: auto;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #ffffff;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table__head {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.table__th {
    padding: 0.75rem 1rem;
    text-align: left;
    font-size: 0.85rem;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.table__td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.95rem;
}

.table__row:last-child .table__td {
    border-bottom: none;
}

.table__td--empty {
    text-align: center;
    color: #94a3b8;
    padding: 2rem;
}

.table__actions {
    display: flex;
    gap: 0.5rem;
}

.table__action {
    font-size: 0.85rem;
    padding: 0.3rem 0.65rem;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    font-weight: 500;
}

.table__action--view {
    background: #e0f2fe;
    color: #0369a1;
}

.table__action--edit {
    background: #fef3c7;
    color: #b45309;
}

.table__action--delete {
    background: #fee2e2;
    color: #b91c1c;
}
</style>
