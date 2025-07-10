// API Configuration for different environments
export const API_CONFIG = {
  development: {
    baseURL: "http://192.168.5.224:8000/api/v1",
    timeout: 15000,
    retryAttempts: 3,
    retryDelay: 1000,
  },
  production: {
    baseURL: "https://your-api-domain.com/api/v1",
    timeout: 10000,
    retryAttempts: 2,
    retryDelay: 1500,
  },
  test: {
    baseURL: "http://localhost:8000/api/v1",
    timeout: 5000,
    retryAttempts: 1,
    retryDelay: 500,
  }
};

// Get current environment config
export const getApiConfig = () => {
  const env = process.env.NODE_ENV || 'development';
  return API_CONFIG[env] || API_CONFIG.development;
};

// HTTP Status Messages
export const HTTP_STATUS_MESSAGES = {
  400: 'Invalid request. Please check your input.',
  401: 'Invalid credentials. Please try again.',
  403: 'Access denied. You don\'t have permission.',
  404: 'Resource not found.',
  408: 'Request timeout. Please try again.',
  409: 'Conflict. Resource already exists.',
  422: 'Validation error. Please check your input.',
  429: 'Too many requests. Please wait and try again.',
  500: 'Server error. Please try again later.',
  502: 'Service temporarily unavailable.',
  503: 'Service unavailable. Please try again later.',
  504: 'Gateway timeout. Please try again.',
};

// Cache configuration
export const CACHE_CONFIG = {
  defaultTTL: 300000, // 5 minutes
  maxSize: 100,
  endpoints: {
    '/products': 600000, // 10 minutes
    '/products/categories': 1800000, // 30 minutes
    '/auth/profile': 300000, // 5 minutes
  }
};
