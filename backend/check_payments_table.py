#!/usr/bin/env python3
"""
Check the current payments table structure
"""
import asyncio
import sys
import os

# Add the app directory to the path
sys.path.append(os.path.join(os.path.dirname(__file__), 'app'))

from app.db.client import DatabaseClient

async def check_table():
    """Check the payments table structure"""
    db = DatabaseClient()
    
    try:
        # Try to get table info
        result = await db.get_records("payments", {})
        print(f"✅ Payments table exists and is accessible")
        print(f"📊 Current records: {len(result)}")
        
        if result:
            print("📋 Sample record structure:")
            for key in result[0].keys():
                print(f"  - {key}")
    
    except Exception as e:
        print(f"❌ Error accessing payments table: {e}")
        print("🔧 This suggests the table needs to be created or updated")

if __name__ == "__main__":
    asyncio.run(check_table())
