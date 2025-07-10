import React, { useState, useEffect } from "react";
import { FlutterWaveButton, closePaymentModal } from "flutterwave-react-v3";
import { useNavigate } from "react-router-dom";
import { useApp } from "../context/AppContext";
import { ordersAPI, paymentsAPI } from "../services/api";

export default function Checkout() {
  const navigate = useNavigate();
  const { user, cart, clearCart } = useApp();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [orderSummary, setOrderSummary] = useState(null);

  useEffect(() => {
    if (cart.length === 0) {
      navigate("/cart");
      return;
    }
    calculateOrderSummary();
  }, [cart, navigate]);

  const calculateOrderSummary = async () => {
    try {
      setLoading(true);
      const cartData = {
        items: cart.map((item) => ({
          product_id: item.id,
          quantity: item.quantity,
          price: item.price,
        })),
      };

      const response = await ordersAPI.calculateCartSummary(cartData);
      setOrderSummary(response.data);
      setError(null);
    } catch (error) {
      console.error("Error calculating order summary:", error);
      setError("Failed to calculate order total");
    } finally {
      setLoading(false);
    }
  };

  const createOrder = async () => {
    try {
      const orderData = {
        items: cart.map((item) => ({
          product_id: item.id,
          quantity: item.quantity,
          price: item.price,
        })),
        shipping_address: {
          street: "123 Main St", // You'd get this from a form
          city: "City",
          state: "State",
          postal_code: "12345",
          country: "US",
        },
      };

      const response = await ordersAPI.createOrder(orderData);
      return response.data;
    } catch (error) {
      console.error("Error creating order:", error);
      throw error;
    }
  };

  const config = orderSummary
    ? {
        public_key:
          process.env.REACT_APP_FLUTTERWAVE_PUBLIC_KEY ||
          "FLWPUBK_TEST-SANDBOXDEMOKEY-X", // Use environment variable
        tx_ref: `order_${Date.now()}`,
        amount: parseFloat(orderSummary.total_amount),
        currency: "USD",
        payment_options: "card, mobilemoney, ussd",
        customer: {
          email: user?.email || "customer@example.com",
          phonenumber: user?.phone || "080****4528",
          name: user?.full_name || "Customer Name",
        },
        customizations: {
          title: "MyStore Payment",
          description: "Payment for items in cart",
          logo: "https://yourstore.com/logo.png",
        },
      }
    : null;

  const handleFlutterPayment = async (callback) => {
    console.log(callback);
    if (callback.status === "successful") {
      try {
        setLoading(true);

        // Create order in backend
        const order = await createOrder();

        // Verify payment with backend
        await paymentsAPI.verifyPayment(callback.transaction_id);

        // Clear cart
        clearCart();

        alert("Payment successful! Order created.");
        navigate("/dashboard");
      } catch (error) {
        console.error("Error processing successful payment:", error);
        alert(
          "Payment was successful but there was an error processing your order. Please contact support."
        );
      } finally {
        setLoading(false);
      }
    } else {
      alert("Payment failed or cancelled.");
    }
    closePaymentModal();
  };

  if (loading) {
    return (
      <div className="p-6 max-w-lg mx-auto">
        <div className="flex justify-center items-center h-64">
          <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-indigo-600"></div>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="p-6 max-w-lg mx-auto">
        <h2 className="text-3xl font-bold mb-6">Checkout</h2>
        <div className="text-red-600 text-center">{error}</div>
      </div>
    );
  }

  if (!orderSummary) {
    return (
      <div className="p-6 max-w-lg mx-auto">
        <h2 className="text-3xl font-bold mb-6">Checkout</h2>
        <div className="text-center">Loading order summary...</div>
      </div>
    );
  }

  return (
    <div className="p-6 max-w-2xl mx-auto">
      <h2 className="text-3xl font-bold mb-6">Checkout</h2>

      {/* Order Summary */}
      <div className="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 className="text-xl font-semibold mb-4">Order Summary</h3>

        {/* Cart Items */}
        <div className="space-y-3 mb-4">
          {cart.map((item) => (
            <div key={item.id} className="flex justify-between items-center">
              <div className="flex items-center space-x-3">
                <img
                  src={item.image}
                  alt={item.name}
                  className="w-12 h-12 object-cover rounded"
                />
                <div>
                  <p className="font-medium">{item.name}</p>
                  <p className="text-sm text-gray-600">Qty: {item.quantity}</p>
                </div>
              </div>
              <p className="font-semibold">${(item.price * item.quantity).toFixed(2)}</p>
            </div>
          ))}
        </div>

        {/* Order Totals */}
        <div className="border-t pt-4 space-y-2">
          <div className="flex justify-between">
            <span>Subtotal:</span>
            <span>${parseFloat(orderSummary.subtotal).toFixed(2)}</span>
          </div>
          <div className="flex justify-between">
            <span>Tax:</span>
            <span>${parseFloat(orderSummary.tax_amount).toFixed(2)}</span>
          </div>
          <div className="flex justify-between">
            <span>Shipping:</span>
            <span>${parseFloat(orderSummary.shipping_amount).toFixed(2)}</span>
          </div>
          <div className="flex justify-between font-bold text-lg border-t pt-2">
            <span>Total:</span>
            <span>${parseFloat(orderSummary.total_amount).toFixed(2)}</span>
          </div>
        </div>
      </div>

      {/* Payment Button */}
      {config && (
        <FlutterWaveButton
          className="w-full bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-md font-semibold transition-colors"
          config={config}
          callback={handleFlutterPayment}
          onClose={() => console.log("Payment modal closed")}
          text={`Pay $${parseFloat(orderSummary.total_amount).toFixed(2)}`}
        />
      )}
    </div>
  );
}
