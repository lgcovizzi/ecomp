<template>
  <div class="instituicao-card">
    <div class="card-header">
      <h3>{{ instituicao.nome_longo }} ({{ instituicao.nome_curto }})</h3>
      <div class="header-actions">
        <span class="count-badge" v-if="instituicao.registros_count !== undefined">
          {{ instituicao.registros_count }}
        </span>
        <button @click="$emit('edit', instituicao)" class="btn-edit">✏️</button>
        <button @click="$emit('delete', instituicao)" class="btn-delete">🗑️</button>
      </div>
    </div>

    <div class="enderecos-section">
      <div v-for="endereco in instituicao.enderecos" :key="endereco.id" class="endereco-card">
        <div class="endereco-header">
          <h4>{{ endereco.titulo }} - {{ endereco.cidade }}/{{ endereco.estado }}</h4>
          <div class="header-actions">
            <span class="count-badge" v-if="endereco.registros_count !== undefined">
              {{ endereco.registros_count }}
            </span>
            <button @click="$emit('editEndereco', endereco)" class="btn-edit">✏️</button>
            <button @click="$emit('deleteEndereco', endereco)" class="btn-delete">🗑️</button>
          </div>
        </div>

        <div class="departamentos-section">
          <div v-for="departamento in endereco.departamentos" :key="departamento.id" class="departamento-card">
            <div class="departamento-header">
              <h5>{{ departamento.nome }}</h5>
              <div class="header-actions">
                <span class="count-badge" v-if="departamento.registros_count !== undefined">
                  {{ departamento.registros_count }}
                </span>
                <button @click="$emit('editDepartamento', departamento)" class="btn-edit">✏️</button>
                <button @click="$emit('deleteDepartamento', departamento)" class="btn-delete">🗑️</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Instituicao } from '@/types'

defineProps<{
  instituicao: Instituicao
}>()

defineEmits<{
  edit: [instituicao: Instituicao]
  delete: [instituicao: Instituicao]
  editEndereco: [endereco: any]
  deleteEndereco: [endereco: any]
  editDepartamento: [departamento: any]
  deleteDepartamento: [departamento: any]
}>()
</script>

<style scoped>
.instituicao-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  overflow: hidden;
}

.card-header {
  background: #3498db;
  color: white;
  padding: 15px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-header h3 {
  margin: 0;
  font-size: 18px;
}

.endereco-card {
  border-left: 3px solid #2ecc71;
  margin: 10px 0 10px 20px;
  padding-left: 15px;
}

.endereco-header {
  background: #f8f9fa;
  padding: 12px 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-radius: 4px;
}

.endereco-header h4 {
  margin: 0;
  font-size: 16px;
  color: #2c3e50;
}

.departamento-card {
  border-left: 2px solid #e74c3c;
  margin: 8px 0 8px 20px;
  padding-left: 15px;
}

.departamento-header {
  background: #fff;
  padding: 10px 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border: 1px solid #dee2e6;
  border-radius: 4px;
}

.departamento-header h5 {
  margin: 0;
  font-size: 14px;
  color: #2c3e50;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.count-badge {
  background: #007bff;
  color: white;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: bold;
  min-width: 20px;
  text-align: center;
}

.btn-edit, .btn-delete {
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
  font-size: 16px;
  transition: transform 0.2s;
}

.btn-edit:hover, .btn-delete:hover {
  transform: scale(1.2);
}

.enderecos-section {
  padding: 15px;
}

.departamentos-section {
  margin-top: 10px;
}
</style>