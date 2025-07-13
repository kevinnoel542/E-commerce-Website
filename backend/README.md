# E-Commerce Backend API

A full-featured e-commerce backend built with FastAPI, Supabase, Flutterwave, and JWT authentication following clean architecture principles.

## Features

- 🔐 **Authentication & Authorization**: JWT-based auth with Supabase
- 🛍️ **Product Management**: CRUD operations for products and categories
- 📦 **Order Management**: Complete order lifecycle management
- 💳 **Payment Processing**: Flutterwave integration for payments
- 🏗️ **Clean Architecture**: Separation of concerns with services, models, and routes
- 📝 **Comprehensive Logging**: Structured logging for monitoring and debugging
- 🔒 **Security**: Password hashing, JWT tokens, CORS protection
- 📚 **API Documentation**: Auto-generated OpenAPI/Swagger docs

## Tech Stack

- **Framework**: FastAPI
- **Database**: Supabase (PostgreSQL)
- **Authentication**: Supabase Auth + JWT
- **Payment Gateway**: Flutterwave
- **Validation**: Pydantic
- **Security**: python-jose, passlib
- **HTTP Client**: requests, httpx

## Project Structure

```
backend/
├── app/
│   ├── __init__.py
│   ├── main.py                 # FastAPI application entry point
│   ├── core/
│   │   ├── config.py          # Configuration settings
│   │   ├── security.py        # JWT and password handling
│   │   └── logging.py         # Logging configuration
│   ├── db/
│   │   └── client.py          # Supabase client and database operations
│   ├── models/
│   │   ├── auth.py            # Authentication models
│   │   ├── product.py         # Product and category models
│   │   └── order.py           # Order and payment models
│   ├── services/
│   │   ├── product_service.py # Product business logic
│   │   ├── order_service.py   # Order business logic
│   │   └── payment_service.py # Payment business logic
│   └── api/
│       └── v1/
│           └── routes/
│               ├── auth.py    # Authentication endpoints
│               ├── products.py # Product endpoints
│               ├── orders.py  # Order endpoints
│               └── payments.py # Payment endpoints
├── logs/                      # Application logs
├── requirements.txt           # Python dependencies
├── .env.example              # Environment variables template
└── README.md                 # This file
```

## Setup Instructions

### 1. Clone and Navigate

```bash
cd backend
```

### 2. Create Virtual Environment

```bash
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate
```

### 3. Install Dependencies

```bash
pip install -r requirements.txt
```

### 4. Environment Configuration

Copy the example environment file and configure your settings:

```bash
cp .env.example .env
```

Edit `.env` with your actual configuration:

```env
# Supabase Configuration
SUPABASE_URL=https://nluxtoziartsvilflbtf.supabase.co
SUPABASE_KEY=your-anon-key-from-supabase-dashboard
SUPABASE_SERVICE_KEY=your-service-role-key-from-supabase-dashboard

# Flutterwave Configuration
FLUTTERWAVE_SECRET_KEY=FLWSECK_TEST-your-secret-key
FLUTTERWAVE_PUBLIC_KEY=FLWPUBK_TEST-your-public-key

# JWT Configuration
JWT_SECRET=your-super-secret-jwt-key-change-in-production
```

### 5. Database Setup

Create the following tables in your Supabase database:

```sql
-- Profiles table (extends Supabase auth.users)
CREATE TABLE profiles (
    id UUID REFERENCES auth.users(id) PRIMARY KEY,
    email TEXT UNIQUE NOT NULL,
    full_name TEXT,
    phone TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE,
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE
);

-- Categories table
CREATE TABLE categories (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name TEXT NOT NULL,
    description TEXT,
    parent_id UUID REFERENCES categories(id),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE,
    created_by UUID REFERENCES auth.users(id)
);

-- Products table
CREATE TABLE products (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name TEXT NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id UUID REFERENCES categories(id),
    brand TEXT,
    sku TEXT UNIQUE,
    stock_quantity INTEGER DEFAULT 0,
    images TEXT[] DEFAULT '{}',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE,
    created_by UUID REFERENCES auth.users(id)
);

-- Orders table
CREATE TABLE orders (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES auth.users(id) NOT NULL,
    order_number TEXT UNIQUE NOT NULL,
    status TEXT NOT NULL DEFAULT 'pending',
    payment_status TEXT NOT NULL DEFAULT 'pending',
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_amount DECIMAL(10,2) DEFAULT 0,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    final_amount DECIMAL(10,2) NOT NULL,
    currency TEXT DEFAULT 'TZS',
    shipping_address JSONB NOT NULL,
    tracking_number TEXT,
    notes TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE
);

-- Order items table
CREATE TABLE order_items (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    order_id UUID REFERENCES orders(id) NOT NULL,
    product_id UUID REFERENCES products(id) NOT NULL,
    product_name TEXT NOT NULL,
    quantity INTEGER NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Payments table
CREATE TABLE payments (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tx_ref TEXT UNIQUE NOT NULL,
    order_id UUID REFERENCES orders(id) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency TEXT NOT NULL,
    customer_email TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'pending',
    payment_link TEXT,
    flutterwave_id TEXT,
    payment_type TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    verified_at TIMESTAMP WITH TIME ZONE,
    webhook_received_at TIMESTAMP WITH TIME ZONE
);
```

### 6. Run the Application

```bash
uvicorn app.main:app --reload --host 0.0.0.0 --port 8000
```

The API will be available at:
- **API**: http://localhost:8000
- **Documentation**: http://localhost:8000/docs
- **Alternative docs**: http://localhost:8000/redoc

## API Endpoints

### Authentication
- `POST /api/v1/auth/register` - Register new user
- `POST /api/v1/auth/login` - Login user
- `POST /api/v1/auth/refresh` - Refresh access token
- `GET /api/v1/auth/profile` - Get user profile
- `PUT /api/v1/auth/profile` - Update user profile

### Products
- `GET /api/v1/products/` - List products
- `GET /api/v1/products/search` - Search products
- `GET /api/v1/products/{id}` - Get product details
- `POST /api/v1/products/` - Create product (admin)
- `PUT /api/v1/products/{id}` - Update product (admin)
- `DELETE /api/v1/products/{id}` - Delete product (admin)

### Orders
- `POST /api/v1/orders/cart/summary` - Calculate cart totals
- `POST /api/v1/orders/` - Create order
- `GET /api/v1/orders/` - Get user orders
- `GET /api/v1/orders/{id}` - Get order details
- `POST /api/v1/orders/{id}/payment` - Create payment for order

### Payments
- `GET /api/v1/payments/verify/{tx_ref}` - Verify payment
- `POST /api/v1/payments/webhook` - Flutterwave webhook
- `GET /api/v1/payments/history` - Payment history

## Development

### Code Style

```bash
# Format code
black app/

# Sort imports
isort app/

# Lint code
flake8 app/
```

### Testing

```bash
# Run tests
pytest

# Run with coverage
pytest --cov=app tests/
```

## Deployment

### Environment Variables for Production

Make sure to set these in production:

```env
DEBUG=False
JWT_SECRET=your-production-secret-key
SUPABASE_URL=your-production-supabase-url
FLUTTERWAVE_SECRET_KEY=your-production-flutterwave-key
```

### Docker Deployment

Create a `Dockerfile`:

```dockerfile
FROM python:3.11-slim

WORKDIR /app

COPY requirements.txt .
RUN pip install -r requirements.txt

COPY . .

CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "8000"]
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

This project is licensed under the MIT License.
