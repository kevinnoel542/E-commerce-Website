import apiClient from "./apiClient";
import { validateResponse, StorageUtils } from "./utils";

/**
 * Professional API Service Layer
 * Provides high-level API methods with proper error handling,
 * validation, and user-friendly responses
 */

/**
 * Authentication Service
 * Handles all authentication-related API calls
 */
class AuthService {
  async login(email, password) {
    try {
      const response = await apiClient.post("/auth/login", { email, password });
      const data = validateResponse(response, ["user", "tokens"]);

      // Store user data securely
      const userData = { ...data.user, tokens: data.tokens };
      StorageUtils.setItem("user", userData);

      // Emit login success event
      window.dispatchEvent(
        new CustomEvent("auth:loginSuccess", {
          detail: { user: data.user },
        })
      );

      return { success: true, data };
    } catch (error) {
      console.error("Login failed:", error);
      return {
        success: false,
        error: error.error || "Login failed",
        status: error.status,
      };
    }
  }

  async register(email, password, fullName, phone) {
    try {
      const response = await apiClient.post("/auth/register", {
        email,
        password,
        full_name: fullName,
        phone,
      });
      const data = validateResponse(response, ["user", "tokens"]);

      // Store user data securely
      const userData = { ...data.user, tokens: data.tokens };
      StorageUtils.setItem("user", userData);

      // Emit registration success event
      window.dispatchEvent(
        new CustomEvent("auth:registerSuccess", {
          detail: { user: data.user },
        })
      );

      return { success: true, data };
    } catch (error) {
      console.error("Registration failed:", error);
      return {
        success: false,
        error: error.error || "Registration failed",
        status: error.status,
      };
    }
  }

  async logout() {
    try {
      // Call logout endpoint (optional - for server-side cleanup)
      await apiClient.post("/auth/logout").catch(() => {
        // Ignore logout endpoint errors
      });

      // Clear local storage
      StorageUtils.removeItem("user");
      StorageUtils.removeItem("cart");

      // Clear API cache
      apiClient.clearCache();

      // Emit logout event
      window.dispatchEvent(new CustomEvent("auth:logout"));

      return { success: true };
    } catch (error) {
      console.error("Logout error:", error);
      // Even if API call fails, clear local data
      StorageUtils.removeItem("user");
      StorageUtils.removeItem("cart");
      return { success: true };
    }
  }

  async getProfile() {
    try {
      const response = await apiClient.get("/auth/profile");
      const data = validateResponse(response, ["id", "email"]);
      return { success: true, data };
    } catch (error) {
      console.error("Get profile failed:", error);
      return {
        success: false,
        error: error.error || "Failed to get profile",
        status: error.status,
      };
    }
  }

  async updateProfile(profileData) {
    try {
      const response = await apiClient.put("/auth/profile", profileData);
      const data = validateResponse(response, ["id", "email"]);

      // Update stored user data
      const user = StorageUtils.getItem("user");
      if (user) {
        const updatedUser = { ...user, ...data };
        StorageUtils.setItem("user", updatedUser);
      }

      return { success: true, data };
    } catch (error) {
      console.error("Update profile failed:", error);
      return {
        success: false,
        error: error.error || "Failed to update profile",
        status: error.status,
      };
    }
  }

  async changePassword(currentPassword, newPassword) {
    try {
      const response = await apiClient.post("/auth/change-password", {
        current_password: currentPassword,
        new_password: newPassword,
      });

      return { success: true, data: response.data };
    } catch (error) {
      console.error("Change password failed:", error);
      return {
        success: false,
        error: error.error || "Failed to change password",
        status: error.status,
      };
    }
  }

  // Utility methods
  isAuthenticated() {
    const user = StorageUtils.getItem("user");
    return !!user?.tokens?.access_token;
  }

  getCurrentUser() {
    return StorageUtils.getItem("user");
  }
}

export const authAPI = new AuthService();

/**
 * Products Service
 * Handles all product-related API calls with caching and validation
 */
class ProductsService {
  async getProducts(page = 1, perPage = 20, categoryId = null, filters = {}) {
    try {
      const params = {
        page,
        per_page: perPage,
        ...(categoryId && { category_id: categoryId }),
        ...filters,
      };

      const response = await apiClient.get("/products", { params });
      const data = validateResponse(response);

      return { success: true, data };
    } catch (error) {
      console.error("Get products failed:", error);
      return {
        success: false,
        error: error.error || "Failed to load products",
        status: error.status,
      };
    }
  }

