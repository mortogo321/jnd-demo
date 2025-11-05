import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api/client'
import type { ShortenedUrl, UrlsResponse, UrlResponse, PaginatedResponse } from '@/types'

export const useUrlStore = defineStore('url', () => {
  // State
  const urls = ref<ShortenedUrl[]>([])
  const adminUrls = ref<ShortenedUrl[]>([])
  const currentUrl = ref<ShortenedUrl | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const pagination = ref({
    currentPage: 1,
    lastPage: 1,
    perPage: 50,
    total: 0
  })

  /**
   * Fetch user's shortened URLs
   */
  async function fetchUrls(): Promise<void> {
    loading.value = true
    error.value = null

    try {
      const response = await api.get<UrlsResponse>('/urls')
      urls.value = response.data.urls
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch URLs'
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Create a new shortened URL
   */
  async function createUrl(originalUrl: string): Promise<ShortenedUrl> {
    loading.value = true
    error.value = null

    try {
      const response = await api.post<UrlResponse>('/urls', {
        original_url: originalUrl
      })

      // Add the new URL to the list
      if (response.data.url) {
        urls.value.unshift(response.data.url)
      }

      return response.data.url
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create shortened URL'
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Fetch a specific URL with analytics
   */
  async function fetchUrl(id: number): Promise<void> {
    loading.value = true
    error.value = null

    try {
      const response = await api.get<UrlResponse>(`/urls/${id}`)
      currentUrl.value = response.data.url
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch URL'
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Delete a shortened URL
   */
  async function deleteUrl(id: number): Promise<void> {
    loading.value = true
    error.value = null

    try {
      await api.delete(`/urls/${id}`)

      // Remove from local state
      urls.value = urls.value.filter((url) => url.id !== id)
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete URL'
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Fetch all URLs (admin only)
   */
  async function fetchAdminUrls(page = 1): Promise<void> {
    loading.value = true
    error.value = null

    try {
      const response = await api.get<PaginatedResponse<ShortenedUrl>>('/admin/urls', {
        params: { page }
      })

      adminUrls.value = response.data.data
      pagination.value = {
        currentPage: response.data.current_page,
        lastPage: response.data.last_page,
        perPage: response.data.per_page,
        total: response.data.total
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch admin URLs'
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Delete any URL (admin only)
   */
  async function deleteAdminUrl(id: number): Promise<void> {
    loading.value = true
    error.value = null

    try {
      await api.delete(`/admin/urls/${id}`)

      // Remove from local state
      adminUrls.value = adminUrls.value.filter((url) => url.id !== id)
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete URL'
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Get the full short URL
   */
  function getShortUrl(shortCode: string): string {
    const baseUrl = import.meta.env.VITE_APP_URL || window.location.origin
    return `${baseUrl}/${shortCode}`
  }

  return {
    // State
    urls,
    adminUrls,
    currentUrl,
    loading,
    error,
    pagination,
    // Actions
    fetchUrls,
    createUrl,
    fetchUrl,
    deleteUrl,
    fetchAdminUrls,
    deleteAdminUrl,
    getShortUrl
  }
})
