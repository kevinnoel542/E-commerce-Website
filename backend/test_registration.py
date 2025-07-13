#!/usr/bin/env python3
"""
Test script to debug registration issues
"""

import requests
import json
import time

def test_registration(email, password="testpass123", full_name="Test User"):
    """Test registration endpoint"""
    url = "http://localhost:8000/api/v1/auth/register"
    
    data = {
        "email": email,
        "password": password,
        "full_name": full_name,
        "phone": "+1234567890"
    }
    
    print(f"Testing registration for: {email}")
    print(f"Request data: {json.dumps(data, indent=2)}")
    
    try:
        response = requests.post(url, json=data, timeout=30)
        
        print(f"Status Code: {response.status_code}")
        print(f"Response Headers: {dict(response.headers)}")
        
        try:
            response_json = response.json()
            print(f"Response Body: {json.dumps(response_json, indent=2)}")
        except:
            print(f"Response Body (raw): {response.text}")
        
        return response.status_code == 200
        
    except requests.exceptions.Timeout:
        print("Request timed out")
        return False
    except requests.exceptions.RequestException as e:
        print(f"Request failed: {e}")
        return False

def test_supabase_direct():
    """Test Supabase connection directly"""
    try:
        from app.db.client import supabase
        print("Testing direct Supabase connection...")
        
        # Test a simple operation
        response = supabase.table("profiles").select("*").limit(1).execute()
        print(f"Direct Supabase test successful: {len(response.data)} records")
        return True
    except Exception as e:
        print(f"Direct Supabase test failed: {e}")
        return False

def main():
    """Run registration tests"""
    print("🧪 Testing Registration Endpoint")
    print("=" * 40)
    
    # Test 1: Direct Supabase connection
    print("\n1. Testing Supabase connection...")
    supabase_ok = test_supabase_direct()
    
    if not supabase_ok:
        print("❌ Supabase connection failed. Check your configuration.")
        return
    
    # Test 2: Registration with fake email
    print("\n2. Testing registration with fake email...")
    fake_email = f"test{int(time.time())}@fake-domain-12345.com"
    fake_result = test_registration(fake_email)
    
    # Test 3: Registration with real email (if you want to test)
    print("\n3. Testing registration with real email...")
    real_email = input("Enter a real email to test (or press Enter to skip): ").strip()
    
    if real_email:
        real_result = test_registration(real_email)
        
        print(f"\n📊 Results:")
        print(f"Fake email ({fake_email}): {'✅ Success' if fake_result else '❌ Failed'}")
        print(f"Real email ({real_email}): {'✅ Success' if real_result else '❌ Failed'}")
        
        if fake_result and not real_result:
            print("\n🔍 Analysis: Fake emails work but real emails don't.")
            print("This suggests an email verification configuration issue.")
            print("\nSolutions:")
            print("1. Check Supabase Auth settings")
            print("2. Disable email confirmation for development")
            print("3. Configure SMTP settings in Supabase")
    else:
        print(f"\n📊 Results:")
        print(f"Fake email ({fake_email}): {'✅ Success' if fake_result else '❌ Failed'}")

if __name__ == "__main__":
    main()
