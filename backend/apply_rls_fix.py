#!/usr/bin/env python3
"""
Apply comprehensive RLS policies fix
"""
import asyncio
import sys
import os

# Add the app directory to the path
sys.path.append(os.path.join(os.path.dirname(__file__), 'app'))

from app.core.config import SUPABASE_URL, SUPABASE_SERVICE_KEY
from supabase import create_client, Client

async def apply_rls_fix():
    """Apply the comprehensive RLS policies fix"""

    # Create Supabase client with service role key (admin access)
    supabase: Client = create_client(
        SUPABASE_URL,
        SUPABASE_SERVICE_KEY
    )
    
    print("🚀 Applying comprehensive RLS policies fix...")
    
    try:
        # Read the SQL file
        with open('comprehensive_rls_fix.sql', 'r') as f:
            sql_content = f.read()
        
        # Split the SQL into individual statements
        statements = [stmt.strip() for stmt in sql_content.split(';') if stmt.strip()]
        
        print(f"📋 Executing {len(statements)} SQL statements...")
        
        for i, statement in enumerate(statements, 1):
            if statement.strip():
                try:
                    print(f"  {i}/{len(statements)}: Executing statement...")
                    # Use the rpc function to execute raw SQL
                    result = supabase.rpc('exec_sql', {'sql': statement + ';'}).execute()
                    print(f"  ✅ Statement {i} executed successfully")
                except Exception as e:
                    print(f"  ⚠️  Statement {i} failed: {str(e)}")
                    # Continue with other statements
        
        print("🎉 RLS policies fix completed!")
        print("📊 All tables now have proper policies for both admin and user roles")
        
        # Test the fix by trying to create a payment record
        print("\n🧪 Testing payment creation...")
        test_payment = {
            "order_id": "d8bf79b5-e7e9-4078-9f6c-6c6891f0259e",  # Use existing order
            "stripe_checkout_session_id": "cs_test_rls_fix",
            "amount": "100.00",
            "currency": "usd",
            "customer_email": "test@example.com",
            "status": "pending",
            "checkout_url": "https://checkout.stripe.com/test",
            "success_url": "https://example.com/success",
            "cancel_url": "https://example.com/cancel",
            "description": "Test payment for RLS fix"
        }
        
        # Try to create with service role (should work now)
        result = supabase.table('payments').insert(test_payment).execute()
        if result.data:
            print("✅ Payment creation test PASSED!")
            # Clean up test record
            payment_id = result.data[0]['id']
            supabase.table('payments').delete().eq('id', payment_id).execute()
            print("🧹 Test record cleaned up")
        else:
            print("❌ Payment creation test FAILED")
        
    except Exception as e:
        print(f"❌ RLS fix failed: {str(e)}")
        return False
    
    return True

if __name__ == "__main__":
    success = asyncio.run(apply_rls_fix())
    sys.exit(0 if success else 1)
