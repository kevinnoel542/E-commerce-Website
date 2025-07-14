-- Simple step-by-step Stripe migration script
-- Run each section separately to identify where errors occur

-- STEP 1: Create backup (run this first)
-- Only run if you have existing data you want to preserve
-- CREATE TABLE payments_backup AS SELECT * FROM payments;

-- STEP 2: Drop existing table (CAREFUL - this deletes data!)
-- Only run after backup is created
-- DROP TABLE IF EXISTS payments CASCADE;

-- STEP 3: Create new Stripe payments table
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
    payment_method_type TEXT,
    
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

-- STEP 4: Enable RLS
ALTER TABLE payments ENABLE ROW LEVEL SECURITY;

-- STEP 5: Create RLS policies
CREATE POLICY "Users can view payments for own orders" ON payments
    FOR SELECT USING (
        EXISTS (
            SELECT 1 FROM orders
            WHERE orders.id = payments.order_id
            AND orders.user_id = auth.uid()
        )
    );

-- STEP 6: Create indexes
CREATE INDEX idx_payments_order_id ON payments(order_id);
CREATE INDEX idx_payments_stripe_session_id ON payments(stripe_checkout_session_id);
CREATE INDEX idx_payments_stripe_payment_intent_id ON payments(stripe_payment_intent_id);
CREATE INDEX idx_payments_status ON payments(status);
CREATE INDEX idx_payments_customer_email ON payments(customer_email);
CREATE INDEX idx_payments_created_at ON payments(created_at);

-- STEP 7: Migrate data (only if you have backup data)
-- Uncomment and modify this section based on your backup table structure
/*
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
    id,
    order_id,
    amount,
    CASE 
        WHEN currency = 'TZS' THEN 'usd'
        ELSE LOWER(COALESCE(currency, 'usd'))
    END as currency,
    COALESCE(customer_email, 'migrated@example.com') as customer_email,
    CASE 
        WHEN status = 'successful' THEN 'succeeded'
        WHEN status = 'cancelled' THEN 'canceled'
        ELSE COALESCE(status, 'pending')
    END as status,
    COALESCE(created_at, NOW()) as created_at,
    'Migrated from previous system' as description
FROM payments_backup
WHERE order_id IS NOT NULL
AND EXISTS (SELECT 1 FROM orders WHERE orders.id = payments_backup.order_id);
*/

-- STEP 8: Verify migration
SELECT 
    COUNT(*) as total_payments,
    COUNT(DISTINCT status) as unique_statuses,
    COUNT(DISTINCT currency) as unique_currencies
FROM payments;

-- STEP 9: Show sample data
SELECT id, order_id, amount, currency, status, created_at
FROM payments 
LIMIT 5;

-- STEP 10: Clean up (optional)
-- DROP TABLE IF EXISTS payments_backup;

-- Add comments
COMMENT ON TABLE payments IS 'Stripe-optimized payments table';
COMMENT ON COLUMN payments.stripe_payment_intent_id IS 'Stripe Payment Intent ID';
COMMENT ON COLUMN payments.stripe_checkout_session_id IS 'Stripe Checkout Session ID';
COMMENT ON COLUMN payments.status IS 'Payment status: pending, processing, succeeded, failed, canceled, refunded, partially_refunded';
