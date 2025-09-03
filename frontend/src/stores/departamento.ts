import { defineStore } from 'pinia'
import api from '@/services/api'
import type { Departamento } from '@/types'

export const useDepartamentoStore = defineStore('departamento', {
  state: () => ({
    departamentos: [] as Departamento[],
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async fetchDepartamentos(enderecoId?: number, instituicaoId?: number) {
      this.loading = true
      let url = '/departamentos'
      
      if (enderecoId) {
        url = `/departamentos/endereco/${enderecoId}`
      } else if (instituicaoId) {
        url = `/departamentos/instituicao/${instituicaoId}`
      }

      try {
        const response = await api.get(url)
        this.departamentos = response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Erro ao carregar departamentos'
      } finally {
        this.loading = false
      }
    },

    async createDepartamento(data: Omit<Departamento, 'id' | 'created_at' | 'updated_at'>) {
      try {
        const response = await api.post('/departamentos', data)
        this.departamentos.unshift(response.data)
        return response.data
      } catch (error: any) {
        throw error.response?.data || error
      }
    },

    async updateDepartamento(id: number, data: Partial<Departamento>) {
      try {
        const response = await api.put(`/departamentos/${id}`, data)
        const index = this.departamentos.findIndex(d => d.id === id)
        if (index !== -1) {
          this.departamentos[index] = response.data
        }
        return response.data
      } catch (error: any) {
        throw error.response?.data || error
      }
    },

    async deleteDepartamento(id: number) {
      try {
        await api.delete(`/departamentos/${id}`)
        this.departamentos = this.departamentos.filter(d => d.id !== id)
      } catch (error: any) {
        throw error.response?.data || error
      }
    },
  },
})