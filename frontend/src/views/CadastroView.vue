<template>
  <div class="cadastro-view">
    <div class="forms-section">
      <div class="form-tabs">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          class="tab-button"
          :class="{ active: activeTab === tab.id }"
          @click="activeTab = tab.id"
        >
          {{ tab.label }}
        </button>
      </div>

      <div class="form-content">
        <InstituicaoForm
          v-if="activeTab === 'instituicao'"
          :editId="editingId"
          @submit="handleInstituicaoSubmit"
          @cancel="handleCancel"
        />
        <EnderecoForm
          v-if="activeTab === 'endereco'"
          :editId="editingId"
          @submit="handleEnderecoSubmit"
          @cancel="handleCancel"
        />
        <DepartamentoForm
          v-if="activeTab === 'departamento'"
          :editId="editingId"
          @submit="handleDepartamentoSubmit"
          @cancel="handleCancel"
        />
      </div>
    </div>

    <div class="list-container">
      <div v-if="loading" class="loading">Carregando...</div>
      <div v-else-if="error" class="error">{{ error }}</div>
      <div v-else class="hierarchical-list">
        <div
          v-for="instituicao in instituicoesComContadores"
          :key="instituicao.id"
          class="instituicao-card"
        >
          <div class="card-header">
            <h3>{{ instituicao.nome_longo }}</h3>
            <div class="card-actions">
              <span class="counter">{{ instituicao.registro_count || 0 }} registros</span>
              <button @click="editItem('instituicao', instituicao.id)" class="btn-edit">Editar</button>
              <button @click="deleteItem('instituicao', instituicao.id)" class="btn-delete">Deletar</button>
            </div>
          </div>

          <div class="enderecos-list">
            <div
              v-for="endereco in getEnderecosComContadores(instituicao.id)"
              :key="endereco.id"
              class="endereco-card"
            >
              <div class="card-header">
                <h4>{{ endereco.titulo }}</h4>
                <div class="card-actions">
                  <span class="counter">{{ endereco.registro_count || 0 }} registros</span>
                  <button @click="editItem('endereco', endereco.id)" class="btn-edit">Editar</button>
                  <button @click="deleteItem('endereco', endereco.id)" class="btn-delete">Deletar</button>
                </div>
              </div>

              <div class="departamentos-list">
                <div
                  v-for="departamento in getDepartamentosComContadores(endereco.id)"
                  :key="departamento.id"
                  class="departamento-card"
                >
                  <div class="card-header">
                    <h5>{{ departamento.nome }}</h5>
                    <div class="card-actions">
                      <span class="counter">{{ departamento.registro_count || 0 }} registros</span>
                      <button @click="editItem('departamento', departamento.id)" class="btn-edit">Editar</button>
                      <button @click="deleteItem('departamento', departamento.id)" class="btn-delete">Deletar</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useInstituicaoStore } from '@/stores/instituicao';
import { useEnderecoStore } from '@/stores/endereco';
import { useDepartamentoStore } from '@/stores/departamento';
import { useRegistroStore } from '@/stores/registro';
import { useAppToast } from '@/composables/useToast';
import InstituicaoForm from '@/components/InstituicaoForm.vue';
import EnderecoForm from '@/components/EnderecoForm.vue';
import DepartamentoForm from '@/components/DepartamentoForm.vue';

interface Tab {
  id: string;
  label: string;
}

const instituicaoStore = useInstituicaoStore();
const enderecoStore = useEnderecoStore();
const departamentoStore = useDepartamentoStore();
const registroStore = useRegistroStore();
const { showSuccess, showError } = useAppToast();

const { instituicoes, loading, error } = storeToRefs(instituicaoStore);

const activeTab = ref<string>('instituicao');
const editingId = ref<number | null>(null);

const tabs: Tab[] = [
  { id: 'instituicao', label: 'Instituição' },
  { id: 'endereco', label: 'Endereço' },
  { id: 'departamento', label: 'Departamento' }
];

onMounted(async () => {
  await Promise.all([
    instituicaoStore.fetchInstituicoes(),
    enderecoStore.fetchEnderecos(),
    departamentoStore.fetchDepartamentos(),
    registroStore.fetchRegistros()
  ]);
});

// Computed para contadores de registros
const instituicoesComContadores = computed(() => {
  return instituicoes.value.map(instituicao => ({
    ...instituicao,
    registro_count: registroStore.registros.filter(r => r.instituicao_id === instituicao.id).length
  }));
});

const getEnderecosComContadores = (instituicaoId: number) => {
  return enderecoStore.enderecos
    .filter(endereco => endereco.instituicao_id === instituicaoId)
    .map(endereco => ({
      ...endereco,
      registro_count: registroStore.registros.filter(r => r.endereco_id === endereco.id).length
    }));
};

const getDepartamentosComContadores = (enderecoId: number) => {
  return departamentoStore.departamentos
    .filter(departamento => departamento.endereco_id === enderecoId)
    .map(departamento => ({
      ...departamento,
      registro_count: registroStore.registros.filter(r => r.departamento_id === departamento.id).length
    }));
};

