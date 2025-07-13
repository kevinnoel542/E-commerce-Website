#!/usr/bin/env python3
"""
Install script for E-Commerce Backend dependencies
This script installs packages one by one to avoid conflicts
"""

import subprocess
import sys

def run_command(command):
    """Run a command and return success status"""
    try:
        print(f"Running: {command}")
        result = subprocess.run(command, shell=True, check=True, capture_output=True, text=True)
        print(f"✅ Success: {command}")
        return True
    except subprocess.CalledProcessError as e:
        print(f"❌ Failed: {command}")
        print(f"Error: {e.stderr}")
        return False

def main():
    """Install all required packages"""
    print("🔧 Installing E-Commerce Backend Dependencies\n")
    
    # List of packages to install
    packages = [
        "pip --upgrade",
        "fastapi",
        "uvicorn[standard]",
        "PyJWT",
        "passlib[bcrypt]",
        "python-multipart",
        "requests",
        "python-dotenv",
        "pydantic",
        "email-validator",
        "python-dateutil",
        "cryptography",
        "supabase"
    ]
    
    failed_packages = []
    
    for package in packages:
        if package == "pip --upgrade":
            success = run_command("python -m pip install --upgrade pip")
        else:
            success = run_command(f"pip install {package}")
        
        if not success:
            failed_packages.append(package)
        print()  # Add spacing
    
    # Summary
    print("📋 Installation Summary:")
    print(f"✅ Successfully installed: {len(packages) - len(failed_packages)} packages")
    
    if failed_packages:
        print(f"❌ Failed to install: {len(failed_packages)} packages")
        print("Failed packages:")
        for package in failed_packages:
            print(f"  - {package}")
        print("\nTry installing failed packages manually:")
        for package in failed_packages:
            print(f"pip install {package}")
    else:
        print("🎉 All packages installed successfully!")
        print("\nYou can now run:")
        print("python test_imports.py")
        print("uvicorn app.main:app --reload")

if __name__ == "__main__":
    main()
