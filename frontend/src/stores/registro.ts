import { defineStore } from 'pinia'
import api from '@/services/api'
import type { Registro } from '@/types'

export const useRegistroStore = defineStore('registro', {
  state: () => ({
    registros: [] as Registro[],
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async fetchRegistros() {
      this.loading = true
      try {
        const response = await api.get('/registros')
        this.registros = response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Erro ao carregar registros'
      } finally {
        this.loading = false
      }
    },

    async createRegistro(data: Omit<Registro, 'id' | 'created_at' | 'updated_at'>) {
      try {
        const response = await api.post('/registros', data)
        this.registros.unshift(response.data)
        return response.data
      } catch (error: any) {
        throw error.response?.data || error
      }
    },

    async updateRegistro(id: number, data: Partial<Registro>) {
      try {
        const response = await api.put(`/registros/${id}`, data)
        const index = this.registros.findIndex(r => r.id === id)
        if (index !== -1) {
          this.registros[index] = response.data
        }
        return response.data
      } catch (error: any) {
        throw error.response?.data || error
      }
    },

    async deleteRegistro(id: number) {
      try {
        await api.delete(`/registros/${id}`)
        this.registros = this.registros.filter(r => r.id !== id)
      } catch (error: any) {
        throw error.response?.data || error
      }
    },
  },
})