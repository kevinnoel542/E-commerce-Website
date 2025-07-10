#!/usr/bin/env python3
"""
Test script to check categories table and create sample data
"""

import asyncio
import sys
import os

# Add the backend directory to Python path
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from app.db.client import db
from datetime import datetime
import uuid

async def test_categories():
    """Test categories table and create sample data"""
    
    print("🧪 Testing Categories Table")
    print("=" * 40)
    
    try:
        # Test 1: Check if categories table exists by trying to fetch records
        print("1. Checking if categories table exists...")
        categories = await db.get_records("categories", {})
        print(f"✅ Categories table exists. Found {len(categories)} categories.")
        
        # Test 2: If no categories exist, create some sample ones
        if len(categories) == 0:
            print("\n2. Creating sample categories...")
            
            sample_categories = [
                {
                    "id": str(uuid.uuid4()),
                    "name": "Electronics",
                    "description": "Electronic devices and gadgets",
                    "is_active": True,
                    "created_at": datetime.utcnow().isoformat()
                },
                {
                    "id": str(uuid.uuid4()),
                    "name": "Clothing",
                    "description": "Fashion and apparel",
                    "is_active": True,
                    "created_at": datetime.utcnow().isoformat()
                },
                {
                    "id": str(uuid.uuid4()),
                    "name": "Books",
                    "description": "Books and literature",
                    "is_active": True,
                    "created_at": datetime.utcnow().isoformat()
                },
                {
                    "id": str(uuid.uuid4()),
                    "name": "Home & Garden",
                    "description": "Home improvement and gardening supplies",
                    "is_active": True,
                    "created_at": datetime.utcnow().isoformat()
                }
            ]
            
            for category in sample_categories:
                try:
                    await db.create_record("categories", category)
                    print(f"✅ Created category: {category['name']}")
                except Exception as e:
                    print(f"❌ Failed to create category {category['name']}: {e}")
        
        # Test 3: Fetch all categories again
        print("\n3. Fetching all categories...")
        all_categories = await db.get_records("categories", {})
        print(f"📊 Total categories in database: {len(all_categories)}")
        
        for cat in all_categories:
            print(f"   - {cat['name']}: {cat['description']}")
        
        # Test 4: Test the API endpoint
        print("\n4. Testing categories API endpoint...")
        print("You can now test: GET http://localhost:8000/api/v1/products/categories/")
        
        return True
        
    except Exception as e:
        error_msg = str(e)
        print(f"❌ Error: {error_msg}")
        
        if "relation" in error_msg.lower() and "does not exist" in error_msg.lower():
            print("\n🔧 Solution: The categories table doesn't exist.")
            print("Please run the database setup script:")
            print("1. Go to your Supabase SQL Editor")
            print("2. Run the contents of 'database_setup.sql' or 'add_role_migration.sql'")
            print("3. Then run this test script again")
        
        return False

async def create_admin_and_test():
    """Create admin user and test category creation"""
    print("\n🔐 Testing Admin Category Creation")
    print("=" * 40)
    
    try:
        # This would require admin authentication
        print("To test admin category creation:")
        print("1. Register an admin user")
        print("2. Login to get admin token")
        print("3. Use POST /api/v1/products/categories/ with admin token")
        
    except Exception as e:
        print(f"❌ Error: {e}")

def main():
    """Main function"""
    print("🚀 Categories Test Script")
    print("=" * 50)
    
    try:
        # Run the async test
        result = asyncio.run(test_categories())
        
        if result:
            print("\n✅ Categories test completed successfully!")
            print("\nNext steps:")
            print("1. Start your FastAPI server: uvicorn app.main:app --reload")
            print("2. Test the endpoint: GET http://localhost:8000/api/v1/products/categories/")
            print("3. Check API docs: http://localhost:8000/docs")
        else:
            print("\n❌ Categories test failed. Please check the database setup.")
            
    except KeyboardInterrupt:
        print("\n⏹️ Test interrupted by user")
    except Exception as e:
        print(f"\n💥 Unexpected error: {e}")

if __name__ == "__main__":
    main()