const handleInstituicaoSubmit = async (data: any) => {
  console.log('Recebido no handler:', data)
  try {
    if (editingId.value) {
      await instituicaoStore.updateInstituicao(editingId.value, data);
    } else {
      await instituicaoStore.createInstituicao(data);
    }
    showSuccess(`Instituição salva com sucesso`);
    editingId.value = null;
    // Recarregar todos os dados
    await Promise.all([
      instituicaoStore.fetchInstituicoes(),
      enderecoStore.fetchEnderecos(),
      departamentoStore.fetchDepartamentos(),
      registroStore.fetchRegistros()
    ]);
  } catch (err: any) {
    console.error('Erro ao salvar instituição:', err)
    showError(err.message || 'Erro ao salvar instituição');
  }
};

const handleEnderecoSubmit = async (data: any) => {
  try {
    if (editingId.value) {
      await enderecoStore.updateEndereco(editingId.value, data);
    } else {
      await enderecoStore.createEndereco(data);
    }
    showSuccess(`Endereço salvo com sucesso`);
    editingId.value = null;
    // Recarregar todos os dados
    await Promise.all([
      instituicaoStore.fetchInstituicoes(),
      enderecoStore.fetchEnderecos(),
      departamentoStore.fetchDepartamentos(),
      registroStore.fetchRegistros()
    ]);
  } catch (err: any) {
    showError(err.message || 'Erro ao salvar endereço');
  }
};

const handleDepartamentoSubmit = async (data: any) => {
  try {
    if (editingId.value) {
      await departamentoStore.updateDepartamento(editingId.value, data);
    } else {
      await departamentoStore.createDepartamento(data);
    }
    showSuccess(`Departamento salvo com sucesso`);
    editingId.value = null;
    // Recarregar todos os dados
    await Promise.all([
      instituicaoStore.fetchInstituicoes(),
      enderecoStore.fetchEnderecos(),
      departamentoStore.fetchDepartamentos(),
      registroStore.fetchRegistros()
    ]);
  } catch (err: any) {
    showError(err.message || 'Erro ao salvar departamento');
  }
};

const editItem = (type: string, id: number) => {
  editingId.value = id;
  activeTab.value = type;
};

const deleteItem = async (type: string, id: number) => {
  try {
    if (confirm('Tem certeza que deseja deletar este item?')) {
      switch (type) {
        case 'instituicao':
          await instituicaoStore.deleteInstituicao(id);
          showSuccess('Instituição deletada com sucesso');
          break;
        case 'endereco':
          await enderecoStore.deleteEndereco(id);
          showSuccess('Endereço deletado com sucesso');
          break;
        case 'departamento':
          await departamentoStore.deleteDepartamento(id);
          showSuccess('Departamento deletado com sucesso');
          break;
      }
      
      // Recarregar todos os dados
      await Promise.all([
        instituicaoStore.fetchInstituicoes(),
        enderecoStore.fetchEnderecos(),
        departamentoStore.fetchDepartamentos(),
        registroStore.fetchRegistros()
      ]);
    }
  } catch (err: any) {
    showError(err.message || 'Erro ao deletar item');
  }
};

const handleCancel = () => {
  editingId.value = null;
};
</script>

<style scoped>
.cadastro-view {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.forms-section {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.form-tabs {
  display: flex;
  border-bottom: 1px solid #ddd;
}

.tab-button {
  padding: 1rem 2rem;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 1rem;
  border-bottom: 3px solid transparent;
  transition: all 0.3s ease;
}

.tab-button:hover {
  background-color: #f8f9fa;
}

.tab-button.active {
  border-bottom-color: #007bff;
  color: #007bff;
  font-weight: 600;
}

.form-content {
  padding: 2rem;
}

.list-container {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 2rem;
}

.hierarchical-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.instituicao-card,
.endereco-card,
.departamento-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 0.5rem;
}

.instituicao-card {
  background-color: #f8f9fa;
  border-left: 4px solid #007bff;
}

.endereco-card {
  margin-left: 2rem;
  background-color: #fff;
  border-left: 4px solid #28a745;
}

.departamento-card {
  margin-left: 4rem;
  background-color: #f8f9fa;
  border-left: 4px solid #ffc107;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.card-header h3,
.card-header h4,
.card-header h5 {
  margin: 0;
  color: #333;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.counter {
  background: #007bff;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.btn-edit,
.btn-delete {
  padding: 0.25rem 0.75rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.875rem;
}

.btn-edit {
  background: #28a745;
  color: white;
}

.btn-delete {
  background: #dc3545;
  color: white;
}

.btn-edit:hover {
  background: #1e7e34;
}

.btn-delete:hover {
  background: #c82333;
}

.enderecos-list,
.departamentos-list {
  margin-top: 0.5rem;
}

.loading,
.error {
  text-align: center;
  padding: 2rem;
  font-size: 1.2rem;
}

.error {
  color: #dc3545;
}
</style>