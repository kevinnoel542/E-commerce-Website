import axios from 'axios';
import { getApiConfig, HTTP_STATUS_MESSAGES, CACHE_CONFIG } from './config';
import { 
  ApiCache, 
  TokenUtils, 
  StorageUtils, 
  retryRequest, 
  generateCacheKey 
} from './utils';

/**
 * Professional API Client with advanced features
 */
class ApiClient {
  constructor() {
    this.config = getApiConfig();
    this.cache = new ApiCache(CACHE_CONFIG.maxSize);
    this.activeRequests = 0;
    this.requestInterceptors = [];
    this.responseInterceptors = [];
    
    this.setupAxiosInstance();
    this.setupInterceptors();
  }

  setupAxiosInstance() {
    this.client = axios.create({
      baseURL: this.config.baseURL,
      timeout: this.config.timeout,
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
    });
  }

  setupInterceptors() {
    // Request interceptor
    this.client.interceptors.request.use(
      (config) => this.handleRequest(config),
      (error) => this.handleRequestError(error)
    );

    // Response interceptor
    this.client.interceptors.response.use(
      (response) => this.handleResponse(response),
      (error) => this.handleResponseError(error)
    );
  }

  handleRequest(config) {
    this.activeRequests++;
    this.emitLoadingEvent('start');
    
    // Add authentication token
    this.addAuthToken(config);
    
    // Add request timestamp
    config.metadata = { startTime: Date.now() };
    
    // Log request in development
    if (process.env.NODE_ENV === 'development') {
      console.log(`🚀 API Request: ${config.method?.toUpperCase()} ${config.url}`, {
        params: config.params,
        data: config.data
      });
    }
    
    return config;
  }

  handleRequestError(error) {
    this.activeRequests--;
    this.emitLoadingEvent('end');
    
    console.error('❌ Request Error:', error);
    return Promise.reject(this.formatError(error));
  }

  async handleResponse(response) {
    this.activeRequests--;
    this.emitLoadingEvent('end');
    
    const duration = Date.now() - response.config.metadata.startTime;
    
    if (process.env.NODE_ENV === 'development') {
      console.log(`✅ API Success: ${response.config.method?.toUpperCase()} ${response.config.url} (${duration}ms)`, {
        status: response.status,
        data: response.data
      });
    }
    
    // Cache GET requests
    if (response.config.method === 'get' && response.status === 200) {
      this.cacheResponse(response);
    }
    
    return response;
  }

  async handleResponseError(error) {
    this.activeRequests--;
    this.emitLoadingEvent('end');
    
    const originalRequest = error.config;
    
    if (process.env.NODE_ENV === 'development') {
      console.error(`❌ API Error: ${originalRequest?.method?.toUpperCase()} ${originalRequest?.url}`, {
        status: error.response?.status,
        message: error.message,
        data: error.response?.data
      });
    }
    
    // Handle token refresh for 401 errors
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;
      
      const refreshed = await this.refreshToken();
      if (refreshed) {
        this.addAuthToken(originalRequest);
        return this.client(originalRequest);
      }
    }
    
    return Promise.reject(this.formatError(error));
  }

  addAuthToken(config) {
    const user = StorageUtils.getItem('user');
    if (user?.tokens?.access_token) {
      if (!TokenUtils.isExpired(user.tokens.access_token)) {
        config.headers.Authorization = `Bearer ${user.tokens.access_token}`;
      }
    }
  }

  async refreshToken() {
    try {
      const user = StorageUtils.getItem('user');
      if (!user?.tokens?.refresh_token) {
        this.handleAuthFailure();
        return false;
      }
      
      const response = await axios.post(
        `${this.config.baseURL}/auth/refresh`,
        { refresh_token: user.tokens.refresh_token }
      );
      
      const { tokens } = response.data;
      const updatedUser = { ...user, tokens };
      
      StorageUtils.setItem('user', updatedUser);
      
      // Emit token refresh event
      this.emitEvent('tokenRefreshed', { tokens });
      
      return true;
    } catch (error) {
      console.error('Token refresh failed:', error);
      this.handleAuthFailure();
      return false;
    }
  }

  handleAuthFailure() {
    StorageUtils.removeItem('user');
    this.emitEvent('authFailure');
    
    // Redirect to login if not already there
    if (window.location.pathname !== '/login') {
      window.location.href = '/login';
    }
  }

  cacheResponse(response) {
    const { url, method, params } = response.config;
    const cacheKey = generateCacheKey(url, params, method);
    const ttl = CACHE_CONFIG.endpoints[url] || CACHE_CONFIG.defaultTTL;
    
    this.cache.set(cacheKey, response, ttl);
  }

  getCachedResponse(config) {
    const cacheKey = generateCacheKey(config.url, config.params, config.method);
    return this.cache.get(cacheKey);
  }

  formatError(error) {
    if (!error.response) {
      return {
        success: false,
        error: 'Network error. Please check your connection.',
        type: 'network',
        originalError: error
      };
    }
    
    const status = error.response.status;
    const message = HTTP_STATUS_MESSAGES[status] || 
                   error.response.data?.detail || 
                   error.response.data?.message || 
                   'An unexpected error occurred';
    
    return {
      success: false,
      error: message,
      status,
      type: 'api',
      originalError: error
    };
  }

  emitLoadingEvent(type) {
    if (type === 'start' && this.activeRequests === 1) {
      this.emitEvent('loadingStart');
    } else if (type === 'end' && this.activeRequests === 0) {
      this.emitEvent('loadingEnd');
    }
  }

  emitEvent(eventName, data = {}) {
    window.dispatchEvent(new CustomEvent(`api:${eventName}`, { detail: data }));
  }

  // Public methods for making requests
  async get(url, config = {}) {
    // Check cache first for GET requests
    if (!config.skipCache) {
      const cached = this.getCachedResponse({ url, params: config.params, method: 'get' });
      if (cached) {
        return cached;
      }
    }
    
    return retryRequest(
      () => this.client.get(url, config),
      this.config.retryAttempts,
      this.config.retryDelay
    );
  }

  async post(url, data, config = {}) {
    return retryRequest(
      () => this.client.post(url, data, config),
      this.config.retryAttempts,
      this.config.retryDelay
    );
  }

  async put(url, data, config = {}) {
    return retryRequest(
      () => this.client.put(url, data, config),
      this.config.retryAttempts,
      this.config.retryDelay
    );
  }

  async patch(url, data, config = {}) {
    return retryRequest(
      () => this.client.patch(url, data, config),
      this.config.retryAttempts,
      this.config.retryDelay
    );
  }

  async delete(url, config = {}) {
    return retryRequest(
      () => this.client.delete(url, config),
      this.config.retryAttempts,
      this.config.retryDelay
    );
  }

  // Utility methods
  clearCache() {
    this.cache.clear();
  }

  getActiveRequestsCount() {
    return this.activeRequests;
  }
}

// Create and export singleton instance
export const apiClient = new ApiClient();
export default apiClient;
