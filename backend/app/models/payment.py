from pydantic import BaseModel, validator
from typing import Optional, Dict, Any
from datetime import datetime
from decimal import Decimal
from enum import Enum

class PaymentStatus(str, Enum):
    """Stripe payment status enum"""
    PENDING = "pending"
    PROCESSING = "processing"
    SUCCEEDED = "succeeded"
    FAILED = "failed"
    CANCELED = "canceled"
    REFUNDED = "refunded"
    PARTIALLY_REFUNDED = "partially_refunded"

class PaymentMethodType(str, Enum):
    """Stripe payment method types"""
    CARD = "card"
    BANK_TRANSFER = "bank_transfer"
    SEPA_DEBIT = "sepa_debit"
    IDEAL = "ideal"
    SOFORT = "sofort"
    GIROPAY = "giropay"
    BANCONTACT = "bancontact"
    EPS = "eps"
    P24 = "p24"
    ALIPAY = "alipay"
    WECHAT_PAY = "wechat_pay"

class PaymentBase(BaseModel):
    """Base payment model"""
    order_id: str
    amount: Decimal
    currency: str = "usd"
    customer_email: str
    description: Optional[str] = None
    
    @validator('amount')
    def validate_amount(cls, v):
        if v <= 0:
            raise ValueError('Amount must be greater than 0')
        return v

    @validator('currency')
    def validate_currency(cls, v):
        # Stripe supported currencies (subset)
        supported_currencies = [
            'usd', 'eur', 'gbp', 'jpy', 'aud', 'cad', 'chf', 'cny', 'sek', 'nok', 'mxn', 'nzd', 'sgd', 'hkd', 'dkk', 'pln'
        ]
        if v.lower() not in supported_currencies:
            raise ValueError(f'Currency must be one of: {", ".join(supported_currencies)}')
        return v.lower()

class PaymentCreate(PaymentBase):
    """Model for creating payments"""
    success_url: Optional[str] = None
    cancel_url: Optional[str] = None
    payment_method_types: Optional[list] = ["card"]

class PaymentUpdate(BaseModel):
    """Model for updating payments"""
    status: Optional[PaymentStatus] = None
    amount_received: Optional[Decimal] = None
    amount_refunded: Optional[Decimal] = None
    succeeded_at: Optional[datetime] = None
    canceled_at: Optional[datetime] = None
    receipt_url: Optional[str] = None
    stripe_metadata: Optional[Dict[str, Any]] = None

class Payment(PaymentBase):
    """Complete payment model"""
    id: str
    
    # Stripe identifiers
    stripe_payment_intent_id: Optional[str] = None
    stripe_checkout_session_id: Optional[str] = None
    stripe_customer_id: Optional[str] = None
    stripe_charge_id: Optional[str] = None
    
    # Status and method
    status: PaymentStatus = PaymentStatus.PENDING
    payment_method_type: Optional[PaymentMethodType] = None
    
    # URLs
    checkout_url: Optional[str] = None
    success_url: Optional[str] = None
    cancel_url: Optional[str] = None
    receipt_url: Optional[str] = None
    
    # Financial tracking
    amount_received: Decimal = Decimal('0.00')
    amount_refunded: Decimal = Decimal('0.00')
    application_fee: Decimal = Decimal('0.00')
    
    # Timestamps
    created_at: datetime
    updated_at: Optional[datetime] = None
    succeeded_at: Optional[datetime] = None
    canceled_at: Optional[datetime] = None
    webhook_received_at: Optional[datetime] = None
    
    # Metadata
    stripe_metadata: Optional[Dict[str, Any]] = None
    webhook_data: Optional[Dict[str, Any]] = None
    invoice_id: Optional[str] = None
    
    class Config:
        from_attributes = True

class PaymentIntent(BaseModel):
    """Stripe Payment Intent model"""
    id: str
    amount: int  # Amount in cents
    currency: str
    status: str
    client_secret: str
    payment_method_types: list
    metadata: Optional[Dict[str, Any]] = None

class CheckoutSession(BaseModel):
    """Stripe Checkout Session model"""
    id: str
    url: str
    payment_status: str
    amount_total: int  # Amount in cents
    currency: str
    customer_email: Optional[str] = None
    metadata: Optional[Dict[str, Any]] = None

class PaymentResponse(BaseModel):
    """Payment response model"""
    id: str
    order_id: str
    status: PaymentStatus
    amount: Decimal
    currency: str
    checkout_url: Optional[str] = None
    created_at: datetime

class PaymentListResponse(BaseModel):
    """Payment list response model"""
    payments: list[PaymentResponse]
    total: int
    page: int
    per_page: int
    total_pages: int

class RefundRequest(BaseModel):
    """Refund request model"""
    payment_id: str
    amount: Optional[Decimal] = None  # If None, refund full amount
    reason: Optional[str] = None
    
    @validator('amount')
    def validate_refund_amount(cls, v):
        if v is not None and v <= 0:
            raise ValueError('Refund amount must be greater than 0')
        return v

class RefundResponse(BaseModel):
    """Refund response model"""
    id: str
    payment_id: str
    amount: Decimal
    currency: str
    status: str
    reason: Optional[str] = None
    created_at: datetime

class WebhookEvent(BaseModel):
    """Stripe webhook event model"""
    id: str
    type: str
    data: Dict[str, Any]
    created: int
    livemode: bool
    pending_webhooks: int
    request: Optional[Dict[str, Any]] = None

class PaymentStats(BaseModel):
    """Payment statistics model"""
    total_payments: int
    successful_payments: int
    pending_payments: int
    failed_payments: int
    total_amount: Decimal
    total_refunded: Decimal
    success_rate: float
    currency: str
