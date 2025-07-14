#!/usr/bin/env python3
"""
Create Admin User Script
This script creates the admin user account if it doesn't exist.
"""

import asyncio
import sys
from pathlib import Path

# Add the app directory to the path
sys.path.append(str(Path(__file__).parent / 'app'))

async def create_admin_user():
    """Create admin user account"""
    print("👤 Creating Admin User Account...")
    print("=" * 50)
    
    try:
        from services.auth_service import AuthService
        from models.auth import UserRegistration
        
        # Admin user details
        admin_email = "starkalyboy@gmail.com"
        admin_password = "user123"
        admin_name = "Admin User"
        admin_phone = "0789898989"
        
        print(f"📧 Email: {admin_email}")
        print(f"👤 Name: {admin_name}")
        print(f"📱 Phone: {admin_phone}")
        print()
        
        # Create registration data
        registration_data = UserRegistration(
            email=admin_email,
            password=admin_password,
            full_name=admin_name,
            phone=admin_phone
        )
        
        # Initialize auth service
        auth_service = AuthService()
        
        print("🔄 Attempting to register admin user...")
        
        try:
            # Try to register the user
            result = await auth_service.register_user(registration_data)
            
            if result.get('success'):
                user_data = result.get('user', {})
                print("✅ Admin user registered successfully!")
                print(f"   User ID: {user_data.get('id')}")
                print(f"   Email: {user_data.get('email')}")
                print(f"   Role: {user_data.get('role', 'user')}")
                
                # Now update the role to admin if it's not already
                if user_data.get('role') != 'admin':
                    print("🔄 Updating user role to admin...")
                    
                    # Import database client
                    from db.client import DatabaseClient
                    db = DatabaseClient()
                    
                    # Update role to admin
                    await db.update_record(
                        'profiles',
                        user_data['id'],
                        {'role': 'admin'}
                    )
                    
                    print("✅ User role updated to admin!")
                
                return True
                
            else:
                error_msg = result.get('error', 'Unknown error')
                print(f"❌ Registration failed: {error_msg}")
                
                # Check if user already exists
                if 'already exists' in error_msg.lower() or 'duplicate' in error_msg.lower():
                    print("🔄 User might already exist. Checking...")
                    
                    from db.client import DatabaseClient
                    db = DatabaseClient()
                    
                    # Check if user exists
                    existing_users = await db.get_records(
                        'profiles',
                        filters={'email': admin_email}
                    )
                    
                    if existing_users:
                        user = existing_users[0]
                        print(f"✅ User already exists!")
                        print(f"   User ID: {user.get('id')}")
                        print(f"   Email: {user.get('email')}")
                        print(f"   Current Role: {user.get('role', 'user')}")
                        
                        # Update role to admin if needed
                        if user.get('role') != 'admin':
                            print("🔄 Updating existing user role to admin...")
                            await db.update_record(
                                'profiles',
                                user['id'],
                                {'role': 'admin'}
                            )
                            print("✅ User role updated to admin!")
                        
                        return True
                    else:
                        print("❌ User doesn't exist and registration failed")
                        return False
                
                return False
                
        except Exception as e:
            print(f"❌ Error during registration: {str(e)}")
            return False
            
    except ImportError as e:
        print(f"❌ Import error: {str(e)}")
        print("   Make sure you're running this from the backend directory")
        return False
    except Exception as e:
        print(f"❌ Unexpected error: {str(e)}")
        return False

def main():
    """Main function"""
    try:
        result = asyncio.run(create_admin_user())
        
        if result:
            print("\n" + "=" * 50)
            print("🎉 ADMIN USER SETUP COMPLETE!")
            print("=" * 50)
            print("You can now login as admin with:")
            print("📧 Email: starkalyboy@gmail.com")
            print("🔑 Password: user123")
            print("🔗 Login URL: http://127.0.0.1:8080/login")
        else:
            print("\n" + "=" * 50)
            print("❌ ADMIN USER SETUP FAILED")
            print("=" * 50)
            print("Please check the error messages above and try again.")
        
        return result
    except Exception as e:
        print(f"❌ Failed to run admin user creation: {str(e)}")
        return False

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)
