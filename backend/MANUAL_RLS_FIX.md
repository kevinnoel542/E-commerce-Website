# 🔧 MANUAL RLS POLICIES FIX

## Instructions:
1. Go to your Supabase Dashboard
2. Navigate to SQL Editor
3. Copy and paste the SQL commands below
4. Run them one by one

## 🚨 CRITICAL FIX FOR PAYMENT CREATION ERROR

The payment creation is failing because of RLS policies. Run these commands to fix it:

### Step 1: Drop All Existing Policies
```sql
-- Drop all existing policies to start fresh
DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN (
        SELECT schemaname, tablename, policyname 
        FROM pg_policies 
        WHERE schemaname = 'public'
    ) LOOP
        EXECUTE format('DROP POLICY IF EXISTS %I ON %I.%I', r.policyname, r.schemaname, r.tablename);
    END LOOP;
END $$;
```

### Step 2: Fix Payments Table Policies (MOST IMPORTANT)
```sql
-- Allow service role to do everything on payments table
CREATE POLICY "Service role can manage all payments" ON payments
    FOR ALL USING (auth.role() = 'service_role');

-- Allow users to view their own payments
CREATE POLICY "Users can view their payments" ON payments
    FOR SELECT USING (
        EXISTS (
            SELECT 1 FROM orders 
            WHERE orders.id = payments.order_id 
            AND orders.user_id = auth.uid()
        )
    );

-- Allow users to create payments for their orders
CREATE POLICY "Users can create payments for their orders" ON payments
    FOR INSERT WITH CHECK (
        EXISTS (
            SELECT 1 FROM orders 
            WHERE orders.id = payments.order_id 
            AND orders.user_id = auth.uid()
        ) OR auth.role() = 'service_role'
    );
```

### Step 3: Fix Orders Table Policies
```sql
-- Service role can manage all orders
CREATE POLICY "Service role can manage all orders" ON orders
    FOR ALL USING (auth.role() = 'service_role');

-- Users can view their own orders
CREATE POLICY "Users can view their orders" ON orders
    FOR SELECT USING (auth.uid() = user_id OR auth.role() = 'service_role');

-- Users can create orders for themselves
CREATE POLICY "Users can create their orders" ON orders
    FOR INSERT WITH CHECK (auth.uid() = user_id OR auth.role() = 'service_role');

-- Users can update their own orders
CREATE POLICY "Users can update their orders" ON orders
    FOR UPDATE USING (auth.uid() = user_id OR auth.role() = 'service_role');
```

### Step 4: Fix Order Items Table Policies
```sql
-- Service role can manage all order items
CREATE POLICY "Service role can manage all order_items" ON order_items
    FOR ALL USING (auth.role() = 'service_role');

-- Users can view items for their orders
CREATE POLICY "Users can view their order items" ON order_items
    FOR SELECT USING (
        EXISTS (
            SELECT 1 FROM orders 
            WHERE orders.id = order_items.order_id 
            AND orders.user_id = auth.uid()
        ) OR auth.role() = 'service_role'
    );

-- Users can create items for their orders
CREATE POLICY "Users can create order items" ON order_items
    FOR INSERT WITH CHECK (
        EXISTS (
            SELECT 1 FROM orders 
            WHERE orders.id = order_items.order_id 
            AND orders.user_id = auth.uid()
        ) OR auth.role() = 'service_role'
    );
```

### Step 5: Fix Products and Categories (Public Access)
```sql
-- Anyone can view products
CREATE POLICY "Anyone can view products" ON products
    FOR SELECT USING (true);

-- Service role can manage products
CREATE POLICY "Service role can manage products" ON products
    FOR ALL USING (auth.role() = 'service_role');

-- Anyone can view categories
CREATE POLICY "Anyone can view categories" ON categories
    FOR SELECT USING (true);

-- Service role can manage categories
CREATE POLICY "Service role can manage categories" ON categories
    FOR ALL USING (auth.role() = 'service_role');
```

### Step 6: Fix Profiles Table
```sql
-- Service role can manage all profiles
CREATE POLICY "Service role can manage all profiles" ON profiles
    FOR ALL USING (auth.role() = 'service_role');

-- Users can view and update their own profile
CREATE POLICY "Users can manage their profile" ON profiles
    FOR ALL USING (auth.uid() = id OR auth.role() = 'service_role');
```

## 🎯 After Running These Commands:

1. Your payment creation should work immediately
2. All API endpoints should respect proper permissions
3. Service role (your backend) can access everything
4. Users can only access their own data

## 🧪 Test the Fix:

After running these commands, test your payment creation endpoint. It should now work without RLS errors!
