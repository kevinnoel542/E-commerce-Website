# Database Setup Guide - Phase 2.1

## ✅ **PHASE 2.1: Prepare Database**

### **Purpose**
Create all necessary tables and security policies for the e-commerce system.

## **Method 1: Automatic Setup (Recommended)**

### **Run the Setup Script**
```bash
cd backend
python run_database_setup.py
```

This script will automatically run all SQL files in the correct order.

## **Method 2: Manual Setup (Supabase SQL Editor)**

If the automatic setup doesn't work, follow these steps:

### **Step 1: Access Supabase SQL Editor**
1. Go to your Supabase project dashboard
2. Navigate to **SQL Editor** in the left sidebar
3. Click **New Query**

### **Step 2: Run SQL Files in Order**

**Execute these files in the exact order shown:**

#### **2.1. Run database_setup.sql**
- Copy the contents of `backend/database_setup.sql`
- Paste into Supabase SQL Editor
- Click **Run** button
- ✅ **Expected Result**: Creates all tables (profiles, categories, products, orders, etc.)

#### **2.2. Run add_role_migration.sql**
- Copy the contents of `backend/add_role_migration.sql`
- Paste into Supabase SQL Editor
- Click **Run** button
- ✅ **Expected Result**: Adds role support and user management triggers

#### **2.3. Run comprehensive_rls_fix.sql**
- Copy the contents of `backend/comprehensive_rls_fix.sql`
- Paste into Supabase SQL Editor
- Click **Run** button
- ✅ **Expected Result**: Sets up proper Row Level Security policies

## **What Gets Created**

### **Tables Created:**
1. **✅ profiles** - User profiles with roles (user/admin)
2. **✅ categories** - Product categories with hierarchy
3. **✅ products** - Product catalog with images and inventory
4. **✅ orders** - Customer orders with status tracking
5. **✅ order_items** - Individual items within orders
6. **✅ payments** - Payment transaction records
7. **✅ shipping_addresses** - Customer shipping information
8. **✅ wishlists** - User wishlist functionality
9. **✅ reviews** - Product reviews and ratings

### **Security Policies (RLS):**
1. **✅ User Access Control** - Users can only access their own data
2. **✅ Admin Privileges** - Admins can manage all data
3. **✅ Public Product Access** - Anyone can view products
4. **✅ Secure Order Management** - Orders are user-specific
5. **✅ Service Role Access** - Backend can perform admin operations

### **Triggers & Functions:**
1. **✅ Auto Profile Creation** - Creates profile when user registers
2. **✅ Role Management** - Handles user role assignments
3. **✅ Timestamp Updates** - Auto-updates modified timestamps

## **Verification Steps**

After running the SQL files, verify the setup:

### **Check Tables Exist**
```sql
SELECT table_name 
FROM information_schema.tables 
WHERE table_schema = 'public' 
ORDER BY table_name;
```

**Expected Tables:**
- categories
- order_items
- orders
- payments
- products
- profiles
- reviews
- shipping_addresses
- wishlists

### **Check RLS Policies**
```sql
SELECT schemaname, tablename, policyname, roles
FROM pg_policies 
WHERE schemaname = 'public'
ORDER BY tablename, policyname;
```

**Expected Policies:**
- Each table should have appropriate RLS policies
- Policies should allow user access to own data
- Policies should allow admin access to all data

### **Test Admin User**
```sql
-- Check if admin user exists
SELECT id, email, role 
FROM profiles 
WHERE email = 'starkalyboy@gmail.com';

-- If not exists, create admin user (run this if needed)
INSERT INTO profiles (id, email, full_name, role, email_verified)
VALUES (
    gen_random_uuid(),
    'starkalyboy@gmail.com',
    'Admin User',
    'admin',
    true
) ON CONFLICT (email) DO UPDATE SET role = 'admin';
```

## **Troubleshooting**

### **Common Issues:**

#### **1. Permission Denied Errors**
- **Solution**: Make sure you're using the service role key
- **Check**: Verify SUPABASE_SERVICE_KEY in .env file

#### **2. Table Already Exists Errors**
- **Solution**: The scripts use `IF NOT EXISTS` - these warnings are normal
- **Action**: Continue with the next script

#### **3. Policy Conflicts**
- **Solution**: Scripts drop existing policies before creating new ones
- **Action**: Re-run the comprehensive_rls_fix.sql if needed

#### **4. Connection Issues**
- **Solution**: Check your DATABASE_URL or Supabase credentials
- **Alternative**: Use Supabase SQL Editor (Method 2)

## **Success Indicators**

✅ **Database Setup Complete When:**
1. All 9 tables are created
2. RLS policies are active on all tables
3. Admin user can be created/exists
4. FastAPI backend can connect and query data
5. No permission errors in backend logs

## **Next Steps**

After successful database setup:
1. **✅ Test FastAPI Connection** - Verify backend can connect
2. **✅ Test Authentication** - Login as admin user
3. **✅ Test Product Creation** - Create a test product
4. **✅ Verify RLS Policies** - Ensure security is working

## **Files Used**
- `database_setup.sql` - Main schema creation
- `add_role_migration.sql` - User role management
- `comprehensive_rls_fix.sql` - Security policies
- `run_database_setup.py` - Automated setup script

---

**Status**: Ready to execute Phase 2.1 database preparation.
