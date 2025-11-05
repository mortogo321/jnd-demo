<template>
  <div class="home">
    <div class="hero">
      <h1>URL Shortener</h1>
      <p>Create short, memorable links in seconds</p>
    </div>

    <div class="container">
      <UrlForm @url-created="handleUrlCreated" />

      <div v-if="recentUrl" class="recent-url">
        <h3>Your Shortened URL:</h3>
        <div class="url-display">
          <a :href="recentUrl" target="_blank">{{ recentUrl }}</a>
        </div>
      </div>

      <div class="features">
        <h2>Features</h2>
        <div class="feature-grid">
          <div class="feature">
            <h3>Fast & Simple</h3>
            <p>Create short URLs in seconds with our easy-to-use interface</p>
          </div>
          <div class="feature">
            <h3>Track Clicks</h3>
            <p>Monitor how many times your links are clicked</p>
          </div>
          <div class="feature">
            <h3>Analytics</h3>
            <p>Get detailed insights about your shortened URLs</p>
          </div>
          <div class="feature">
            <h3>Secure</h3>
            <p>All URLs are securely stored and protected</p>
          </div>
        </div>
      </div>

      <div class="cta">
        <h2>Ready to get started?</h2>
        <p>Sign up for free to manage and track all your shortened URLs</p>
        <router-link to="/register" class="btn btn-large">Create Free Account</router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import UrlForm from '@/components/UrlForm.vue'

const recentUrl = ref('')

function handleUrlCreated(url: string) {
  recentUrl.value = url
}
</script>

<style scoped>
.home {
  min-height: calc(100vh - 60px);
}

.hero {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  text-align: center;
  padding: 4rem 2rem;
}

.hero h1 {
  font-size: 3rem;
  margin: 0 0 1rem 0;
}

.hero p {
  font-size: 1.25rem;
  margin: 0;
  opacity: 0.9;
}

.container {
  max-width: 800px;
  margin: -2rem auto 0;
  padding: 0 1rem 4rem;
}

.recent-url {
  margin-top: 2rem;
  padding: 1.5rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.recent-url h3 {
  margin-top: 0;
  color: #2c3e50;
}

.url-display {
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 4px;
  text-align: center;
}

.url-display a {
  font-size: 1.25rem;
  color: #3498db;
  text-decoration: none;
  word-break: break-all;
}

.url-display a:hover {
  text-decoration: underline;
}

.features {
  margin-top: 4rem;
}

.features h2 {
  text-align: center;
  color: #2c3e50;
  margin-bottom: 2rem;
}

.feature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
}

.feature {
  text-align: center;
  padding: 1.5rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.feature h3 {
  color: #3498db;
  margin-top: 0;
}

.feature p {
  color: #7f8c8d;
  line-height: 1.6;
}

.cta {
  margin-top: 4rem;
  text-align: center;
  padding: 3rem 2rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.cta h2 {
  color: #2c3e50;
  margin-top: 0;
}

.cta p {
  color: #7f8c8d;
  font-size: 1.1rem;
  margin-bottom: 2rem;
}

.btn {
  padding: 0.75rem 2rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.3s;
  text-decoration: none;
  display: inline-block;
}

.btn-large {
  padding: 1rem 3rem;
  font-size: 1.1rem;
  background: #3498db;
  color: white;
}

.btn-large:hover {
  background: #2980b9;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
</style>
