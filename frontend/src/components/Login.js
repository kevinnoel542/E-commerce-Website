import React, { useState, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useApp } from "../context/AppContext";

export default function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const navigate = useNavigate();
  const { login, loading, error, isAuthenticated, clearError, isAdmin } =
    useApp();

  const validateEmail = (email) => /\S+@\S+\.\S+/.test(email);

  // Redirect if already authenticated
  useEffect(() => {
    if (isAuthenticated) {
      navigate("/dashboard");
    }
  }, [isAuthenticated, navigate]);

  // Clear error when component unmounts or email/password changes
  useEffect(() => {
    return () => clearError();
  }, [clearError]);

  useEffect(() => {
    if (error) {
      const timer = setTimeout(() => clearError(), 5000);
      return () => clearTimeout(timer);
    }
  }, [error, clearError]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    clearError();

    if (!email || !password) {
      console.log("❌ Missing email or password");
      return;
    }

    if (!validateEmail(email)) {
      console.log("❌ Invalid email format");
      return;
    }

    console.log("🔄 Starting login process...");
    const result = await login(email, password);
    console.log("🔄 Login result:", result);

    if (result.success) {
      console.log("✅ Login successful, checking user role...");

      // Check if user is admin and redirect accordingly
      if (isAdmin(result.user)) {
        console.log("🔑 Admin user detected, redirecting to admin panel...");
        navigate("/admin");
      } else {
        console.log("👤 Regular user, redirecting to dashboard...");
        navigate("/dashboard");
      }
    } else {
      console.log("❌ Login failed:", result.error);
    }
  };

  return (
    <>
      <style>
        {`
          @keyframes gradientShift {
            0% {
              background-position: 100% 50%;
            }
            50% {
              background-position: 0% 50%;
            }
            100% {
              background-position: 100% 50%;
            }
          }
        `}
      </style>

      <div
        className="min-h-screen flex items-center justify-center p-6"
        style={{
          background: "linear-gradient(270deg, #4f46e5, #9333ea, #4f46e5)", // Indigo-purple-indigo
          backgroundSize: "600% 600%",
          animation: "gradientShift 15s ease infinite",
        }}
      >
        <div
          className="max-w-md w-full rounded-xl p-8"
          style={{
            background: "rgba(255, 255, 255, 0.2)", // glass effect
            backdropFilter: "blur(10px)",
            border: "1px solid rgba(255, 255, 255, 0.3)",
            boxShadow: "0 8px 32px 0 rgba(31, 38, 135, 0.37)",
          }}
        >
          <h2 className="text-3xl font-extrabold text-center mb-6 text-slate-900">
            Login to Your Account
          </h2>

          {error && (
            <p
              className="mb-4 text-red-600 font-semibold text-center"
              aria-live="polite"
            >
              {error}
            </p>
          )}

          <form onSubmit={handleSubmit} className="space-y-6">
            <input
              type="email"
              placeholder="Email address"
              className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              disabled={loading}
              autoComplete="email"
            />

            <input
              type="password"
              placeholder="Password"
              className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              disabled={loading}
              autoComplete="current-password"
            />

            <button
              type="submit"
              disabled={loading}
              className="w-full bg-teal-500 hover:bg-teal-700 text-white py-3 rounded-lg font-semibold transition"
            >
              {loading ? "Logging in..." : "Login"}
            </button>
          </form>

          <div className="mt-6 flex justify-between text-sm text-gray-600">
            <Link
              to="/register"
              className="hover:text-teal-700 font-medium transition"
            >
              Create an account
            </Link>
            <Link
              to="/forgot-password"
              className="hover:text-teal-700 font-medium transition"
            >
              Forgot Password?
            </Link>
          </div>
        </div>
      </div>
    </>
  );
}