  async getProduct(id) {
    try {
      if (!id) {
        throw new Error("Product ID is required");
      }

      const response = await apiClient.get(`/products/${id}`);
      const data = validateResponse(response, ["id", "name", "price"]);

      return { success: true, data };
    } catch (error) {
      console.error("Get product failed:", error);
      return {
        success: false,
        error: error.error || "Failed to load product",
        status: error.status,
      };
    }
  }

  async searchProducts(query, filters = {}, page = 1, perPage = 20) {
    try {
      if (!query || query.trim().length < 2) {
        return {
          success: false,
          error: "Search query must be at least 2 characters long",
        };
      }

      const params = {
        q: query.trim(),
        page,
        per_page: perPage,
        ...filters,
      };

      const response = await apiClient.get("/products/search", { params });
      const data = validateResponse(response);

      return { success: true, data };
    } catch (error) {
      console.error("Search products failed:", error);
      return {
        success: false,
        error: error.error || "Search failed",
        status: error.status,
      };
    }
  }

  async getCategories() {
    try {
      const response = await apiClient.get("/products/categories");
      const data = validateResponse(response);

      return { success: true, data };
    } catch (error) {
      console.error("Get categories failed:", error);
      return {
        success: false,
        error: error.error || "Failed to load categories",
        status: error.status,
      };
    }
  }

  async getFeaturedProducts(limit = 10) {
    try {
      const response = await apiClient.get("/products/featured", {
        params: { limit },
      });
      const data = validateResponse(response);

      return { success: true, data };
    } catch (error) {
      console.error("Get featured products failed:", error);
      return {
        success: false,
        error: error.error || "Failed to load featured products",
        status: error.status,
      };
    }
  }

  async getProductReviews(productId, page = 1, perPage = 10) {
    try {
      const response = await apiClient.get(`/products/${productId}/reviews`, {
        params: { page, per_page: perPage },
      });
      const data = validateResponse(response);

      return { success: true, data };
    } catch (error) {
      console.error("Get product reviews failed:", error);
      return {
        success: false,
        error: error.error || "Failed to load reviews",
        status: error.status,
      };
    }
  }
}

export const productsAPI = new ProductsService();

/**
 * Orders Service
 * Handles all order-related API calls with validation
 */
class OrdersService {
  async calculateCartSummary(cart) {
    try {
      if (!cart || !Array.isArray(cart) || cart.length === 0) {
        return {
          success: false,
          error: "Cart is empty or invalid",
        };
      }

      const response = await apiClient.post("/orders/cart/summary", {
        items: cart,
      });
      const data = validateResponse(response, ["subtotal", "total"]);

      return { success: true, data };
    } catch (error) {
      console.error("Calculate cart summary failed:", error);
      return {
        success: false,
        error: error.error || "Failed to calculate cart summary",
        status: error.status,
      };
    }
  }

  async createOrder(orderData) {
    try {
      if (!orderData || !orderData.items || orderData.items.length === 0) {
        return {
          success: false,
          error: "Order data is invalid or empty",
        };
      }

      const response = await apiClient.post("/orders", orderData);
      const data = validateResponse(response, ["id", "status", "total"]);

      // Emit order created event
      window.dispatchEvent(
        new CustomEvent("order:created", {
          detail: { order: data },
        })
      );

      return { success: true, data };
    } catch (error) {
      console.error("Create order failed:", error);
      return {
        success: false,
        error: error.error || "Failed to create order",
        status: error.status,
      };
    }
  }

  async getUserOrders(page = 1, perPage = 20, status = null) {
    try {
      const params = {
        page,
        per_page: perPage,
        ...(status && { status }),
      };

      const response = await apiClient.get("/orders", { params });
      const data = validateResponse(response);

      return { success: true, data };
    } catch (error) {
      console.error("Get user orders failed:", error);
      return {
        success: false,
        error: error.error || "Failed to load orders",
        status: error.status,
      };
    }
  }

  async getOrder(id) {
    try {
      if (!id) {
        throw new Error("Order ID is required");
      }

      const response = await apiClient.get(`/orders/${id}`);
      const data = validateResponse(response, ["id", "status", "total"]);

      return { success: true, data };
    } catch (error) {
      console.error("Get order failed:", error);
      return {
        success: false,
        error: error.error || "Failed to load order",
        status: error.status,
      };
    }
  }

