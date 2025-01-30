<script setup>
import Sidebar from '../../Components/Sidebar.vue'
import Header from '../../Components/Header.vue'
import Board from '../../Components/Board.vue'
</script>

<template>
    <Head title="Scrum Board" />
  <div class="min-h-screen bg-gray-50">
    <Sidebar />
    <Header />
    <Board />
  </div>
</template>