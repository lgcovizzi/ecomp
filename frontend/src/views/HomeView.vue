<template>
  <div class="home-view">
    <h1>Dashboard</h1>
    
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">🏢</div>
        <div class="stat-content">
          <h3>{{ instituicoesCount }}</h3>
          <p>Instituições</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon">📍</div>
        <div class="stat-content">
          <h3>{{ enderecosCount }}</h3>
          <p>Endereços</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon">🏢</div>
        <div class="stat-content">
          <h3>{{ departamentosCount }}</h3>
          <p>Departamentos</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon">👤</div>
        <div class="stat-content">
          <h3>{{ registrosCount }}</h3>
          <p>Usuários</p>
        </div>
      </div>
    </div>


  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useInstituicaoStore } from '@/stores/instituicao'
import { useEnderecoStore } from '@/stores/endereco'
import { useDepartamentoStore } from '@/stores/departamento'
import { useRegistroStore } from '@/stores/registro'

const instituicaoStore = useInstituicaoStore()
const enderecoStore = useEnderecoStore()
const departamentoStore = useDepartamentoStore()
const registroStore = useRegistroStore()

const instituicoesCount = ref(0)
const enderecosCount = ref(0)
const departamentosCount = ref(0)
const registrosCount = ref(0)

onMounted(async () => {
  try {
    await Promise.all([
      instituicaoStore.fetchInstituicoes(),
      enderecoStore.fetchEnderecos(),
      departamentoStore.fetchDepartamentos(),
      registroStore.fetchRegistros()
    ])
    
    instituicoesCount.value = instituicaoStore.instituicoes.length
    enderecosCount.value = enderecoStore.enderecos.length
    departamentosCount.value = departamentoStore.departamentos.length
    registrosCount.value = registroStore.registros.length
  } catch (error) {
    console.error('Erro ao carregar estatísticas:', error)
  }
})
</script>

<style scoped>
.home-view {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.home-view h1 {
  margin-bottom: 30px;
  color: #2c3e50;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.stat-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 15px;
}

.stat-icon {
  font-size: 40px;
  line-height: 1;
}

.stat-content h3 {
  margin: 0;
  font-size: 28px;
  color: #2c3e50;
}

.stat-content p {
  margin: 0;
  color: #7f8c8d;
  font-size: 14px;
}

.quick-actions h2 {
  margin-bottom: 20px;
  color: #2c3e50;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.action-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  padding: 30px;
  text-decoration: none;
  color: inherit;
  transition: transform 0.2s, box-shadow 0.2s;
  text-align: center;
}

.action-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.action-icon {
  font-size: 48px;
  margin-bottom: 15px;
}

.action-card h3 {
  margin: 0 0 10px 0;
  color: #2c3e50;
}

.action-card p {
  margin: 0;
  color: #7f8c8d;
  font-size: 14px;
}
</style>
