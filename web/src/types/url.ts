export interface UrlClick {
  id: number
  shortened_url_id: number
  ip_address: string | null
  user_agent: string | null
  referer: string | null
  created_at: string
}

export interface ShortenedUrl {
  id: number
  user_id: number
  original_url: string
  short_code: string
  clicks: number
  created_at: string
  updated_at: string
  url_clicks?: UrlClick[]
  url_clicks_count?: number
}
