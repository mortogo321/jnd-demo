<script setup lang="ts">
import { ref } from 'vue'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { useUrlStore } from '@/stores/urlStore'
import { LinkIcon, Copy, Check } from 'lucide-vue-next'

const urlStore = useUrlStore()
const url = ref('')
const loading = ref(false)
const error = ref('')
const createdUrl = ref<any>(null)
const copied = ref(false)

const handleSubmit = async () => {
  if (!url.value) {
    error.value = 'Please enter a URL'
    return
  }

  try {
    loading.value = true
    error.value = ''
    createdUrl.value = await urlStore.createUrl(url.value)
    url.value = ''
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to create short URL'
  } finally {
    loading.value = false
  }
}

const copyToClipboard = async () => {
  if (createdUrl.value?.short_url) {
    await navigator.clipboard.writeText(createdUrl.value.short_url)
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2000)
  }
}

const createAnother = () => {
  createdUrl.value = null
  copied.value = false
}
</script>

<template>
  <Card class="w-full md:w-1/2 mx-auto">
    <CardHeader>
      <CardTitle class="flex items-center gap-2">
        <LinkIcon class="w-6 h-6" />
        Create New Short URL
      </CardTitle>
    </CardHeader>
    <CardContent class="space-y-4">
      <!-- Success State -->
      <div v-if="createdUrl" class="space-y-4">
        <div class="p-4 bg-muted rounded-lg space-y-3">
          <div>
            <p class="text-sm text-muted-foreground mb-1">Original URL</p>
            <p class="text-sm font-medium break-all">{{ createdUrl.original_url }}</p>
          </div>
          <div>
            <p class="text-sm text-muted-foreground mb-1">Short URL</p>
            <div class="flex items-center gap-2">
              <a
                :href="createdUrl.short_url"
                target="_blank"
                class="text-sm font-medium text-primary hover:underline break-all flex-1"
              >
                {{ createdUrl.short_url }}
              </a>
              <Button
                size="icon"
                variant="outline"
                @click="copyToClipboard"
                :class="copied ? 'bg-green-50 border-green-500' : ''"
              >
                <Check v-if="copied" class="w-4 h-4 text-green-600" />
                <Copy v-else class="w-4 h-4" />
              </Button>
            </div>
          </div>
        </div>
        <Button @click="createAnother" variant="outline" class="w-full">
          Create Another URL
        </Button>
      </div>

      <!-- Create Form -->
      <div v-else class="space-y-4">
        <div class="space-y-2">
          <label for="url-input" class="text-sm font-medium">Enter your long URL here</label>
          <Input
            id="url-input"
            v-model="url"
            type="url"
            placeholder="https://example.com/very/long/url"
            @keyup.enter="handleSubmit"
            :disabled="loading"
          />
          <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        </div>

        <div class="flex justify-center">
          <Button @click="handleSubmit" :disabled="loading || !url" size="lg" class="min-w-[200px]">
            <LinkIcon v-if="!loading" class="w-4 h-4 mr-2" />
            <span v-if="loading">Creating...</span>
            <span v-else>Shorten URL</span>
          </Button>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
