import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';

/**
 * Premium Hero Section - Bootstrap 5 Implementation
 * Combines Apple's clean aesthetic with rich e-commerce functionality
 * Mobile-first responsive design with smooth animations
 */
const PremiumHero = () => {
  const [isVisible, setIsVisible] = useState(false);

  // Trigger entrance animation on component mount
  useEffect(() => {
    const timer = setTimeout(() => setIsVisible(true), 100);
    return () => clearTimeout(timer);
  }, []);

  // Premium hero content with conversion-focused messaging
  const heroContent = {
    headline: "Discover. Shop. Experience.",
    tagline: "Premium products curated for the modern lifestyle. Quality that speaks, prices that surprise.",
    ctaText: "Shop Collection",
    ctaLink: "/products"
  };

  // Featured categories for clean, organized display
  const featuredCategories = [
    {
      id: 1,
      name: "Premium Electronics",
      description: "Latest tech innovations",
      image: "https://images.unsplash.com/photo-1468495244123-6c6c332eeece?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80",
      link: "/products?category=electronics"
    },
    {
      id: 2,
      name: "Designer Fashion", 
      description: "Curated style collections",
      image: "https://images.unsplash.com/photo-1445205170230-053b83016050?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80",
      link: "/products?category=fashion"
    },
    {
      id: 3,
      name: "Home Essentials",
      description: "Transform your space", 
      image: "https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80",
      link: "/products?category=home"
    }
  ];

  return (
    <>
      {/* Custom CSS for premium styling */}
      <style>{`
        .hero-gradient {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          min-height: 100vh;
          position: relative;
          overflow: hidden;
        }
        
        .hero-gradient::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
          opacity: 0.1;
          z-index: 1;
        }
        
        .hero-content {
          position: relative;
          z-index: 2;
        }
        
        .fade-in-up {
          opacity: 0;
          transform: translateY(30px);
          transition: all 0.8s ease-out;
        }
        
        .fade-in-up.visible {
          opacity: 1;
          transform: translateY(0);
        }
        
        .premium-btn {
          background: linear-gradient(45deg, #ff6b6b, #ee5a24);
          border: none;
          padding: 16px 40px;
          font-size: 1.1rem;
          font-weight: 600;
          border-radius: 50px;
          color: white;
          text-decoration: none;
          display: inline-block;
          transition: all 0.3s ease;
          box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
        }
        
        .premium-btn:hover {
          transform: translateY(-2px);
          box-shadow: 0 12px 35px rgba(255, 107, 107, 0.4);
          color: white;
          text-decoration: none;
        }
        
        .category-card {
          background: white;
          border-radius: 20px;
          overflow: hidden;
          transition: all 0.3s ease;
          box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
          height: 100%;
        }
        
        .category-card:hover {
          transform: translateY(-10px);
          box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .category-image {
          height: 200px;
          background-size: cover;
          background-position: center;
          position: relative;
        }
        
        .category-overlay {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: linear-gradient(45deg, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.1));
        }
        
        @media (max-width: 768px) {
          .hero-gradient {
            min-height: 80vh;
          }
          
          .premium-btn {
            padding: 14px 30px;
            font-size: 1rem;
          }
          
          .category-image {
            height: 150px;
          }
        }
      `}</style>

      {/* Main Hero Section */}
      <section className="hero-gradient d-flex align-items-center">
        <div className="container hero-content">
          <div className="row justify-content-center text-center">
            <div className="col-lg-8">
              {/* Hero Headline */}
              <h1 
                className={`display-2 fw-bold text-white mb-4 fade-in-up ${isVisible ? 'visible' : ''}`}
                style={{ transitionDelay: '0.2s' }}
              >
                {heroContent.headline}
              </h1>
              
              {/* Hero Tagline */}
              <p 
                className={`lead text-white-50 mb-5 fade-in-up ${isVisible ? 'visible' : ''}`}
                style={{ 
                  fontSize: '1.3rem', 
                  lineHeight: '1.6',
                  transitionDelay: '0.4s' 
                }}
              >
                {heroContent.tagline}
              </p>
              
              {/* Single Call-to-Action Button */}
              <Link 
                to={heroContent.ctaLink}
                className={`premium-btn fade-in-up ${isVisible ? 'visible' : ''}`}
                style={{ transitionDelay: '0.6s' }}
              >
                {heroContent.ctaText}
                <i className="fas fa-arrow-right ms-2"></i>
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* Featured Categories Section */}
      <section className="py-5" style={{ backgroundColor: '#f8f9fa' }}>
        <div className="container">
          {/* Section Header */}
          <div className="row mb-5">
            <div className="col-12 text-center">
              <h2 className="display-5 fw-bold text-dark mb-3">
                Shop by Category
              </h2>
              <p className="lead text-muted">
                Discover our carefully curated collections
              </p>
            </div>
          </div>
          
          {/* Category Cards */}
          <div className="row g-4">
            {featuredCategories.map((category, index) => (
              <div key={category.id} className="col-lg-4 col-md-6">
                <Link 
                  to={category.link} 
                  className="text-decoration-none"
                >
                  <div 
                    className={`category-card fade-in-up ${isVisible ? 'visible' : ''}`}
                    style={{ transitionDelay: `${0.8 + index * 0.2}s` }}
                  >
                    {/* Category Image */}
                    <div 
                      className="category-image"
                      style={{ backgroundImage: `url(${category.image})` }}
                    >
                      <div className="category-overlay"></div>
                    </div>
                    
                    {/* Category Content */}
                    <div className="p-4">
                      <h4 className="fw-bold text-dark mb-2">
                        {category.name}
                      </h4>
                      <p className="text-muted mb-0">
                        {category.description}
                      </p>
                    </div>
                  </div>
                </Link>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Trust Indicators Section */}
      <section className="py-5 bg-white">
        <div className="container">
          <div className="row text-center">
            <div className="col-lg-3 col-md-6 mb-4">
              <div className="p-4">
                <i className="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                <h5 className="fw-bold">Free Shipping</h5>
                <p className="text-muted">On orders over $50</p>
              </div>
            </div>
            <div className="col-lg-3 col-md-6 mb-4">
              <div className="p-4">
                <i className="fas fa-undo fa-3x text-primary mb-3"></i>
                <h5 className="fw-bold">Easy Returns</h5>
                <p className="text-muted">30-day return policy</p>
              </div>
            </div>
            <div className="col-lg-3 col-md-6 mb-4">
              <div className="p-4">
                <i className="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                <h5 className="fw-bold">Secure Payment</h5>
                <p className="text-muted">SSL encrypted checkout</p>
              </div>
            </div>
            <div className="col-lg-3 col-md-6 mb-4">
              <div className="p-4">
                <i className="fas fa-headset fa-3x text-primary mb-3"></i>
                <h5 className="fw-bold">24/7 Support</h5>
                <p className="text-muted">Always here to help</p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </>
  );
};

export default PremiumHero;
