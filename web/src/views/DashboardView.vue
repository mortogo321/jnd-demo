<template>
  <div class="min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-7xl">
      <div class="mb-8">
        <h1 class="text-4xl font-bold mb-2">Dashboard</h1>
        <p class="text-muted-foreground">Manage your shortened URLs</p>
      </div>

      <div class="space-y-8">
        <CreateShortUrlCard />

        <div class="w-full">
          <UrlList :urls="urlStore.urls" @deleted="handleDeleted" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useUrlStore } from '@/stores/urlStore'
import CreateShortUrlCard from '@/components/CreateShortUrlCard.vue'
import UrlList from '@/components/UrlList.vue'

const urlStore = useUrlStore()

onMounted(async () => {
  await urlStore.fetchUrls()
})

function handleDeleted() {
  // URL is already removed from the list by the store
}
</script>
