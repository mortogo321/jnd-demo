import type { User } from './user'
import type { ShortenedUrl } from './url'

export interface ApiResponse<T = any> {
  message?: string
  data?: T
  errors?: Record<string, string[]>
}

export interface AuthResponse {
  message: string
  user: User
  token: string
}

export interface UserResponse {
  user: User
}

export interface UrlsResponse {
  urls: ShortenedUrl[]
}

export interface UrlResponse {
  message?: string
  url: ShortenedUrl
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}

export interface ErrorResponse {
  message: string
  errors?: Record<string, string[]>
}
