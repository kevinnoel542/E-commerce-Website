import React, { useState, useEffect } from "react";
import { useParams, Link } from "react-router-dom";
import { productsAPI } from "../services/api";
import { useApp } from "../context/AppContext";

export default function ProductDetail() {
  const { id } = useParams();
  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [selectedImage, setSelectedImage] = useState(0);
  const [quantity, setQuantity] = useState(1);
  const { addToCart } = useApp();

  useEffect(() => {
    fetchProduct();
  }, [id]);

  const fetchProduct = async () => {
    try {
      setLoading(true);
      const response = await productsAPI.getProduct(id);
      setProduct(response.data);
      setError(null);
    } catch (error) {
      console.error("Error fetching product:", error);
      if (error.response?.status === 404) {
        setError("Product not found");
      } else {
        setError("Failed to load product. Please try again.");
      }
    } finally {
      setLoading(false);
    }
  };

  const handleAddToCart = () => {
    try {
      addToCart(product, quantity);
      alert(`${quantity} ${product.name}(s) added to cart!`);
    } catch (error) {
      console.error("Error adding to cart:", error);
      alert("Failed to add product to cart.");
    }
  };

  if (loading) {
    return (
      <div className="max-w-4xl mx-auto p-6">
        <div className="flex justify-center items-center h-64">
          <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-indigo-600"></div>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="max-w-4xl mx-auto p-6 text-center">
        <h1 className="text-2xl font-bold text-red-600">{error}</h1>
        <Link
          to="/products"
          className="text-indigo-600 hover:underline mt-4 inline-block"
        >
          &larr; Back to Products
        </Link>
      </div>
    );
  }

  if (!product) {
    return (
      <div className="p-6 text-center text-red-600">Product not found.</div>
    );
  }

  const images =
    product.images && product.images.length > 0
      ? product.images
      : ["https://via.placeholder.com/400x300?text=No+Image"];

  return (
    <div className="max-w-4xl mx-auto p-6">
      <Link
        to="/products"
        className="text-indigo-600 hover:underline mb-4 inline-block"
      >
        &larr; Back to Products
      </Link>
      <div className="flex flex-col md:flex-row gap-6">
        <div className="md:w-1/2">
          <img
            src={images[selectedImage]}
            alt={product.name}
            className="w-full rounded-lg object-cover mb-4"
          />
          {images.length > 1 && (
            <div className="flex gap-2 overflow-x-auto">
              {images.map((image, index) => (
                <img
                  key={index}
                  src={image}
                  alt={`${product.name} ${index + 1}`}
                  className={`w-20 h-20 object-cover rounded cursor-pointer border-2 ${
                    selectedImage === index
                      ? "border-indigo-600"
                      : "border-gray-300"
                  }`}
                  onClick={() => setSelectedImage(index)}
                />
              ))}
            </div>
          )}
        </div>
        <div className="md:w-1/2 flex flex-col justify-between">
          <div>
            <h1 className="text-3xl font-bold mb-4">{product.name}</h1>
            {product.brand && (
              <p className="text-gray-600 mb-2">Brand: {product.brand}</p>
            )}
            {product.sku && (
              <p className="text-gray-600 mb-2">SKU: {product.sku}</p>
            )}
            <p className="text-gray-700 mb-6">{product.description}</p>
            <p className="text-indigo-600 font-extrabold text-2xl mb-4">
              ${parseFloat(product.price).toFixed(2)}
            </p>

            {/* Stock Status */}
            <div className="mb-4">
              {product.stock_quantity > 0 ? (
                <p className="text-green-600 font-semibold">
                  In Stock ({product.stock_quantity} available)
                </p>
              ) : (
                <p className="text-red-600 font-semibold">Out of Stock</p>
              )}
            </div>

            {/* Quantity Selector */}
            {product.stock_quantity > 0 && (
              <div className="mb-6">
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Quantity:
                </label>
                <div className="flex items-center space-x-2">
                  <button
                    onClick={() => setQuantity(Math.max(1, quantity - 1))}
                    className="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded"
                  >
                    -
                  </button>
                  <span className="px-4 py-1 border border-gray-300 rounded">
                    {quantity}
                  </span>
                  <button
                    onClick={() =>
                      setQuantity(
                        Math.min(product.stock_quantity, quantity + 1)
                      )
                    }
                    className="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded"
                  >
                    +
                  </button>
                </div>
              </div>
            )}
          </div>

          <button
            onClick={handleAddToCart}
            disabled={product.stock_quantity === 0}
            className="mt-6 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white py-3 rounded-md font-semibold transition-colors"
          >
            {product.stock_quantity === 0 ? "Out of Stock" : "Add to Cart"}
          </button>
        </div>
      </div>
    </div>
  );
}
