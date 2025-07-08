#!/usr/bin/env python3
"""
Development startup script for E-Commerce Backend
This script handles compatibility issues and provides fallbacks
"""

import sys
import os
import subprocess
from pathlib import Path

def check_python_version():
    """Check Python version and warn about compatibility"""
    version = sys.version_info
    print(f"🐍 Python version: {version.major}.{version.minor}.{version.micro}")
    
    if version.major == 3 and version.minor >= 13:
        print("⚠️  Warning: Python 3.13+ may have compatibility issues with some packages")
        print("   Consider using Python 3.11 or 3.12 for better compatibility")
        print("   The app will try to use mock clients as fallbacks")
    
    return True

def check_dependencies():
    """Check if required dependencies are installed"""
    required_packages = [
        "fastapi",
        "uvicorn",
        "PyJWT",
        "passlib",
        "python-multipart",
        "requests",
        "python-dotenv",
        "pydantic",
        "email-validator",
        "python-dateutil",
        "cryptography"
    ]
    
    missing_packages = []
    
    for package in required_packages:
        try:
            __import__(package.replace("-", "_"))
            print(f"✅ {package}")
        except ImportError:
            missing_packages.append(package)
            print(f"❌ {package}")
    
    # Check supabase separately as it's optional
    try:
        import supabase
        print("✅ supabase (optional)")
    except ImportError:
        print("⚠️  supabase (will use mock client)")
    
    if missing_packages:
        print(f"\n❌ Missing packages: {', '.join(missing_packages)}")
        print("Run: pip install " + " ".join(missing_packages))
        return False
    
    print("\n✅ All required dependencies are available!")
    return True

def setup_environment():
    """Setup environment variables"""
    env_file = Path(".env")
    if not env_file.exists():
        print("⚠️  .env file not found")
        print("Creating basic .env file...")
        
        basic_env = """# Basic configuration for development
DEBUG=True
JWT_SECRET=dev-secret-key-change-in-production
ALGORITHM=HS256
ACCESS_TOKEN_EXPIRE_MINUTES=30
REFRESH_TOKEN_EXPIRE_DAYS=7

# Supabase (optional - will use mock if not provided)
# SUPABASE_URL=https://your-project.supabase.co
# SUPABASE_KEY=your-anon-key
# SUPABASE_SERVICE_KEY=your-service-key

# Flutterwave (optional - will use mock if not provided)
# FLUTTERWAVE_SECRET_KEY=your-secret-key
# FLUTTERWAVE_PUBLIC_KEY=your-public-key

# Application settings
DEFAULT_CURRENCY=TZS
CORS_ORIGINS=http://localhost:3000,http://localhost:3001
"""
        
        with open(".env", "w") as f:
            f.write(basic_env)
        
        print("✅ Created basic .env file")
    else:
        print("✅ .env file found")

def start_application():
    """Start the FastAPI application"""
    print("\n🚀 Starting E-Commerce Backend...")
    print("📚 API Documentation will be available at: http://localhost:8000/docs")
    print("🔧 Using mock clients for missing services")
    print("\nPress Ctrl+C to stop the server\n")
    
    try:
        # Start uvicorn
        subprocess.run([
            sys.executable, "-m", "uvicorn", 
            "app.main:app", 
            "--reload", 
            "--host", "0.0.0.0", 
            "--port", "8000"
        ], check=True)
    except KeyboardInterrupt:
        print("\n👋 Server stopped")
    except subprocess.CalledProcessError as e:
        print(f"\n❌ Failed to start server: {e}")
        print("\nTry running manually:")
        print("uvicorn app.main:app --reload")

def main():
    """Main startup function"""
    print("🔍 E-Commerce Backend Development Startup")
    print("=" * 50)
    
    # Check Python version
    check_python_version()
    print()
    
    # Check dependencies
    print("📦 Checking dependencies...")
    if not check_dependencies():
        print("\n❌ Please install missing dependencies first")
        return
    
    print()
    
    # Setup environment
    print("⚙️  Setting up environment...")
    setup_environment()
    print()
    
    # Start application
    start_application()

if __name__ == "__main__":
    main()
