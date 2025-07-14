-- Diagnostic script to identify potential migration issues
-- Run this BEFORE running the migration to identify problems

-- Check 1: Does payments table exist?
SELECT 
    CASE 
        WHEN EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'payments') 
        THEN 'EXISTS' 
        ELSE 'MISSING' 
    END as payments_table_status;

-- Check 2: What columns exist in current payments table?
SELECT column_name, data_type, is_nullable
FROM information_schema.columns 
WHERE table_name = 'payments' 
ORDER BY ordinal_position;

-- Check 3: How many records are in payments table?
SELECT 
    CASE 
        WHEN EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'payments') 
        THEN (SELECT COUNT(*)::text FROM payments)
        ELSE 'TABLE_MISSING' 
    END as payment_record_count;

-- Check 4: What are the current payment statuses?
SELECT 
    CASE 
        WHEN EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'payments') 
        THEN (
            SELECT string_agg(DISTINCT status, ', ') 
            FROM payments 
            WHERE status IS NOT NULL
        )
        ELSE 'TABLE_MISSING' 
    END as current_payment_statuses;

-- Check 5: What currencies are being used?
SELECT 
    CASE 
        WHEN EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'payments') 
        THEN (
            SELECT string_agg(DISTINCT currency, ', ') 
            FROM payments 
            WHERE currency IS NOT NULL
        )
        ELSE 'TABLE_MISSING' 
    END as current_currencies;

-- Check 6: Are there any payments with missing order_id?
SELECT 
    CASE 
        WHEN EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'payments') 
        THEN (
            SELECT COUNT(*)::text 
            FROM payments 
            WHERE order_id IS NULL
        )
        ELSE 'TABLE_MISSING' 
    END as payments_with_null_order_id;

-- Check 7: Are there any payments referencing non-existent orders?
SELECT 
    CASE 
        WHEN EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'payments') 
        THEN (
            SELECT COUNT(*)::text 
            FROM payments p
            WHERE p.order_id IS NOT NULL 
            AND NOT EXISTS (SELECT 1 FROM orders o WHERE o.id = p.order_id)
        )
        ELSE 'TABLE_MISSING' 
    END as payments_with_invalid_order_id;

-- Check 8: Does orders table exist?
SELECT 
    CASE 
        WHEN EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'orders') 
        THEN 'EXISTS' 
        ELSE 'MISSING' 
    END as orders_table_status;

-- Check 9: Current RLS policies on payments table
SELECT schemaname, tablename, policyname, permissive, roles, cmd, qual
FROM pg_policies 
WHERE tablename = 'payments';

-- Check 10: Current indexes on payments table
SELECT indexname, indexdef
FROM pg_indexes 
WHERE tablename = 'payments';

-- Check 11: Sample payment data (first 3 records)
SELECT 
    CASE 
        WHEN EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'payments') 
        THEN 'SAMPLE_DATA_BELOW'
        ELSE 'TABLE_MISSING' 
    END as sample_data_status;

-- Show sample data if table exists
DO $$
BEGIN
    IF EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'payments') THEN
        RAISE NOTICE 'Sample payment records:';
        FOR rec IN 
            SELECT id, order_id, amount, currency, status, created_at
            FROM payments 
            LIMIT 3
        LOOP
            RAISE NOTICE 'ID: %, Order: %, Amount: %, Currency: %, Status: %, Created: %', 
                rec.id, rec.order_id, rec.amount, rec.currency, rec.status, rec.created_at;
        END LOOP;
    END IF;
END $$;

-- Check 12: Potential data issues
SELECT 
    'Data Issues Found:' as issue_summary,
    CASE 
        WHEN EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'payments') 
        THEN (
            SELECT COUNT(*) 
            FROM payments 
            WHERE amount IS NULL OR amount <= 0
        )::text || ' payments with invalid amounts'
        ELSE 'TABLE_MISSING'
    END as invalid_amounts,
    CASE 
        WHEN EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'payments') 
        THEN (
            SELECT COUNT(*) 
            FROM payments 
            WHERE customer_email IS NULL OR customer_email = ''
        )::text || ' payments with missing emails'
        ELSE 'TABLE_MISSING'
    END as missing_emails;

-- Final recommendation
SELECT 
    CASE 
        WHEN NOT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'payments') 
        THEN 'SAFE_TO_CREATE: No existing payments table found. You can run the migration safely.'
        WHEN (SELECT COUNT(*) FROM payments) = 0 
        THEN 'SAFE_TO_RECREATE: Payments table exists but is empty. Safe to recreate.'
        WHEN EXISTS (
            SELECT 1 FROM payments p
            WHERE p.order_id IS NOT NULL 
            AND NOT EXISTS (SELECT 1 FROM orders o WHERE o.id = p.order_id)
        )
        THEN 'WARNING: Some payments reference non-existent orders. Clean data first.'
        ELSE 'READY_TO_MIGRATE: Payments table has data and appears ready for migration.'
    END as migration_recommendation;
