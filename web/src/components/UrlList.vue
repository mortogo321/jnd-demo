<template>
  <Card>
    <CardHeader>
      <CardTitle>{{ title }}</CardTitle>
    </CardHeader>
    <CardContent>
      <div v-if="urlStore.loading" class="text-center py-8 text-muted-foreground">
        Loading...
      </div>

      <div v-else-if="displayUrls.length === 0" class="text-center py-12">
        <LinkIcon class="w-12 h-12 mx-auto text-muted-foreground mb-4" />
        <p class="text-muted-foreground">No URLs found. Create your first shortened URL above!</p>
      </div>

      <div v-else class="space-y-4">
        <Card v-for="url in displayUrls" :key="url.id" class="border-2 hover:border-primary/50 transition-colors">
          <CardContent class="p-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <div class="flex-1 space-y-3">
                <!-- Original URL -->
                <div class="flex items-start gap-2">
                  <ExternalLink class="w-4 h-4 text-muted-foreground mt-1 flex-shrink-0" />
                  <div class="flex-1 min-w-0">
                    <p class="text-xs text-muted-foreground mb-1">Original URL</p>
                    <a
                      :href="url.original_url"
                      target="_blank"
                      rel="noopener"
                      class="text-sm text-primary hover:underline break-all"
                    >
                      {{ url.original_url }}
                    </a>
                  </div>
                </div>

                <!-- Short URL -->
                <div class="flex items-start gap-2">
                  <Link2 class="w-4 h-4 text-muted-foreground mt-1 flex-shrink-0" />
                  <div class="flex-1 min-w-0">
                    <p class="text-xs text-muted-foreground mb-1">Short URL</p>
                    <a
                      :href="getShortUrl(url.short_code)"
                      target="_blank"
                      class="text-sm font-medium text-primary hover:underline break-all"
                    >
                      {{ getShortUrl(url.short_code) }}
                    </a>
                  </div>
                </div>

                <!-- Meta Info -->
                <div class="flex items-center gap-4 text-xs text-muted-foreground">
                  <div class="flex items-center gap-1">
                    <MousePointerClick class="w-3 h-3" />
                    <span class="font-semibold text-green-600">{{ url.clicks }} clicks</span>
                  </div>
                  <div class="flex items-center gap-1">
                    <Calendar class="w-3 h-3" />
                    <span>{{ formatDate(url.created_at) }}</span>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex gap-2 md:flex-col md:items-end">
                <Button
                  size="sm"
                  variant="outline"
                  @click="copyUrl(url.short_code, url.id)"
                  :class="copiedId === url.id ? 'bg-green-50 border-green-500' : ''"
                >
                  <Check v-if="copiedId === url.id" class="w-4 h-4 mr-1 text-green-600" />
                  <Copy v-else class="w-4 h-4 mr-1" />
                  {{ copiedId === url.id ? 'Copied!' : 'Copy' }}
                </Button>
                <Button size="sm" variant="destructive" @click="deleteUrl(url.id)">
                  <Trash2 class="w-4 h-4 mr-1" />
                  Delete
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </CardContent>
  </Card>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useUrlStore } from '@/stores/urlStore'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import {
  LinkIcon,
  ExternalLink,
  Link2,
  MousePointerClick,
  Calendar,
  Copy,
  Check,
  Trash2
} from 'lucide-vue-next'
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

async function copyUrl(shortCode: string, id: number) {
  const shortUrl = getShortUrl(shortCode)

  try {
    await navigator.clipboard.writeText(shortUrl)
    copiedId.value = id
    setTimeout(() => {
      copiedId.value = null
    }, 2000)
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
