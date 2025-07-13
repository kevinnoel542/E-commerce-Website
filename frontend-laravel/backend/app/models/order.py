from pydantic import BaseModel, validator
from typing import Optional, List
from datetime import datetime
from decimal import Decimal
from enum import Enum

class OrderStatus(str, Enum):
    PENDING = "pending"
    CONFIRMED = "confirmed"
    PROCESSING = "processing"
    SHIPPED = "shipped"
    DELIVERED = "delivered"
    CANCELLED = "cancelled"
    REFUNDED = "refunded"

class PaymentStatus(str, Enum):
    PENDING = "pending"
    PAID = "paid"
    FAILED = "failed"
    REFUNDED = "refunded"

class OrderItemBase(BaseModel):
    product_id: str
    quantity: int
    unit_price: Decimal
    
    @validator('quantity')
    def validate_quantity(cls, v):
        if v <= 0:
            raise ValueError('Quantity must be greater than 0')
        return v
    
    @validator('unit_price')
    def validate_unit_price(cls, v):
        if v <= 0:
            raise ValueError('Unit price must be greater than 0')
        return v

class OrderItemCreate(OrderItemBase):
    pass

class OrderItem(OrderItemBase):
    id: str
    order_id: str
    product_name: str
    total_price: Decimal
    created_at: datetime
    
    class Config:
        from_attributes = True

class ShippingAddress(BaseModel):
    full_name: str
    phone: str
    address_line_1: str
    address_line_2: Optional[str] = None
    city: str
    state: str
    postal_code: str
    country: str = "Tanzania"
    
    @validator('full_name', 'phone', 'address_line_1', 'city', 'state', 'postal_code')
    def validate_required_fields(cls, v):
        if not v or len(v.strip()) == 0:
            raise ValueError('This field is required')
        return v.strip()

class OrderCreate(BaseModel):
    items: List[OrderItemCreate]
    shipping_address: ShippingAddress
    notes: Optional[str] = None
    
    @validator('items')
    def validate_items(cls, v):
        if not v or len(v) == 0:
            raise ValueError('Order must contain at least one item')
        return v

class OrderUpdate(BaseModel):
    status: Optional[OrderStatus] = None
    payment_status: Optional[PaymentStatus] = None
    tracking_number: Optional[str] = None
    notes: Optional[str] = None

class Order(BaseModel):
    id: str
    user_id: str
    order_number: str
    status: OrderStatus
    payment_status: PaymentStatus
    total_amount: Decimal
    shipping_amount: Decimal
    tax_amount: Decimal
    discount_amount: Decimal
    final_amount: Decimal
    currency: str
    shipping_address: ShippingAddress
    tracking_number: Optional[str] = None
    notes: Optional[str] = None
    created_at: datetime
    updated_at: Optional[datetime] = None
    items: List[OrderItem] = []
    
    class Config:
        from_attributes = True

class OrderResponse(BaseModel):
    id: str
    order_number: str
    status: OrderStatus
    payment_status: PaymentStatus
    total_amount: Decimal
    final_amount: Decimal
    currency: str
    created_at: datetime
    items_count: int
    shipping_address: ShippingAddress

class OrderListResponse(BaseModel):
    orders: List[OrderResponse]
    total: int
    page: int
    per_page: int
    total_pages: int

class OrderSummary(BaseModel):
    subtotal: Decimal
    shipping_amount: Decimal
    tax_amount: Decimal
    discount_amount: Decimal
    total_amount: Decimal
    currency: str

class CartItem(BaseModel):
    product_id: str
    quantity: int
    
    @validator('quantity')
    def validate_quantity(cls, v):
        if v <= 0:
            raise ValueError('Quantity must be greater than 0')
        return v

class Cart(BaseModel):
    items: List[CartItem]
    
    @validator('items')
    def validate_items(cls, v):
        if not v or len(v) == 0:
            raise ValueError('Cart must contain at least one item')
        return v

class CartSummary(BaseModel):
    items: List[dict]  # Product details with quantities
    summary: OrderSummary
