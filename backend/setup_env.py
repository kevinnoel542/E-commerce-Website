#!/usr/bin/env python3
"""
Environment Setup Script for E-Commerce Backend
This script helps you set up the required environment variables.
"""

import os
import secrets
import string

def generate_jwt_secret(length=64):
    """Generate a secure JWT secret key"""
    alphabet = string.ascii_letters + string.digits + "!@#$%^&*"
    return ''.join(secrets.choice(alphabet) for _ in range(length))

def create_env_file():
    """Create a .env file with default values"""
    
    print("🔧 Setting up environment variables for E-Commerce Backend")
    print("=" * 60)
    
    # Check if .env already exists
    if os.path.exists('.env'):
        response = input("⚠️  .env file already exists. Overwrite? (y/N): ")
        if response.lower() != 'y':
            print("❌ Setup cancelled.")
            return
    
    # Generate JWT secret
    jwt_secret = generate_jwt_secret()
    
    # Create .env content
    env_content = f"""# Supabase Configuration
# Get these from your Supabase project settings
SUPABASE_URL=https://your-project-id.supabase.co
SUPABASE_KEY=your-anon-public-key
SUPABASE_SERVICE_KEY=your-service-role-key

# Flutterwave Configuration
# Get these from your Flutterwave dashboard
FLUTTERWAVE_SECRET_KEY=FLWSECK_TEST-your-secret-key
FLUTTERWAVE_PUBLIC_KEY=FLWPUBK_TEST-your-public-key

# JWT Configuration
# Auto-generated secure secret key
JWT_SECRET={jwt_secret}

# Application Configuration
DEBUG=True
DEFAULT_CURRENCY=USD

# Payment URLs
PAYMENT_SUCCESS_URL=http://localhost:3000/payment-success
PAYMENT_CANCEL_URL=http://localhost:3000/payment-cancel

# Email Configuration (Optional - for notifications)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASSWORD=your-app-password
"""
    
    # Write .env file
    with open('.env', 'w') as f:
        f.write(env_content)
    
    print("✅ .env file created successfully!")
    print("\n📝 Next steps:")
    print("1. Edit the .env file and add your Supabase credentials")
    print("2. Add your Flutterwave API keys")
    print("3. Configure other settings as needed")
    print("\n🔗 Where to get credentials:")
    print("• Supabase: https://app.supabase.com/project/your-project/settings/api")
    print("• Flutterwave: https://dashboard.flutterwave.com/settings/apis")
    print("\n🚀 Once configured, start the server with:")
    print("   python run.py")
    print("   or")
    print("   uvicorn app.main:app --reload")

if __name__ == "__main__":
    create_env_file()
