-- Migration script to update payments table from Flutterwave to Stripe structure
-- Run this script to migrate your existing payments table

-- Step 1: Check if payments table exists and has data
DO $$
BEGIN
    IF EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'payments') THEN
        RAISE NOTICE 'Payments table exists. Proceeding with migration...';
    ELSE
        RAISE NOTICE 'Payments table does not exist. Will create new table...';
    END IF;
END $$;

-- Step 2: Backup the existing payments table (only if it exists and has data)
DO $$
BEGIN
    IF EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'payments') THEN
        -- Check if table has data
        IF (SELECT COUNT(*) FROM payments) > 0 THEN
            EXECUTE 'CREATE TABLE IF NOT EXISTS payments_backup AS SELECT * FROM payments';
            RAISE NOTICE 'Created backup table with % rows', (SELECT COUNT(*) FROM payments_backup);
        ELSE
            RAISE NOTICE 'Payments table is empty, skipping backup';
        END IF;
    END IF;
END $$;

-- Step 3: Temporarily disable RLS to avoid permission issues during migration
DO $$
BEGIN
    IF EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'payments') THEN
        ALTER TABLE payments DISABLE ROW LEVEL SECURITY;
        RAISE NOTICE 'Disabled RLS on existing payments table';
    END IF;
END $$;

-- Step 4: Drop the existing payments table and recreate with Stripe structure
DROP TABLE IF EXISTS payments CASCADE;

-- Create new Stripe-optimized payments table
CREATE TABLE payments (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    order_id UUID REFERENCES orders(id) NOT NULL,
    
    -- Stripe identifiers
    stripe_payment_intent_id TEXT UNIQUE,
    stripe_checkout_session_id TEXT UNIQUE,
    stripe_customer_id TEXT,
    stripe_charge_id TEXT,
    
    -- Payment details
    amount DECIMAL(10,2) NOT NULL CHECK (amount > 0),
    currency TEXT NOT NULL DEFAULT 'usd',
    customer_email TEXT NOT NULL,
    
    -- Status tracking
    status TEXT NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'processing', 'succeeded', 'failed', 'canceled', 'refunded', 'partially_refunded')),
    payment_method_type TEXT, -- card, bank_transfer, etc.
    
    -- URLs and links
    checkout_url TEXT,
    success_url TEXT,
    cancel_url TEXT,
    
    -- Financial tracking
    amount_received DECIMAL(10,2) DEFAULT 0,
    amount_refunded DECIMAL(10,2) DEFAULT 0,
    application_fee DECIMAL(10,2) DEFAULT 0,
    
    -- Timestamps
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE,
    succeeded_at TIMESTAMP WITH TIME ZONE,
    canceled_at TIMESTAMP WITH TIME ZONE,
    
    -- Webhook and metadata
    webhook_received_at TIMESTAMP WITH TIME ZONE,
    stripe_metadata JSONB,
    webhook_data JSONB,
    
    -- Additional Stripe fields
    receipt_url TEXT,
    invoice_id TEXT,
    description TEXT
);

-- Enable Row Level Security for payments
ALTER TABLE payments ENABLE ROW LEVEL SECURITY;

-- Recreate RLS policies
DROP POLICY IF EXISTS "Users can view payments for own orders" ON payments;

CREATE POLICY "Users can view payments for own orders" ON payments
    FOR SELECT USING (
        EXISTS (
            SELECT 1 FROM orders
            WHERE orders.id = payments.order_id
            AND orders.user_id = auth.uid()
        )
    );

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_payments_order_id ON payments(order_id);
CREATE INDEX IF NOT EXISTS idx_payments_stripe_session_id ON payments(stripe_checkout_session_id);
CREATE INDEX IF NOT EXISTS idx_payments_stripe_payment_intent_id ON payments(stripe_payment_intent_id);
CREATE INDEX IF NOT EXISTS idx_payments_status ON payments(status);
CREATE INDEX IF NOT EXISTS idx_payments_customer_email ON payments(customer_email);
CREATE INDEX IF NOT EXISTS idx_payments_created_at ON payments(created_at);

