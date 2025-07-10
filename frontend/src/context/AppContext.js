import React, { createContext, useContext, useReducer, useEffect } from "react";
import { authAPI, setupApiEventListeners } from "../services/api";

// Initial state
const initialState = {
  user: null,
  isAuthenticated: false,
  loading: true,
  cart: [],
  cartCount: 0,
  error: null,
  toast: null,
};

// Action types
const actionTypes = {
  SET_LOADING: "SET_LOADING",
  SET_USER: "SET_USER",
  SET_ERROR: "SET_ERROR",
  SET_TOAST: "SET_TOAST",
  CLEAR_TOAST: "CLEAR_TOAST",
  LOGOUT: "LOGOUT",
  SET_CART: "SET_CART",
  ADD_TO_CART: "ADD_TO_CART",
  UPDATE_CART_ITEM: "UPDATE_CART_ITEM",
  REMOVE_FROM_CART: "REMOVE_FROM_CART",
  CLEAR_CART: "CLEAR_CART",
};

// Reducer
const appReducer = (state, action) => {
  switch (action.type) {
    case actionTypes.SET_LOADING:
      return { ...state, loading: action.payload };

    case actionTypes.SET_USER:
      return {
        ...state,
        user: action.payload,
        isAuthenticated: !!action.payload,
        loading: false,
        error: null,
      };

    case actionTypes.SET_ERROR:
      return { ...state, error: action.payload, loading: false };

    case actionTypes.SET_TOAST:
      return { ...state, toast: action.payload };

    case actionTypes.CLEAR_TOAST:
      return { ...state, toast: null };

    case actionTypes.LOGOUT:
      localStorage.removeItem("user");
      localStorage.removeItem("cart");
      return {
        ...state,
        user: null,
        isAuthenticated: false,
        cart: [],
        cartCount: 0,
        error: null,
      };

    case actionTypes.SET_CART:
      const cartCount = action.payload.reduce(
        (total, item) => total + item.quantity,
        0
      );
      return {
        ...state,
        cart: action.payload,
        cartCount,
      };

    case actionTypes.ADD_TO_CART:
      const existingItem = state.cart.find(
        (item) => item.id === action.payload.id
      );
      let newCart;

      if (existingItem) {
        newCart = state.cart.map((item) =>
          item.id === action.payload.id
            ? { ...item, quantity: item.quantity + action.payload.quantity }
            : item
        );
      } else {
        newCart = [...state.cart, action.payload];
      }

      localStorage.setItem("cart", JSON.stringify(newCart));
      const newCartCount = newCart.reduce(
        (total, item) => total + item.quantity,
        0
      );

      return {
        ...state,
        cart: newCart,
        cartCount: newCartCount,
      };

    case actionTypes.UPDATE_CART_ITEM:
      const updatedCart = state.cart.map((item) =>
        item.id === action.payload.id
          ? { ...item, quantity: action.payload.quantity }
          : item
      );

      localStorage.setItem("cart", JSON.stringify(updatedCart));
      const updatedCartCount = updatedCart.reduce(
        (total, item) => total + item.quantity,
        0
      );

      return {
        ...state,
        cart: updatedCart,
        cartCount: updatedCartCount,
      };

    case actionTypes.REMOVE_FROM_CART:
      const filteredCart = state.cart.filter(
        (item) => item.id !== action.payload
      );
      localStorage.setItem("cart", JSON.stringify(filteredCart));
      const filteredCartCount = filteredCart.reduce(
        (total, item) => total + item.quantity,
        0
      );

      return {
        ...state,
        cart: filteredCart,
        cartCount: filteredCartCount,
      };

    case actionTypes.CLEAR_CART:
      localStorage.removeItem("cart");
      return {
        ...state,
        cart: [],
        cartCount: 0,
      };

    default:
      return state;
  }
};

// Create context
const AppContext = createContext();

