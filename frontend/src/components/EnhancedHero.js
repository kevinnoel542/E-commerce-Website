import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';

const EnhancedHero = () => {
  const [isVisible, setIsVisible] = useState(false);
  const [currentSlide, setCurrentSlide] = useState(0);

  // Premium hero content with Apple-inspired messaging
  const heroContent = {
    headline: "Discover. Shop. Experience.",
    tagline: "Premium products curated for the modern lifestyle. Quality that speaks, prices that surprise.",
    ctaText: "Shop Collection",
    ctaLink: "/products"
  };

  // Featured categories with premium imagery
  const featuredCategories = [
    {
      id: 1,
      name: "Premium Electronics",
      description: "Latest tech innovations",
      image: "https://images.unsplash.com/photo-1468495244123-6c6c332eeece?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      link: "/products?category=electronics"
    },
    {
      id: 2,
      name: "Designer Fashion",
      description: "Curated style collections",
      image: "https://images.unsplash.com/photo-1445205170230-053b83016050?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      link: "/products?category=fashion"
    },
    {
      id: 3,
      name: "Home Essentials",
      description: "Transform your space",
      image: "https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
      link: "/products?category=home"
    }
  ];

  const quickDeals = [
    {
      id: 1,
      title: "Lightning Deal",
      discount: "50% OFF",
      originalPrice: 199.99,
      salePrice: 99.99,
      image: "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80",
      timeLeft: "2h 15m",
      claimed: 45
    },
    {
      id: 2,
      title: "Daily Deal",
      discount: "30% OFF",
      originalPrice: 79.99,
      salePrice: 55.99,
      image: "https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80",
      timeLeft: "8h 42m",
      claimed: 23
    },
    {
      id: 3,
      title: "Flash Sale",
      discount: "40% OFF",
      originalPrice: 149.99,
      salePrice: 89.99,
      image: "https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80",
      timeLeft: "1h 33m",
      claimed: 67
    }
  ];

  const categories = [
    {
      name: "Electronics",
      image: "https://images.unsplash.com/photo-1498049794561-7780e7231661?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80",
      link: "/products?category=electronics"
    },
    {
      name: "Fashion",
      image: "https://images.unsplash.com/photo-1445205170230-053b83016050?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80",
      link: "/products?category=clothing"
    },
    {
      name: "Home & Garden",
      image: "https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80",
      link: "/products?category=home"
    },
    {
      name: "Books",
      image: "https://images.unsplash.com/photo-1481627834876-b7833e8f5570?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80",
      link: "/products?category=books"
    }
  ];

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % banners.length);
    }, 5000);
    return () => clearInterval(timer);
  }, [banners.length]);

  return (
    <div className="bg-gray-100">
      {/* Main Banner Carousel */}
      <div className="relative h-96 overflow-hidden">
        {banners.map((banner, index) => (
          <div
            key={banner.id}
            className={`absolute inset-0 transition-transform duration-500 ease-in-out ${
              index === currentSlide ? 'translate-x-0' : 'translate-x-full'
            }`}
            style={{
              backgroundImage: `linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url(${banner.image})`,
              backgroundSize: 'cover',
              backgroundPosition: 'center'
            }}
          >
            <div className="flex items-center justify-center h-full text-white text-center">
              <div className="max-w-2xl px-4">
                <h1 className="text-5xl font-bold mb-4">{banner.title}</h1>
                <p className="text-xl mb-8">{banner.subtitle}</p>
                <Link
                  to={banner.link}
                  className="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg text-lg font-semibold transition-colors inline-block"
                >
                  {banner.cta}
                </Link>
              </div>
            </div>
          </div>
        ))}
        
        {/* Carousel Indicators */}
        <div className="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
          {banners.map((_, index) => (
            <button
              key={index}
              onClick={() => setCurrentSlide(index)}
              className={`w-3 h-3 rounded-full transition-colors ${
                index === currentSlide ? 'bg-white' : 'bg-white/50'
              }`}
            />
          ))}
        </div>

        {/* Navigation Arrows */}
        <button
          onClick={() => setCurrentSlide((prev) => (prev - 1 + banners.length) % banners.length)}
          className="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full transition-colors"
        >
          <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button
          onClick={() => setCurrentSlide((prev) => (prev + 1) % banners.length)}
          className="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full transition-colors"
        >
          <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>

      {/* Quick Deals Section */}
      <div className="max-w-7xl mx-auto px-4 py-8">
        <div className="bg-white rounded-lg shadow-sm p-6 mb-8">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-2xl font-bold text-gray-900">⚡ Lightning Deals</h2>
            <Link to="/deals" className="text-orange-500 hover:text-orange-600 font-semibold">
              See all deals →
            </Link>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {quickDeals.map((deal) => (
              <div key={deal.id} className="border rounded-lg p-4 hover:shadow-md transition-shadow">
                <div className="relative mb-4">
                  <img
                    src={deal.image}
                    alt={deal.title}
                    className="w-full h-32 object-cover rounded"
                  />
                  <span className="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-sm font-bold">
                    {deal.discount}
                  </span>
                </div>
                
                <h3 className="font-semibold mb-2">{deal.title}</h3>
                
                <div className="flex items-center space-x-2 mb-2">
                  <span className="text-lg font-bold text-red-600">${deal.salePrice}</span>
                  <span className="text-sm text-gray-500 line-through">${deal.originalPrice}</span>
                </div>
                
                <div className="flex items-center justify-between text-sm text-gray-600 mb-3">
                  <span>⏰ {deal.timeLeft} left</span>
                  <span>{deal.claimed}% claimed</span>
                </div>
                
                <div className="w-full bg-gray-200 rounded-full h-2 mb-3">
                  <div 
                    className="bg-orange-500 h-2 rounded-full transition-all duration-300"
                    style={{ width: `${deal.claimed}%` }}
                  ></div>
                </div>
                
                <button className="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 rounded font-semibold transition-colors">
                  Claim Deal
                </button>
              </div>
            ))}
          </div>
        </div>

        {/* Categories Grid */}
        <div className="bg-white rounded-lg shadow-sm p-6">
          <h2 className="text-2xl font-bold text-gray-900 mb-6">Shop by Category</h2>
          
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
            {categories.map((category) => (
              <Link
                key={category.name}
                to={category.link}
                className="group text-center hover:transform hover:scale-105 transition-all duration-200"
              >
                <div className="relative overflow-hidden rounded-lg mb-3">
                  <img
                    src={category.image}
                    alt={category.name}
                    className="w-full h-32 object-cover group-hover:scale-110 transition-transform duration-300"
                  />
                  <div className="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-colors"></div>
                </div>
                <h3 className="font-semibold text-gray-900 group-hover:text-orange-500 transition-colors">
                  {category.name}
                </h3>
              </Link>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
};

export default EnhancedHero;
