#!/usr/bin/env python3
"""
Diagnostic script to check for common errors
"""

import sys
import os
import importlib.util

# Add the backend directory to Python path
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

def check_imports():
    """Check if all required modules can be imported"""
    print("🔍 Checking Imports")
    print("=" * 30)
    
    modules_to_check = [
        ("fastapi", "FastAPI framework"),
        ("pydantic", "Data validation"),
        ("supabase", "Database client"),
        ("stripe", "Payment processing"),
        ("PIL", "Image processing (Pillow)"),
        ("aiofiles", "Async file operations"),
    ]
    
    all_good = True
    
    for module_name, description in modules_to_check:
        try:
            __import__(module_name)
            print(f"✅ {module_name}: {description}")
        except ImportError as e:
            print(f"❌ {module_name}: {description} - {e}")
            all_good = False
    
    return all_good

def check_app_imports():
    """Check if app modules can be imported"""
    print("\n🔍 Checking App Modules")
    print("=" * 30)
    
    app_modules = [
        ("app.core.config", "Configuration"),
        ("app.db.client", "Database client"),
        ("app.models.product", "Product models"),
        ("app.services.product_service", "Product service"),
        ("app.services.image_service", "Image service"),
        ("app.api.v1.routes.products", "Product routes"),
    ]
    
    all_good = True
    
    for module_name, description in app_modules:
        try:
            __import__(module_name)
            print(f"✅ {module_name}: {description}")
        except ImportError as e:
            print(f"❌ {module_name}: {description} - {e}")
            all_good = False
        except Exception as e:
            print(f"⚠️ {module_name}: {description} - {e}")
            all_good = False
    
    return all_good

def check_directories():
    """Check if required directories exist"""
    print("\n🔍 Checking Directories")
    print("=" * 30)
    
    required_dirs = [
        "app",
        "app/api",
        "app/api/v1",
        "app/api/v1/routes",
        "app/core",
        "app/db",
        "app/models",
        "app/services",
        "uploads",
        "uploads/products",
    ]
    
    all_good = True
    
    for dir_path in required_dirs:
        if os.path.exists(dir_path):
            print(f"✅ {dir_path}/")
        else:
            print(f"❌ {dir_path}/ - Missing")
            all_good = False
            
            # Create missing directories
            try:
                os.makedirs(dir_path, exist_ok=True)
                print(f"   📁 Created {dir_path}/")
            except Exception as e:
                print(f"   ❌ Failed to create {dir_path}/: {e}")
    
    return all_good

def check_env_file():
    """Check if .env file exists and has required variables"""
    print("\n🔍 Checking Environment")
    print("=" * 30)
    
    if not os.path.exists(".env"):
        print("❌ .env file not found")
        return False
    
    print("✅ .env file exists")
    
    required_vars = [
        "SUPABASE_URL",
        "SUPABASE_ANON_KEY",
        "SUPABASE_SERVICE_ROLE_KEY",
        "JWT_SECRET",
        "STRIPE_SECRET_KEY",
        "STRIPE_PUBLISHABLE_KEY",
        "ADMIN_SECRET"
    ]
    
    try:
        with open(".env", "r") as f:
            env_content = f.read()
        
        missing_vars = []
        for var in required_vars:
            if var not in env_content or f"{var}=" not in env_content:
                missing_vars.append(var)
        
        if missing_vars:
            print(f"⚠️ Missing environment variables: {', '.join(missing_vars)}")
            return False
        else:
            print("✅ All required environment variables present")
            return True
            
    except Exception as e:
        print(f"❌ Error reading .env file: {e}")
        return False

def test_basic_endpoints():
    """Test if the server can start and basic endpoints work"""
    print("\n🔍 Testing Basic Functionality")
    print("=" * 30)
    
    try:
        # Try to import the main app
        from app.main import app
        print("✅ FastAPI app can be imported")
        
        # Check if routes are registered
        routes = [route.path for route in app.routes]
        expected_routes = [
            "/health",
            "/api/v1/products/categories/",
            "/api/v1/auth/login",
            "/api/v1/auth/register"
        ]
        
        for route in expected_routes:
            if any(route in r for r in routes):
                print(f"✅ Route registered: {route}")
            else:
                print(f"❌ Route missing: {route}")
        
        return True
        
    except Exception as e:
        print(f"❌ Error importing app: {e}")
        return False

def main():
    """Run all diagnostic checks"""
    print("🩺 E-commerce Backend Diagnostics")
    print("=" * 50)
    
    checks = [
        ("External Dependencies", check_imports),
        ("App Modules", check_app_imports),
        ("Directory Structure", check_directories),
        ("Environment Configuration", check_env_file),
        ("Basic Functionality", test_basic_endpoints),
    ]
    
    results = {}
    
    for check_name, check_func in checks:
        try:
            results[check_name] = check_func()
        except Exception as e:
            print(f"\n❌ Error during {check_name}: {e}")
            results[check_name] = False
    
    print(f"\n📊 Diagnostic Summary")
    print("=" * 30)
    
    all_passed = True
    for check_name, passed in results.items():
        status = "✅ PASS" if passed else "❌ FAIL"
        print(f"{check_name}: {status}")
        if not passed:
            all_passed = False
    
    if all_passed:
        print(f"\n🎉 All checks passed! Your backend should be working.")
        print("Start the server with: uvicorn app.main:app --reload")
    else:
        print(f"\n⚠️ Some checks failed. Please fix the issues above.")
        print("\nCommon solutions:")
        print("1. Install missing dependencies: pip install -r requirements.txt")
        print("2. Check your .env file configuration")
        print("3. Run database setup in Supabase")
        print("4. Ensure all directories exist")

if __name__ == "__main__":
    main()
