import logging
import sys
from datetime import datetime
from app.core.config import DEBUG

# Configure logging format
log_format = "%(asctime)s | %(levelname)s | %(name)s | %(message)s"
date_format = "%Y-%m-%d %H:%M:%S"

# Set logging level based on DEBUG setting
log_level = logging.DEBUG if DEBUG else logging.INFO

# Configure root logger
logging.basicConfig(
    level=log_level,
    format=log_format,
    datefmt=date_format,
    handlers=[
        logging.StreamHandler(sys.stdout),
        logging.FileHandler(f"logs/ecommerce_{datetime.now().strftime('%Y%m%d')}.log", mode='a')
    ]
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
