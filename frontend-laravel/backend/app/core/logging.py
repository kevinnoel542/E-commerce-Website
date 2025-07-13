import logging
import sys
import os
from datetime import datetime

# Get DEBUG setting with fallback
try:
    from app.core.config import DEBUG
except ImportError:
    DEBUG = os.getenv("DEBUG", "True").lower() == "true"

# Configure logging format (no emojis for Windows compatibility)
log_format = "%(asctime)s | %(levelname)s | %(name)s | %(message)s"
date_format = "%Y-%m-%d %H:%M:%S"

# Set logging level based on DEBUG setting
log_level = logging.DEBUG if DEBUG else logging.INFO

# Create logs directory if it doesn't exist
os.makedirs("logs", exist_ok=True)

# Configure handlers with UTF-8 encoding
stdout_handler = logging.StreamHandler(sys.stdout)
stdout_handler.setFormatter(logging.Formatter(log_format, date_format))

# Force UTF-8 encoding for Windows compatibility
if hasattr(stdout_handler.stream, 'reconfigure'):
    try:
        stdout_handler.stream.reconfigure(encoding='utf-8')
    except Exception:
        pass  # Fallback silently if reconfigure fails

file_handler = logging.FileHandler(
    f"logs/ecommerce_{datetime.now().strftime('%Y%m%d')}.log",
    mode='a',
    encoding='utf-8'
)
file_handler.setFormatter(logging.Formatter(log_format, date_format))

# Configure root logger
logging.basicConfig(
    level=log_level,
    handlers=[stdout_handler, file_handler]
)

# Create logger for the application
logger = logging.getLogger("ecommerce")

# Create specialized loggers for different components
auth_logger = logging.getLogger("ecommerce.auth")
payment_logger = logging.getLogger("ecommerce.payment")
order_logger = logging.getLogger("ecommerce.order")
product_logger = logging.getLogger("ecommerce.product")

# Disable some noisy loggers in production
if not DEBUG:
    logging.getLogger("uvicorn.access").setLevel(logging.WARNING)
    logging.getLogger("httpx").setLevel(logging.WARNING)

def log_request(method: str, url: str, user_email: str = None):
    """Log API requests"""
    user_info = f" | User: {user_email}" if user_email else ""
    logger.info(f"API Request: {method} {url}{user_info}")

def log_payment_event(event: str, tx_ref: str, amount: float = None, status: str = None):
    """Log payment-related events"""
    amount_info = f" | Amount: {amount}" if amount else ""
    status_info = f" | Status: {status}" if status else ""
    payment_logger.info(f"Payment {event}: {tx_ref}{amount_info}{status_info}")

def log_order_event(event: str, order_id: str, user_email: str = None):
    """Log order-related events"""
    user_info = f" | User: {user_email}" if user_email else ""
    order_logger.info(f"Order {event}: {order_id}{user_info}")

def log_auth_event(event: str, email: str, success: bool = True):
    """Log authentication events"""
    status = "SUCCESS" if success else "FAILED"
    auth_logger.info(f"Auth {event}: {email} | Status: {status}")

def log_error(error: Exception, context: str = ""):
    """Log errors with context"""
    context_info = f" | Context: {context}" if context else ""
    logger.error(f"Error: {str(error)}{context_info}", exc_info=True)
