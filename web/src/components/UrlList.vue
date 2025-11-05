<template>
  <div class="url-list">
    <h2>{{ title }}</h2>

    <div v-if="urlStore.loading" class="loading">Loading...</div>

    <div v-else-if="displayUrls.length === 0" class="empty-state">
      <p>No URLs found. Create your first shortened URL above!</p>
    </div>

    <div v-else class="url-items">
      <div v-for="url in displayUrls" :key="url.id" class="url-item">
        <div class="url-info">
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
          <button @click="copyUrl(url.short_code)" class="btn btn-small btn-copy">
            {{ copiedId === url.id ? 'Copied!' : 'Copy' }}
          </button>
          <button @click="deleteUrl(url.id)" class="btn btn-small btn-danger">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useUrlStore } from '@/stores/urlStore'
import type { ShortenedUrl } from '@/types'

interface Props {
  urls: ShortenedUrl[]
  title?: string
  showUser?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Your URLs',
  showUser: false
})

const emit = defineEmits<{
  deleted: [id: number]
}>()

const urlStore = useUrlStore()
const copiedId = ref<number | null>(null)

const displayUrls = computed(() => props.urls)

function getShortUrl(shortCode: string): string {
  return urlStore.getShortUrl(shortCode)
}

async function copyUrl(shortCode: string) {
  const shortUrl = getShortUrl(shortCode)
  const url = props.urls.find((u) => u.short_code === shortCode)

  try {
    await navigator.clipboard.writeText(shortUrl)
    if (url) {
      copiedId.value = url.id
      setTimeout(() => {
        copiedId.value = null
      }, 2000)
    }
  } catch (err) {
    console.error('Failed to copy:', err)
  }
}

async function deleteUrl(id: number) {
  if (confirm('Are you sure you want to delete this URL?')) {
    try {
      await urlStore.deleteUrl(id)
      emit('deleted', id)
    } catch (error) {
      console.error('Failed to delete URL:', error)
    }
  }
}

function formatDate(dateString: string): string {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>

<style scoped>
.url-list {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h2 {
  margin-top: 0;
  color: #2c3e50;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #7f8c8d;
}

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

.url-original,
.url-short {
  margin-bottom: 0.5rem;
}

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

.btn-small {
  padding: 0.4rem 0.8rem;
}

.btn-copy {
  background: #3498db;
  color: white;
}

.btn-copy:hover {
  background: #2980b9;
}

.btn-danger {
  background: #e74c3c;
  color: white;
}

.btn-danger:hover {
  background: #c0392b;
}
</style>
