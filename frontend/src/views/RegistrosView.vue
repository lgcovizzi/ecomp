<template>
  <div class="registros-view">
    <div class="header">
      <h1>Registrar</h1>
      <div class="header-actions">
        <button @click="showForm = true" class="btn-add">
          Adicionar
        </button>
      </div>
    </div>

    <div v-if="loading" class="loading">
      Carregando...
    </div>

    <div v-else-if="error" class="error">
      {{ error }}
    </div>

    <div v-else>
      <div class="registros-list">
        <div v-for="registro in registros" :key="registro.id" class="registro-card">
          <div class="registro-info">
            <h3>{{ registro.nome }}</h3>
            <div class="registro-details">
              <p v-if="registro.instituicao">
                <strong>Instituição:</strong> {{ registro.instituicao.nome_longo }}
              </p>
              <p v-if="registro.endereco">
                <strong>Endereço:</strong> {{ registro.endereco.titulo }}
              </p>
              <p v-if="registro.departamento">
                <strong>Departamento:</strong> {{ registro.departamento.nome }}
              </p>
            </div>
          </div>
          <div class="registro-actions">
            <button @click="editRegistro(registro)" class="btn-edit">✏️</button>
            <button @click="deleteRegistro(registro)" class="btn-delete">🗑️</button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showForm" class="modal-overlay" @click.self="closeForm">
      <div class="modal">
        <RegistroForm
          :registro="editingRegistro"
          :instituicoes="instituicoes"
          :loading="formLoading"
          @submit="saveRegistro"
          @cancel="closeForm"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useRegistroStore } from '@/stores/registro'
import { useInstituicaoStore } from '@/stores/instituicao'
import { useAppToast } from '@/composables/useToast'
import RegistroForm from '@/components/RegistroForm.vue'
import type { Registro } from '@/types'

const registroStore = useRegistroStore()
const instituicaoStore = useInstituicaoStore()
const { showSuccess, showError } = useAppToast()

const { registros, loading, error } = storeToRefs(registroStore)
const { instituicoes } = storeToRefs(instituicaoStore)

const showForm = ref(false)
const formLoading = ref(false)
const editingRegistro = ref<Registro | null>(null)

onMounted(async () => {
  await loadRegistros()
  await instituicaoStore.fetchInstituicoes()
})

const loadRegistros = async () => {
  try {
    await registroStore.fetchRegistros()
  } catch (error: any) {
    showError('Erro ao carregar registros')
  }
}

const editRegistro = (registro: Registro) => {
  editingRegistro.value = registro
  showForm.value = true
}

const deleteRegistro = async (registro: Registro) => {
  if (confirm(`Deseja realmente excluir "${registro.nome}"?`)) {
    try {
      await registroStore.deleteRegistro(registro.id)
      showSuccess('Funcionário excluído com sucesso')
    } catch (error: any) {
      showError(error.message || 'Erro ao excluir funcionário')
    }
  }
}

const saveRegistro = async (data: Partial<Registro>) => {
  formLoading.value = true
  try {
    if (editingRegistro.value) {
      await registroStore.updateRegistro(editingRegistro.value.id, data)
      showSuccess('Funcionário atualizado com sucesso')
    } else {
      await registroStore.createRegistro(data as any)
      showSuccess('Funcionário criado com sucesso')
    }
    closeForm()
  } catch (error: any) {
    showError(error.message || 'Erro ao salvar funcionário')
  } finally {
    formLoading.value = false
  }
}

const closeForm = () => {
  showForm.value = false
  editingRegistro.value = null
}
</script>

<style scoped>
.registros-view {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.header {
  margin-bottom: 30px;
}

.header h1 {
  margin: 0 0 15px 0;
  color: #2c3e50;
}

.header-actions {
  margin-top: 10px;
}

.btn-add {
  background: #27ae60;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  font-weight: bold;
}

.btn-add:hover {
  background: #219a52;
}

.registros-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.registro-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.registro-info h3 {
  margin: 0 0 10px 0;
  color: #2c3e50;
}

.registro-details p {
  margin: 5px 0;
  color: #7f8c8d;
  font-size: 14px;
}

.registro-actions {
  display: flex;
  gap: 10px;
}

.btn-edit, .btn-delete {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 16px;
  padding: 8px;
  transition: transform 0.2s;
}

.btn-edit:hover, .btn-delete:hover {
  transform: scale(1.2);
}

.loading, .error {
  text-align: center;
  padding: 40px;
  font-size: 18px;
}

.error {
  color: #e74c3c;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  border-radius: 8px;
  padding: 20px;
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}
</style>