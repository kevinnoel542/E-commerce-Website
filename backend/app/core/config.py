import os
from dotenv import load_dotenv

load_dotenv()

# Supabase Configuration
SUPABASE_URL = os.getenv("SUPABASE_URL")
SUPABASE_KEY = os.getenv("SUPABASE_ANON_KEY") or os.getenv("SUPABASE_KEY")  # Support both names
SUPABASE_SERVICE_KEY = os.getenv("SUPABASE_SERVICE_KEY")

# Stripe Configuration
STRIPE_PUBLISHABLE_KEY = os.getenv("STRIPE_PUBLISHABLE_KEY")
STRIPE_SECRET_KEY = os.getenv("STRIPE_SECRET_KEY")
STRIPE_WEBHOOK_SECRET = os.getenv("STRIPE_WEBHOOK_SECRET")

# JWT Configuration
JWT_SECRET = os.getenv("JWT_SECRET_KEY") or os.getenv("JWT_SECRET", "your-secret-key-change-in-production")
ALGORITHM = os.getenv("JWT_ALGORITHM", "HS256")
ACCESS_TOKEN_EXPIRE_MINUTES = int(os.getenv("ACCESS_TOKEN_EXPIRE_MINUTES", "30"))
REFRESH_TOKEN_EXPIRE_DAYS = 7

# Application Configuration
APP_NAME = "E-Commerce API"
APP_VERSION = "1.0.0"
DEBUG = os.getenv("DEBUG", "False").lower() == "true"

# Payment Configuration
FRONTEND_URL = os.getenv("FRONTEND_URL", "https://yourdomain.com")
BACKEND_URL = os.getenv("BACKEND_URL", "http://localhost:8000")
PAYMENT_SUCCESS_URL = os.getenv("PAYMENT_SUCCESS_URL", f"{FRONTEND_URL}/payment-success")
PAYMENT_CANCEL_URL = os.getenv("PAYMENT_CANCEL_URL", f"{FRONTEND_URL}/payment-cancel")

# Email Configuration (for notifications)
SMTP_HOST = os.getenv("SMTP_HOST")
SMTP_PORT = int(os.getenv("SMTP_PORT", "587"))
SMTP_USER = os.getenv("SMTP_USER")
SMTP_PASSWORD = os.getenv("SMTP_PASSWORD")

# Currency Configuration
DEFAULT_CURRENCY = os.getenv("DEFAULT_CURRENCY", "USD")

# Admin Configuration
ADMIN_SECRET = os.getenv("ADMIN_SECRET", "super-secret-admin-key-change-in-production")

# Environment Detection
ENVIRONMENT = os.getenv("ENVIRONMENT", "development")
IS_PRODUCTION = ENVIRONMENT == "production"

# Validation
def validate_config():
    """Validate that all required environment variables are set"""
    required_vars = [
        "SUPABASE_URL",
        "SUPABASE_SERVICE_KEY",
        "STRIPE_SECRET_KEY"
    ]

    # Only require JWT_SECRET in production
    if IS_PRODUCTION:
        required_vars.append("JWT_SECRET_KEY")

    missing_vars = []
    for var in required_vars:
        if var == "SUPABASE_KEY":
            # Check both possible names
            if not (os.getenv("SUPABASE_ANON_KEY") or os.getenv("SUPABASE_KEY")):
                missing_vars.append("SUPABASE_ANON_KEY or SUPABASE_KEY")
        elif not os.getenv(var):
            missing_vars.append(var)

    if missing_vars:
        raise ValueError(f"Missing required environment variables: {', '.join(missing_vars)}")

    return True
