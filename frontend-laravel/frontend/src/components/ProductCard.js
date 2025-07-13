import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { useApp } from '../context/AppContext';

const ProductCard = ({ product }) => {
  const [isWishlisted, setIsWishlisted] = useState(false);
  const [imageLoaded, setImageLoaded] = useState(false);
  const { addToCart } = useApp();

  const handleAddToCart = (e) => {
    e.preventDefault();
    e.stopPropagation();
    addToCart(product, 1);
  };

  const handleWishlist = (e) => {
    e.preventDefault();
    e.stopPropagation();
    setIsWishlisted(!isWishlisted);
  };

  const getDiscountPercentage = () => {
    if (product.original_price && product.price < product.original_price) {
      return Math.round(((product.original_price - product.price) / product.original_price) * 100);
    }
    return null;
  };

  const getRatingStars = (rating = 4.2) => {
    const stars = [];
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;

    for (let i = 0; i < fullStars; i++) {
      stars.push(
        <svg key={i} className="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
          <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
        </svg>
      );
    }

    if (hasHalfStar) {
      stars.push(
        <svg key="half" className="w-4 h-4 text-yellow-400" viewBox="0 0 20 20">
          <defs>
            <linearGradient id="half-fill">
              <stop offset="50%" stopColor="#FCD34D"/>
              <stop offset="50%" stopColor="#E5E7EB"/>
            </linearGradient>
          </defs>
          <path fill="url(#half-fill)" d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
        </svg>
      );
    }

    const emptyStars = 5 - Math.ceil(rating);
    for (let i = 0; i < emptyStars; i++) {
      stars.push(
        <svg key={`empty-${i}`} className="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
          <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
        </svg>
      );
    }

    return stars;
  };

  const discountPercentage = getDiscountPercentage();
  const rating = 4.2; // This would come from your API
  const reviewCount = 156; // This would come from your API

  return (
    <Link to={`/product/${product.id}`} className="group block">
      <div className="bg-white rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-lg transition-all duration-200 overflow-hidden">
        {/* Image Container */}
        <div className="relative aspect-square overflow-hidden bg-gray-100">
          {!imageLoaded && (
            <div className="absolute inset-0 flex items-center justify-center">
              <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div>
            </div>
          )}
          
          <img
            src={product.images?.[0] || "https://via.placeholder.com/300x300?text=No+Image"}
            alt={product.name}
            className={`w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ${
              imageLoaded ? 'opacity-100' : 'opacity-0'
            }`}
            onLoad={() => setImageLoaded(true)}
            loading="lazy"
          />

          {/* Badges */}
          <div className="absolute top-2 left-2 flex flex-col space-y-1">
            {discountPercentage && (
              <span className="bg-red-500 text-white px-2 py-1 rounded text-xs font-bold">
                -{discountPercentage}%
              </span>
            )}
            {product.is_bestseller && (
              <span className="bg-orange-500 text-white px-2 py-1 rounded text-xs font-bold">
                #1 Best Seller
              </span>
            )}
            {product.is_new && (
              <span className="bg-green-500 text-white px-2 py-1 rounded text-xs font-bold">
                New
              </span>
            )}
          </div>

          {/* Wishlist Button */}
          <button
            onClick={handleWishlist}
            className="absolute top-2 right-2 p-2 rounded-full bg-white/80 hover:bg-white transition-colors opacity-0 group-hover:opacity-100"
          >
            <svg
              className={`w-5 h-5 ${isWishlisted ? 'text-red-500 fill-current' : 'text-gray-600'}`}
              fill={isWishlisted ? 'currentColor' : 'none'}
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
              />
            </svg>
          </button>

          {/* Quick Add to Cart (appears on hover) */}
          <div className="absolute bottom-2 left-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <button
              onClick={handleAddToCart}
              className="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded font-semibold transition-colors"
            >
              Add to Cart
            </button>
          </div>
        </div>

        {/* Product Info */}
        <div className="p-4">
          {/* Brand */}
          {product.brand && (
            <p className="text-sm text-gray-600 mb-1">{product.brand}</p>
          )}

          {/* Product Name */}
          <h3 className="font-medium text-gray-900 mb-2 line-clamp-2 group-hover:text-orange-600 transition-colors">
            {product.name}
          </h3>

          {/* Rating */}
          <div className="flex items-center space-x-1 mb-2">
            <div className="flex items-center">
              {getRatingStars(rating)}
            </div>
            <span className="text-sm text-gray-600">({reviewCount})</span>
          </div>

          {/* Price */}
          <div className="flex items-center space-x-2 mb-2">
            <span className="text-lg font-bold text-gray-900">
              ${parseFloat(product.price).toFixed(2)}
            </span>
            {product.original_price && product.original_price > product.price && (
              <span className="text-sm text-gray-500 line-through">
                ${parseFloat(product.original_price).toFixed(2)}
              </span>
            )}
          </div>

          {/* Shipping Info */}
          <div className="text-sm text-gray-600 mb-2">
            {product.free_shipping ? (
              <span className="text-green-600 font-medium">✓ FREE Shipping</span>
            ) : (
              <span>+ $5.99 shipping</span>
            )}
          </div>

          {/* Stock Status */}
          <div className="text-sm">
            {product.stock_quantity > 10 ? (
              <span className="text-green-600">In Stock</span>
            ) : product.stock_quantity > 0 ? (
              <span className="text-orange-600">Only {product.stock_quantity} left!</span>
            ) : (
              <span className="text-red-600">Out of Stock</span>
            )}
          </div>

          {/* Prime Badge (if applicable) */}
          {product.is_prime && (
            <div className="mt-2">
              <span className="bg-blue-500 text-white px-2 py-1 rounded text-xs font-bold">
                Prime
              </span>
            </div>
          )}
        </div>
      </div>
    </Link>
  );
};

export default ProductCard;
