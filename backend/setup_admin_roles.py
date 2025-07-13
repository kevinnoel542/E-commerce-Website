#!/usr/bin/env python3
"""
Setup admin roles in the database
Run this script to add role functionality to your e-commerce backend
"""

import os
import sys
from pathlib import Path

# Add the app directory to Python path
sys.path.append(str(Path(__file__).parent / "app"))

from app.db.client import admin_supabase
from app.core.logging import logger

def run_sql_file(file_path: str):
    """Run SQL commands from a file"""
    try:
        with open(file_path, 'r') as file:
            sql_content = file.read()
        
        # Split by semicolon and execute each statement
        statements = [stmt.strip() for stmt in sql_content.split(';') if stmt.strip()]
        
        for statement in statements:
            if statement:
                try:
                    logger.info(f"Executing SQL: {statement[:100]}...")
                    result = admin_supabase.rpc('exec_sql', {'sql': statement}).execute()
                    logger.info("SQL executed successfully")
                except Exception as e:
                    # Try direct execution for DDL statements
                    try:
                        result = admin_supabase.postgrest.session.post(
                            f"{admin_supabase.url}/rest/v1/rpc/exec_sql",
                            json={'sql': statement},
                            headers=admin_supabase.postgrest.session.headers
                        )
                        if result.status_code == 200:
                            logger.info("SQL executed successfully (direct)")
                        else:
                            logger.warning(f"SQL execution warning: {result.text}")
                    except Exception as e2:
                        logger.error(f"Failed to execute SQL: {statement[:100]}... Error: {str(e2)}")
                        
    except Exception as e:
        logger.error(f"Error running SQL file {file_path}: {str(e)}")
        return False
    
    return True

def setup_admin_user(email: str, role: str = "super_admin"):
    """Set up an admin user"""
    try:
        logger.info(f"Setting up admin user: {email}")
        
        # Update user role
        result = admin_supabase.table("profiles").update({
            "role": role
        }).eq("email", email).execute()
        
        if result.data:
            logger.info(f"Successfully set {email} as {role}")
            return True
        else:
            logger.warning(f"User {email} not found. Please register first, then run this script.")
            return False
            
    except Exception as e:
        logger.error(f"Error setting up admin user: {str(e)}")
        return False

def main():
    """Main setup function"""
    logger.info("Starting admin roles setup...")
    
    # Run the SQL migration
    sql_file = Path(__file__).parent / "add_user_roles.sql"
    if sql_file.exists():
        logger.info("Running database migration...")
        if run_sql_file(str(sql_file)):
            logger.info("Database migration completed successfully")
        else:
            logger.error("Database migration failed")
            return False
    else:
        logger.error(f"SQL file not found: {sql_file}")
        return False
    
    # Setup admin user if email is provided
    admin_email = input("Enter admin email (or press Enter to skip): ").strip()
    if admin_email:
        if setup_admin_user(admin_email):
            logger.info(f"Admin setup completed for {admin_email}")
        else:
            logger.warning("Admin setup failed. You can manually update the role in the database.")
    
    logger.info("Setup completed!")
    print("\n" + "="*50)
    print("🎉 Admin roles setup completed!")
    print("="*50)
    print("\nNext steps:")
    print("1. Restart your backend server")
    print("2. Login with your admin account")
    print("3. You should now be redirected to the admin dashboard")
    print("\nTo make more users admin, update their role in the database:")
    print("UPDATE profiles SET role = 'admin' WHERE email = 'user@example.com';")
    print("="*50)
    
    return True

if __name__ == "__main__":
    main()
