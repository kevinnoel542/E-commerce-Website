import React from 'react';
import { Link } from 'react-router-dom';

/**
 * Simple Hero Component - Bootstrap 5 Fallback
 * Clean, minimal design that always works
 */
const SimpleHero = () => {
  return (
    <div>
      {/* Hero Section */}
      <div className="bg-primary text-white py-5" style={{ minHeight: '70vh' }}>
        <div className="container h-100 d-flex align-items-center justify-content-center">
          <div className="text-center">
            <h1 className="display-4 fw-bold mb-4">
              Discover. Shop. Experience.
            </h1>
            <p className="lead mb-4">
              Premium products curated for the modern lifestyle. Quality that speaks, prices that surprise.
            </p>
            <Link 
              to="/products" 
              className="btn btn-warning btn-lg px-5 py-3 fw-bold"
              style={{ borderRadius: '50px' }}
            >
              Shop Collection
            </Link>
          </div>
        </div>
      </div>

      {/* Categories Section */}
      <div className="py-5 bg-light">
        <div className="container">
          <div className="row text-center mb-5">
            <div className="col-12">
              <h2 className="display-5 fw-bold">Shop by Category</h2>
              <p className="lead text-muted">Discover our carefully curated collections</p>
            </div>
          </div>
          
          <div className="row g-4">
            <div className="col-lg-4 col-md-6">
              <div className="card h-100 border-0 shadow-sm">
                <img 
                  src="https://images.unsplash.com/photo-1468495244123-6c6c332eeece?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                  className="card-img-top" 
                  alt="Electronics"
                  style={{ height: '200px', objectFit: 'cover' }}
                />
                <div className="card-body">
                  <h5 className="card-title fw-bold">Premium Electronics</h5>
                  <p className="card-text text-muted">Latest tech innovations</p>
                  <Link to="/products?category=electronics" className="btn btn-outline-primary">
                    Shop Now
                  </Link>
                </div>
              </div>
            </div>
            
            <div className="col-lg-4 col-md-6">
              <div className="card h-100 border-0 shadow-sm">
                <img 
                  src="https://images.unsplash.com/photo-1445205170230-053b83016050?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                  className="card-img-top" 
                  alt="Fashion"
                  style={{ height: '200px', objectFit: 'cover' }}
                />
                <div className="card-body">
                  <h5 className="card-title fw-bold">Designer Fashion</h5>
                  <p className="card-text text-muted">Curated style collections</p>
                  <Link to="/products?category=fashion" className="btn btn-outline-primary">
                    Shop Now
                  </Link>
                </div>
              </div>
            </div>
            
            <div className="col-lg-4 col-md-6">
              <div className="card h-100 border-0 shadow-sm">
                <img 
                  src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                  className="card-img-top" 
                  alt="Home"
                  style={{ height: '200px', objectFit: 'cover' }}
                />
                <div className="card-body">
                  <h5 className="card-title fw-bold">Home Essentials</h5>
                  <p className="card-text text-muted">Transform your space</p>
                  <Link to="/products?category=home" className="btn btn-outline-primary">
                    Shop Now
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Trust Indicators */}
      <div className="py-5 bg-white">
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
      </div>
    </div>
  );
};

export default SimpleHero;
