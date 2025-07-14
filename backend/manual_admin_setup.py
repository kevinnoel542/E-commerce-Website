#!/usr/bin/env python3
"""
Manual Admin Setup Script
This script provides manual admin user creation with interactive prompts.
"""

import asyncio
import sys
from pathlib import Path

# Add the app directory to the path
sys.path.append(str(Path(__file__).parent / 'app'))

async def manual_admin_setup():
    """Manual admin setup with user interaction"""
    print("🛠️  Manual Admin Setup")
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
        
        # Default admin details
        default_email = "starkalyboy@gmail.com"
        default_name = "Admin User"
        default_phone = "0789898989"
        
        print("\n📝 Admin User Details:")
        print(f"Email: {default_email}")
        print(f"Name: {default_name}")
        print(f"Phone: {default_phone}")
        print(f"Role: admin")
        
        # Check if admin user exists
        print(f"\n🔍 Checking if admin user exists...")
        existing_users = await db.get_records(
            'profiles',
            filters={'email': default_email}
        )
        
        if existing_users:
            user = existing_users[0]
            current_role = user.get('role', 'user')
            
            print(f"✅ User found!")
            print(f"   Current role: {current_role}")
            print(f"   User ID: {user.get('id')}")
            print(f"   Name: {user.get('full_name')}")
            print(f"   Active: {user.get('is_active')}")
            
            if current_role != 'admin':
                print(f"\n🔄 User role is '{current_role}', updating to 'admin'...")
                
                try:
                    await db.update_record(
                        'profiles',
                        user['id'],
                        {'role': 'admin'}
                    )
                    print("✅ User role updated to admin successfully!")
                    
                    # Verify the update
                    updated_user = await db.get_record('profiles', user['id'])
                    if updated_user and updated_user.get('role') == 'admin':
                        print("✅ Role update verified")
                    else:
                        print("⚠️  Role update verification failed")
                        
                except Exception as e:
                    print(f"❌ Failed to update user role: {str(e)}")
                    return False
            else:
                print("✅ User already has admin role")
                
        else:
            print("⚠️  Admin user not found in profiles table")
            print("\n📋 This could mean:")
            print("   1. User hasn't registered through the auth system yet")
            print("   2. Database tables aren't properly set up")
            print("   3. User was deleted")
            
            print(f"\n🔄 Attempting to create admin profile...")
            
            # Generate UUID for admin user
            import uuid
            admin_id = str(uuid.uuid4())
            
            admin_data = {
                'id': admin_id,
                'email': default_email,
                'full_name': default_name,
                'phone': default_phone,
                'role': 'admin',
                'is_active': True,
                'email_verified': True
            }
            
            try:
                await db.create_record('profiles', admin_data)
                print("✅ Admin profile created successfully!")
                print(f"   User ID: {admin_id}")
                
            except Exception as e:
                print(f"❌ Failed to create admin profile: {str(e)}")
                print("\n💡 Recommended actions:")
                print("   1. Register the user through the FastAPI /auth/register endpoint")
                print("   2. Then run this script again to update the role")
                print("   3. Or manually insert into Supabase profiles table")
                return False
        
        # Final verification
        print(f"\n🔍 Final verification...")
        admin_users = await db.get_records(
            'profiles',
            filters={'email': default_email, 'role': 'admin'}
        )
        
        if admin_users:
            admin = admin_users[0]
            print("✅ Admin user verification successful!")
            print(f"   ID: {admin.get('id')}")
            print(f"   Email: {admin.get('email')}")
            print(f"   Name: {admin.get('full_name')}")
            print(f"   Role: {admin.get('role')}")
            print(f"   Active: {admin.get('is_active')}")
            print(f"   Email Verified: {admin.get('email_verified')}")
            
            return True
        else:
            print("❌ Admin user verification failed")
            return False
            
    except Exception as e:
        print(f"❌ Error in manual admin setup: {str(e)}")
        return False

def show_instructions():
    """Show manual setup instructions"""
    print("\n📖 MANUAL SETUP INSTRUCTIONS")
    print("=" * 50)
    print("If this script fails, you can manually create the admin user:")
    print()
    print("1. 🌐 Register through FastAPI:")
    print("   - Go to: http://localhost:8000/docs")
    print("   - Use POST /auth/register endpoint")
    print("   - Register with: starkalyboy@gmail.com / user123")
    print()
    print("2. 🗄️  Update role in Supabase:")
    print("   - Go to Supabase dashboard")
    print("   - Open Table Editor")
    print("   - Find 'profiles' table")
    print("   - Update role column to 'admin' for the user")
    print()
    print("3. ✅ Verify through this script:")
    print("   - Run: python manual_admin_setup.py")

def main():
    """Main function"""
    try:
        result = asyncio.run(manual_admin_setup())
        
        print("\n" + "=" * 50)
        if result:
            print("🎉 MANUAL ADMIN SETUP COMPLETE!")
            print("✅ Admin user is ready for use")
            print("\nLogin Details:")
            print("📧 Email: starkalyboy@gmail.com")
            print("🔑 Password: user123")
            print("🔗 Login URL: http://127.0.0.1:8080/login")
        else:
            print("❌ MANUAL ADMIN SETUP FAILED")
            show_instructions()
        print("=" * 50)
        
        return result
    except Exception as e:
        print(f"❌ Failed to run manual admin setup: {str(e)}")
        show_instructions()
        return False

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)
