#!/usr/bin/env python3
"""
Migration script to update payments table for Stripe integration
"""
import os
import sys
from supabase import create_client, Client

# Add the app directory to the path
sys.path.append(os.path.join(os.path.dirname(__file__), 'app'))

from app.core.config import settings

def run_migration():
    """Run the payments table migration"""
    
    # Create Supabase client with service role key (admin access)
    supabase: Client = create_client(
        settings.SUPABASE_URL,
        settings.SUPABASE_SERVICE_ROLE_KEY
    )
    
    print("🚀 Starting payments table migration...")
    
    try:
        # Step 1: Drop existing payments table
        print("📋 Step 1: Dropping existing payments table...")
        drop_sql = """
        DROP TABLE IF EXISTS payments CASCADE;
        """
        supabase.rpc('exec_sql', {'sql': drop_sql}).execute()
        print("✅ Existing payments table dropped")
        
        # Step 2: Create new payments table with Stripe structure
        print("📋 Step 2: Creating new payments table...")
        create_sql = """
        CREATE TABLE payments (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            order_id UUID NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
            stripe_session_id VARCHAR(255) UNIQUE NOT NULL,
            stripe_payment_intent_id VARCHAR(255),
            amount DECIMAL(10,2) NOT NULL,
            currency VARCHAR(3) NOT NULL DEFAULT 'usd',
            status payment_status NOT NULL DEFAULT 'pending',
            customer_email VARCHAR(255) NOT NULL,
            metadata JSONB DEFAULT '{}',
            created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
            updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
        );
        """
        supabase.rpc('exec_sql', {'sql': create_sql}).execute()
        print("✅ New payments table created")
        
        # Step 3: Create indexes
        print("📋 Step 3: Creating indexes...")
        index_sql = """
        CREATE INDEX idx_payments_order_id ON payments(order_id);
        CREATE INDEX idx_payments_stripe_session_id ON payments(stripe_session_id);
        CREATE INDEX idx_payments_status ON payments(status);
        CREATE INDEX idx_payments_created_at ON payments(created_at);
        """
        supabase.rpc('exec_sql', {'sql': index_sql}).execute()
        print("✅ Indexes created")
        
        # Step 4: Enable RLS
        print("📋 Step 4: Enabling Row Level Security...")
        rls_sql = """
        ALTER TABLE payments ENABLE ROW LEVEL SECURITY;
        """
        supabase.rpc('exec_sql', {'sql': rls_sql}).execute()
        print("✅ RLS enabled")
        
        # Step 5: Create RLS policies
        print("📋 Step 5: Creating RLS policies...")
        policies_sql = """
        -- Policy for users to view their own payments
        CREATE POLICY "Users can view their own payments" ON payments
            FOR SELECT USING (
                EXISTS (
                    SELECT 1 FROM orders 
                    WHERE orders.id = payments.order_id 
                    AND orders.user_id = auth.uid()
                )
            );
        
        -- Policy for service role to manage all payments
        CREATE POLICY "Service role can manage all payments" ON payments
            FOR ALL USING (auth.role() = 'service_role');
        
        -- Policy for authenticated users to insert payments for their orders
        CREATE POLICY "Users can create payments for their orders" ON payments
            FOR INSERT WITH CHECK (
                EXISTS (
                    SELECT 1 FROM orders 
                    WHERE orders.id = payments.order_id 
                    AND orders.user_id = auth.uid()
                )
            );
        """
        supabase.rpc('exec_sql', {'sql': policies_sql}).execute()
        print("✅ RLS policies created")
        
        # Step 6: Create updated_at trigger
        print("📋 Step 6: Creating updated_at trigger...")
        trigger_sql = """
        CREATE OR REPLACE FUNCTION update_updated_at_column()
        RETURNS TRIGGER AS $$
        BEGIN
            NEW.updated_at = NOW();
            RETURN NEW;
        END;
        $$ language 'plpgsql';
        
        CREATE TRIGGER update_payments_updated_at 
            BEFORE UPDATE ON payments 
            FOR EACH ROW 
            EXECUTE FUNCTION update_updated_at_column();
        """
        supabase.rpc('exec_sql', {'sql': trigger_sql}).execute()
        print("✅ Updated_at trigger created")
        
        print("🎉 Migration completed successfully!")
        print("📊 Payments table is now ready for Stripe integration")
        
    except Exception as e:
        print(f"❌ Migration failed: {str(e)}")
        return False
    
    return True

if __name__ == "__main__":
    success = run_migration()
    sys.exit(0 if success else 1)
