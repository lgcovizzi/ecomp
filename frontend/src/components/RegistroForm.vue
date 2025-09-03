<template>
  <div class="form-container">
    <h2>{{ isEdit ? 'Editar' : 'Novo' }} Registro</h2>
    <form @submit.prevent="submitForm">
      <div class="form-group">
        <label for="nome">Nome *</label>
        <input
          id="nome"
          v-model="form.nome"
          type="text"
          required
          maxlength="255"
        />
      </div>

      <div class="form-group">
        <label for="instituicao">Instituição</label>
        <select
          id="instituicao"
          v-model="form.instituicao_id"
          @change="onInstituicaoChange"
        >
          <option value="">Selecione uma instituição</option>
          <option
            v-for="instituicao in instituicoes"
            :key="instituicao.id"
            :value="instituicao.id"
          >
            {{ instituicao.nome_longo }}
          </option>
        </select>
      </div>

      <div class="form-group">
        <label for="endereco">Endereço</label>
        <select
          id="endereco"
          v-model="form.endereco_id"
          :disabled="!form.instituicao_id"
          @change="onEnderecoChange"
        >
          <option value="">Selecione um endereço</option>
          <option
            v-for="endereco in enderecos"
            :key="endereco.id"
            :value="endereco.id"
          >
            {{ endereco.titulo }} - {{ endereco.cidade }}/{{ endereco.estado }}
          </option>
        </select>
      </div>

      <div class="form-group">
        <label for="departamento">Departamento</label>
        <select
          id="departamento"
          v-model="form.departamento_id"
          :disabled="!form.endereco_id"
        >
          <option value="">Selecione um departamento</option>
          <option
            v-for="departamento in departamentos"
            :key="departamento.id"
            :value="departamento.id"
          >
            {{ departamento.nome }}
          </option>
        </select>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-primary" :disabled="loading">
          {{ loading ? 'Salvando...' : 'Salvar' }}
        </button>
        <button type="button" class="btn-secondary" @click="$emit('cancel')">
          Cancelar
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { useInstituicaoStore } from '@/stores/instituicao'
import { useEnderecoStore } from '@/stores/endereco'
import { useDepartamentoStore } from '@/stores/departamento'
import type { Registro } from '@/types'

const props = defineProps<{
  registro?: Registro
  instituicoes: any[]
  loading?: boolean
}>()

const emit = defineEmits<{
  submit: [data: Partial<Registro>]
  cancel: []
}>()

const instituicaoStore = useInstituicaoStore()
const enderecoStore = useEnderecoStore()
const departamentoStore = useDepartamentoStore()

const isEdit = ref(false)
const form = ref({
  nome: '',
  instituicao_id: null as number | null,
  endereco_id: null as number | null,
  departamento_id: null as number | null,
})

const enderecos = ref<any[]>([])
const departamentos = ref<any[]>([])

const loadEnderecos = async (instituicaoId: number) => {
  try {
    await enderecoStore.fetchEnderecos(instituicaoId)
    enderecos.value = enderecoStore.enderecos
  } catch (error) {
    console.error('Erro ao carregar endereços:', error)
  }
}

const loadDepartamentos = async (enderecoId: number) => {
  try {
    await departamentoStore.fetchDepartamentos(enderecoId)
    departamentos.value = departamentoStore.departamentos
  } catch (error) {
    console.error('Erro ao carregar departamentos:', error)
  }
}

const onInstituicaoChange = async () => {
  form.value.endereco_id = null
  form.value.departamento_id = null
  enderecos.value = []
  departamentos.value = []
  
  if (form.value.instituicao_id) {
    await loadEnderecos(form.value.instituicao_id)
  }
}

const onEnderecoChange = async () => {
  form.value.departamento_id = null
  departamentos.value = []
  
  if (form.value.endereco_id) {
    await loadDepartamentos(form.value.endereco_id)
  }
}

watch(() => props.registro, (newRegistro) => {
  if (newRegistro && newRegistro.id) {
    isEdit.value = true
    form.value = {
      nome: newRegistro.nome || '',
      instituicao_id: newRegistro.instituicao_id || null,
      endereco_id: newRegistro.endereco_id || null,
      departamento_id: newRegistro.departamento_id || null,
    }
    
    // Carregar endereços e departamentos se necessário
    if (form.value.instituicao_id) {
      loadEnderecos(form.value.instituicao_id)
    }
    if (form.value.endereco_id) {
      loadDepartamentos(form.value.endereco_id)
    }
  } else {
    isEdit.value = false
    form.value = {
      nome: '',
      instituicao_id: null,
      endereco_id: null,
      departamento_id: null,
    }
    enderecos.value = []
    departamentos.value = []
  }
}, { immediate: true })

const submitForm = () => {
  emit('submit', {
    ...form.value,
    instituicao_id: form.value.instituicao_id || undefined,
    endereco_id: form.value.endereco_id || undefined,
    departamento_id: form.value.departamento_id || undefined,
  })
}
</script>

<style scoped>
.form-container {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  max-width: 500px;
  margin: 0 auto;
}

.form-container h2 {
  margin-top: 0;
  color: #2c3e50;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
  color: #2c3e50;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #3498db;
}

.form-group select:disabled {
  background: #f8f9fa;
  cursor: not-allowed;
}

.form-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.btn-primary {
  background: #3498db;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-primary:hover:not(:disabled) {
  background: #2980b9;
}

.btn-primary:disabled {
  background: #bdc3c7;
  cursor: not-allowed;
}

.btn-secondary {
  background: #95a5a6;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-secondary:hover {
  background: #7f8c8d;
}
</style>