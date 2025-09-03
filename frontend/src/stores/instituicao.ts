import { defineStore } from 'pinia'
import api from '@/services/api'
import type { Instituicao } from '@/types'

export const useInstituicaoStore = defineStore('instituicao', {
  state: () => ({
    instituicoes: [] as Instituicao[],
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async fetchInstituicoes() {
      this.loading = true
      try {
        const response = await api.get('/instituicoes')
        this.instituicoes = response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Erro ao carregar instituições'
      } finally {
        this.loading = false
      }
    },

    async createInstituicao(data: Omit<Instituicao, 'id' | 'created_at' | 'updated_at'>) {
      try {
        const response = await api.post('/instituicoes', data)
        this.instituicoes.unshift(response.data)
        return response.data
      } catch (error: any) {
        throw error.response?.data || error
      }
    },

    async updateInstituicao(id: number, data: Partial<Instituicao>) {
      try {
        const response = await api.put(`/instituicoes/${id}`, data)
        const index = this.instituicoes.findIndex(i => i.id === id)
        if (index !== -1) {
          this.instituicoes[index] = response.data
        }
        return response.data
      } catch (error: any) {
        throw error.response?.data || error
      }
    },

    async deleteInstituicao(id: number) {
      try {
        await api.delete(`/instituicoes/${id}`)
        this.instituicoes = this.instituicoes.filter(i => i.id !== id)
      } catch (error: any) {
        throw error.response?.data || error
      }
    },
  },
})