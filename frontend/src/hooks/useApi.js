import { useState, useEffect, useCallback } from 'react';
import { authAPI, productsAPI, ordersAPI, paymentsAPI } from '../services/api';

/**
 * Custom hook for API calls with loading states and error handling
 */
export const useApi = () => {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  // Clear error after 5 seconds
  useEffect(() => {
    if (error) {
      const timer = setTimeout(() => setError(null), 5000);
      return () => clearTimeout(timer);
    }
  }, [error]);

  const executeRequest = useCallback(async (apiCall) => {
    try {
      setLoading(true);
      setError(null);
      
      const result = await apiCall();
      
      if (!result.success) {
        setError(result.error);
        return { success: false, error: result.error };
      }
      
      return result;
    } catch (err) {
      const errorMessage = err.message || 'An unexpected error occurred';
      setError(errorMessage);
      return { success: false, error: errorMessage };
    } finally {
      setLoading(false);
    }
  }, []);

  return {
    loading,
    error,
    executeRequest,
    clearError: () => setError(null)
  };
};

/**
 * Hook for authentication operations
 */
export const useAuth = () => {
  const { loading, error, executeRequest, clearError } = useApi();

  const login = useCallback(async (email, password) => {
    return executeRequest(() => authAPI.login(email, password));
  }, [executeRequest]);

  const register = useCallback(async (email, password, fullName, phone) => {
    return executeRequest(() => authAPI.register(email, password, fullName, phone));
  }, [executeRequest]);

  const logout = useCallback(async () => {
    return executeRequest(() => authAPI.logout());
  }, [executeRequest]);

  const getProfile = useCallback(async () => {
    return executeRequest(() => authAPI.getProfile());
  }, [executeRequest]);

  const updateProfile = useCallback(async (profileData) => {
    return executeRequest(() => authAPI.updateProfile(profileData));
  }, [executeRequest]);

  const changePassword = useCallback(async (currentPassword, newPassword) => {
    return executeRequest(() => authAPI.changePassword(currentPassword, newPassword));
  }, [executeRequest]);

  return {
    loading,
    error,
    clearError,
    login,
    register,
    logout,
    getProfile,
    updateProfile,
    changePassword,
    isAuthenticated: authAPI.isAuthenticated(),
    currentUser: authAPI.getCurrentUser()
  };
};

/**
 * Hook for product operations
 */
export const useProducts = () => {
  const { loading, error, executeRequest, clearError } = useApi();

  const getProducts = useCallback(async (page = 1, perPage = 20, categoryId = null, filters = {}) => {
    return executeRequest(() => productsAPI.getProducts(page, perPage, categoryId, filters));
  }, [executeRequest]);

  const getProduct = useCallback(async (id) => {
    return executeRequest(() => productsAPI.getProduct(id));
  }, [executeRequest]);

  const searchProducts = useCallback(async (query, filters = {}, page = 1, perPage = 20) => {
    return executeRequest(() => productsAPI.searchProducts(query, filters, page, perPage));
  }, [executeRequest]);

  const getCategories = useCallback(async () => {
    return executeRequest(() => productsAPI.getCategories());
  }, [executeRequest]);

  const getFeaturedProducts = useCallback(async (limit = 10) => {
    return executeRequest(() => productsAPI.getFeaturedProducts(limit));
  }, [executeRequest]);

  return {
    loading,
    error,
    clearError,
    getProducts,
    getProduct,
    searchProducts,
    getCategories,
    getFeaturedProducts
  };
};

/**
 * Hook for order operations
 */
export const useOrders = () => {
  const { loading, error, executeRequest, clearError } = useApi();

  const calculateCartSummary = useCallback(async (cart) => {
    return executeRequest(() => ordersAPI.calculateCartSummary(cart));
  }, [executeRequest]);

  const createOrder = useCallback(async (orderData) => {
    return executeRequest(() => ordersAPI.createOrder(orderData));
  }, [executeRequest]);

  const getUserOrders = useCallback(async (page = 1, perPage = 20, status = null) => {
    return executeRequest(() => ordersAPI.getUserOrders(page, perPage, status));
  }, [executeRequest]);

  const getOrder = useCallback(async (id) => {
    return executeRequest(() => ordersAPI.getOrder(id));
  }, [executeRequest]);

  const cancelOrder = useCallback(async (id, reason = '') => {
    return executeRequest(() => ordersAPI.cancelOrder(id, reason));
  }, [executeRequest]);

  return {
    loading,
    error,
    clearError,
    calculateCartSummary,
    createOrder,
    getUserOrders,
    getOrder,
    cancelOrder
  };
};

/**
 * Hook for payment operations
 */
export const usePayments = () => {
  const { loading, error, executeRequest, clearError } = useApi();

  const initializePayment = useCallback(async (orderData) => {
    return executeRequest(() => paymentsAPI.initializePayment(orderData));
  }, [executeRequest]);

  const verifyPayment = useCallback(async (reference) => {
    return executeRequest(() => paymentsAPI.verifyPayment(reference));
  }, [executeRequest]);

  const getPaymentMethods = useCallback(async () => {
    return executeRequest(() => paymentsAPI.getPaymentMethods());
  }, [executeRequest]);

  const getPaymentHistory = useCallback(async (page = 1, perPage = 20) => {
    return executeRequest(() => paymentsAPI.getPaymentHistory(page, perPage));
  }, [executeRequest]);

  const refundPayment = useCallback(async (paymentId, amount, reason = '') => {
    return executeRequest(() => paymentsAPI.refundPayment(paymentId, amount, reason));
  }, [executeRequest]);

  return {
    loading,
    error,
    clearError,
    initializePayment,
    verifyPayment,
    getPaymentMethods,
    getPaymentHistory,
    refundPayment
  };
};

/**
 * Hook for API loading state across the entire app
 */
export const useGlobalLoading = () => {
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    const handleLoadingStart = () => setIsLoading(true);
    const handleLoadingEnd = () => setIsLoading(false);

    window.addEventListener('api:loadingStart', handleLoadingStart);
    window.addEventListener('api:loadingEnd', handleLoadingEnd);

    return () => {
      window.removeEventListener('api:loadingStart', handleLoadingStart);
      window.removeEventListener('api:loadingEnd', handleLoadingEnd);
    };
  }, []);

  return isLoading;
};
