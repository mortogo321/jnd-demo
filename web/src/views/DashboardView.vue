<template>
  <div class="dashboard">
    <div class="container">
      <div class="dashboard-header">
        <h1>Dashboard</h1>
        <p>Manage your shortened URLs</p>
      </div>

      <div class="dashboard-content">
        <UrlForm title="Create New Short URL" @url-created="handleUrlCreated" />

        <div class="url-list-section">
          <UrlList :urls="urlStore.urls" @deleted="handleDeleted" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useUrlStore } from '@/stores/urlStore'
import UrlForm from '@/components/UrlForm.vue'
import UrlList from '@/components/UrlList.vue'

const urlStore = useUrlStore()

onMounted(async () => {
  await urlStore.fetchUrls()
})

function handleUrlCreated() {
  // URL is already added to the list by the store
}

function handleDeleted() {
  // URL is already removed from the list by the store
}
</script>

<style scoped>
.dashboard {
  min-height: calc(100vh - 60px);
  background: #f5f7fa;
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.dashboard-header {
  margin-bottom: 2rem;
}

.dashboard-header h1 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
}

.dashboard-header p {
  margin: 0;
  color: #7f8c8d;
}

.dashboard-content {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.url-list-section {
  width: 100%;
}

@media (max-width: 768px) {
  .dashboard {
    padding: 1rem 0;
  }
}
</style>
