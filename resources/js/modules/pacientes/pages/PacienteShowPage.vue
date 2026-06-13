<template>
    <section class="show">
        <div v-if="loading" class="show__loading">Cargando datos del paciente…</div>
        <div v-else-if="errorMessage" class="show__error">{{ errorMessage }}</div>
        <template v-else-if="paciente">
            <header class="show__header">
                <div>
                    <h2 class="show__title">{{ paciente.nombre }} {{ paciente.apellido }}</h2>
                    <p class="show__subtitle">Expediente: {{ paciente.numero_expediente }}</p>
                </div>
                <div class="show__actions">
                    <router-link
                        :to="{ name: 'pacientes.edit', params: { id: paciente.id } }"
                        class="show__edit"
                    >
                        Editar
                    </router-link>
                    <router-link
                        :to="{ name: 'pacientes.index' }"
                        class="show__back"
                    >
                        Volver al listado
                    </router-link>
                </div>
            </header>

            <div class="show__grid">
                <div class="show__card">
                    <h3 class="show__card-title">Información personal</h3>
                    <dl class="show__dl">
                        <div class="show__row">
                            <dt class="show__dt">Fecha de nacimiento</dt>
                            <dd class="show__dd">{{ paciente.fecha_nacimiento }}</dd>
                        </div>
                        <div class="show__row">
                            <dt class="show__dt">Género</dt>
                            <dd class="show__dd">{{ generoLabel(paciente.genero) }}</dd>
                        </div>
                        <div class="show__row">
                            <dt class="show__dt">Tipo de sangre</dt>
                            <dd class="show__dd">{{ paciente.tipo_sangre ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="show__card">
                    <h3 class="show__card-title">Contacto</h3>
                    <dl class="show__dl">
                        <div class="show__row">
                            <dt class="show__dt">Teléfono</dt>
                            <dd class="show__dd">{{ paciente.telefono ?? '—' }}</dd>
                        </div>
                        <div class="show__row">
                            <dt class="show__dt">Email</dt>
                            <dd class="show__dd">{{ paciente.email ?? '—' }}</dd>
                        </div>
                        <div class="show__row">
                            <dt class="show__dt">Dirección</dt>
                            <dd class="show__dd">{{ paciente.direccion ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="show__card">
                    <h3 class="show__card-title">Contacto de emergencia</h3>
                    <dl class="show__dl">
                        <div class="show__row">
                            <dt class="show__dt">Nombre</dt>
                            <dd class="show__dd">{{ paciente.contacto_emergencia_nombre ?? '—' }}</dd>
                        </div>
                        <div class="show__row">
                            <dt class="show__dt">Teléfono</dt>
                            <dd class="show__dd">{{ paciente.contacto_emergencia_telefono ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="show__card">
                    <h3 class="show__card-title">Alergias conocidas</h3>
                    <p class="show__text">{{ paciente.alergias_conocidas ?? 'Ninguna registrada.' }}</p>
                </div>
            </div>
        </template>
    </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { usePacienteStore } from '@/modules/pacientes/stores/paciente';

const route = useRoute();
const store = usePacienteStore();

const paciente = ref(null);
const loading = ref(true);
const errorMessage = ref('');

onMounted(async () => {
    try {
        paciente.value = await store.fetchById(route.params.id);
    } catch (error) {
        errorMessage.value = error?.response?.data?.message ?? 'Error al cargar el paciente.';
    } finally {
        loading.value = false;
    }
});

function generoLabel(genero) {
    const labels = { M: 'Masculino', F: 'Femenino', Otro: 'Otro' };

    return labels[genero] ?? genero;
}
</script>

<style scoped>
.show__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 1.5rem;
}

.show__title {
    font-size: 1.5rem;
    font-weight: 700;
}

.show__subtitle {
    color: #64748b;
    font-size: 0.9rem;
}

.show__actions {
    display: flex;
    gap: 0.5rem;
}

.show__edit {
    background: #2563eb;
    color: #ffffff;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
}

.show__back {
    border: 1px solid #cbd5e1;
    color: #475569;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
}

.show__grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.show__card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.25rem;
}

.show__card-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: #1e293b;
}

.show__dl {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.show__row {
    display: flex;
    justify-content: space-between;
}

.show__dt {
    color: #64748b;
    font-size: 0.85rem;
}

.show__dd {
    font-weight: 500;
    font-size: 0.9rem;
}

.show__text {
    color: #475569;
    font-size: 0.9rem;
}

.show__loading {
    text-align: center;
    padding: 2rem;
    color: #64748b;
}

.show__error {
    background: #fee2e2;
    color: #b91c1c;
    padding: 1rem;
    border-radius: 8px;
}
</style>
