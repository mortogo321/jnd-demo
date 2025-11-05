import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api, { apiClient } from '@/api/client'
import type { User, AuthResponse, UserResponse } from '@/types'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))
  const loading = ref(false)
  const error = ref<string | null>(null)

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => user.value?.is_admin ?? false)

  /**
   * Set authentication token
   */
  function setToken(newToken: string): void {
    token.value = newToken
    localStorage.setItem('auth_token', newToken)
  }

  /**
   * Clear authentication token
   */
  function clearToken(): void {
    token.value = null
    localStorage.removeItem('auth_token')
  }

  /**
   * Register a new user
   */
  async function register(name: string, email: string, password: string, passwordConfirmation: string): Promise<void> {
    loading.value = true
    error.value = null

    try {
      await apiClient.fetchCsrfToken()

      const response = await api.post<AuthResponse>('/register', {
        name,
        email,
        password,
        password_confirmation: passwordConfirmation
      })

      user.value = response.data.user
      setToken(response.data.token)
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Registration failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Login user
   */
  async function login(email: string, password: string): Promise<void> {
    loading.value = true
    error.value = null

    try {
      await apiClient.fetchCsrfToken()

      const response = await api.post<AuthResponse>('/login', {
        email,
        password
      })

      user.value = response.data.user
      setToken(response.data.token)
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Login failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Logout user
   */
  async function logout(): Promise<void> {
    loading.value = true
    error.value = null

    try {
      if (token.value) {
        await api.post('/logout')
      }
    } catch (err: any) {
      console.error('Logout error:', err)
    } finally {
      user.value = null
      clearToken()
      loading.value = false
    }
  }

  /**
   * Fetch current user data
   */
  async function fetchUser(): Promise<void> {
    if (!token.value) {
      return
    }

    loading.value = true
    error.value = null

    try {
      const response = await api.get<UserResponse>('/user')
      user.value = response.data.user
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch user'
      clearToken()
      user.value = null
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Initialize auth state on app load
   */
  async function init(): Promise<void> {
    if (token.value) {
      try {
        await fetchUser()
      } catch {
        // User fetch failed, token is invalid
      }
    }
  }

  return {
    // State
    user,
    token,
    loading,
    error,
    // Getters
    isAuthenticated,
    isAdmin,
    // Actions
    register,
    login,
    logout,
    fetchUser,
    init
  }
})
