import React, { useState } from "react";
import { useAdmin, PERMISSIONS } from "../../context/AdminContext";
import AdminSidebar from "./AdminSidebar";
import AdminHeader from "./AdminHeader";
import DashboardOverview from "./DashboardOverview";
import UserManagement from "./UserManagement";
import ProductManagement from "./ProductManagement";
import OrderManagement from "./OrderManagement";
import Analytics from "./Analytics";
import Settings from "./Settings";

const AdminDashboard = () => {
  const { adminUser, loading, hasPermission } = useAdmin();
  const [activeTab, setActiveTab] = useState("overview");
  const [sidebarOpen, setSidebarOpen] = useState(true);

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-100 flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-indigo-600"></div>
      </div>
    );
  }

  if (!adminUser) {
    return (
      <div className="min-h-screen bg-gray-100 flex items-center justify-center">
        <div className="text-center">
          <h2 className="text-2xl font-bold text-gray-900 mb-4">
            Access Denied
          </h2>
          <p className="text-gray-600">
            You don't have permission to access the admin panel.
          </p>
        </div>
      </div>
    );
  }

  const renderContent = () => {
    switch (activeTab) {
      case "overview":
        return <DashboardOverview onNavigate={setActiveTab} />;

      case "users":
        if (!hasPermission(PERMISSIONS.VIEW_USERS)) {
          return (
            <div className="p-6 text-center text-red-600">
              Access Denied: You don't have permission to view users.
            </div>
          );
        }
        return <UserManagement />;

      case "products":
        if (!hasPermission(PERMISSIONS.VIEW_PRODUCTS)) {
          return (
            <div className="p-6 text-center text-red-600">
              Access Denied: You don't have permission to view products.
            </div>
          );
        }
        return <ProductManagement />;

      case "orders":
        if (!hasPermission(PERMISSIONS.VIEW_ORDERS)) {
          return (
            <div className="p-6 text-center text-red-600">
              Access Denied: You don't have permission to view orders.
            </div>
          );
        }
        return <OrderManagement />;

      case "analytics":
        if (!hasPermission(PERMISSIONS.VIEW_ANALYTICS)) {
          return (
            <div className="p-6 text-center text-red-600">
              Access Denied: You don't have permission to view analytics.
            </div>
          );
        }
        return <Analytics />;

      case "settings":
        if (!hasPermission(PERMISSIONS.MANAGE_SETTINGS)) {
          return (
            <div className="p-6 text-center text-red-600">
              Access Denied: You don't have permission to manage settings.
            </div>
          );
        }
        return <Settings />;

      default:
        return <DashboardOverview />;
    }
  };

  return (
    <div className="min-h-screen bg-gray-100">
      {/* Admin Header */}
      <AdminHeader sidebarOpen={sidebarOpen} setSidebarOpen={setSidebarOpen} />

      <div className="flex">
        {/* Sidebar */}
        <AdminSidebar
          activeTab={activeTab}
          setActiveTab={setActiveTab}
          sidebarOpen={sidebarOpen}
        />

        {/* Main Content */}
        <div
          className={`flex-1 transition-all duration-300 ${
            sidebarOpen ? "ml-64" : "ml-16"
          }`}
        >
          <main className="p-6">{renderContent()}</main>
        </div>
      </div>
    </div>
  );
};

export default AdminDashboard;
