<template>
  <div class="min-h-screen py-8">
    <div class="max-w-[1200px] mx-auto px-4">
      <div class="mb-8">
        <h1 class="text-4xl font-bold mb-2">Admin Panel</h1>
        <p class="text-muted-foreground">Manage all shortened URLs</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        <Card>
          <CardContent class="pt-6 text-center">
            <h3 class="text-sm font-medium text-muted-foreground uppercase mb-2">Total URLs</h3>
            <p class="text-3xl font-bold text-primary">{{ urlStore.pagination.total }}</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent class="pt-6 text-center">
            <h3 class="text-sm font-medium text-muted-foreground uppercase mb-2">Current Page</h3>
            <p class="text-3xl font-bold text-primary">{{ urlStore.pagination.currentPage }} / {{ urlStore.pagination.lastPage }}</p>
          </CardContent>
        </Card>
      </div>

      <Card>
        <CardContent class="p-6">
          <div v-if="urlStore.loading" class="text-center py-8 text-muted-foreground">
            Loading...
          </div>

          <div v-else-if="urlStore.adminUrls.length === 0" class="text-center py-8">
            <p class="text-muted-foreground">No URLs found in the system.</p>
          </div>

          <div v-else class="space-y-4">
            <Card v-for="url in urlStore.adminUrls" :key="url.id" class="border-2 hover:border-primary/50 transition-colors">
              <CardContent class="p-4">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                  <div class="flex-1 space-y-2">
                    <div class="text-sm">
                      <strong class="text-foreground inline-block w-20">ID:</strong>
                      <span class="text-muted-foreground">{{ url.id }}</span>
                    </div>
                    <div class="text-sm">
                      <strong class="text-foreground inline-block w-20">Original:</strong>
                      <a :href="url.original_url" target="_blank" rel="noopener" class="text-primary hover:underline break-all">
                        {{ url.original_url }}
                      </a>
                    </div>
                    <div class="text-sm">
                      <strong class="text-foreground inline-block w-20">Short URL:</strong>
                      <a :href="getShortUrl(url.short_code)" target="_blank" class="text-primary hover:underline break-all">
                        {{ getShortUrl(url.short_code) }}
                      </a>
                    </div>
                    <div class="flex gap-4 text-xs text-muted-foreground mt-2">
                      <span class="font-semibold text-green-600">Clicks: {{ url.clicks }}</span>
                      <span>Created: {{ formatDate(url.created_at) }}</span>
                    </div>
                  </div>

                  <div>
                    <Button @click="deleteUrl(url.id)" variant="destructive" size="sm">
                      <Trash2 class="w-4 h-4 mr-1" />
                      Delete
                    </Button>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          <div v-if="urlStore.pagination.lastPage > 1" class="mt-8 flex justify-center items-center gap-4">
            <Button
              @click="changePage(urlStore.pagination.currentPage - 1)"
              :disabled="urlStore.pagination.currentPage === 1"
              variant="secondary"
              size="sm"
            >
              Previous
            </Button>
            <span class="text-sm text-muted-foreground">
              Page {{ urlStore.pagination.currentPage }} of {{ urlStore.pagination.lastPage }}
            </span>
            <Button
              @click="changePage(urlStore.pagination.currentPage + 1)"
              :disabled="urlStore.pagination.currentPage === urlStore.pagination.lastPage"
              variant="secondary"
              size="sm"
            >
              Next
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useUrlStore } from '@/stores/urlStore'
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Trash2 } from 'lucide-vue-next'

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
