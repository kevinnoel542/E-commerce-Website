#!/usr/bin/env python3
"""
Test script to debug login and demonstrate JWT protection
"""

import requests
import json

BASE_URL = "http://localhost:8000"

def test_login(email, password):
    """Test login endpoint"""
    url = f"{BASE_URL}/api/v1/auth/login"
    
    data = {
        "email": email,
        "password": password
    }
    
    print(f"🔐 Testing login for: {email}")
    print(f"Request: POST {url}")
    print(f"Data: {json.dumps(data, indent=2)}")
    
    try:
        response = requests.post(url, json=data, timeout=30)
        
        print(f"Status: {response.status_code}")
        
        if response.status_code == 200:
            result = response.json()
            print("✅ Login successful!")
            print(f"Message: {result.get('message')}")
            
            if result.get('tokens'):
                tokens = result['tokens']
                print(f"🎫 Access Token: {tokens['access_token'][:50]}...")
                print(f"🔄 Refresh Token: {tokens['refresh_token'][:50]}...")
                return tokens
            else:
                print("⚠️ No tokens returned")
                return None
        else:
            try:
                error = response.json()
                print(f"❌ Login failed: {error.get('detail', 'Unknown error')}")
            except:
                print(f"❌ Login failed: {response.text}")
            return None
            
    except Exception as e:
        print(f"❌ Request failed: {e}")
        return None

def test_protected_endpoint(access_token, endpoint="/api/v1/auth/profile"):
    """Test a protected endpoint with JWT token"""
    url = f"{BASE_URL}{endpoint}"
    
    headers = {
        "Authorization": f"Bearer {access_token}",
        "Content-Type": "application/json"
    }
    
    print(f"\n🛡️ Testing protected endpoint: {endpoint}")
    print(f"Request: GET {url}")
    print(f"Headers: Authorization: Bearer {access_token[:20]}...")
    
    try:
        response = requests.get(url, headers=headers, timeout=30)
        
        print(f"Status: {response.status_code}")
        
        if response.status_code == 200:
            result = response.json()
            print("✅ Protected endpoint access successful!")
            print(f"Response: {json.dumps(result, indent=2)}")
            return True
        else:
            try:
                error = response.json()
                print(f"❌ Access denied: {error.get('detail', 'Unknown error')}")
            except:
                print(f"❌ Access denied: {response.text}")
            return False
            
    except Exception as e:
        print(f"❌ Request failed: {e}")
        return False

def test_unprotected_endpoint(endpoint="/api/v1/products/"):
    """Test an unprotected endpoint"""
    url = f"{BASE_URL}{endpoint}"
    
    print(f"\n🌐 Testing unprotected endpoint: {endpoint}")
    print(f"Request: GET {url}")
    
    try:
        response = requests.get(url, timeout=30)
        
        print(f"Status: {response.status_code}")
        
        if response.status_code == 200:
            print("✅ Unprotected endpoint access successful!")
            return True
        else:
            print(f"❌ Unexpected error: {response.status_code}")
            return False
            
    except Exception as e:
        print(f"❌ Request failed: {e}")
        return False

def test_without_token(endpoint="/api/v1/auth/profile"):
    """Test protected endpoint without token"""
    url = f"{BASE_URL}{endpoint}"
    
    print(f"\n🚫 Testing protected endpoint WITHOUT token: {endpoint}")
    print(f"Request: GET {url}")
    
    try:
        response = requests.get(url, timeout=30)
        
        print(f"Status: {response.status_code}")
        
        if response.status_code == 401:
            print("✅ Correctly rejected (401 Unauthorized)")
            return True
        else:
            print(f"❌ Unexpected response: {response.status_code}")
            return False
            
    except Exception as e:
        print(f"❌ Request failed: {e}")
        return False

def main():
    """Run login and JWT tests"""
    print("🧪 Testing Login and JWT Protection")
    print("=" * 50)
    
    # Get user credentials
    email = input("Enter email to test login: ").strip()
    if not email:
        email = "test@example.com"
    
    password = input("Enter password: ").strip()
    if not password:
        password = "password123"
    
    # Test 1: Login
    print("\n1. Testing Login...")
    tokens = test_login(email, password)
    
    if not tokens:
        print("\n❌ Login failed. Cannot test JWT protection.")
        print("\nPossible issues:")
        print("- User doesn't exist (register first)")
        print("- Wrong password")
        print("- Email not verified")
        print("- Supabase configuration issue")
        return
    
    access_token = tokens['access_token']
    
    # Test 2: Protected endpoint with token
    print("\n2. Testing Protected Endpoint WITH Token...")
    test_protected_endpoint(access_token)
    
    # Test 3: Protected endpoint without token
    print("\n3. Testing Protected Endpoint WITHOUT Token...")
    test_without_token()
    
    # Test 4: Unprotected endpoint
    print("\n4. Testing Unprotected Endpoint...")
    test_unprotected_endpoint()
    
    print(f"\n📋 JWT Protection Summary:")
    print(f"🔐 Protected endpoints require: Authorization: Bearer <access_token>")
    print(f"🌐 Unprotected endpoints: No authentication needed")
    print(f"🎫 Access token expires in: 30 minutes")
    print(f"🔄 Use refresh token to get new access token")

if __name__ == "__main__":
    main()
