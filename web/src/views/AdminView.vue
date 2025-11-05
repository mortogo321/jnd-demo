<template>
  <div class="admin">
    <div class="container">
      <div class="admin-header">
        <h1>Admin Panel</h1>
        <p>Manage all shortened URLs</p>
      </div>

      <div class="admin-stats">
        <div class="stat-card">
          <h3>Total URLs</h3>
          <p class="stat-number">{{ urlStore.pagination.total }}</p>
        </div>
        <div class="stat-card">
          <h3>Current Page</h3>
          <p class="stat-number">{{ urlStore.pagination.currentPage }} / {{ urlStore.pagination.lastPage }}</p>
        </div>
      </div>

      <div class="admin-content">
        <div v-if="urlStore.loading" class="loading">Loading...</div>

        <div v-else-if="urlStore.adminUrls.length === 0" class="empty-state">
          <p>No URLs found in the system.</p>
        </div>

        <div v-else class="url-items">
          <div v-for="url in urlStore.adminUrls" :key="url.id" class="url-item">
            <div class="url-info">
              <div class="url-id">
                <strong>ID:</strong> {{ url.id }}
              </div>
              <div class="url-original">
                <strong>Original:</strong>
                <a :href="url.original_url" target="_blank" rel="noopener">{{ url.original_url }}</a>
              </div>
              <div class="url-short">
                <strong>Short URL:</strong>
                <a :href="getShortUrl(url.short_code)" target="_blank">{{ getShortUrl(url.short_code) }}</a>
              </div>
              <div class="url-meta">
                <span class="clicks">Clicks: {{ url.clicks }}</span>
                <span class="date">Created: {{ formatDate(url.created_at) }}</span>
              </div>
            </div>

            <div class="url-actions">
              <button @click="deleteUrl(url.id)" class="btn btn-small btn-danger">Delete</button>
            </div>
          </div>
        </div>

        <div v-if="urlStore.pagination.lastPage > 1" class="pagination">
          <button
            @click="changePage(urlStore.pagination.currentPage - 1)"
            :disabled="urlStore.pagination.currentPage === 1"
            class="btn btn-secondary"
          >
            Previous
          </button>
          <span class="page-info">
            Page {{ urlStore.pagination.currentPage }} of {{ urlStore.pagination.lastPage }}
          </span>
          <button
            @click="changePage(urlStore.pagination.currentPage + 1)"
            :disabled="urlStore.pagination.currentPage === urlStore.pagination.lastPage"
            class="btn btn-secondary"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useUrlStore } from '@/stores/urlStore'

const urlStore = useUrlStore()

onMounted(async () => {
  await urlStore.fetchAdminUrls()
})

function getShortUrl(shortCode: string): string {
  return urlStore.getShortUrl(shortCode)
}

async function deleteUrl(id: number) {
  if (confirm('Are you sure you want to delete this URL? This action cannot be undone.')) {
    try {
      await urlStore.deleteAdminUrl(id)
    } catch (error) {
      console.error('Failed to delete URL:', error)
      alert('Failed to delete URL. Please try again.')
    }
  }
}

async function changePage(page: number) {
  await urlStore.fetchAdminUrls(page)
}

function formatDate(dateString: string): string {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<style scoped>
.admin {
  min-height: calc(100vh - 60px);
  background: #f5f7fa;
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.admin-header {
  margin-bottom: 2rem;
}

.admin-header h1 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
}

.admin-header p {
  margin: 0;
  color: #7f8c8d;
}

.admin-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.stat-card h3 {
  margin: 0 0 0.5rem 0;
  color: #7f8c8d;
  font-size: 0.9rem;
  text-transform: uppercase;
}

.stat-number {
  margin: 0;
  font-size: 2rem;
  font-weight: bold;
  color: #3498db;
}

.admin-content {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.loading,
.empty-state {
  text-align: center;
  padding: 2rem;
  color: #7f8c8d;
}

.url-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.url-item {
  padding: 1rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: box-shadow 0.3s;
}

.url-item:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.url-info {
  flex: 1;
}

.url-id,
.url-original,
.url-short {
  margin-bottom: 0.5rem;
}

.url-id strong,
.url-original strong,
.url-short strong {
  display: inline-block;
  width: 80px;
  color: #2c3e50;
}

.url-original a,
.url-short a {
  color: #3498db;
  text-decoration: none;
  word-break: break-all;
}

.url-original a:hover,
.url-short a:hover {
  text-decoration: underline;
}

.url-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.875rem;
  color: #7f8c8d;
  margin-top: 0.5rem;
}

.clicks {
  font-weight: 600;
  color: #27ae60;
}

.url-actions {
  display: flex;
  gap: 0.5rem;
}

.btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.875rem;
  transition: all 0.3s;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-small {
  padding: 0.4rem 0.8rem;
}

.btn-secondary {
  background: #95a5a6;
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background: #7f8c8d;
}

.btn-danger {
  background: #e74c3c;
  color: white;
}

.btn-danger:hover {
  background: #c0392b;
}

.pagination {
  margin-top: 2rem;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
}

.page-info {
  color: #7f8c8d;
}
</style>
