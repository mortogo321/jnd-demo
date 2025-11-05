# Multi-stage Dockerfile for Vue.js Frontend

# Build argument for environment file
ARG ENV_FILE=development

# Stage 1: Build
FROM node:20-alpine AS build

WORKDIR /app

# Copy environment file
ARG ENV_FILE
COPY web/.env.${ENV_FILE} .env

# Copy package files
COPY web/package.json web/pnpm-lock.yaml* ./

# Install pnpm
RUN npm install -g pnpm

# Install dependencies
RUN pnpm install --frozen-lockfile

# Copy application code
COPY web/ .

# Run prebuild (format, lint, type-check)
RUN pnpm run prebuild

# Build application (env vars will be loaded from .env file)
RUN pnpm run build

# Stage 2: Production with Nginx
FROM nginx:alpine AS production

# Copy built assets
COPY --from=build /app/dist /usr/share/nginx/html

# Copy nginx configuration
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

# Stage 3: Development
FROM node:20-alpine AS development

WORKDIR /app

# Install Alpine dependencies for native modules
RUN apk add --no-cache curl libc6-compat openssl

# Copy environment file
ARG ENV_FILE
COPY web/.env.${ENV_FILE} .env

# Install pnpm globally
RUN npm install -g pnpm

# Copy package files
COPY web/package.json web/pnpm-lock.yaml* ./

# Install dependencies with force for clean install
RUN pnpm install --frozen-lockfile

# Copy application code
COPY web/ .

EXPOSE 5173

CMD ["pnpm", "run", "dev", "--host"]
