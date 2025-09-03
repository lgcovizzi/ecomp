<template>
  <div class="form-container">
    <h2>{{ isEdit ? 'Editar' : 'Nova' }} Instituição</h2>
    <form @submit.prevent="submitForm">
      <div class="form-group">
        <label for="nome_longo">Nome Completo *</label>
        <input
          id="nome_longo"
          v-model="form.nome_longo"
          type="text"
          required
          maxlength="255"
        />
      </div>

      <div class="form-group">
        <label for="nome_curto">Nome Curto *</label>
        <input
          id="nome_curto"
          v-model="form.nome_curto"
          type="text"
          required
          maxlength="50"
        />
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
import type { Instituicao } from '@/types'

const props = defineProps<{
  instituicao?: Instituicao
  loading?: boolean
  editId?: number | null
}>()

const emit = defineEmits<{
  submit: [data: Partial<Instituicao>]
  cancel: []
}>()

const instituicaoStore = useInstituicaoStore()
const isEdit = ref(false)
const form = ref({
  nome_longo: '',
  nome_curto: '',
})

onMounted(async () => {
  if (props.editId) {
    await instituicaoStore.fetchInstituicoes()
    const instituicao = instituicaoStore.instituicoes.find(i => i.id === props.editId)
    if (instituicao) {
      isEdit.value = true
      form.value = {
        nome_longo: instituicao.nome_longo,
        nome_curto: instituicao.nome_curto,
      }
    }
  }
})

watch(() => props.instituicao, (newInstituicao) => {
  if (newInstituicao) {
    isEdit.value = true
    form.value = {
      nome_longo: newInstituicao.nome_longo,
      nome_curto: newInstituicao.nome_curto,
    }
  } else {
    isEdit.value = false
    form.value = {
      nome_longo: '',
      nome_curto: '',
    }
  }
}, { immediate: true })

const submitForm = () => {
  console.log('Enviando formulário:', form.value)
  emit('submit', form.value)
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

.form-group input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form-group input:focus {
  outline: none;
  border-color: #3498db;
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