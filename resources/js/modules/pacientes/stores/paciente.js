import { defineStore } from 'pinia';
import api from '@/plugins/axios';

export const usePacienteStore = defineStore('paciente', {
    state: () => ({
        pacientes: [],
        currentPaciente: null,
        loading: false,
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0,
            per_page: 15,
        },
    }),
    actions: {
        async fetchAll(params = {}) {
            this.loading = true;

            try {
                const { data } = await api.get('/pacientes', { params });

                this.pacientes = data.data;
                this.pagination = {
                    current_page: data.current_page ?? 1,
                    last_page: data.last_page ?? 1,
                    total: data.total ?? 0,
                    per_page: data.per_page ?? 15,
                };

                return data.data;
            } catch (error) {
                throw error;
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
            } catch (error) {
                throw error;
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
