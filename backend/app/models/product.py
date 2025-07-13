from pydantic import BaseModel, validator
from typing import Optional, List
from datetime import datetime
from decimal import Decimal

class ProductBase(BaseModel):
    name: str
    description: str
    price: Decimal
    category_id: Optional[str] = None
    brand: Optional[str] = None
    sku: Optional[str] = None
    
    @validator('name')
    def validate_name(cls, v):
        if len(v.strip()) < 2:
            raise ValueError('Product name must be at least 2 characters long')
        return v.strip()
    
    @validator('price')
    def validate_price(cls, v):
        if v <= 0:
            raise ValueError('Price must be greater than 0')
        return v

class ProductCreate(ProductBase):
    stock_quantity: int = 0
    images: Optional[List[str]] = []
    
    @validator('stock_quantity')
    def validate_stock(cls, v):
        if v < 0:
            raise ValueError('Stock quantity cannot be negative')
        return v

class ProductUpdate(BaseModel):
    name: Optional[str] = None
    description: Optional[str] = None
    price: Optional[Decimal] = None
    category_id: Optional[str] = None
    brand: Optional[str] = None
    sku: Optional[str] = None
    stock_quantity: Optional[int] = None
    images: Optional[List[str]] = None
    is_active: Optional[bool] = None
    
    @validator('name')
    def validate_name(cls, v):
        if v and len(v.strip()) < 2:
            raise ValueError('Product name must be at least 2 characters long')
        return v.strip() if v else v
    
    @validator('price')
    def validate_price(cls, v):
        if v is not None and v <= 0:
            raise ValueError('Price must be greater than 0')
        return v
    
    @validator('stock_quantity')
    def validate_stock(cls, v):
        if v is not None and v < 0:
            raise ValueError('Stock quantity cannot be negative')
        return v

class Product(ProductBase):
    id: str
    stock_quantity: int
    images: List[str] = []
    is_active: bool = True
    created_at: datetime
    updated_at: Optional[datetime] = None
    
    class Config:
        from_attributes = True

class ProductResponse(BaseModel):
    id: str
    name: str
    description: str
    price: Decimal
    category_id: Optional[str] = None
    brand: Optional[str] = None
    sku: Optional[str] = None
    stock_quantity: int
    images: List[str] = []
    is_active: bool = True
    created_at: datetime
    category_name: Optional[str] = None

class ProductListResponse(BaseModel):
    products: List[ProductResponse]
    total: int
    page: int
    per_page: int
    total_pages: int

class CategoryBase(BaseModel):
    name: str
    description: Optional[str] = None
    parent_id: Optional[str] = None
    
    @validator('name')
    def validate_name(cls, v):
        if len(v.strip()) < 2:
            raise ValueError('Category name must be at least 2 characters long')
        return v.strip()

class CategoryCreate(CategoryBase):
    pass

class CategoryUpdate(BaseModel):
    name: Optional[str] = None
    description: Optional[str] = None
    parent_id: Optional[str] = None
    is_active: Optional[bool] = None
    
    @validator('name')
    def validate_name(cls, v):
        if v and len(v.strip()) < 2:
            raise ValueError('Category name must be at least 2 characters long')
        return v.strip() if v else v

class Category(CategoryBase):
    id: str
    is_active: bool = True
    created_at: datetime
    updated_at: Optional[datetime] = None
    
    class Config:
        from_attributes = True

class ProductSearchRequest(BaseModel):
    query: Optional[str] = None
    category_id: Optional[str] = None
    min_price: Optional[Decimal] = None
    max_price: Optional[Decimal] = None
    brand: Optional[str] = None
    in_stock_only: bool = False
    page: int = 1
    per_page: int = 20
    
    @validator('per_page')
    def validate_per_page(cls, v):
        if v > 100:
            raise ValueError('Per page cannot exceed 100')
        return v
