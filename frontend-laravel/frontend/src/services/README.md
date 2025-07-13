# Professional API Service Layer

This is a complete rewrite of the API service layer with enterprise-grade features and best practices.

## 🚀 Features

### Core Features
- ✅ **Automatic Token Refresh** - Seamless JWT token management
- ✅ **Request Retry Logic** - Automatic retry for failed requests
- ✅ **Response Caching** - Smart caching for GET requests
- ✅ **Error Handling** - Comprehensive error handling with user-friendly messages
- ✅ **Loading States** - Global and per-request loading management
- ✅ **Request Validation** - Input validation before API calls
- ✅ **Response Validation** - Ensures API responses have expected structure
- ✅ **Event System** - Custom events for API operations
- ✅ **Environment Config** - Different configs for dev/prod/test
- ✅ **Security Headers** - CSRF protection and secure headers
- ✅ **Request Timeouts** - Configurable timeouts to prevent hanging
- ✅ **Monitoring & Logging** - Detailed logging for debugging

### Advanced Features
- 🔄 **Automatic Retry** - Retries failed requests with exponential backoff
- 📦 **Smart Caching** - Caches GET requests with configurable TTL
- 🔐 **Token Management** - Automatic token validation and refresh
- 📊 **Request Analytics** - Tracks request performance and errors
- 🎯 **Type Safety** - Full TypeScript support (when enabled)
- 🔔 **Event Driven** - Emits events for auth, orders, payments
- 🛡️ **Security** - CSRF protection, secure storage, input validation

## 📁 File Structure

```
src/services/
├── config.js          # Environment configuration
├── utils.js           # Utility functions and classes
├── apiClient.js       # Core API client with interceptors
├── api.js             # Service layer with business logic
└── README.md          # This file

src/hooks/
└── useApi.js          # React hooks for easy API usage
```

## 🔧 Configuration

### Environment Setup
```javascript
// config.js
export const API_CONFIG = {
  development: {
    baseURL: "http://localhost:8000/api/v1",
    timeout: 15000,
    retryAttempts: 3,
  },
  production: {
    baseURL: "https://api.yourdomain.com/api/v1",
    timeout: 10000,
    retryAttempts: 2,
  }
};
```

### Cache Configuration
```javascript
// config.js
export const CACHE_CONFIG = {
  defaultTTL: 300000, // 5 minutes
  endpoints: {
    '/products': 600000, // 10 minutes
    '/products/categories': 1800000, // 30 minutes
  }
};
```

## 🎯 Usage Examples

### Using Service Classes Directly
```javascript
import { authAPI, productsAPI, ordersAPI, paymentsAPI } from './services/api';

// Authentication
const loginResult = await authAPI.login('user@example.com', 'password');
if (loginResult.success) {
  console.log('User logged in:', loginResult.data.user);
} else {
  console.error('Login failed:', loginResult.error);
}

// Products
const productsResult = await productsAPI.getProducts(1, 20);
if (productsResult.success) {
  console.log('Products:', productsResult.data);
}
```

### Using React Hooks (Recommended)
```javascript
import { useAuth, useProducts, useOrders } from '../hooks/useApi';

function LoginComponent() {
  const { login, loading, error } = useAuth();
  
  const handleLogin = async (email, password) => {
    const result = await login(email, password);
    if (result.success) {
      // Handle success
      navigate('/dashboard');
    }
    // Error is automatically handled by the hook
  };
  
  return (
    <form onSubmit={handleLogin}>
      {error && <div className="error">{error}</div>}
      <button disabled={loading}>
        {loading ? 'Logging in...' : 'Login'}
      </button>
    </form>
  );
}
```

### Global Loading State
```javascript
import { useGlobalLoading } from '../hooks/useApi';

function App() {
  const isLoading = useGlobalLoading();
  
  return (
    <div>
      {isLoading && <LoadingSpinner />}
      {/* Your app content */}
    </div>
  );
}
```

## 📡 Event System

The API layer emits custom events for various operations:

```javascript
// Listen to API events
window.addEventListener('api:loadingStart', () => {
  console.log('API request started');
});

window.addEventListener('auth:loginSuccess', (event) => {
  console.log('User logged in:', event.detail.user);
});

window.addEventListener('order:created', (event) => {
  console.log('Order created:', event.detail.order);
});

window.addEventListener('payment:verified', (event) => {
  console.log('Payment verified:', event.detail.payment);
});
```

## 🛡️ Error Handling

### Automatic Error Handling
- Network errors are automatically detected and retried
- 401 errors trigger automatic token refresh
- User-friendly error messages for all HTTP status codes
- Validation errors are caught before API calls

### Custom Error Handling
```javascript
const result = await authAPI.login(email, password);
if (!result.success) {
  switch (result.status) {
    case 401:
      showError('Invalid credentials');
      break;
    case 429:
      showError('Too many attempts. Please wait.');
      break;
    default:
      showError(result.error);
  }
}
```

## 🔄 Migration Guide

### From Old API to New API

**Old Way:**
```javascript
import { authAPI } from './services/api';

try {
  const response = await authAPI.login(email, password);
  const { user, tokens } = response.data;
  // Manual token storage and error handling
} catch (error) {
  // Manual error handling
}
```

**New Way:**
```javascript
import { useAuth } from '../hooks/useApi';

const { login, loading, error } = useAuth();

const result = await login(email, password);
if (result.success) {
  // Success is automatically handled
  // Tokens are automatically stored
  // Events are automatically emitted
}
// Errors are automatically handled and displayed
```

## 🔧 Setup Instructions

1. **Initialize API Event Listeners** (in your main App.js):
```javascript
import { setupApiEventListeners } from './services/api';

function App() {
  useEffect(() => {
    setupApiEventListeners();
  }, []);
  
  return <YourApp />;
}
```

2. **Update Your Components** to use the new hooks:
```javascript
// Replace old direct API calls with hooks
const { login } = useAuth();
const { getProducts } = useProducts();
const { createOrder } = useOrders();
```

3. **Environment Variables** - Make sure your environment is configured in `config.js`

## 🚀 Benefits

1. **Better User Experience** - Loading states, error messages, retry logic
2. **Developer Experience** - Easy-to-use hooks, comprehensive logging
3. **Performance** - Request caching, automatic retries, optimized requests
4. **Reliability** - Automatic token refresh, error recovery, validation
5. **Maintainability** - Clean separation of concerns, event-driven architecture
6. **Scalability** - Configurable for different environments, extensible design

## 🔍 Debugging

Enable detailed logging in development:
```javascript
// All API requests and responses are logged in development mode
// Check browser console for detailed information
```

Monitor API events:
```javascript
// Listen to all API events for debugging
window.addEventListener('api:loadingStart', () => console.log('Loading...'));
window.addEventListener('api:loadingEnd', () => console.log('Done'));
window.addEventListener('api:authFailure', () => console.log('Auth failed'));
```
