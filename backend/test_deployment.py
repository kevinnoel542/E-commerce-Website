#!/usr/bin/env python3
"""
Test script to verify the API is ready for deployment
"""
import asyncio
import sys
import os
import requests
import json
from datetime import datetime

# Add the app directory to the path
sys.path.append(os.path.join(os.path.dirname(__file__), 'app'))

def test_health_endpoint():
    """Test the health endpoint"""
    try:
        response = requests.get("http://localhost:8000/health")
        if response.status_code == 200:
            print("✅ Health endpoint working")
            return True
        else:
            print(f"❌ Health endpoint failed: {response.status_code}")
            return False
    except Exception as e:
        print(f"❌ Health endpoint error: {str(e)}")
        return False

def test_docs_endpoint():
    """Test the API documentation endpoint"""
    try:
        response = requests.get("http://localhost:8000/docs")
        if response.status_code == 200:
            print("✅ API docs endpoint working")
            return True
        else:
            print(f"❌ API docs failed: {response.status_code}")
            return False
    except Exception as e:
        print(f"❌ API docs error: {str(e)}")
        return False

def test_products_endpoint():
    """Test the products endpoint"""
    try:
        response = requests.get("http://localhost:8000/api/v1/products")
        if response.status_code == 200:
            print("✅ Products endpoint working")
            return True
        else:
            print(f"❌ Products endpoint failed: {response.status_code}")
            return False
    except Exception as e:
        print(f"❌ Products endpoint error: {str(e)}")
        return False

def test_environment_variables():
    """Test that required environment variables are set"""
    required_vars = [
        "SUPABASE_URL",
        "SUPABASE_SERVICE_KEY",
        "STRIPE_SECRET_KEY"
    ]
    
    missing_vars = []
    for var in required_vars:
        if not os.getenv(var):
            missing_vars.append(var)
    
    if missing_vars:
        print(f"❌ Missing environment variables: {', '.join(missing_vars)}")
        return False
    else:
        print("✅ All required environment variables are set")
        return True

def test_config_import():
    """Test that the config can be imported without errors"""
    try:
        from app.core.config import SUPABASE_URL, STRIPE_SECRET_KEY
        print("✅ Config imports successfully")
        return True
    except Exception as e:
        print(f"❌ Config import error: {str(e)}")
        return False

def test_database_connection():
    """Test database connection"""
    try:
        from app.db.client import DatabaseClient
        db = DatabaseClient()
        print("✅ Database client created successfully")
        return True
    except Exception as e:
        print(f"❌ Database connection error: {str(e)}")
        return False

def test_stripe_import():
    """Test Stripe import and configuration"""
    try:
        import stripe
        from app.core.config import STRIPE_SECRET_KEY
        if STRIPE_SECRET_KEY:
            stripe.api_key = STRIPE_SECRET_KEY
            print("✅ Stripe configured successfully")
            return True
        else:
            print("❌ Stripe secret key not set")
            return False
    except Exception as e:
        print(f"❌ Stripe configuration error: {str(e)}")
        return False

def main():
    """Run all tests"""
    print("🧪 Testing E-Commerce API for deployment readiness...")
    print("=" * 50)
    
    tests = [
        ("Environment Variables", test_environment_variables),
        ("Config Import", test_config_import),
        ("Database Connection", test_database_connection),
        ("Stripe Configuration", test_stripe_import),
        ("Health Endpoint", test_health_endpoint),
        ("API Docs", test_docs_endpoint),
        ("Products Endpoint", test_products_endpoint),
    ]
    
    passed = 0
    total = len(tests)
    
    for test_name, test_func in tests:
        print(f"\n🔍 Testing {test_name}...")
        if test_func():
            passed += 1
        else:
            print(f"   ⚠️  {test_name} test failed")
    
    print("\n" + "=" * 50)
    print(f"📊 Test Results: {passed}/{total} tests passed")
    
    if passed == total:
        print("🎉 All tests passed! Your API is ready for deployment!")
        return True
    else:
        print("❌ Some tests failed. Please fix the issues before deploying.")
        return False

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)
