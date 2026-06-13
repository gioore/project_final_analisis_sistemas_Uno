import { defineStore } from 'pinia';
import api from '@/plugins/axios';

export const usePacienteStore = defineStore('paciente', {
    state: () => ({
        pacientes: [],
        currentPaciente: null,
        loading: false,
    }),
    actions: {
        async fetchAll(params = {}) {
            this.loading = true;

            try {
                const { data } = await api.get('/pacientes', { params });

                this.pacientes = data.data;

                return data.data;
            } finally {
                this.loading = false;
            }
        },
        async fetchById(id) {
            this.loading = true;

            try {
                const { data } = await api.get(`/pacientes/${id}`);

                this.currentPaciente = data.data;

                return data.data;
            } finally {
                this.loading = false;
            }
        },
        async create(payload) {
            const { data } = await api.post('/pacientes', payload);

            return data;
        },
        async update(id, payload) {
            const { data } = await api.put(`/pacientes/${id}`, payload);

            return data;
        },
        async delete(id) {
            const { data } = await api.delete(`/pacientes/${id}`);

            return data;
        },
    },
});
