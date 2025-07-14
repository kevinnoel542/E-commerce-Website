#!/usr/bin/env python3
"""
Frontend-ready test script - Tests all endpoints that frontend will use
"""

import requests
import json
import time

BASE_URL = "http://localhost:8000"

def test_health():
    """Test health endpoint"""
    print("🏥 Testing Health Endpoint")
    print("=" * 30)
    
    try:
        response = requests.get(f"{BASE_URL}/health")
        if response.status_code == 200:
            print("✅ Health endpoint working")
            return True
        else:
            print(f"❌ Health endpoint failed: {response.status_code}")
            return False
    except Exception as e:
        print(f"❌ Health endpoint error: {e}")
        return False

def test_categories():
    """Test categories endpoint (should work for frontend)"""
    print("\n📂 Testing Categories Endpoint")
    print("=" * 30)
    
    try:
        response = requests.get(f"{BASE_URL}/api/v1/products/categories/")
        print(f"Status: {response.status_code}")
        
        if response.status_code == 200:
            categories = response.json()
            print(f"✅ Categories working! Found {len(categories)} categories")
            
            # Show sample data structure for frontend
            if categories:
                print("📋 Sample category structure:")
                sample = categories[0]
                for key, value in sample.items():
                    print(f"   {key}: {type(value).__name__} = {value}")
            
            return categories
        else:
            print(f"❌ Categories failed: {response.text}")
            return []
            
    except Exception as e:
        print(f"❌ Categories error: {e}")
        return []

def test_user_registration():
    """Test user registration"""
    print("\n👤 Testing User Registration")
    print("=" * 30)
    
    user_email = f"testuser{int(time.time())}@example.com"
    user_data = {
        "email": user_email,
        "password": "password123",
        "full_name": "Test User"
    }
    
    try:
        response = requests.post(f"{BASE_URL}/api/v1/auth/register", json=user_data)
        print(f"Status: {response.status_code}")
        
        if response.status_code == 200:
            result = response.json()
            print("✅ User registration working!")
            print(f"Message: {result.get('message')}")
            return user_email, "password123"
        else:
            print(f"❌ User registration failed: {response.text}")
            return None, None
            
    except Exception as e:
        print(f"❌ User registration error: {e}")
        return None, None

def test_user_login(email, password):
    """Test user login and return token"""
    print(f"\n🔐 Testing User Login")
    print("=" * 30)
    
    if not email:
        print("❌ No email provided")
        return None
    
    login_data = {"email": email, "password": password}
    
    try:
        response = requests.post(f"{BASE_URL}/api/v1/auth/login", json=login_data)
        print(f"Status: {response.status_code}")
        
        if response.status_code == 200:
            result = response.json()
            print("✅ User login working!")
            
            if result.get('tokens'):
                token = result['tokens']['access_token']
                user = result.get('user', {})
                print(f"User role: {user.get('role', 'unknown')}")
                print(f"Token received: {token[:20]}...")
                return token
            else:
                print("⚠️ No tokens in response")
                return None
        else:
            print(f"❌ User login failed: {response.text}")
            return None
            
    except Exception as e:
        print(f"❌ User login error: {e}")
        return None

def test_user_profile(token):
    """Test user profile endpoint"""
    print(f"\n👤 Testing User Profile")
    print("=" * 30)
    
    if not token:
        print("❌ No token provided")
        return False
    
    headers = {"Authorization": f"Bearer {token}"}
    
    try:
        response = requests.get(f"{BASE_URL}/api/v1/auth/profile", headers=headers)
        print(f"Status: {response.status_code}")
        
        if response.status_code == 200:
            profile = response.json()
            print("✅ User profile working!")
            print("📋 Profile structure:")
            for key, value in profile.items():
                print(f"   {key}: {type(value).__name__} = {value}")
            return True
        else:
            print(f"❌ User profile failed: {response.text}")
            return False
            
    except Exception as e:
        print(f"❌ User profile error: {e}")
        return False

def test_products():
    """Test products endpoint"""
    print(f"\n📦 Testing Products Endpoint")
    print("=" * 30)
    
    try:
        response = requests.get(f"{BASE_URL}/api/v1/products/")
        print(f"Status: {response.status_code}")
        
        if response.status_code == 200:
            result = response.json()
            products = result.get('products', [])
            print(f"✅ Products working! Found {len(products)} products")
            
            # Show sample data structure for frontend
            if products:
                print("📋 Sample product structure:")
                sample = products[0]
                for key, value in sample.items():
                    print(f"   {key}: {type(value).__name__} = {value}")
            
            return products
        else:
            print(f"❌ Products failed: {response.text}")
            return []
            
    except Exception as e:
        print(f"❌ Products error: {e}")
        return []

