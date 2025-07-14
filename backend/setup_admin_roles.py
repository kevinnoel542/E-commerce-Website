#!/usr/bin/env python3
"""
Admin Roles Setup Script
This script ensures at least one admin user exists in the system.
"""

import asyncio
import sys
from pathlib import Path

# Add the app directory to the path
sys.path.append(str(Path(__file__).parent / 'app'))

async def setup_admin_roles():
    """Setup admin roles and ensure admin user exists"""
    print("🔧 Setting up Admin Roles...")
    print("=" * 50)
    
    try:
        from db.client import DatabaseClient
        from core.config import validate_config
        
        # Validate configuration
        validate_config()
        print("✅ Configuration validated")
        
        # Initialize database client
        db = DatabaseClient()
        print("✅ Database connection established")
        
        # Admin user details
        admin_email = "starkalyboy@gmail.com"
        admin_data = {
            "email": admin_email,
            "full_name": "Admin User",
            "phone": "0789898989",
            "role": "admin",
            "is_active": True,
            "email_verified": True
        }
        
        print(f"👤 Checking for admin user: {admin_email}")
        
        # Check if admin user already exists
        existing_users = await db.get_records(
            'profiles',
            filters={'email': admin_email}
        )
        
        if existing_users:
            user = existing_users[0]
            current_role = user.get('role', 'user')
            print(f"✅ User found with role: {current_role}")
            
            if current_role != 'admin':
                print("🔄 Updating user role to admin...")
                await db.update_record(
                    'profiles',
                    user['id'],
                    {'role': 'admin'}
                )
                print("✅ User role updated to admin")
            else:
                print("✅ User already has admin role")
                
        else:
            print("⚠️  Admin user not found")
            print("🔄 Creating admin user profile...")
            
            # Generate a UUID for the admin user
            import uuid
            admin_id = str(uuid.uuid4())
            admin_data['id'] = admin_id
            
            try:
                await db.create_record('profiles', admin_data)
                print("✅ Admin user profile created")
            except Exception as e:
                print(f"❌ Failed to create admin profile: {str(e)}")
                print("ℹ️  You may need to register the user through the auth system first")
                return False
        
        # Verify admin user exists and has correct role
        print("🔍 Verifying admin setup...")
        admin_users = await db.get_records(
            'profiles',
            filters={'email': admin_email, 'role': 'admin'}
        )
        
        if admin_users:
            admin = admin_users[0]
            print("✅ Admin verification successful!")
            print(f"   ID: {admin.get('id')}")
            print(f"   Email: {admin.get('email')}")
            print(f"   Name: {admin.get('full_name')}")
            print(f"   Role: {admin.get('role')}")
            print(f"   Active: {admin.get('is_active')}")
            return True
        else:
            print("❌ Admin verification failed")
            return False
            
    except Exception as e:
        print(f"❌ Error setting up admin roles: {str(e)}")
        return False

def main():
    """Main function"""
    try:
        result = asyncio.run(setup_admin_roles())
        
        print("\n" + "=" * 50)
        if result:
            print("🎉 ADMIN ROLES SETUP COMPLETE!")
            print("✅ At least one admin user exists in the system")
            print("\nAdmin Login Details:")
            print("📧 Email: starkalyboy@gmail.com")
            print("🔑 Password: user123")
        else:
            print("❌ ADMIN ROLES SETUP FAILED")
            print("Please check the error messages above")
        print("=" * 50)
        
        return result
    except Exception as e:
        print(f"❌ Failed to run admin setup: {str(e)}")
        return False

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)
