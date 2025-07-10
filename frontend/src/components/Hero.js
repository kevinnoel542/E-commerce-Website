import React from "react";
import { Link } from "react-router-dom";

// Dummy data for products & testimonials
const featuredProducts = [
  {
    id: 1,
    name: "Wireless Headphones",
    price: 99.99,
    img: "https://via.placeholder.com/150",
  },
  {
    id: 2,
    name: "Smartwatch Pro",
    price: 199.99,
    img: "https://via.placeholder.com/150",
  },
  {
    id: 3,
    name: "Gaming Mouse",
    price: 49.99,
    img: "https://via.placeholder.com/150",
  },
];

const testimonials = [
  {
    id: 1,
    name: "Alice M.",
    feedback: "Amazing service and fast shipping! Highly recommend.",
    avatar: "https://i.pravatar.cc/60?img=1",
  },
  {
    id: 2,
    name: "John D.",
    feedback: "Quality products at great prices. Will shop again!",
    avatar: "https://i.pravatar.cc/60?img=2",
  },
  {
    id: 3,
    name: "Sara K.",
    feedback: "Customer support was super helpful and friendly.",
    avatar: "https://i.pravatar.cc/60?img=3",
  },
];

const Hero = () => {
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

      <div>
        {/* Hero Section with animated gradient background */}
        <section
          className="text-white py-28 px-6 text-center relative overflow-hidden"
          style={{
            background:
              "linear-gradient(270deg, #7c3aed, #4f46e5, #9333ea, #4f46e5, #7c3aed)", // Indigo-purple-indigo gradient
            backgroundSize: "600% 600%",
            animation: "gradientShift 15s ease infinite",
          }}
        >
          <div className="max-w-4xl mx-auto">
            <h1 className="text-5xl md:text-7xl font-extrabold mb-6 drop-shadow-lg animate-fadeIn">
              Welcome to My E-commerce Store
            </h1>
            <p className="mb-10 text-xl md:text-2xl drop-shadow-md max-w-2xl mx-auto">
              Discover amazing products and shop securely with confidence.
            </p>

            <div className="flex flex-col md:flex-row justify-center space-y-4 md:space-y-0 md:space-x-6 max-w-md mx-auto">
              <Link to="/products">
                <button
                  aria-label="Shop Now"
                  className="bg-white text-indigo-700 px-8 py-3 rounded-lg shadow-lg font-semibold hover:bg-gray-200 transition flex items-center justify-center space-x-2"
                >
                  <span>Shop Now</span>
                  <svg
                    className="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      d="M9 5l7 7-7 7"
                    ></path>
                  </svg>
                </button>
              </Link>
              <Link to="/deals">
                <button className="bg-indigo-800 bg-opacity-50 hover:bg-opacity-70 text-white px-8 py-3 rounded-lg font-semibold transition">
                  View Deals
                </button>
              </Link>
            </div>

            {/* Floating circles for depth */}
            <div className="absolute top-0 left-0 w-32 h-32 bg-white opacity-10 rounded-full animate-pulse -z-10"></div>
            <div className="absolute bottom-10 right-10 w-48 h-48 bg-white opacity-10 rounded-full animate-pulse delay-1000 -z-10"></div>
          </div>
        </section>

        {/* The rest of your page sections go here unchanged */}
        {/* Unique Selling Points, Featured Products, Testimonials, etc. */}
      </div>

      {/* Unique Selling Points */}
      <section className="py-16 px-6 bg-gray-50">
        <div className="max-w-5xl mx-auto text-center">
          <h2 className="text-3xl font-bold mb-10">Why Shop With Us?</h2>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div className="bg-white rounded-lg p-6 shadow text-indigo-700 flex flex-col items-center">
              <svg
                className="w-12 h-12 mb-4"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M3 10h4l3 8 4-16 3 8h4"
                ></path>
              </svg>
              <h3 className="font-semibold mb-2">Fast Shipping</h3>
              <p className="text-gray-600">
                Get your orders delivered quickly and safely.
              </p>
            </div>
            <div className="bg-white rounded-lg p-6 shadow text-indigo-700 flex flex-col items-center">
              <svg
                className="w-12 h-12 mb-4"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M12 8c1.657 0 3-1.567 3-3.5S13.657 1 12 1 9 2.567 9 4.5 10.343 8 12 8z"
                ></path>
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M12 15v6"
                ></path>
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M12 21c1.104 0 2-1.343 2-3"
                ></path>
              </svg>
              <h3 className="font-semibold mb-2">Secure Payment</h3>
              <p className="text-gray-600">
                Your transactions are 100% safe and encrypted.
              </p>
            </div>
            <div className="bg-white rounded-lg p-6 shadow text-indigo-700 flex flex-col items-center">
              <svg
                className="w-12 h-12 mb-4"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M7 8h10M7 12h4m-4 4h6"
                ></path>
              </svg>
              <h3 className="font-semibold mb-2">Easy Returns</h3>
              <p className="text-gray-600">
                Hassle-free returns and refunds within 30 days.
              </p>
            </div>
            <div className="bg-white rounded-lg p-6 shadow text-indigo-700 flex flex-col items-center">
              <svg
                className="w-12 h-12 mb-4"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M3 10h4l3 8 4-16 3 8h4"
                ></path>
              </svg>
              <h3 className="font-semibold mb-2">24/7 Support</h3>
              <p className="text-gray-600">
                We’re here to help anytime you need us.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Featured Products */}
      <section className="py-16 px-6 max-w-6xl mx-auto">
        <h2 className="text-3xl font-bold mb-10 text-center">
          Featured Products
        </h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
          {featuredProducts.map((product) => (
            <div
              key={product.id}
              className="border rounded-lg shadow hover:shadow-lg transition p-4 flex flex-col"
            >
              <img
                src={product.img}
                alt={product.name}
                className="h-48 w-full object-cover rounded-md mb-4"
              />
              <h3 className="text-xl font-semibold mb-2">{product.name}</h3>
              <p className="text-indigo-600 font-bold mb-4">
                ${product.price.toFixed(2)}
              </p>
              <Link
                to={`/product/${product.id}`}
                className="mt-auto bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 text-center font-semibold"
              >
                View Details
              </Link>
            </div>
          ))}
        </div>
      </section>

      {/* Testimonials */}
      <section className="bg-indigo-50 py-16 px-6">
        <div className="max-w-4xl mx-auto text-center">
          <h2 className="text-3xl font-bold mb-10">What Our Customers Say</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {testimonials.map(({ id, name, feedback, avatar }) => (
              <div
                key={id}
                className="bg-white rounded-lg shadow p-6 flex flex-col items-center"
              >
                <img
                  src={avatar}
                  alt={name}
                  className="rounded-full w-16 h-16 mb-4"
                />
                <p className="italic text-gray-700 mb-2">"{feedback}"</p>
                <p className="font-semibold">{name}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Trust Badges & Guarantees */}
      <section className="py-16 px-6 max-w-5xl mx-auto text-center">
        <h2 className="text-3xl font-bold mb-10">Shop With Confidence</h2>
        <div className="flex flex-wrap justify-center gap-12 text-indigo-700">
          <div className="flex flex-col items-center space-y-2 max-w-xs">
            <svg
              className="w-12 h-12"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                d="M5 13l4 4L19 7"
              ></path>
            </svg>
            <p className="font-semibold">100% Secure Payment</p>
          </div>
          <div className="flex flex-col items-center space-y-2 max-w-xs">
            <svg
              className="w-12 h-12"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                d="M12 8c1.657 0 3-1.567 3-3.5S13.657 1 12 1 9 2.567 9 4.5 10.343 8 12 8z"
              ></path>
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                d="M12 15v6"
              ></path>
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                d="M12 21c1.104 0 2-1.343 2-3"
              ></path>
            </svg>
            <p className="font-semibold">Money-Back Guarantee</p>
          </div>
          <div className="flex flex-col items-center space-y-2 max-w-xs">
            <svg
              className="w-12 h-12"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                d="M3 10h4l3 8 4-16 3 8h4"
              ></path>
            </svg>
            <p className="font-semibold">Fast & Reliable Shipping</p>
          </div>
        </div>
      </section>
      {/* Footer */}
      <footer className="bg-gray-100 py-6 text-center text-gray-600 text-sm mt-16">
        &copy; {new Date().getFullYear()} MyStore. All rights reserved.
      </footer>
    </>
  );
};

export default Hero;
