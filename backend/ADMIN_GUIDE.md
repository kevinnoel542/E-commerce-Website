# Admin System Guide

## 🔐 **User Roles System**

Your e-commerce API now supports two user roles:

- **👤 USER**: Regular customers who can browse products, place orders, and manage their profiles
- **👑 ADMIN**: Administrators who can manage products, categories, and have full system access

## 🚀 **Admin Registration**

### **Endpoint**: `POST /api/v1/auth/admin/register`

**Request Body:**
```json
{
  "email": "admin@example.com",
  "password": "securepassword123",
  "full_name": "Admin User",
  "phone": "+1234567890",
  "admin_secret": "super-secret-admin-key-change-in-production"
}
```

**Example:**
```bash
curl -X POST "http://localhost:8000/api/v1/auth/admin/register" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@yourdomain.com",
    "password": "admin123456",
    "full_name": "System Administrator",
    "phone": "+255123456789",
    "admin_secret": "super-secret-admin-key-change-in-production"
  }'
```

**Success Response:**
```json
{
  "message": "Admin user registered successfully. Please check your email for verification.",
  "user": {
    "id": "uuid",
    "email": "admin@yourdomain.com",
    "full_name": "System Administrator",
    "role": "admin",
    "created_at": "2025-07-08T15:30:00Z",
    "is_active": true,
    "email_verified": false
  }
}
```

## 🔑 **Admin Login**

Use the same login endpoint as regular users:

**Endpoint**: `POST /api/v1/auth/login`

```bash
curl -X POST "http://localhost:8000/api/v1/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@yourdomain.com",
    "password": "admin123456"
  }'
```

The JWT token returned will include the admin role, giving access to protected endpoints.

## 🛡️ **Protected Admin Endpoints**

### **Product Management (Admin Only):**
- `POST /api/v1/products/` - Create product
- `PUT /api/v1/products/{id}` - Update product
- `DELETE /api/v1/products/{id}` - Delete product

### **Category Management (Admin Only):**
- `POST /api/v1/products/categories/` - Create category
- `PUT /api/v1/products/categories/{id}` - Update category
- `DELETE /api/v1/products/categories/{id}` - Delete category

### **Example Admin Request:**
```bash
# First login to get admin token
ADMIN_TOKEN=$(curl -X POST "http://localhost:8000/api/v1/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@yourdomain.com", "password": "admin123456"}' \
  | jq -r '.tokens.access_token')

# Create a product (admin only)
curl -X POST "http://localhost:8000/api/v1/products/" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -d '{
    "name": "New Product",
    "description": "Product description",
    "price": 99.99,
    "stock_quantity": 100
  }'
```

## 🌐 **Public Endpoints (No Authentication Required):**
- `GET /api/v1/products/` - List products
- `GET /api/v1/products/{id}` - Get product details
- `GET /api/v1/products/search` - Search products
- `GET /api/v1/products/categories/` - List categories
- `POST /api/v1/auth/register` - User registration
- `POST /api/v1/auth/login` - User/Admin login

## 👤 **User Endpoints (Authentication Required):**
- `GET /api/v1/auth/profile` - Get user profile
- `PUT /api/v1/auth/profile` - Update user profile
- `POST /api/v1/orders/` - Create order
- `GET /api/v1/orders/` - Get user orders
- All payment endpoints

## ⚠️ **Error Responses**

**403 Forbidden** - When a regular user tries to access admin endpoints:
```json
{
  "detail": "Admin access required"
}
```

**401 Unauthorized** - When no token is provided:
```json
{
  "detail": "Could not validate credentials"
}
```

**400 Bad Request** - When admin secret is wrong:
```json
{
  "detail": "Invalid admin secret"
}
```

## 🔧 **Configuration**

### **Environment Variables:**
```env
# Admin secret key (change in production!)
ADMIN_SECRET=super-secret-admin-key-change-in-production
```

### **Database Schema:**
The `profiles` table now includes a `role` field:
```sql
role TEXT DEFAULT 'user' CHECK (role IN ('user', 'admin'))
```

## 🚀 **Getting Started**

1. **Update your database** by running the updated `database_setup.sql`
2. **Set the admin secret** in your `.env` file
3. **Register the first admin** using the admin registration endpoint
4. **Login as admin** to get admin JWT token
5. **Start managing products and categories**

## 💡 **Best Practices**

1. **Change the admin secret** in production
2. **Use strong passwords** for admin accounts
3. **Limit admin registrations** by keeping the secret secure
4. **Monitor admin activities** through logs
5. **Regularly rotate JWT secrets**

## 🔍 **Testing Admin Functionality**

```bash
# 1. Register admin
curl -X POST "http://localhost:8000/api/v1/auth/admin/register" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@test.com",
    "password": "admin123",
    "full_name": "Test Admin",
    "admin_secret": "super-secret-admin-key-change-in-production"
  }'

# 2. Login as admin
curl -X POST "http://localhost:8000/api/v1/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@test.com", "password": "admin123"}'

# 3. Use admin token to create product
curl -X POST "http://localhost:8000/api/v1/products/" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "name": "Test Product",
    "description": "A test product",
    "price": 29.99
  }'
```

Your e-commerce platform now has proper role-based access control! 🎉
