<template>
    <form class="form" @submit.prevent="handleSubmit">
        <div class="form__grid">
            <label class="form__label">
                Número de expediente *
                <input
                    v-model="form.numero_expediente"
                    class="form__input"
                    type="text"
                    required
                >
                <span v-if="errors.numero_expediente" class="form__error">{{ errors.numero_expediente[0] }}</span>
            </label>

            <label class="form__label">
                Nombre *
                <input
                    v-model="form.nombre"
                    class="form__input"
                    type="text"
                    required
                >
                <span v-if="errors.nombre" class="form__error">{{ errors.nombre[0] }}</span>
            </label>

            <label class="form__label">
                Apellido *
                <input
                    v-model="form.apellido"
                    class="form__input"
                    type="text"
                    required
                >
                <span v-if="errors.apellido" class="form__error">{{ errors.apellido[0] }}</span>
            </label>

            <label class="form__label">
                Fecha de nacimiento *
                <input
                    v-model="form.fecha_nacimiento"
                    class="form__input"
                    type="date"
                    required
                >
                <span v-if="errors.fecha_nacimiento" class="form__error">{{ errors.fecha_nacimiento[0] }}</span>
            </label>

            <label class="form__label">
                Género *
                <select v-model="form.genero" class="form__input" required>
                    <option value="">Seleccionar…</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                    <option value="Otro">Otro</option>
                </select>
                <span v-if="errors.genero" class="form__error">{{ errors.genero[0] }}</span>
            </label>

            <label class="form__label">
                Tipo de sangre
                <select v-model="form.tipo_sangre" class="form__input">
                    <option value="">Seleccionar…</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </label>

            <label class="form__label">
                Teléfono
                <input
                    v-model="form.telefono"
                    class="form__input"
                    type="text"
                >
            </label>

            <label class="form__label">
                Email
                <input
                    v-model="form.email"
                    class="form__input"
                    type="email"
                >
                <span v-if="errors.email" class="form__error">{{ errors.email[0] }}</span>
            </label>
        </div>

        <label class="form__label">
            Dirección
            <textarea
                v-model="form.direccion"
                class="form__input form__textarea"
                rows="3"
            ></textarea>
        </label>

        <label class="form__label">
            Alergias conocidas
            <textarea
                v-model="form.alergias_conocidas"
                class="form__input form__textarea"
                rows="2"
            ></textarea>
        </label>

        <fieldset class="form__fieldset">
            <legend class="form__legend">Contacto de emergencia</legend>
            <div class="form__grid">
                <label class="form__label">
                    Nombre
                    <input
                        v-model="form.contacto_emergencia_nombre"
                        class="form__input"
                        type="text"
                    >
                </label>
                <label class="form__label">
                    Teléfono
                    <input
                        v-model="form.contacto_emergencia_telefono"
                        class="form__input"
                        type="text"
                    >
                </label>
            </div>
        </fieldset>

        <div v-if="errorMessage" class="form__error-msg">
            {{ errorMessage }}
        </div>

        <div class="form__actions">
            <router-link
                :to="{ name: 'pacientes.index' }"
                class="form__cancel"
            >
                Cancelar
            </router-link>
            <button
                class="form__submit"
                type="submit"
                :disabled="loading"
            >
                {{ loading ? 'Guardando…' : (isEditing ? 'Actualizar paciente' : 'Crear paciente') }}
            </button>
        </div>
    </form>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    paciente: {
        type: Object,
        default: null,
    },
    onSubmit: {
        type: Function,
        required: true,
    },
});

const isEditing = !!props.paciente;

const form = ref({
    numero_expediente: '',
    nombre: '',
    apellido: '',
    fecha_nacimiento: '',
    genero: '',
    direccion: '',
    telefono: '',
    email: '',
    tipo_sangre: '',
    alergias_conocidas: '',
    contacto_emergencia_nombre: '',
    contacto_emergencia_telefono: '',
});

const errors = ref({});
const errorMessage = ref('');
const loading = ref(false);

onMounted(() => {
    if (props.paciente) {
        form.value = {
            numero_expediente: props.paciente.numero_expediente ?? '',
            nombre: props.paciente.nombre ?? '',
            apellido: props.paciente.apellido ?? '',
            fecha_nacimiento: props.paciente.fecha_nacimiento ?? '',
            genero: props.paciente.genero ?? '',
            direccion: props.paciente.direccion ?? '',
            telefono: props.paciente.telefono ?? '',
            email: props.paciente.email ?? '',
            tipo_sangre: props.paciente.tipo_sangre ?? '',
            alergias_conocidas: props.paciente.alergias_conocidas ?? '',
            contacto_emergencia_nombre: props.paciente.contacto_emergencia_nombre ?? '',
            contacto_emergencia_telefono: props.paciente.contacto_emergencia_telefono ?? '',
        };
    }
});

async function handleSubmit() {
    errors.value = {};
    errorMessage.value = '';
    loading.value = true;

    try {
        await props.onSubmit({ ...form.value });
    } catch (error) {
        const response = error?.response?.data;

        if (response?.errors) {
            errors.value = response.errors;
        }

        errorMessage.value = response?.message ?? 'Error al guardar el paciente.';
    } finally {
        loading.value = false;
    }
}
</script>

<style scoped>
.form {
    max-width: 720px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
}

.form__grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.form__label {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    font-size: 0.9rem;
    color: #334155;
}

.form__input {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 0.6rem 0.75rem;
    font-size: 0.95rem;
}

.form__textarea {
    resize: vertical;
}

.form__error {
    color: #b91c1c;
    font-size: 0.8rem;
}

.form__fieldset {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
}

.form__legend {
    font-weight: 600;
    color: #475569;
    padding: 0 0.3rem;
}

.form__error-msg {
    background: #fee2e2;
    color: #b91c1c;
    padding: 0.75rem;
    border-radius: 8px;
    margin-top: 0.75rem;
}

.form__actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-top: 1.25rem;
}

.form__cancel {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 0.6rem 1.2rem;
    text-decoration: none;
    color: #475569;
}

.form__submit {
    border: none;
    border-radius: 8px;
    padding: 0.6rem 1.2rem;
    background: #2563eb;
    color: #ffffff;
    font-weight: 600;
    cursor: pointer;
}

.form__submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>