def test_admin_endpoints():
    """Test admin-specific endpoints"""
    print(f"\n👑 Testing Admin Endpoints")
    print("=" * 30)
    
    # Try to register admin
    admin_data = {
        "email": f"admin{int(time.time())}@test.com",
        "password": "admin123456",
        "full_name": "Test Admin",
        "admin_secret": "super-secret-admin-key-change-in-production"
    }
    
    try:
        # Register admin
        response = requests.post(f"{BASE_URL}/api/v1/auth/admin/register", json=admin_data)
        if response.status_code != 200:
            print(f"⚠️ Admin registration failed: {response.text}")
            return False
        
        # Login as admin
        login_response = requests.post(f"{BASE_URL}/api/v1/auth/login", json={
            "email": admin_data["email"],
            "password": admin_data["password"]
        })
        
        if login_response.status_code != 200:
            print(f"❌ Admin login failed: {login_response.text}")
            return False
        
        admin_token = login_response.json()['tokens']['access_token']
        
        # Test admin product creation
        headers = {"Authorization": f"Bearer {admin_token}"}
        product_data = {
            "name": "Test Product",
            "description": "A test product",
            "price": 99.99,
            "stock_quantity": 10
        }
        
        product_response = requests.post(
            f"{BASE_URL}/api/v1/products/",
            json=product_data,
            headers=headers
        )
        
        if product_response.status_code == 200:
            product = product_response.json()
            print("✅ Admin product creation working!")
            print(f"Auto-generated SKU: {product.get('sku')}")
            return True
        else:
            print(f"❌ Admin product creation failed: {product_response.text}")
            return False
            
    except Exception as e:
        print(f"❌ Admin endpoints error: {e}")
        return False

def test_cors():
    """Test CORS headers"""
    print(f"\n🌐 Testing CORS Headers")
    print("=" * 30)
    
    try:
        # Test preflight request
        headers = {
            "Origin": "http://localhost:3000",
            "Access-Control-Request-Method": "GET",
            "Access-Control-Request-Headers": "authorization,content-type"
        }
        
        response = requests.options(f"{BASE_URL}/api/v1/products/categories/", headers=headers)
        
        cors_headers = {
            "Access-Control-Allow-Origin": response.headers.get("Access-Control-Allow-Origin"),
            "Access-Control-Allow-Methods": response.headers.get("Access-Control-Allow-Methods"),
            "Access-Control-Allow-Headers": response.headers.get("Access-Control-Allow-Headers")
        }
        
        if cors_headers["Access-Control-Allow-Origin"]:
            print("✅ CORS headers present!")
            for header, value in cors_headers.items():
                if value:
                    print(f"   {header}: {value}")
            return True
        else:
            print("⚠️ CORS headers missing - frontend may have issues")
            return False
            
    except Exception as e:
        print(f"❌ CORS test error: {e}")
        return False

def main():
    """Run all frontend-ready tests"""
    print("🚀 Frontend-Ready API Test Suite")
    print("=" * 50)
    
    results = {}
    
    # Test 1: Health check
    results["health"] = test_health()
    
    # Test 2: Categories (public endpoint)
    categories = test_categories()
    results["categories"] = len(categories) >= 0
    
    # Test 3: Products (public endpoint)
    products = test_products()
    results["products"] = len(products) >= 0
    
    # Test 4: User registration and login
    user_email, user_password = test_user_registration()
    results["user_registration"] = user_email is not None
    
    if user_email:
        user_token = test_user_login(user_email, user_password)
        results["user_login"] = user_token is not None
        
        if user_token:
            results["user_profile"] = test_user_profile(user_token)
    
    # Test 5: Admin endpoints
    results["admin_endpoints"] = test_admin_endpoints()
    
    # Test 6: CORS
    results["cors"] = test_cors()
    
    # Summary
    print(f"\n📊 Frontend-Ready Test Results")
    print("=" * 40)
    
    passed = sum(results.values())
    total = len(results)
    
    for test_name, passed_test in results.items():
        status = "✅ PASS" if passed_test else "❌ FAIL"
        print(f"{test_name.replace('_', ' ').title()}: {status}")
    
    print(f"\nOverall: {passed}/{total} tests passed")
    
    if passed == total:
        print(f"\n🎉 All tests passed! Your API is frontend-ready!")
        print(f"\n📋 Frontend Integration Notes:")
        print(f"• Base URL: {BASE_URL}")
        print(f"• Categories: GET /api/v1/products/categories/")
        print(f"• Products: GET /api/v1/products/")
        print(f"• Auth: POST /api/v1/auth/register, /api/v1/auth/login")
        print(f"• Profile: GET /api/v1/auth/profile (requires Bearer token)")
        print(f"• Admin: POST /api/v1/auth/admin/register")
    else:
        print(f"\n⚠️ Some tests failed. Please fix the issues above.")

if __name__ == "__main__":
    main()
