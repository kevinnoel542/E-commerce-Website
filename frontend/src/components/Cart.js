import React from "react";
import { Link } from "react-router-dom";
import { useApp } from "../context/AppContext";

export default function Cart() {
  const { cart, updateCartItem, removeFromCart, clearCart } = useApp();

  // Increment quantity
  const increaseQuantity = (item) => {
    updateCartItem(item.id, item.quantity + 1);
  };

  // Decrement quantity (minimum 1)
  const decreaseQuantity = (item) => {
    if (item.quantity > 1) {
      updateCartItem(item.id, item.quantity - 1);
    }
  };

  // Remove item from cart
  const removeItem = (item) => {
    removeFromCart(item.id);
  };

  // Calculate total
  const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

  return (
    <div className="p-6 max-w-2xl mx-auto">
      <h2 className="text-3xl font-bold mb-6">Your Cart</h2>

      {cart.length === 0 ? (
        <p className="text-gray-600">Your cart is empty.</p>
      ) : (
        <div className="space-y-4">
          {cart.map((item, index) => (
            <div
              key={index}
              className="flex justify-between items-center border-b pb-3"
            >
              <div>
                <p className="font-medium">{item.name}</p>
                <div className="flex items-center space-x-2 mt-1">
                  <button
                    onClick={() => decreaseQuantity(item)}
                    className="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300"
                  >
                    −
                  </button>
                  <span>{item.quantity}</span>
                  <button
                    onClick={() => increaseQuantity(item)}
                    className="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300"
                  >
                    +
                  </button>
                </div>
              </div>
              <div className="text-right">
                <p className="font-semibold">
                  ${(item.price * item.quantity).toFixed(2)}
                </p>
                <button
                  onClick={() => removeItem(item)}
                  className="text-sm text-red-600 hover:underline mt-1"
                >
                  Remove
                </button>
              </div>
            </div>
          ))}

          <div className="flex justify-between mt-6 border-t pt-4">
            <span className="text-lg font-semibold">Total:</span>
            <span className="text-lg font-bold">${total.toFixed(2)}</span>
          </div>

          <div className="mt-6 space-y-3">
            <Link to="/checkout">
              <button className="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg transition">
                Proceed to Checkout
              </button>
            </Link>
            <button
              onClick={() => {
                if (
                  window.confirm("Are you sure you want to clear your cart?")
                ) {
                  clearCart();
                }
              }}
              className="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition"
            >
              Clear Cart
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