  async updateOrderStatus(id, status) {
    try {
      if (!id || !status) {
        throw new Error("Order ID and status are required");
      }

      const response = await apiClient.put(`/orders/${id}/status`, { status });
      const data = validateResponse(response);

      // Emit order status updated event
      window.dispatchEvent(
        new CustomEvent("order:statusUpdated", {
          detail: { orderId: id, status, order: data },
        })
      );

      return { success: true, data };
    } catch (error) {
      console.error("Update order status failed:", error);
      return {
        success: false,
        error: error.error || "Failed to update order status",
        status: error.status,
      };
    }
  }

  async cancelOrder(id, reason = "") {
    try {
      const response = await apiClient.post(`/orders/${id}/cancel`, { reason });
      const data = validateResponse(response);

      // Emit order cancelled event
      window.dispatchEvent(
        new CustomEvent("order:cancelled", {
          detail: { orderId: id, reason, order: data },
        })
      );

      return { success: true, data };
    } catch (error) {
      console.error("Cancel order failed:", error);
      return {
        success: false,
        error: error.error || "Failed to cancel order",
        status: error.status,
      };
    }
  }
}

export const ordersAPI = new OrdersService();

/**
 * Payments Service
 * Handles all payment-related API calls with validation
 */
class PaymentsService {
  async initializePayment(orderData) {
    try {
      if (!orderData || !orderData.amount || !orderData.currency) {
        return {
          success: false,
          error: 'Payment data is invalid'
        };
      }

      const response = await apiClient.post("/payments/initialize", orderData);
      const data = validateResponse(response, ['payment_url', 'reference']);

      // Emit payment initialized event
      window.dispatchEvent(new CustomEvent('payment:initialized', {
        detail: { payment: data }
      }));

      return { success: true, data };
    } catch (error) {
      console.error('Initialize payment failed:', error);
      return {
        success: false,
        error: error.error || 'Failed to initialize payment',
        status: error.status
      };
    }
  }

  async verifyPayment(reference) {
    try {
      if (!reference) {
        throw new Error('Payment reference is required');
      }

      const response = await apiClient.post("/payments/verify", { reference });
      const data = validateResponse(response, ['status', 'reference']);

      // Emit payment verification event
      window.dispatchEvent(new CustomEvent('payment:verified', {
        detail: { payment: data }
      }));

      return { success: true, data };
    } catch (error) {
      console.error('Verify payment failed:', error);
      return {
        success: false,
        error: error.error || 'Failed to verify payment',
        status: error.status
      };
    }
  }

  async getPaymentMethods() {
    try {
      const response = await apiClient.get("/payments/methods");
      const data = validateResponse(response);

      return { success: true, data };
    } catch (error) {
      console.error('Get payment methods failed:', error);
      return {
        success: false,
        error: error.error || 'Failed to load payment methods',
        status: error.status
      };
    }
  }

  async getPaymentHistory(page = 1, perPage = 20) {
    try {
      const params = { page, per_page: perPage };
      const response = await apiClient.get("/payments/history", { params });
      const data = validateResponse(response);

      return { success: true, data };
    } catch (error) {
      console.error('Get payment history failed:', error);
      return {
        success: false,
        error: error.error || 'Failed to load payment history',
        status: error.status
      };
    }
  }

  async refundPayment(paymentId, amount, reason = '') {
    try {
      if (!paymentId || !amount) {
        throw new Error('Payment ID and amount are required');
      }

      const response = await apiClient.post(`/payments/${paymentId}/refund`, {
        amount,
        reason
      });
      const data = validateResponse(response);

      // Emit refund event
      window.dispatchEvent(new CustomEvent('payment:refunded', {
        detail: { payment: data }
      }));

      return { success: true, data };
    } catch (error) {
      console.error('Refund payment failed:', error);
      return {
        success: false,
        error: error.error || 'Failed to process refund',
        status: error.status
      };
    }
  }
}

export const paymentsAPI = new PaymentsService();

/**
 * API Event Listeners
 * Set up global event listeners for API events
 */
export const setupApiEventListeners = () => {
  // Loading events
  window.addEventListener('api:loadingStart', () => {
    console.log('🔄 API Loading started');
  });

  window.addEventListener('api:loadingEnd', () => {
    console.log('✅ API Loading ended');
  });

  // Auth events
  window.addEventListener('api:authFailure', () => {
    console.log('🔒 Authentication failed - redirecting to login');
  });

  window.addEventListener('api:tokenRefreshed', (event) => {
    console.log('🔄 Token refreshed successfully', event.detail);
  });
};

// Export the API client for direct access if needed
export { apiClient };

// Export all services
export default {
  auth: authAPI,
  products: productsAPI,
  orders: ordersAPI,
  payments: paymentsAPI,
  client: apiClient,
  setupEventListeners: setupApiEventListeners
};
