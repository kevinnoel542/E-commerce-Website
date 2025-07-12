#!/usr/bin/env python3
"""
Test script for new features: Categories fix, Image upload, Auto-SKU
"""

import requests
import json
import os
from pathlib import Path

BASE_URL = "http://localhost:8000"

def test_categories():
    """Test the fixed categories endpoint"""
    print("🧪 Testing Categories Endpoint")
    print("=" * 40)
    
    try:
        response = requests.get(f"{BASE_URL}/api/v1/products/categories/")
        print(f"Status: {response.status_code}")
        
        if response.status_code == 200:
            categories = response.json()
            print(f"✅ Categories endpoint working! Found {len(categories)} categories")
            
            for cat in categories:
                print(f"   - {cat['name']}: {cat['description']}")
            
            return True
        else:
            print(f"❌ Categories endpoint failed: {response.text}")
            return False
            
    except Exception as e:
        print(f"❌ Error testing categories: {e}")
        return False

def test_admin_login():
    """Test admin login and return token"""
    print("\n🔐 Testing Admin Login")
    print("=" * 40)
    
    # Try to login with admin credentials
    login_data = {
        "email": "admin@test.com",
        "password": "admin123456"
    }
    
    try:
        response = requests.post(f"{BASE_URL}/api/v1/auth/login", json=login_data)
        
        if response.status_code == 200:
            result = response.json()
            if result.get('tokens'):
                token = result['tokens']['access_token']
                print(f"✅ Admin login successful!")
                print(f"User role: {result.get('user', {}).get('role', 'unknown')}")
                return token
            else:
                print("❌ No tokens in response")
                return None
        else:
            print(f"❌ Admin login failed: {response.text}")
            print("\n💡 Tip: Register an admin first:")
            print("curl -X POST 'http://localhost:8000/api/v1/auth/admin/register' \\")
            print("  -H 'Content-Type: application/json' \\")
            print("  -d '{")
            print('    "email": "admin@test.com",')
            print('    "password": "admin123456",')
            print('    "full_name": "Test Admin",')
            print('    "admin_secret": "super-secret-admin-key-change-in-production"')
            print("  }'")
            return None
            
    except Exception as e:
        print(f"❌ Error during admin login: {e}")
        return None

def test_auto_sku_product_creation(admin_token):
    """Test product creation with auto-generated SKU"""
    print("\n📦 Testing Auto-SKU Product Creation")
    print("=" * 40)
    
    if not admin_token:
        print("❌ No admin token available")
        return False
    
    headers = {
        "Authorization": f"Bearer {admin_token}",
        "Content-Type": "application/json"
    }
    
    product_data = {
        "name": "Test Product Auto SKU",
        "description": "A test product with auto-generated SKU",
        "price": 99.99,
        "stock_quantity": 50
        # Note: No SKU provided - should be auto-generated
    }
    
    try:
        response = requests.post(
            f"{BASE_URL}/api/v1/products/",
            json=product_data,
            headers=headers
        )
        
        if response.status_code == 200:
            product = response.json()
            print(f"✅ Product created successfully!")
            print(f"Product ID: {product['id']}")
            print(f"Auto-generated SKU: {product['sku']}")
            print(f"Name: {product['name']}")
            return product['id']
        else:
            print(f"❌ Product creation failed: {response.text}")
            return None
            
    except Exception as e:
        print(f"❌ Error creating product: {e}")
        return None

