import React, { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useApp } from "../context/AppContext";

export default function NavBar() {
  const navigate = useNavigate();
  const { isAuthenticated, logout, cartCount, user, isAdmin } = useApp();
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  const handleLogout = async () => {
    await logout();
    navigate("/login");
  };

  return (
    <nav className="bg-indigo-600 text-white px-6 py-4 flex items-center justify-between flex-wrap">
      <Link to="/" className="font-bold text-2xl tracking-wide">
        MyStore
      </Link>

      {/* Mobile menu button */}
      <button
        onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
        className="block md:hidden focus:outline-none"
        aria-label="Toggle menu"
      >
        <svg className="w-6 h-6 fill-current" viewBox="0 0 24 24">
          {isMobileMenuOpen ? (
            <path
              fillRule="evenodd"
              clipRule="evenodd"
              d="M18.364 5.636a1 1 0 00-1.414-1.414L12 9.172 7.05 4.222A1 1 0 105.636 5.636L10.586 10.586 5.636 15.536a1 1 0 101.414 1.414L12 12.828l4.95 4.95a1 1 0 001.414-1.414L13.414 10.586l4.95-4.95z"
            />
          ) : (
            <path fillRule="evenodd" d="M4 6h16M4 12h16M4 18h16" />
          )}
        </svg>
      </button>

      {/* Menu Links */}
      <div
        className={`w-full md:w-auto md:flex md:items-center md:space-x-6 mt-4 md:mt-0 ${
          isMobileMenuOpen ? "block" : "hidden"
        }`}
      >
        {isAuthenticated && (
          <>
            <Link
              to="/products"
              className="block mt-2 md:mt-0 hover:underline"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Products
            </Link>
            <Link
              to="/cart"
              className="block mt-2 md:mt-0 hover:underline relative"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Cart
              {cartCount > 0 && (
                <span className="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                  {cartCount}
                </span>
              )}
            </Link>
          </>
        )}

        {isAuthenticated ? (
          <>
            <Link
              to="/dashboard"
              className="block mt-2 md:mt-0 hover:underline"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Dashboard
            </Link>
            <Link
              to="/orders"
              className="block mt-2 md:mt-0 hover:underline"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Orders
            </Link>
            <Link
              to="/profile"
              className="block mt-2 md:mt-0 hover:underline"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Profile
            </Link>
            {isAdmin(user) && (
              <Link
                to="/admin"
                className="block mt-2 md:mt-0 hover:underline text-orange-400"
                onClick={() => setIsMobileMenuOpen(false)}
              >
                Admin Panel
              </Link>
            )}
            <button
              onClick={() => {
                handleLogout();
                setIsMobileMenuOpen(false);
              }}
              className="block mt-2 md:mt-0 hover:underline focus:outline-none"
            >
              Logout
            </button>
          </>
        ) : (
          <>
            <Link
              to="/login"
              className="block mt-2 md:mt-0 hover:underline"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Login
            </Link>
            <Link
              to="/register"
              className="block mt-2 md:mt-0 hover:underline"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Register
            </Link>
          </>
        )}
      </div>
    </nav>
  );
}
