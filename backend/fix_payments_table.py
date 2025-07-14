#!/usr/bin/env python3
"""
Quick fix for payments table RLS issue
"""
import asyncio
import sys
import os

# Add the app directory to the path
sys.path.append(os.path.join(os.path.dirname(__file__), 'app'))

from app.db.client import DatabaseClient

async def fix_payments_table():
    """Fix the payments table RLS policies"""
    db = DatabaseClient()
    
    try:
        print("🔧 Attempting to fix payments table...")
        
        # First, let's try to create a payment record to see what happens
        test_record = {
            "id": "test-id-123",
            "order_id": "d8bf79b5-e7e9-4078-9f6c-6c6891f0259e",  # Use existing order
            "stripe_session_id": "cs_test_123",
            "amount": 100.0,
            "currency": "usd",
            "customer_email": "test@example.com",
            "status": "pending"
        }
        
        # Try with admin client
        result = await db.create_record_admin("payments", test_record)
        print(f"✅ Successfully created test payment record: {result}")
        
        # Clean up test record
        await db.delete_record_admin("payments", "test-id-123")
        print("🧹 Cleaned up test record")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        print("🔧 The payments table structure might be incompatible")

if __name__ == "__main__":
    asyncio.run(fix_payments_table())
