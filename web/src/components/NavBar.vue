<template>
  <nav class="bg-card border-b border-border py-4 shadow-sm">
    <div class="max-w-[1200px] mx-auto px-4 flex justify-between items-center">
      <div>
        <router-link to="/" class="no-underline">
          <h1 class="m-0 text-foreground text-2xl font-semibold">URL Shortener</h1>
        </router-link>
      </div>

      <div class="flex items-center gap-6">
        <template v-if="authStore.isAuthenticated">
          <router-link to="/dashboard" class="text-foreground no-underline text-sm font-medium transition-colors hover:text-primary">
            Dashboard
          </router-link>
          <router-link v-if="authStore.isAdmin" to="/admin" class="text-foreground no-underline text-sm font-medium transition-colors hover:text-primary">
            Admin
          </router-link>
          <div class="flex items-center gap-4">
            <span class="text-foreground text-sm font-medium">{{ authStore.user?.name }}</span>
            <Button @click="handleLogout" variant="secondary" size="sm">
              <LogOut class="w-4 h-4 mr-2" />
              Logout
            </Button>
          </div>
        </template>
        <template v-else>
          <router-link to="/login" class="text-foreground no-underline text-sm font-medium transition-colors hover:text-primary">
            Login
          </router-link>
          <Button as-child size="sm">
            <router-link to="/register">Sign Up</router-link>
          </Button>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore'
import { useRouter } from 'vue-router'
import { Button } from '@/components/ui/button'
import { LogOut } from 'lucide-vue-next'

const authStore = useAuthStore()
const router = useRouter()

async function handleLogout() {
  try {
    await authStore.logout()
    router.push('/')
  } catch (error) {
    console.error('Logout failed:', error)
  }
}
</script>
