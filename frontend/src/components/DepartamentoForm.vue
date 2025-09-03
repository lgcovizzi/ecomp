<template>
  <form @submit.prevent="submitForm">
    <div class="form-group">
      <label for="instituicao">Instituição</label>
      <select id="instituicao" v-model="selectedInstituicao" required>
        <option v-for="instituicao in instituicoes" :key="instituicao.id" :value="instituicao.id">
          {{ instituicao.nome_curto }}
        </option>
      </select>
    </div>
    <div class="form-group">
      <label for="endereco">Endereço</label>
      <select id="endereco" v-model="departamento.endereco_id" required :disabled="!selectedInstituicao">
        <option v-for="endereco in enderecos" :key="endereco.id" :value="endereco.id">
          {{ endereco.titulo }}
        </option>
      </select>
    </div>
    <div class="form-group">
      <label for="nome">Nome</label>
      <input id="nome" v-model="departamento.nome" type="text" required />
    </div>
    <button type="submit">Salvar</button>
  </form>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useInstituicaoStore } from '@/stores/instituicao';
import { useEnderecoStore } from '@/stores/endereco';
import { useDepartamentoStore } from '@/stores/departamento';
import type { Departamento } from '@/types';

const props = defineProps<{
  departamento?: Departamento
  loading?: boolean
  editId?: number | null
}>()

const emit = defineEmits<{
  submit: [data: Partial<Departamento>]
  cancel: []
}>()

const instituicaoStore = useInstituicaoStore();
const enderecoStore = useEnderecoStore();
const departamentoStore = useDepartamentoStore();

const selectedInstituicao = ref<number | null>(null);
const departamento = ref<Partial<Departamento>>({
  endereco_id: null,
  nome: '',
  instituicao_id: null
});

const { instituicoes } = storeToRefs(instituicaoStore);
const { enderecos } = storeToRefs(enderecoStore);

onMounted(async () => {
  await instituicaoStore.fetchInstituicoes();
  
  if (props.editId) {
    await departamentoStore.fetchDepartamentos();
    const departamentoData = departamentoStore.departamentos.find(d => d.id === props.editId);
    if (departamentoData) {
      departamento.value = {
        instituicao_id: departamentoData.instituicao_id,
        endereco_id: departamentoData.endereco_id,
        nome: departamentoData.nome
      };
      selectedInstituicao.value = departamentoData.instituicao_id;
      await enderecoStore.fetchEnderecos(departamentoData.instituicao_id);
    }
  }
});

watch(selectedInstituicao, (newVal) => {
  if (newVal) {
    departamento.value.instituicao_id = newVal;
    enderecoStore.fetchEnderecos(newVal);
  }
});

watch(() => props.editId, async (newEditId) => {
  if (newEditId) {
    await departamentoStore.fetchDepartamentos();
    const departamentoData = departamentoStore.departamentos.find(d => d.id === newEditId);
    if (departamentoData) {
      departamento.value = {
        instituicao_id: departamentoData.instituicao_id,
        endereco_id: departamentoData.endereco_id,
        nome: departamentoData.nome
      };
      selectedInstituicao.value = departamentoData.instituicao_id;
      await enderecoStore.fetchEnderecos(departamentoData.instituicao_id);
    }
  } else {
    departamento.value = {
      endereco_id: null,
      nome: '',
      instituicao_id: null
    };
    selectedInstituicao.value = null;
  }
});

const submitForm = () => {
  emit('submit', departamento.value);
};
</script>

<style scoped>
.form-group {
  margin-bottom: 15px;
}
label {
  display: block;
  margin-bottom: 5px;
}
input, select {
  width: 100%;
  padding: 8px;
  box-sizing: border-box;
}
</style>