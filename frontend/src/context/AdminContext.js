import React, { createContext, useContext, useState, useEffect } from "react";

const AdminContext = createContext();

// Admin roles and permissions
export const ROLES = {
  SUPER_ADMIN: "super_admin",
  ADMIN: "admin",
  MANAGER: "manager",
  MODERATOR: "moderator",
};

export const PERMISSIONS = {
  // User management
  VIEW_USERS: "view_users",
  CREATE_USERS: "create_users",
  EDIT_USERS: "edit_users",
  DELETE_USERS: "delete_users",

  // Product management
  VIEW_PRODUCTS: "view_products",
  CREATE_PRODUCTS: "create_products",
  EDIT_PRODUCTS: "edit_products",
  DELETE_PRODUCTS: "delete_products",

  // Order management
  VIEW_ORDERS: "view_orders",
  EDIT_ORDERS: "edit_orders",
  DELETE_ORDERS: "delete_orders",

  // Analytics
  VIEW_ANALYTICS: "view_analytics",
  VIEW_REPORTS: "view_reports",

  // System settings
  MANAGE_SETTINGS: "manage_settings",
  MANAGE_ROLES: "manage_roles",
};

// Role permissions mapping
const ROLE_PERMISSIONS = {
  [ROLES.SUPER_ADMIN]: Object.values(PERMISSIONS), // All permissions

  [ROLES.ADMIN]: [
    PERMISSIONS.VIEW_USERS,
    PERMISSIONS.CREATE_USERS,
    PERMISSIONS.EDIT_USERS,
    PERMISSIONS.VIEW_PRODUCTS,
    PERMISSIONS.CREATE_PRODUCTS,
    PERMISSIONS.EDIT_PRODUCTS,
    PERMISSIONS.DELETE_PRODUCTS,
    PERMISSIONS.VIEW_ORDERS,
    PERMISSIONS.EDIT_ORDERS,
    PERMISSIONS.VIEW_ANALYTICS,
    PERMISSIONS.VIEW_REPORTS,
  ],

  [ROLES.MANAGER]: [
    PERMISSIONS.VIEW_USERS,
    PERMISSIONS.VIEW_PRODUCTS,
    PERMISSIONS.CREATE_PRODUCTS,
    PERMISSIONS.EDIT_PRODUCTS,
    PERMISSIONS.VIEW_ORDERS,
    PERMISSIONS.EDIT_ORDERS,
    PERMISSIONS.VIEW_ANALYTICS,
  ],

  [ROLES.MODERATOR]: [
    PERMISSIONS.VIEW_USERS,
    PERMISSIONS.VIEW_PRODUCTS,
    PERMISSIONS.EDIT_PRODUCTS,
    PERMISSIONS.VIEW_ORDERS,
  ],
};

export const AdminProvider = ({ children }) => {
  const [adminUser, setAdminUser] = useState(null);
  const [loading, setLoading] = useState(true);

  // Mock admin user - replace with real authentication
  useEffect(() => {
    // Simulate checking admin authentication
    // You can change the role here to test different permission levels
    const mockAdminUser = {
      id: 1,
      name: "Admin User",
      email: "admin@mystore.com",
      role: ROLES.SUPER_ADMIN, // Try: SUPER_ADMIN, ADMIN, MANAGER, MODERATOR
      avatar: "https://via.placeholder.com/40x40?text=A",
    };

    setTimeout(() => {
      setAdminUser(mockAdminUser);
      setLoading(false);
    }, 1000);
  }, []);

  // Check if user has specific permission
  const hasPermission = (permission) => {
    if (!adminUser) return false;
    const userPermissions = ROLE_PERMISSIONS[adminUser.role] || [];
    return userPermissions.includes(permission);
  };

  // Check if user has specific role
  const hasRole = (role) => {
    if (!adminUser) return false;
    return adminUser.role === role;
  };

  // Check if user has any of the specified roles
  const hasAnyRole = (roles) => {
    if (!adminUser) return false;
    return roles.includes(adminUser.role);
  };

  const value = {
    adminUser,
    loading,
    hasPermission,
    hasRole,
    hasAnyRole,
    setAdminUser,
    ROLES,
    PERMISSIONS,
  };

  return (
    <AdminContext.Provider value={value}>{children}</AdminContext.Provider>
  );
};

export const useAdmin = () => {
  const context = useContext(AdminContext);
  if (!context) {
    throw new Error("useAdmin must be used within AdminProvider");
  }
  return context;
};

export default AdminContext;
