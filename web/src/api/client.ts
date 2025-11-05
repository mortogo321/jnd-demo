import axios, { type AxiosInstance } from 'axios'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'

class ApiClient {
  private client: AxiosInstance
  private csrfTokenFetched = false

  constructor() {
    this.client = axios.create({
      baseURL: `${API_BASE_URL}/api`,
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json'
      },
      withCredentials: true
    })

    this.setupInterceptors()
  }

  /**
   * Setup request and response interceptors
   */
  private setupInterceptors(): void {
    // Request interceptor - add auth token
    this.client.interceptors.request.use(
      (config) => {
        const token = localStorage.getItem('auth_token')
        if (token) {
          config.headers.Authorization = `Bearer ${token}`
        }
        return config
      },
      (error) => Promise.reject(error)
    )

    // Response interceptor - handle errors
    this.client.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401) {
          // Clear auth state on unauthorized
          localStorage.removeItem('auth_token')
          window.location.href = '/login'
        }
        return Promise.reject(error)
      }
    )
  }

  /**
   * Fetch CSRF token from Laravel (for Sanctum SPA authentication)
   */
  async fetchCsrfToken(): Promise<void> {
    if (this.csrfTokenFetched) {
      return
    }

    try {
      await axios.get(`${API_BASE_URL}/sanctum/csrf-cookie`, {
        withCredentials: true
      })
      this.csrfTokenFetched = true
    } catch (error) {
      console.error('Failed to fetch CSRF token:', error)
    }
  }

  /**
   * Get the axios instance
   */
  getInstance(): AxiosInstance {
    return this.client
  }
}

// Create singleton instance
const apiClient = new ApiClient()

// Export the axios instance
export default apiClient.getInstance()

// Export the client class for CSRF token fetching
export { apiClient }