def test_image_upload(admin_token):
    """Test image upload functionality"""
    print("\n🖼️ Testing Image Upload")
    print("=" * 40)
    
    if not admin_token:
        print("❌ No admin token available")
        return False
    
    # Create a simple test image file
    test_image_path = "test_image.jpg"
    try:
        # Create a simple 1x1 pixel JPEG for testing
        from PIL import Image
        img = Image.new('RGB', (100, 100), color='red')
        img.save(test_image_path, 'JPEG')
        
        headers = {"Authorization": f"Bearer {admin_token}"}
        
        with open(test_image_path, 'rb') as f:
            files = {"file": ("test_image.jpg", f, "image/jpeg")}
            response = requests.post(
                f"{BASE_URL}/api/v1/products/upload-image",
                files=files,
                headers=headers
            )
        
        # Clean up test file
        os.remove(test_image_path)
        
        if response.status_code == 200:
            result = response.json()
            print(f"✅ Image uploaded successfully!")
            print(f"URL: {result['url']}")
            print(f"Filename: {result['filename']}")
            print(f"Size: {result['size']} bytes")
            return result['url']
        else:
            print(f"❌ Image upload failed: {response.text}")
            return None
            
    except ImportError:
        print("❌ PIL (Pillow) not installed. Install with: pip install Pillow")
        return None
    except Exception as e:
        print(f"❌ Error uploading image: {e}")
        return None

def test_product_with_images(admin_token):
    """Test creating product with images in one request"""
    print("\n📦🖼️ Testing Product Creation with Images")
    print("=" * 40)
    
    if not admin_token:
        print("❌ No admin token available")
        return False
    
    try:
        # Create test images
        from PIL import Image
        test_files = []
        
        for i in range(2):
            filename = f"test_product_image_{i}.jpg"
            img = Image.new('RGB', (200, 200), color=['blue', 'green'][i])
            img.save(filename, 'JPEG')
            test_files.append(filename)
        
        headers = {"Authorization": f"Bearer {admin_token}"}
        
        # Prepare form data
        data = {
            "name": "Product with Images",
            "description": "A product created with multiple images",
            "price": 149.99,
            "stock_quantity": 25
        }
        
        files = []
        for filename in test_files:
            files.append(("images", (filename, open(filename, 'rb'), "image/jpeg")))
        
        response = requests.post(
            f"{BASE_URL}/api/v1/products/create-with-images",
            data=data,
            files=files,
            headers=headers
        )
        
        # Close files and clean up
        for _, (_, file_obj, _) in files:
            file_obj.close()
        
        for filename in test_files:
            os.remove(filename)
        
        if response.status_code == 200:
            result = response.json()
            product = result['product']
            images = result['uploaded_images']
            
            print(f"✅ Product with images created successfully!")
            print(f"Product ID: {product['id']}")
            print(f"Auto-generated SKU: {product['sku']}")
            print(f"Images uploaded: {len(images)}")
            
            for i, img in enumerate(images):
                print(f"   Image {i+1}: {img['url']}")
            
            return True
        else:
            print(f"❌ Product with images creation failed: {response.text}")
            return False
            
    except ImportError:
        print("❌ PIL (Pillow) not installed. Install with: pip install Pillow")
        return False
    except Exception as e:
        print(f"❌ Error creating product with images: {e}")
        return False

def main():
    """Run all tests"""
    print("🚀 Testing New E-commerce Features")
    print("=" * 50)
    
    # Test 1: Categories endpoint
    categories_ok = test_categories()
    
    # Test 2: Admin login
    admin_token = test_admin_login()
    
    if admin_token:
        # Test 3: Auto-SKU product creation
        product_id = test_auto_sku_product_creation(admin_token)
        
        # Test 4: Image upload
        image_url = test_image_upload(admin_token)
        
        # Test 5: Product with images
        product_with_images_ok = test_product_with_images(admin_token)
    
    print(f"\n📊 Test Results Summary:")
    print(f"Categories endpoint: {'✅ Working' if categories_ok else '❌ Failed'}")
    print(f"Admin authentication: {'✅ Working' if admin_token else '❌ Failed'}")
    
    if admin_token:
        print(f"Auto-SKU generation: {'✅ Working' if product_id else '❌ Failed'}")
        print(f"Image upload: {'✅ Working' if image_url else '❌ Failed'}")
        print(f"Product with images: {'✅ Working' if 'product_with_images_ok' in locals() and product_with_images_ok else '❌ Failed'}")
    
    print(f"\n🎯 Next Steps:")
    print("1. Ensure your FastAPI server is running: uvicorn app.main:app --reload")
    print("2. Check API docs: http://localhost:8000/docs")
    print("3. Test image access: http://localhost:8000/uploads/products/[filename]")

if __name__ == "__main__":
    main()
