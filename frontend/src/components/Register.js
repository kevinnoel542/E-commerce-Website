import React, { useState, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useApp } from "../context/AppContext";

export default function Register() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [fullName, setFullName] = useState("");
  const [phone, setPhone] = useState("");
  const [localError, setLocalError] = useState(null);
  const navigate = useNavigate();
  const { register, loading, error, isAuthenticated, clearError } = useApp();

  const validateEmail = (email) => /\S+@\S+\.\S+/.test(email);

  // Redirect if already authenticated
  useEffect(() => {
    if (isAuthenticated) {
      navigate("/dashboard");
    }
  }, [isAuthenticated, navigate]);

  // Clear error when component unmounts
  useEffect(() => {
    return () => {
      clearError();
      setLocalError(null);
    };
  }, [clearError]);

  // Clear errors after 5 seconds
  useEffect(() => {
    if (error || localError) {
      const timer = setTimeout(() => {
        clearError();
        setLocalError(null);
      }, 5000);
      return () => clearTimeout(timer);
    }
  }, [error, localError, clearError]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    clearError();
    setLocalError(null);

    if (!email || !password || !fullName) {
      setLocalError("Please fill all required fields");
      return;
    }

    if (!validateEmail(email)) {
      setLocalError("Invalid email format");
      return;
    }

    if (password.length < 6) {
      setLocalError("Password must be at least 6 characters");
      return;
    }

    if (fullName.trim().length < 2) {
      setLocalError("Full name must be at least 2 characters");
      return;
    }

    console.log("🔄 Starting registration process...");
    const result = await register(
      email,
      password,
      fullName.trim(),
      phone || null
    );
    console.log("🔄 Registration result:", result);

    if (result.success) {
      console.log("✅ Registration successful, navigating to dashboard...");
      navigate("/dashboard");
    } else {
      console.log("❌ Registration failed:", result.error);
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
            Create an Account
          </h2>

          {(error || localError) && (
            <p
              className="mb-4 text-red-600 font-semibold text-center"
              aria-live="polite"
            >
              {error || localError}
            </p>
          )}

          <form onSubmit={handleSubmit} className="space-y-6">
            <input
              type="text"
              placeholder="Full Name *"
              className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
              value={fullName}
              onChange={(e) => setFullName(e.target.value)}
              disabled={loading}
              autoComplete="email"
            />

            <input
              type="email"
              placeholder="Email address *"
              className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              disabled={loading}
              autoComplete="new-password"
            />

            <input
              type="tel"
              placeholder="Phone number (optional)"
              className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
              value={phone}
              onChange={(e) => setPhone(e.target.value)}
              disabled={loading}
            />

            <input
              type="password"
              placeholder="Password *"
              className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              disabled={loading}
              required
            />

            <button
              type="submit"
              disabled={loading}
              className="w-full bg-teal-500 hover:bg-teal-700 text-white py-3 rounded-lg font-semibold transition"
            >
              {loading ? "Registering..." : "Register"}
            </button>
          </form>

          <div className="mt-6 flex justify-between text-sm text-gray-600">
            <Link
              to="/login"
              className="hover:text-teal-700 font-medium transition"
            >
              Already have an account? Login
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