// Provider component
export const AppProvider = ({ children }) => {
  const [state, dispatch] = useReducer(appReducer, initialState);

  // Initialize app state
  useEffect(() => {
    initializeApp();
  }, []);

  const initializeApp = async () => {
    try {
      // Load user from localStorage
      const storedUser = localStorage.getItem("user");
      if (storedUser) {
        const userData = JSON.parse(storedUser);

        // Verify token is still valid by fetching profile
        try {
          const response = await authAPI.getProfile();
          dispatch({
            type: actionTypes.SET_USER,
            payload: { ...userData, ...response.data },
          });
        } catch (error) {
          // Token expired or invalid, remove from storage
          localStorage.removeItem("user");
          dispatch({ type: actionTypes.SET_USER, payload: null });
        }
      } else {
        dispatch({ type: actionTypes.SET_USER, payload: null });
      }

      // Load cart from localStorage
      const storedCart = localStorage.getItem("cart");
      if (storedCart) {
        try {
          const cartData = JSON.parse(storedCart);
          dispatch({ type: actionTypes.SET_CART, payload: cartData });
        } catch (error) {
          console.error("Error parsing cart data:", error);
          localStorage.removeItem("cart");
          dispatch({ type: actionTypes.SET_CART, payload: [] });
        }
      } else {
        dispatch({ type: actionTypes.SET_CART, payload: [] });
      }
    } catch (error) {
      console.error("Error initializing app:", error);
      dispatch({
        type: actionTypes.SET_ERROR,
        payload: "Failed to initialize app",
      });
    }
  };

  // Helper function to check if user is admin
  const isAdmin = (user) => {
    const adminRoles = ["super_admin", "admin", "manager", "moderator"];
    return user && user.role && adminRoles.includes(user.role.toLowerCase());
  };

  // Actions
  const login = async (email, password) => {
    try {
      dispatch({ type: actionTypes.SET_LOADING, payload: true });
      console.log("🔄 Attempting login for:", email);

      const result = await authAPI.login(email, password);
      console.log("🔄 Login result:", result);

      if (result.success) {
        const { user, tokens } = result.data;
        const userData = { ...user, tokens };

        // The new API already handles localStorage storage
        dispatch({ type: actionTypes.SET_USER, payload: userData });

        console.log("✅ Login successful, user data saved:", userData);

        // Show success toast
        dispatch({
          type: actionTypes.SET_TOAST,
          payload: {
            message: "Login successful! Redirecting...",
            type: "success",
          },
        });

        return { success: true, user: userData };
      } else {
        console.log("❌ Login failed:", result.error);
        dispatch({ type: actionTypes.SET_ERROR, payload: result.error });
        return { success: false, error: result.error };
      }
    } catch (error) {
      console.error("❌ Login error:", error);
      const errorMessage = error.message || "Login failed";
      dispatch({ type: actionTypes.SET_ERROR, payload: errorMessage });
      return { success: false, error: errorMessage };
    } finally {
      dispatch({ type: actionTypes.SET_LOADING, payload: false });
    }
  };

  const register = async (email, password, fullName, phone) => {
    try {
      dispatch({ type: actionTypes.SET_LOADING, payload: true });
      console.log("🔄 Attempting registration for:", email);

      const result = await authAPI.register(email, password, fullName, phone);
      console.log("🔄 Registration result:", result);

      if (result.success) {
        const { user, tokens } = result.data;
        const userData = { ...user, tokens };

        // The new API already handles localStorage storage
        dispatch({ type: actionTypes.SET_USER, payload: userData });

        console.log("✅ Registration successful, user data saved:", userData);

        // Show success toast
        dispatch({
          type: actionTypes.SET_TOAST,
          payload: {
            message: "Registration successful! Welcome!",
            type: "success",
          },
        });

        return { success: true };
      } else {
        console.log("❌ Registration failed:", result.error);
        dispatch({ type: actionTypes.SET_ERROR, payload: result.error });
        return { success: false, error: result.error };
      }
    } catch (error) {
      console.error("❌ Registration error:", error);
      const errorMessage = error.message || "Registration failed";
      dispatch({ type: actionTypes.SET_ERROR, payload: errorMessage });
      return { success: false, error: errorMessage };
    } finally {
      dispatch({ type: actionTypes.SET_LOADING, payload: false });
    }
  };

  const logout = async () => {
    try {
      await authAPI.logout();
    } catch (error) {
      console.error("Logout error:", error);
    } finally {
      dispatch({ type: actionTypes.LOGOUT });
    }
  };

  const addToCart = (product, quantity = 1) => {
    dispatch({
      type: actionTypes.ADD_TO_CART,
      payload: {
        id: product.id,
        name: product.name,
        price: parseFloat(product.price),
        image:
          product.images?.[0] ||
          "https://via.placeholder.com/300x200?text=No+Image",
        quantity,
      },
    });
  };

  const updateCartItem = (id, quantity) => {
    if (quantity <= 0) {
      dispatch({ type: actionTypes.REMOVE_FROM_CART, payload: id });
    } else {
      dispatch({
        type: actionTypes.UPDATE_CART_ITEM,
        payload: { id, quantity },
      });
    }
  };

  const removeFromCart = (id) => {
    dispatch({ type: actionTypes.REMOVE_FROM_CART, payload: id });
  };

  const clearCart = () => {
    dispatch({ type: actionTypes.CLEAR_CART });
  };

  const clearError = () => {
    dispatch({ type: actionTypes.SET_ERROR, payload: null });
  };

  const clearToast = () => {
    dispatch({ type: actionTypes.CLEAR_TOAST });
  };

  const value = {
    ...state,
    login,
    register,
    logout,
    addToCart,
    updateCartItem,
    removeFromCart,
    clearCart,
    clearError,
    clearToast,
    isAdmin,
  };

  return <AppContext.Provider value={value}>{children}</AppContext.Provider>;
};

// Custom hook to use the context
export const useApp = () => {
  const context = useContext(AppContext);
  if (!context) {
    throw new Error("useApp must be used within an AppProvider");
  }
  return context;
};

export default AppContext;
