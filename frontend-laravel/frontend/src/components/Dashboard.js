import React, { useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useApp } from "../context/AppContext";
import { ordersAPI } from "../services/api";

export default function Dashboard() {
  const navigate = useNavigate();
  const { user, logout, cartCount } = useApp();
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (user) {
      fetchOrders();
    }
  }, [user]);

  const fetchOrders = async () => {
    try {
      setLoading(true);
      const response = await ordersAPI.getUserOrders(1, 10);
      setOrders(response.data.orders || []);
      setError(null);
    } catch (error) {
      console.error("Error fetching orders:", error);
      setError("Failed to load orders");
      setOrders([]);
    } finally {
      setLoading(false);
    }
  };

  const handleLogout = async () => {
    await logout();
    navigate("/login");
  };

  return (
    <div className="min-h-screen bg-gray-100 flex items-center justify-center p-6">
      <div className="bg-white shadow-lg rounded-xl p-8 max-w-lg w-full">
        <h2 className="text-3xl font-bold mb-4 text-indigo-600">Dashboard</h2>
        <p className="text-gray-700 text-lg mb-6">
          Welcome back,{" "}
          <span className="font-semibold">
            {user?.full_name || user?.email}
          </span>{" "}
          👋
        </p>

        {/* Quick Stats */}
        <div className="flex justify-around mb-8">
          <div className="bg-indigo-100 rounded-lg p-4 w-1/2 mx-2 text-center">
            <p className="text-2xl font-bold text-indigo-700">{cartCount}</p>
            <p className="text-gray-600">Items in Cart</p>
          </div>
          <div className="bg-indigo-100 rounded-lg p-4 w-1/2 mx-2 text-center">
            <p className="text-2xl font-bold text-indigo-700">
              {orders.length}
            </p>
            <p className="text-gray-600">Recent Orders</p>
          </div>
        </div>

        {/* Recent Orders */}
        <div className="mb-8">
          <h3 className="text-xl font-semibold mb-3">Recent Orders</h3>
          {loading ? (
            <div className="flex justify-center py-4">
              <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>
          ) : error ? (
            <p className="text-red-600">{error}</p>
          ) : orders.length === 0 ? (
            <p className="text-gray-600">You have no recent orders.</p>
          ) : (
            <ul className="space-y-3 max-h-48 overflow-y-auto">
              {orders.map((order) => (
                <li
                  key={order.id}
                  className="flex justify-between bg-gray-50 p-3 rounded shadow-sm"
                >
                  <div>
                    <p className="font-semibold">Order #{order.id}</p>
                    <p className="text-sm text-gray-500">
                      {new Date(order.created_at).toLocaleDateString()}
                    </p>
                  </div>
                  <div className="text-right">
                    <p className="font-semibold">
                      ${parseFloat(order.total_amount).toFixed(2)}
                    </p>
                    <p
                      className={`text-sm ${
                        order.status === "delivered"
                          ? "text-green-600"
                          : order.status === "shipped"
                          ? "text-blue-600"
                          : order.status === "processing"
                          ? "text-yellow-600"
                          : order.status === "cancelled"
                          ? "text-red-600"
                          : "text-gray-600"
                      }`}
                    >
                      {order.status.charAt(0).toUpperCase() +
                        order.status.slice(1)}
                    </p>
                  </div>
                </li>
              ))}
            </ul>
          )}
        </div>

        {/* Action Links */}
        <div className="flex justify-between mb-6">
          <Link
            to="/profile"
            className="text-indigo-600 hover:underline font-semibold"
          >
            Manage Profile
          </Link>
          <Link
            to="/orders"
            className="text-indigo-600 hover:underline font-semibold"
          >
            View All Orders
          </Link>
        </div>

        {/* Logout Button */}
        <button
          onClick={handleLogout}
          className="w-full bg-red-500 text-white py-2 rounded hover:bg-red-600 transition"
        >
          Logout
        </button>
      </div>
    </div>
  );
}
