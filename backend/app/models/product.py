from pydantic import BaseModel, field_validator
from typing import Optional, List, Union
from datetime import datetime
from fastapi import UploadFile
import json

class ProductBase(BaseModel):
    name: str
    description: str
    price: float  # Changed from Decimal to float
    category_id: Optional[str] = None
    brand: Optional[str] = None
    sku: Optional[str] = None

    @field_validator('name')
    @classmethod
    def validate_name(cls, v):
        if len(v.strip()) < 2:
            raise ValueError('Product name must be at least 2 characters long')
        return v.strip()

    @field_validator('price')
    @classmethod
    def validate_price(cls, v):
        # Always convert to float, regardless of input type
        try:
            v = float(v)
        except (ValueError, TypeError):
            raise ValueError('Price must be a valid number')

        if v <= 0:
            raise ValueError('Price must be greater than 0')
        return v

class ProductCreate(ProductBase):
    stock_quantity: int = 0
    images: Optional[List[str]] = []
    @field_validator('stock_quantity')
    @classmethod
    def validate_stock(cls, v):
        if v < 0:
            raise ValueError('Stock quantity cannot be negative')
        return v

class ProductUpdate(BaseModel):
    name: Optional[str] = None
    description: Optional[str] = None
    price: Optional[float] = None  # Changed from Decimal to float
    category_id: Optional[str] = None
    brand: Optional[str] = None
    sku: Optional[str] = None
    stock_quantity: Optional[int] = None
    images: Optional[List[str]] = None
    is_active: Optional[bool] = None

    @field_validator('name')
    @classmethod
    def validate_name(cls, v):
        if v and len(v.strip()) < 2:
            raise ValueError('Product name must be at least 2 characters long')
        return v.strip() if v else v

    @field_validator('price')
    @classmethod
    def validate_price(cls, v):
        if v is not None:
            # Convert to float
            try:
                v = float(v)
            except (ValueError, TypeError):
                raise ValueError('Price must be a valid number')

            if v <= 0:
                raise ValueError('Price must be greater than 0')
        return v

    @field_validator('stock_quantity')
    @classmethod
    def validate_stock(cls, v):
        if v is not None and v < 0:
            raise ValueError('Stock quantity cannot be negative')
        return v

class ProductPatch(BaseModel):
    """Model for partial product updates - only allows specific fields"""
    name: Optional[str] = None
    description: Optional[str] = None
    price: Optional[float] = None
    category_id: Optional[str] = None
    brand: Optional[str] = None
    stock_quantity: Optional[int] = None
    is_active: Optional[bool] = None
    # Note: SKU and images are excluded from partial updates for safety

    @field_validator('name')
    @classmethod
    def validate_name(cls, v):
        if v and len(v.strip()) < 2:
            raise ValueError('Product name must be at least 2 characters long')
        return v.strip() if v else v

    @field_validator('price')
    @classmethod
    def validate_price(cls, v):
        if v is not None:
            try:
                v = float(v)
            except (ValueError, TypeError):
                raise ValueError('Price must be a valid number')

            if v <= 0:
                raise ValueError('Price must be greater than 0')
        return v

    @field_validator('stock_quantity')
    @classmethod
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
    price: float  # Changed from Decimal to float
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

    @field_validator('name')
    @classmethod
    def validate_name(cls, v):
        if len(v.strip()) < 2:
            raise ValueError('Category name must be at least 2 characters long')
        return v.strip()

    @field_validator('parent_id')
    @classmethod
    def validate_parent_id(cls, v):
        if v is not None and v.strip():
            import uuid
            try:
                uuid.UUID(v.strip())
                return v.strip()
            except ValueError:
                raise ValueError('parent_id must be a valid UUID format or null for root categories')
        return None

class CategoryCreate(CategoryBase):
    pass

class CategoryUpdate(BaseModel):
    name: Optional[str] = None
    description: Optional[str] = None
    parent_id: Optional[str] = None
    is_active: Optional[bool] = None

    @field_validator('name')
    @classmethod
    def validate_name(cls, v):
        if v and len(v.strip()) < 2:
            raise ValueError('Category name must be at least 2 characters long')
        return v.strip() if v else v

class CategoryPatch(BaseModel):
    """Model for partial category updates - only allows specific fields"""
    name: Optional[str] = None
    description: Optional[str] = None
    # Note: parent_id and is_active are excluded from partial updates for safety

    @field_validator('name')
    @classmethod
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
    min_price: Optional[float] = None  # Changed from Decimal to float
    max_price: Optional[float] = None  # Changed from Decimal to float
    brand: Optional[str] = None
    in_stock_only: bool = False
    page: int = 1
    per_page: int = 20

    @field_validator('per_page')
    @classmethod
    def validate_per_page(cls, v):
        if v > 100:
            raise ValueError('Per page cannot exceed 100')
        return v

class ImageUploadResponse(BaseModel):
    url: str
    filename: str
    size: int
    content_type: str

class ProductWithImages(BaseModel):
    product: Product
    uploaded_images: List[ImageUploadResponse] = []
