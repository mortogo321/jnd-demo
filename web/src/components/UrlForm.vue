<template>
  <div class="url-form">
    <h2>{{ title }}</h2>
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <input
          v-model="originalUrl"
          type="url"
          placeholder="Enter your long URL here..."
          class="form-input"
          required
        />
      </div>

      <button type="submit" class="btn btn-primary" :disabled="urlStore.loading">
        {{ urlStore.loading ? 'Shortening...' : 'Shorten URL' }}
      </button>

      <div v-if="error" class="error-message">{{ error }}</div>
      <div v-if="shortUrl" class="success-message">
        <p>Your shortened URL:</p>
        <div class="short-url-container">
          <input :value="shortUrl" readonly class="short-url-input" ref="shortUrlInput" />
          <button @click="copyToClipboard" class="btn btn-secondary">
            {{ copied ? 'Copied!' : 'Copy' }}
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useUrlStore } from '@/stores/urlStore'

interface Props {
  title?: string
}

withDefaults(defineProps<Props>(), {
  title: 'Shorten Your URL'
})

const emit = defineEmits<{
  urlCreated: [url: string]
}>()

const urlStore = useUrlStore()
const originalUrl = ref('')
const shortUrl = ref('')
const error = ref('')
const copied = ref(false)
const shortUrlInput = ref<HTMLInputElement | null>(null)

async function handleSubmit() {
  error.value = ''
  shortUrl.value = ''
  copied.value = false

  try {
    const url = await urlStore.createUrl(originalUrl.value)
    shortUrl.value = urlStore.getShortUrl(url.short_code)
    originalUrl.value = ''
    emit('urlCreated', shortUrl.value)
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to shorten URL'
  }
}

async function copyToClipboard() {
  if (shortUrlInput.value) {
    try {
      await navigator.clipboard.writeText(shortUrl.value)
      copied.value = true
      setTimeout(() => {
        copied.value = false
      }, 2000)
    } catch (err) {
      console.error('Failed to copy:', err)
    }
  }
}
</script>

<style scoped>
.url-form {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h2 {
  margin-top: 0;
  color: #2c3e50;
}

.form-group {
  margin-bottom: 1rem;
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.form-input:focus {
  outline: none;
  border-color: #3498db;
}

.btn {
  padding: 0.75rem 2rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.3s;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-primary {
  background: #3498db;
  color: white;
  width: 100%;
}

.btn-primary:hover:not(:disabled) {
  background: #2980b9;
}

.btn-secondary {
  background: #95a5a6;
  color: white;
  white-space: nowrap;
}

.btn-secondary:hover {
  background: #7f8c8d;
}

.error-message {
  margin-top: 1rem;
  padding: 0.75rem;
  background: #fee;
  color: #c33;
  border-radius: 4px;
}

.success-message {
  margin-top: 1rem;
  padding: 1rem;
  background: #efe;
  border-radius: 4px;
}

.success-message p {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
  font-weight: 600;
}

.short-url-container {
  display: flex;
  gap: 0.5rem;
}

.short-url-input {
  flex: 1;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-family: monospace;
  background: white;
}
</style>
