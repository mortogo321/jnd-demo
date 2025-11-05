<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-600 via-blue-600 to-purple-700 p-4">
    <Card class="w-full max-w-md">
      <CardHeader class="space-y-1">
        <CardTitle class="text-2xl text-center">Login</CardTitle>
        <p class="text-sm text-muted-foreground text-center">
          Welcome back! Please login to your account.
        </p>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="handleLogin" class="space-y-4">
          <div class="space-y-2">
            <label for="email" class="text-sm font-medium">Email</label>
            <Input
              id="email"
              v-model="email"
              type="email"
              placeholder="your@email.com"
              required
            />
          </div>

          <div class="space-y-2">
            <label for="password" class="text-sm font-medium">Password</label>
            <Input
              id="password"
              v-model="password"
              type="password"
              placeholder="Enter your password"
              required
            />
          </div>

          <div v-if="error" class="p-3 text-sm text-destructive bg-destructive/10 rounded-md">
            {{ error }}
          </div>

          <Button type="submit" class="w-full" :disabled="authStore.loading">
            {{ authStore.loading ? 'Logging in...' : 'Login' }}
          </Button>
        </form>

        <p class="mt-4 text-sm text-center text-muted-foreground">
          Don't have an account?
          <router-link to="/register" class="text-primary hover:underline font-medium">
            Sign up here
          </router-link>
        </p>
      </CardContent>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const error = ref('')

async function handleLogin() {
  error.value = ''

  try {
    await authStore.login(email.value, password.value)

    const redirect = route.query.redirect as string
    router.push(redirect || '/dashboard')
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Login failed. Please check your credentials.'
  }
}
</script>
