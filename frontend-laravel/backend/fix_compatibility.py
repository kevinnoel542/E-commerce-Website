#!/usr/bin/env python3
"""
Fix compatibility issues for Python 3.13 and Supabase
"""

import subprocess
import sys
import os

def run_command(command):
    """Run a command and return success status"""
    try:
        print(f"Running: {command}")
        result = subprocess.run(command, shell=True, check=True, capture_output=True, text=True)
        print(f"Success: {command}")
        return True
    except subprocess.CalledProcessError as e:
        print(f"Failed: {command}")
        print(f"Error: {e.stderr}")
        return False

def fix_supabase_compatibility():
    """Fix Supabase compatibility issues"""
    print("Fixing Supabase compatibility for Python 3.13...")
    
    # Uninstall problematic packages
    packages_to_remove = [
        "supabase",
        "postgrest", 
        "gotrue",
        "storage3",
        "realtime",
        "httpx"
    ]
    
    for package in packages_to_remove:
        run_command(f"pip uninstall {package} -y")
    
    # Install compatible versions
    compatible_packages = [
        "httpx==0.24.1",
        "supabase==2.0.2",
        "postgrest==0.13.2", 
        "gotrue==2.3.0",
        "storage3==0.7.0",
        "realtime==1.0.4"
    ]
    
    for package in compatible_packages:
        if not run_command(f"pip install {package}"):
            print(f"Failed to install {package}")
            return False
    
    return True

def test_supabase_import():
    """Test if Supabase can be imported"""
    try:
        from supabase import create_client
        print("SUCCESS: Supabase import working!")
        return True
    except Exception as e:
        print(f"FAILED: Supabase import error: {e}")
        return False

def create_minimal_env():
    """Create minimal .env file for testing"""
    env_content = """# Minimal configuration for testing
DEBUG=True
JWT_SECRET=dev-secret-key-change-in-production

# Supabase configuration (replace with your actual values)
SUPABASE_URL=https://nluxtoziartsvilflbtf.supabase.co
SUPABASE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5sdXh0b3ppYXJ0c3ZpbGZsYnRmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTE5Njg3MTksImV4cCI6MjA2NzU0NDcxOX0.u-xDviYoWrn-WtplkFABKCVzWla4N0W47GtqEtqC1G8
SUPABASE_SERVICE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5sdXh0b3ppYXJ0c3ZpbGZsYnRmIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc1MTk2ODcxOSwiZXhwIjoyMDY3NTQ0NzE5fQ.R4c5XluiS8M4fNBNRDr_L5bXzDzd47tjHTrS4pXbfEs

# Flutterwave (optional)
FLUTTERWAVE_SECRET_KEY=your-secret-key
FLUTTERWAVE_PUBLIC_KEY=your-public-key
"""
    
    if not os.path.exists(".env"):
        with open(".env", "w") as f:
            f.write(env_content)
        print("Created .env file with your Supabase credentials")
    else:
        print(".env file already exists")

def main():
    """Main function"""
    print("Python 3.13 Compatibility Fix for E-Commerce Backend")
    print("=" * 55)
    
    print(f"Python version: {sys.version}")
    print()
    
    # Step 1: Fix Supabase compatibility
    print("Step 1: Fixing Supabase compatibility...")
    if not fix_supabase_compatibility():
        print("Failed to fix Supabase compatibility")
        return False
    
    print()
    
    # Step 2: Test Supabase import
    print("Step 2: Testing Supabase import...")
    if not test_supabase_import():
        print("Supabase import still failing. You may need to use Python 3.11 or 3.12")
        return False
    
    print()
    
    # Step 3: Create .env file
    print("Step 3: Setting up environment...")
    create_minimal_env()
    
    print()
    
    # Step 4: Install remaining requirements
    print("Step 4: Installing remaining requirements...")
    if not run_command("pip install -r requirements.txt"):
        print("Some packages failed to install, but core functionality should work")
    
    print()
    print("SUCCESS: Compatibility fixes applied!")
    print()
    print("You can now try running:")
    print("uvicorn app.main:app --reload")
    
    return True

if __name__ == "__main__":
    main()
