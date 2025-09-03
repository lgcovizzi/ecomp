import { defineStore } from 'pinia'
import api from '@/services/api'
import type { Endereco } from '@/types'

export const useEnderecoStore = defineStore('endereco', {
  state: () => ({
    enderecos: [] as Endereco[],
    loading: false,
    error: null as string | null,
  }),

  actions: {
    async fetchEnderecos(instituicaoId?: number) {
      this.loading = true
      try {
        const url = instituicaoId 
          ? `/enderecos/instituicao/${instituicaoId}`
          : '/enderecos'
        const response = await api.get(url)
        this.enderecos = response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Erro ao carregar endereços'
      } finally {
        this.loading = false
      }
    },

    async createEndereco(data: Omit<Endereco, 'id' | 'created_at' | 'updated_at'>) {
      try {
        const response = await api.post('/enderecos', data)
        this.enderecos.unshift(response.data)
        return response.data
      } catch (error: any) {
        throw error.response?.data || error
      }
    },

    async updateEndereco(id: number, data: Partial<Endereco>) {
      try {
        const response = await api.put(`/enderecos/${id}`, data)
        const index = this.enderecos.findIndex(e => e.id === id)
        if (index !== -1) {
          this.enderecos[index] = response.data
        }
        return response.data
      } catch (error: any) {
        throw error.response?.data || error
      }
    },

    async deleteEndereco(id: number) {
      try {
        await api.delete(`/enderecos/${id}`)
        this.enderecos = this.enderecos.filter(e => e.id !== id)
      } catch (error: any) {
        throw error.response?.data || error
      }
    },
  },
})