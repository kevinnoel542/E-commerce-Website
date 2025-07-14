#!/usr/bin/env python3
"""
Database Verification Script
This script verifies that the database setup was completed successfully
by testing the connection through the FastAPI backend.
"""

import sys
import asyncio
from pathlib import Path

# Add the app directory to the path
sys.path.append(str(Path(__file__).parent / 'app'))

async def verify_database():
    """Verify database setup through FastAPI backend"""
    print("🔍 Verifying Database Setup...")
    print("=" * 50)
    
    try:
        # Import after adding to path
        from db.client import DatabaseClient
        from core.config import validate_config
        
        # Validate configuration first
        print("1. ✅ Validating configuration...")
        validate_config()
        print("   ✅ Configuration is valid")
        
        # Test database connection
        print("2. 🔌 Testing database connection...")
        db = DatabaseClient()
        
        # Test basic connection
        result = await db.get_records('profiles', limit=1)
        print("   ✅ Database connection successful")
        
        # Check if tables exist by trying to query them
        tables_to_check = [
            'profiles',
            'categories', 
            'products',
            'orders',
            'order_items',
            'payments'
        ]
        
        print("3. 📋 Checking required tables...")
        existing_tables = []
        missing_tables = []
        
        for table in tables_to_check:
            try:
                await db.get_records(table, limit=1)
                existing_tables.append(table)
                print(f"   ✅ {table}")
            except Exception as e:
                missing_tables.append(table)
                print(f"   ❌ {table} - {str(e)}")
        
        # Check for admin user
        print("4. 👤 Checking admin user...")
        try:
            admin_users = await db.get_records(
                'profiles', 
                filters={'email': 'starkalyboy@gmail.com'}
            )
            if admin_users:
                admin_user = admin_users[0]
                role = admin_user.get('role', 'user')
                print(f"   ✅ Admin user found with role: {role}")
                if role != 'admin':
                    print("   ⚠️  User exists but role is not 'admin'")
            else:
                print("   ⚠️  Admin user not found")
        except Exception as e:
            print(f"   ❌ Error checking admin user: {str(e)}")
        
        # Summary
        print("\n" + "=" * 50)
        print("📊 VERIFICATION SUMMARY")
        print("=" * 50)
        print(f"✅ Tables found: {len(existing_tables)}/{len(tables_to_check)}")
        print(f"❌ Tables missing: {len(missing_tables)}")
        
        if existing_tables:
            print("\n✅ Existing tables:")
            for table in existing_tables:
                print(f"   - {table}")
        
        if missing_tables:
            print("\n❌ Missing tables:")
            for table in missing_tables:
                print(f"   - {table}")
            print("\n🔧 To fix missing tables:")
            print("   1. Go to your Supabase project dashboard")
            print("   2. Open SQL Editor")
            print("   3. Run the SQL files in order:")
            print("      - database_setup.sql")
            print("      - add_role_migration.sql")
            print("      - comprehensive_rls_fix.sql")
        
        # Overall status
        if len(existing_tables) == len(tables_to_check):
            print("\n🎉 DATABASE SETUP COMPLETE!")
            print("   All required tables are present and accessible.")
            return True
        else:
            print("\n⚠️  DATABASE SETUP INCOMPLETE")
            print("   Some tables are missing. Please run the SQL setup files.")
            return False
            
    except ImportError as e:
        print(f"❌ Import error: {str(e)}")
        print("   Make sure you're running this from the backend directory")
        return False
    except Exception as e:
        print(f"❌ Verification failed: {str(e)}")
        print("   This might indicate that the database setup is incomplete")
        return False

def main():
    """Main function"""
    try:
        result = asyncio.run(verify_database())
        return result
    except Exception as e:
        print(f"❌ Failed to run verification: {str(e)}")
        return False

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)