-- Step 6: Migrate existing data from backup (if any)
DO $$
DECLARE
    backup_exists BOOLEAN := FALSE;
    backup_count INTEGER := 0;
BEGIN
    -- Check if backup table exists
    SELECT EXISTS (
        SELECT FROM information_schema.tables
        WHERE table_name = 'payments_backup'
    ) INTO backup_exists;

    IF backup_exists THEN
        SELECT COUNT(*) FROM payments_backup INTO backup_count;
        RAISE NOTICE 'Found backup table with % rows', backup_count;

        IF backup_count > 0 THEN
            -- Migrate data with error handling
            INSERT INTO payments (
                id,
                order_id,
                amount,
                currency,
                customer_email,
                status,
                created_at,
                description
            )
            SELECT
                COALESCE(pb.id, gen_random_uuid()) as id,
                pb.order_id,
                COALESCE(pb.amount, 0) as amount,
                CASE
                    WHEN COALESCE(pb.currency, '') = 'TZS' THEN 'usd'
                    WHEN COALESCE(pb.currency, '') = '' THEN 'usd'
                    ELSE LOWER(COALESCE(pb.currency, 'usd'))
                END as currency,
                COALESCE(pb.customer_email, 'unknown@example.com') as customer_email,
                CASE
                    WHEN COALESCE(pb.status, '') = 'successful' THEN 'succeeded'
                    WHEN COALESCE(pb.status, '') = 'pending' THEN 'pending'
                    WHEN COALESCE(pb.status, '') = 'failed' THEN 'failed'
                    WHEN COALESCE(pb.status, '') = 'cancelled' THEN 'canceled'
                    WHEN COALESCE(pb.status, '') = 'refunded' THEN 'refunded'
                    ELSE 'pending'
                END as status,
                COALESCE(pb.created_at, NOW()) as created_at,
                'Migrated from Flutterwave - Order #' || COALESCE(pb.order_id::text, 'unknown') as description
            FROM payments_backup pb
            WHERE pb.order_id IS NOT NULL
            AND EXISTS (SELECT 1 FROM orders WHERE orders.id = pb.order_id)
            ON CONFLICT (id) DO NOTHING;

            GET DIAGNOSTICS backup_count = ROW_COUNT;
            RAISE NOTICE 'Successfully migrated % payment records', backup_count;
        END IF;
    ELSE
        RAISE NOTICE 'No backup table found, skipping data migration';
    END IF;
END $$;

-- Update any orders that reference the old payment structure
-- This ensures order payment_status remains consistent
UPDATE orders 
SET payment_status = CASE 
    WHEN payment_status = 'successful' THEN 'paid'
    ELSE payment_status
END
WHERE payment_status = 'successful';

-- Optional: Drop the backup table after verifying migration
-- DROP TABLE payments_backup;

-- Grant necessary permissions (adjust as needed for your setup)
-- GRANT SELECT, INSERT, UPDATE ON payments TO authenticated;
-- GRANT SELECT, INSERT, UPDATE ON payments TO service_role;

COMMENT ON TABLE payments IS 'Stripe-optimized payments table for e-commerce orders';
COMMENT ON COLUMN payments.stripe_payment_intent_id IS 'Stripe Payment Intent ID for tracking payments';
COMMENT ON COLUMN payments.stripe_checkout_session_id IS 'Stripe Checkout Session ID for payment links';
COMMENT ON COLUMN payments.amount_received IS 'Actual amount received (may differ from amount due to fees)';
COMMENT ON COLUMN payments.amount_refunded IS 'Total amount refunded for this payment';
COMMENT ON COLUMN payments.status IS 'Payment status: pending, processing, succeeded, failed, canceled, refunded, partially_refunded';
