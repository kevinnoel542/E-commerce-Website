# Admin Role Setup Guide

## Quick Fix for Admin Dashboard Access

Your backend is missing role information. Here's how to fix it:

### Option 1: Manual Database Update (Recommended)

1. **Go to your Supabase Dashboard**
   - Open your Supabase project
   - Go to SQL Editor

2. **Add the role column:**
   ```sql
   ALTER TABLE profiles ADD COLUMN IF NOT EXISTS role TEXT DEFAULT 'user' 
   CHECK (role IN ('user', 'admin', 'super_admin', 'manager', 'moderator'));
   ```

3. **Update your user to be admin:**
   ```sql
   UPDATE profiles SET role = 'super_admin' WHERE email = 'your-admin-email@example.com';
   ```

4. **Restart your backend server**

### Option 2: Run the Setup Script

1. **Navigate to backend folder:**
   ```bash
   cd backend
   ```

2. **Run the setup script:**
   ```bash
   python setup_admin_roles.py
   ```

3. **Follow the prompts to set up your admin user**

### Option 3: Run SQL File Manually

1. **Copy the contents of `add_user_roles.sql`**
2. **Paste and run in Supabase SQL Editor**
3. **Update your user role manually:**
   ```sql
   UPDATE profiles SET role = 'super_admin' WHERE email = 'your-email@example.com';
   ```

## Verification

After setup, your login response should include:
```json
{
  "user": {
    "id": "...",
    "email": "admin@example.com",
    "role": "super_admin",
    ...
  }
}
```

## Available Roles

- **user**: Regular customer (default)
- **moderator**: View-only admin access
- **manager**: Can manage products and orders
- **admin**: Full admin access except user management
- **super_admin**: Full access to everything

## Troubleshooting

### Issue: Still showing user dashboard after login
**Solution**: Clear browser cache and localStorage, then login again

### Issue: Role not showing in API response
**Solution**: Check if the role column was added to profiles table

### Issue: Database migration failed
**Solution**: Run the SQL commands manually in Supabase SQL Editor

## Testing

1. **Login with admin account**
2. **Check browser console for user object**
3. **Should redirect to `/admin` instead of `/dashboard`**
4. **Admin navigation should be visible**

## Security Notes

- Only super_admin can change user roles
- Admin policies are automatically applied
- Regular users cannot access admin endpoints
- Role-based access is enforced at database level

---

**Need help?** Check the backend logs for any errors during setup.
