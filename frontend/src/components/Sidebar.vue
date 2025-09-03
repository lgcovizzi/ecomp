<template>
  <div class="sidebar" :class="{ collapsed: isCollapsed }">
    <button class="toggle-btn" @click="toggleSidebar">
      <span v-if="!isCollapsed">☰</span>
      <span v-else>☰</span>
    </button>
    
    <nav class="nav-menu">
      <router-link to="/" class="nav-item" title="Home">
        <span class="icon">🏠</span>
        <span class="text" v-if="!isCollapsed">Home</span>
      </router-link>
      
      <router-link to="/cadastro" class="nav-item" title="Cadastro">
        <span class="icon">📝</span>
        <span class="text" v-if="!isCollapsed">Cadastro</span>
      </router-link>
      
      <router-link to="/registros" class="nav-item" title="Registro">
        <span class="icon">👤</span>
        <span class="text" v-if="!isCollapsed">Registro</span>
      </router-link>
    </nav>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const isCollapsed = ref(false)

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value
}
</script>

<style scoped>
.sidebar {
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  width: 200px;
  background: #2c3e50;
  transition: width 0.3s ease;
  z-index: 1000;
  display: flex;
  flex-direction: column;
}

.sidebar.collapsed {
  width: 60px;
}

.toggle-btn {
  background: none;
  border: none;
  color: white;
  padding: 15px;
  cursor: pointer;
  font-size: 18px;
  width: 100%;
  text-align: left;
}

.nav-menu {
  flex: 1;
  padding: 10px 0;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 12px 15px;
  color: white;
  text-decoration: none;
  transition: background-color 0.2s;
  white-space: nowrap;
}

.nav-item:hover {
  background-color: #34495e;
}

.nav-item.router-link-active {
  background-color: #3498db;
}

.icon {
  font-size: 20px;
  margin-right: 10px;
  min-width: 24px;
  text-align: center;
}

.sidebar.collapsed .icon {
  margin-right: 0;
}

.text {
  font-size: 14px;
}
</style>