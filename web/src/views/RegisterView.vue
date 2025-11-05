<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-600 via-blue-600 to-purple-700 p-4">
    <Card class="w-full max-w-md">
      <CardHeader class="space-y-1">
        <CardTitle class="text-2xl text-center">Create Account</CardTitle>
        <p class="text-sm text-muted-foreground text-center">
          Join us and start shortening your URLs!
        </p>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="handleRegister" class="space-y-4">
          <div class="space-y-2">
            <label for="name" class="text-sm font-medium">Name</label>
            <Input
              id="name"
              v-model="name"
              type="text"
              placeholder="Your name"
              required
            />
          </div>

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
              placeholder="At least 8 characters"
              required
            />
          </div>

          <div class="space-y-2">
            <label for="password_confirmation" class="text-sm font-medium">Confirm Password</label>
            <Input
              id="password_confirmation"
              v-model="passwordConfirmation"
              type="password"
              placeholder="Confirm your password"
              required
            />
          </div>

          <div v-if="error" class="p-3 text-sm text-destructive bg-destructive/10 rounded-md">
            {{ error }}
          </div>

          <Button type="submit" class="w-full" :disabled="authStore.loading">
            {{ authStore.loading ? 'Creating account...' : 'Create Account' }}
          </Button>
        </form>

        <p class="mt-4 text-sm text-center text-muted-foreground">
          Already have an account?
          <router-link to="/login" class="text-primary hover:underline font-medium">
            Login here
          </router-link>
        </p>
      </CardContent>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'

const router = useRouter()
const authStore = useAuthStore()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const error = ref('')

async function handleRegister() {
  error.value = ''

  if (password.value !== passwordConfirmation.value) {
    error.value = 'Passwords do not match'
    return
  }

  try {
    await authStore.register(name.value, email.value, password.value, passwordConfirmation.value)
    router.push('/dashboard')
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Registration failed. Please try again.'
  }
}
</script>
