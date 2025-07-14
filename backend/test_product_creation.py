#!/usr/bin/env python3
"""
Test script for product creation with Decimal fix
"""

import requests
import json
import time

BASE_URL = "http://localhost:8000"

def get_admin_token():
    """Get admin token for testing"""
    print("🔐 Getting Admin Token")
    print("=" * 30)
    
    # Try to login with existing admin
    login_data = {
        "email": "starkalyboy@gmail.com",
        "password": "your_password_here"  # Replace with actual password
    }
    
    try:
        response = requests.post(f"{BASE_URL}/api/v1/auth/login", json=login_data)
        
        if response.status_code == 200:
            result = response.json()
            if result.get('tokens'):
                token = result['tokens']['access_token']
                user = result.get('user', {})
                print(f"✅ Login successful!")
                print(f"User role: {user.get('role', 'unknown')}")
                return token
            else:
                print("❌ No tokens in response")
                return None
        else:
            print(f"❌ Login failed: {response.text}")
            
            # Try to register admin if login fails
            print("\n🔧 Trying to register admin...")
            admin_data = {
                "email": "admin@test.com",
                "password": "admin123456",
                "full_name": "Test Admin",
                "admin_secret": "super-secret-admin-key-change-in-production"
            }
            
            reg_response = requests.post(f"{BASE_URL}/api/v1/auth/admin/register", json=admin_data)
            if reg_response.status_code == 200:
                print("✅ Admin registered successfully!")
                
                # Now login with new admin
                login_response = requests.post(f"{BASE_URL}/api/v1/auth/login", json={
                    "email": admin_data["email"],
                    "password": admin_data["password"]
                })
                
                if login_response.status_code == 200:
                    result = login_response.json()
                    return result['tokens']['access_token']
            
            return None
            
    except Exception as e:
        print(f"❌ Error during admin authentication: {e}")
        return None

def test_product_creation_with_decimal(admin_token):
    """Test product creation with various price formats"""
    print(f"\n📦 Testing Product Creation with Decimal Fix")
    print("=" * 50)
    
    if not admin_token:
        print("❌ No admin token available")
        return False
    
    headers = {
        "Authorization": f"Bearer {admin_token}",
        "Content-Type": "application/json"
    }
    
    # Test cases with different price formats
    test_products = [
        {
            "name": "Test Product 1",
            "description": "Product with integer price",
            "price": 110,  # Integer
            "stock_quantity": 20
        },
        {
            "name": "Test Product 2", 
            "description": "Product with float price",
            "price": 99.99,  # Float
            "stock_quantity": 15
        },
        {
            "name": "Test Product 3",
            "description": "Product with string price",
            "price": "149.50",  # String (should be converted)
            "stock_quantity": 30
        },
        {
            "name": "Test Product 4",
            "description": "Product with auto SKU",
            "price": 75.25,
            "stock_quantity": 10,
            "sku": "string"  # Should trigger auto-generation
        }
    ]
    
    successful_products = []
    
    for i, product_data in enumerate(test_products, 1):
        print(f"\n🧪 Test {i}: {product_data['name']}")
        print(f"Price type: {type(product_data['price']).__name__}")
        print(f"Price value: {product_data['price']}")
        
        try:
            response = requests.post(
                f"{BASE_URL}/api/v1/products/",
                json=product_data,
                headers=headers
            )
            
            print(f"Status: {response.status_code}")
            
            if response.status_code == 200:
                product = response.json()
                print(f"✅ Product created successfully!")
                print(f"   ID: {product['id']}")
                print(f"   SKU: {product['sku']}")
                print(f"   Price: {product['price']} ({type(product['price']).__name__})")
                successful_products.append(product)
            else:
                print(f"❌ Product creation failed: {response.text}")
                
        except Exception as e:
            print(f"❌ Error creating product: {e}")
    
    print(f"\n📊 Results: {len(successful_products)}/{len(test_products)} products created successfully")
    return len(successful_products) == len(test_products)

def test_product_with_category(admin_token):
    """Test product creation with category"""
    print(f"\n📂 Testing Product with Category")
    print("=" * 40)
    
    if not admin_token:
        print("❌ No admin token available")
        return False
    
    headers = {
        "Authorization": f"Bearer {admin_token}",
        "Content-Type": "application/json"
    }
    
    # First, get available categories
    try:
        cat_response = requests.get(f"{BASE_URL}/api/v1/products/categories/")
        categories = cat_response.json() if cat_response.status_code == 200 else []
        
        category_id = None
        if categories:
            category_id = categories[0]['id']
            print(f"Using category: {categories[0]['name']} (ID: {category_id})")
        else:
            print("No categories available, creating product without category")
        
        product_data = {
            "name": "Categorized Product",
            "description": "A product with category",
            "price": 199.99,
            "category_id": category_id,
            "brand": "Test Brand",
            "stock_quantity": 25
        }
        
        response = requests.post(
            f"{BASE_URL}/api/v1/products/",
            json=product_data,
            headers=headers
        )
        
        if response.status_code == 200:
            product = response.json()
            print(f"✅ Categorized product created!")
            print(f"   SKU: {product['sku']}")
            print(f"   Category ID: {product.get('category_id')}")
            return True
        else:
            print(f"❌ Categorized product creation failed: {response.text}")
            return False
            
    except Exception as e:
        print(f"❌ Error creating categorized product: {e}")
        return False

def test_product_listing():
    """Test product listing to see created products"""
    print(f"\n📋 Testing Product Listing")
    print("=" * 40)
    
    try:
        response = requests.get(f"{BASE_URL}/api/v1/products/")
        
        if response.status_code == 200:
            result = response.json()
            products = result.get('products', [])
            print(f"✅ Found {len(products)} products")
            
            for product in products[:3]:  # Show first 3 products
                print(f"   - {product['name']}: ${product['price']} (SKU: {product['sku']})")
            
            return True
        else:
            print(f"❌ Product listing failed: {response.text}")
            return False
            
    except Exception as e:
        print(f"❌ Error listing products: {e}")
        return False

def main():
    """Run all product creation tests"""
    print("🚀 Product Creation Test Suite (Decimal Fix)")
    print("=" * 60)
    
    # Get admin token
    admin_token = get_admin_token()
    
    if not admin_token:
        print("\n❌ Could not get admin token. Please check:")
        print("1. Server is running: uvicorn app.main:app --reload")
        print("2. Admin user exists or can be created")
        print("3. Database is properly configured")
        return
    
    results = {}
    
    # Test 1: Basic product creation with decimal fix
    results["decimal_fix"] = test_product_creation_with_decimal(admin_token)
    
    # Test 2: Product with category
    results["with_category"] = test_product_with_category(admin_token)
    
    # Test 3: Product listing
    results["listing"] = test_product_listing()
    
    # Summary
    print(f"\n📊 Test Results Summary")
    print("=" * 40)
    
    passed = sum(results.values())
    total = len(results)
    
    for test_name, passed_test in results.items():
        status = "✅ PASS" if passed_test else "❌ FAIL"
        print(f"{test_name.replace('_', ' ').title()}: {status}")
    
    print(f"\nOverall: {passed}/{total} tests passed")
    
    if passed == total:
        print(f"\n🎉 All tests passed! Product creation is working!")
        print(f"\n📋 Your admin can now:")
        print(f"• Create products with any price format")
        print(f"• Auto-generate SKUs")
        print(f"• Add products to categories")
        print(f"• Upload images (if configured)")
    else:
        print(f"\n⚠️ Some tests failed. Check the errors above.")

if __name__ == "__main__":
    main()
