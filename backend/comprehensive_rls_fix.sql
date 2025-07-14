-- =====================================================
-- COMPREHENSIVE RLS POLICIES FIX FOR E-COMMERCE API
-- This script fixes all RLS policies to allow both admin and user roles
-- =====================================================

-- First, drop all existing policies to start fresh
DO $$
DECLARE
    r RECORD;
BEGIN
    -- Drop all existing policies on all tables
    FOR r IN (
        SELECT schemaname, tablename, policyname 
        FROM pg_policies 
        WHERE schemaname = 'public'
    ) LOOP
        EXECUTE format('DROP POLICY IF EXISTS %I ON %I.%I', r.policyname, r.schemaname, r.tablename);
    END LOOP;
END $$;

-- =====================================================
-- PROFILES TABLE POLICIES
-- =====================================================

-- Users can view and update their own profile
CREATE POLICY "Users can manage their own profile" ON profiles
    FOR ALL USING (
        auth.uid() = id OR 
        auth.role() = 'service_role' OR
        (auth.jwt() ->> 'role')::text = 'admin'
    );

-- =====================================================
-- PRODUCTS TABLE POLICIES  
-- =====================================================

-- Everyone can view products
CREATE POLICY "Anyone can view products" ON products
    FOR SELECT USING (true);

-- Only admins and service role can manage products
CREATE POLICY "Admins can manage products" ON products
    FOR ALL USING (
        auth.role() = 'service_role' OR
        (auth.jwt() ->> 'role')::text = 'admin'
    );

-- =====================================================
-- CATEGORIES TABLE POLICIES
-- =====================================================

-- Everyone can view categories
CREATE POLICY "Anyone can view categories" ON categories
    FOR SELECT USING (true);

-- Only admins and service role can manage categories
CREATE POLICY "Admins can manage categories" ON categories
    FOR ALL USING (
        auth.role() = 'service_role' OR
        (auth.jwt() ->> 'role')::text = 'admin'
    );

-- =====================================================
-- ORDERS TABLE POLICIES
-- =====================================================

-- Users can view their own orders, admins can view all
CREATE POLICY "Users can view their orders, admins view all" ON orders
    FOR SELECT USING (
        auth.uid() = user_id OR 
        auth.role() = 'service_role' OR
        (auth.jwt() ->> 'role')::text = 'admin'
    );

-- Users can create orders for themselves, admins can create any
CREATE POLICY "Users can create their orders, admins create any" ON orders
    FOR INSERT WITH CHECK (
        auth.uid() = user_id OR 
        auth.role() = 'service_role' OR
        (auth.jwt() ->> 'role')::text = 'admin'
    );

-- Users can update their own orders, admins can update any
CREATE POLICY "Users can update their orders, admins update any" ON orders
    FOR UPDATE USING (
        auth.uid() = user_id OR 
        auth.role() = 'service_role' OR
        (auth.jwt() ->> 'role')::text = 'admin'
    );

-- Only admins and service role can delete orders
CREATE POLICY "Admins can delete orders" ON orders
    FOR DELETE USING (
        auth.role() = 'service_role' OR
        (auth.jwt() ->> 'role')::text = 'admin'
    );

-- =====================================================
-- ORDER_ITEMS TABLE POLICIES
-- =====================================================

-- Users can view items for their orders, admins can view all
CREATE POLICY "Users can view their order items, admins view all" ON order_items
    FOR SELECT USING (
        EXISTS (
            SELECT 1 FROM orders 
            WHERE orders.id = order_items.order_id 
            AND (orders.user_id = auth.uid() OR auth.role() = 'service_role' OR (auth.jwt() ->> 'role')::text = 'admin')
        )
    );

-- Users can create items for their orders, admins can create any
CREATE POLICY "Users can create their order items, admins create any" ON order_items
    FOR INSERT WITH CHECK (
        EXISTS (
            SELECT 1 FROM orders 
            WHERE orders.id = order_items.order_id 
            AND (orders.user_id = auth.uid() OR auth.role() = 'service_role' OR (auth.jwt() ->> 'role')::text = 'admin')
        )
    );

-- Users can update items for their orders, admins can update any
CREATE POLICY "Users can update their order items, admins update any" ON order_items
    FOR UPDATE USING (
        EXISTS (
            SELECT 1 FROM orders 
            WHERE orders.id = order_items.order_id 
            AND (orders.user_id = auth.uid() OR auth.role() = 'service_role' OR (auth.jwt() ->> 'role')::text = 'admin')
        )
    );

-- Only admins and service role can delete order items
CREATE POLICY "Admins can delete order items" ON order_items
    FOR DELETE USING (
        auth.role() = 'service_role' OR
        (auth.jwt() ->> 'role')::text = 'admin'
    );

-- =====================================================
-- PAYMENTS TABLE POLICIES
-- =====================================================

-- Users can view payments for their orders, admins can view all
CREATE POLICY "Users can view their payments, admins view all" ON payments
    FOR SELECT USING (
        EXISTS (
            SELECT 1 FROM orders 
            WHERE orders.id = payments.order_id 
            AND (orders.user_id = auth.uid() OR auth.role() = 'service_role' OR (auth.jwt() ->> 'role')::text = 'admin')
        )
    );

-- Users can create payments for their orders, admins can create any
CREATE POLICY "Users can create their payments, admins create any" ON payments
    FOR INSERT WITH CHECK (
        EXISTS (
            SELECT 1 FROM orders 
            WHERE orders.id = payments.order_id 
            AND (orders.user_id = auth.uid() OR auth.role() = 'service_role' OR (auth.jwt() ->> 'role')::text = 'admin')
        )
    );

-- Users can update payments for their orders, admins can update any
CREATE POLICY "Users can update their payments, admins update any" ON payments
    FOR UPDATE USING (
        EXISTS (
            SELECT 1 FROM orders 
            WHERE orders.id = payments.order_id 
            AND (orders.user_id = auth.uid() OR auth.role() = 'service_role' OR (auth.jwt() ->> 'role')::text = 'admin')
        )
    );

-- Only admins and service role can delete payments
CREATE POLICY "Admins can delete payments" ON payments
    FOR DELETE USING (
        auth.role() = 'service_role' OR
        (auth.jwt() ->> 'role')::text = 'admin'
    );

-- =====================================================
-- ENSURE ALL TABLES HAVE RLS ENABLED
-- =====================================================

ALTER TABLE profiles ENABLE ROW LEVEL SECURITY;
ALTER TABLE products ENABLE ROW LEVEL SECURITY;
ALTER TABLE categories ENABLE ROW LEVEL SECURITY;
ALTER TABLE orders ENABLE ROW LEVEL SECURITY;
ALTER TABLE order_items ENABLE ROW LEVEL SECURITY;
ALTER TABLE payments ENABLE ROW LEVEL SECURITY;
