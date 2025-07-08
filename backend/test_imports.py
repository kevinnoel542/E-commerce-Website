#!/usr/bin/env python3
"""
Test script to verify all required imports work correctly
"""

def test_imports():
    """Test all critical imports"""
    try:
        print("Testing FastAPI imports...")
        from fastapi import FastAPI
        print("✅ FastAPI imported successfully")
        
        print("Testing JWT imports...")
        import jwt
        print("✅ JWT imported successfully")
        
        print("Testing Passlib imports...")
        from passlib.context import CryptContext
        print("✅ Passlib imported successfully")
        
        print("Testing Supabase imports...")
        from supabase import create_client
        print("✅ Supabase imported successfully")
        
        print("Testing Pydantic imports...")
        from pydantic import BaseModel
        print("✅ Pydantic imported successfully")
        
        print("Testing Requests imports...")
        import requests
        print("✅ Requests imported successfully")
        
        print("Testing Python-dotenv imports...")
        from dotenv import load_dotenv
        print("✅ Python-dotenv imported successfully")
        
        print("\n🎉 All imports successful! Your environment is ready.")
        return True
        
    except ImportError as e:
        print(f"❌ Import error: {e}")
        print("\nPlease install missing packages:")
        print("pip install -r requirements.txt")
        return False
    except Exception as e:
        print(f"❌ Unexpected error: {e}")
        return False

def test_jwt_functionality():
    """Test JWT encoding/decoding"""
    try:
        print("\nTesting JWT functionality...")
        import jwt
        
        # Test data
        payload = {"user_id": "123", "email": "test@example.com"}
        secret = "test-secret"
        
        # Encode
        token = jwt.encode(payload, secret, algorithm="HS256")
        print("✅ JWT encoding successful")
        
        # Decode
        decoded = jwt.decode(token, secret, algorithms=["HS256"])
        print("✅ JWT decoding successful")
        
        if decoded == payload:
            print("✅ JWT payload matches")
            return True
        else:
            print("❌ JWT payload mismatch")
            return False
            
    except Exception as e:
        print(f"❌ JWT test failed: {e}")
        return False

def test_password_hashing():
    """Test password hashing"""
    try:
        print("\nTesting password hashing...")
        from passlib.context import CryptContext
        
        pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")
        
        # Hash password
        password = "test123"
        hashed = pwd_context.hash(password)
        print("✅ Password hashing successful")
        
        # Verify password
        is_valid = pwd_context.verify(password, hashed)
        if is_valid:
            print("✅ Password verification successful")
            return True
        else:
            print("❌ Password verification failed")
            return False
            
    except Exception as e:
        print(f"❌ Password hashing test failed: {e}")
        return False

if __name__ == "__main__":
    print("🔍 Testing E-Commerce Backend Dependencies\n")
    
    imports_ok = test_imports()
    jwt_ok = test_jwt_functionality()
    password_ok = test_password_hashing()
    
    if imports_ok and jwt_ok and password_ok:
        print("\n🎉 All tests passed! You can now run the application:")
        print("uvicorn app.main:app --reload")
    else:
        print("\n❌ Some tests failed. Please fix the issues above.")
        print("\nTry running:")
        print("pip install --upgrade pip")
        print("pip install -r requirements.txt")
