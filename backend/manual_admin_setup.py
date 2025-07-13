#!/usr/bin/env python3
"""
Manual admin setup script
This script manually adds the role column and sets up an admin user
"""

import os
import sys
from pathlib import Path

# Add the app directory to Python path
sys.path.append(str(Path(__file__).parent / "app"))

from app.db.client import admin_supabase
from app.core.logging import logger

def add_role_column():
    """Add role column to profiles table"""
    try:
        logger.info("Adding role column to profiles table...")
        
        # First, let's check if the column already exists
        result = admin_supabase.table("profiles").select("*").limit(1).execute()
        if result.data and len(result.data) > 0:
            first_profile = result.data[0]
            if 'role' in first_profile:
                logger.info("Role column already exists")
                return True
        
        # Add role column using raw SQL through a stored procedure
        # We'll use the rpc method to execute raw SQL
        logger.info("Role column doesn't exist, need to add it manually via Supabase dashboard")
        return False
        
    except Exception as e:
        logger.error(f"Error checking/adding role column: {str(e)}")
        return False

def setup_admin_user(email: str, role: str = "super_admin"):
    """Set up an admin user by updating their role"""
    try:
        logger.info(f"Setting up admin user: {email}")
        
        # First check if user exists
        result = admin_supabase.table("profiles").select("*").eq("email", email).execute()
        
        if not result.data:
            logger.error(f"User {email} not found. Please register first.")
            return False
        
        user_data = result.data[0]
        logger.info(f"Found user: {user_data}")
        
        # Check if role column exists
        if 'role' not in user_data:
            logger.error("Role column doesn't exist. Please add it manually first.")
            return False
        
        # Update user role
        update_result = admin_supabase.table("profiles").update({
            "role": role
        }).eq("email", email).execute()
        
        if update_result.data:
            logger.info(f"Successfully set {email} as {role}")
            return True
        else:
            logger.error(f"Failed to update user role for {email}")
            return False
            
    except Exception as e:
        logger.error(f"Error setting up admin user: {str(e)}")
        return False

def main():
    """Main setup function"""
    logger.info("Starting manual admin setup...")
    
    # Check if role column exists
    if not add_role_column():
        print("\n" + "="*60)
        print("⚠️  MANUAL SETUP REQUIRED")
        print("="*60)
        print("\nThe role column needs to be added manually.")
        print("Please follow these steps:")
        print("\n1. Go to your Supabase Dashboard")
        print("2. Navigate to SQL Editor")
        print("3. Run this SQL command:")
        print("\n   ALTER TABLE profiles ADD COLUMN role TEXT DEFAULT 'user';")
        print("\n4. Then run this script again")
        print("="*60)
        return False
    
    # Setup admin user
    admin_email = input("Enter admin email: ").strip()
    if admin_email:
        if setup_admin_user(admin_email):
            print("\n" + "="*50)
            print("🎉 Admin setup completed!")
            print("="*50)
            print(f"\n✅ {admin_email} is now a super_admin")
            print("\nNext steps:")
            print("1. Restart your backend server")
            print("2. Login with your admin account")
            print("3. You should now be redirected to the admin dashboard")
            print("="*50)
            return True
        else:
            print("\n❌ Admin setup failed. Check the logs for details.")
            return False
    else:
        print("No email provided. Exiting.")
        return False

if __name__ == "__main__":
    main()
