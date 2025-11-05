<template>
  <div class="min-h-screen">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-purple-600 via-blue-600 to-purple-700 text-white text-center py-20 px-4">
      <h1 class="text-5xl md:text-6xl font-bold mb-4">URL Shortener</h1>
      <p class="text-xl md:text-2xl opacity-90">Create short, memorable links in seconds</p>
    </div>

    <div class="container mx-auto px-4 max-w-4xl -mt-8 pb-16">
      <!-- Create URL Card -->
      <CreateShortUrlCard />

      <!-- Features Section -->
      <div class="mt-16">
        <h2 class="text-3xl font-bold text-center text-foreground mb-8">Features</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Card>
            <CardContent class="pt-6 text-center">
              <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <Zap class="w-6 h-6 text-primary" />
              </div>
              <h3 class="text-lg font-semibold mb-2">Fast & Simple</h3>
              <p class="text-sm text-muted-foreground">
                Create short URLs in seconds with our easy-to-use interface
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="pt-6 text-center">
              <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <MousePointerClick class="w-6 h-6 text-primary" />
              </div>
              <h3 class="text-lg font-semibold mb-2">Track Clicks</h3>
              <p class="text-sm text-muted-foreground">
                Monitor how many times your links are clicked
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="pt-6 text-center">
              <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <BarChart3 class="w-6 h-6 text-primary" />
              </div>
              <h3 class="text-lg font-semibold mb-2">Analytics</h3>
              <p class="text-sm text-muted-foreground">
                Get detailed insights about your shortened URLs
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="pt-6 text-center">
              <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <Shield class="w-6 h-6 text-primary" />
              </div>
              <h3 class="text-lg font-semibold mb-2">Secure</h3>
              <p class="text-sm text-muted-foreground">
                All URLs are securely stored and protected
              </p>
            </CardContent>
          </Card>
        </div>
      </div>

      <!-- CTA Section -->
      <Card class="mt-16 text-center">
        <CardContent class="py-12">
          <h2 class="text-3xl font-bold mb-3">Ready to get started?</h2>
          <p class="text-lg text-muted-foreground mb-6">
            Sign up for free to manage and track all your shortened URLs
          </p>
          <div class="flex justify-center">
            <Button size="lg" as-child class="min-w-[200px]">
              <router-link to="/register" class="inline-flex items-center justify-center">Create Free Account</router-link>
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import CreateShortUrlCard from '@/components/CreateShortUrlCard.vue'
import { Zap, MousePointerClick, BarChart3, Shield } from 'lucide-vue-next'
</script>
